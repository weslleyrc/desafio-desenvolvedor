<?php

namespace App\Services;

use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Maatwebsite\Excel\Facades\Excel;

class FileUploadService
{
    public function uploadFile(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'Nenhum arquivo enviado.'], 400);
        }
    
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        
        // Verifica se o arquivo já existe no dbz
        if (FileUpload::where('filename', $filename)->exists()) {
            return response()->json(['error' => 'Arquivo já enviado anteriormente.'], 400);
        }

        //Verifica a extensão do arquivo
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ['csv', 'xlsx'])) {
            return response()->json(['error' => 'Formato de arquivo não suportado.'], 400);
        }
    
        // Processa o arquivo e retorna os dados como array de documentos
        $success = $this->processFile($file, $extension);

        if (!$success) {
            return response()->json(['error' => 'Erro ao processar o arquivo.'], 500);
        }
    
        return response()->json(['message' => 'Upload realizado com sucesso!'], 201);
    }

    private function processFile($file, $extension)
    {
        // Processa CSV
        if ($extension === 'csv') {
            return $this->processCsv($file);
        }

        // Processa Excel
        if ($extension === 'xlsx') {
            return $this->processExcel($file);
        }

        return null;
    }

    private function processCsv($file)
    {
        $filename = $file->getClientOriginalName();
        $uploadedAt = now();

         // Lê o conteúdo original
        $content = file_get_contents($file->getRealPath());

        // Converte para UTF-8
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');

        // Quebra por linhas
        $lines = preg_split("/\r\n|\n|\r/", $content);

        // Remove a linha extra com "Status do Arquivo", se necessário
        if (isset($lines[0]) && str_contains($lines[0], 'Status do Arquivo')) {
            array_shift($lines); // remove a primeira linha
        }

        // Remove linhas vazias
        $lines = array_filter($lines, fn($line) => trim($line) !== '');

        // Junta de novo
        $content = implode(PHP_EOL, $lines);

        // Salva em arquivo temporário
        $tmpPath = storage_path('app/tmp_upload.csv');
        file_put_contents($tmpPath, $content);

        // Lê com League\Csv
        $csv = Reader::createFromPath($tmpPath, 'r');
        $csv->setDelimiter(";");

        // Agora sim, define a primeira linha REAL como cabeçalho
        $csv->setHeaderOffset(0);

        $records = [];

        foreach ($csv->getRecords() as $record) {
            $record['filename'] = $filename;
            $record['uploaded_at'] = $uploadedAt;
            $records[] = $record;

            // Insere por lote a cada 1000 registros
            if (count($records) >= 1000) {
                FileUpload::insert($records);
                $records = [];
            }
        }

        // Insere o que restar
        if (count($records) > 0) {
            FileUpload::insert($records);
        }

        return true;
    }

    private function processExcel($file)
    {
        $data = Excel::toArray([], $file);
        if (empty($data) || empty($data[0])) {
            return null;
        }
    
        //Remove a linha extra "Status do Arquivo"

        if(isset($data[0][0]) && str_contains($data[0][0][0], 'Status do Arquivo')){
            array_shift($data[0]);
        }

        $header = array_shift($data[0]);
        $records = [];

        $filename = $file->getClientOriginalName();
        $uploadedAt = now();
    
        foreach ($data[0] as $row) {
            $document = array_combine($header, $row);
    
            foreach ($document as $key => $value) {
                $document[$key] = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
            }
    
            $document['filename'] = request()->file('file')->getClientOriginalName();
            $document['uploaded_at'] = now();
    
            $records[] = $document;

            //inserir por lote a cada 1000 registros
            if(count($records) >= 1000){
                FileUpload::insert($records);
                $records;
            }
        
        }
        //verifica se há registros
        if (count($records) > 0 ) {
            FileUpload::insert($records);
        }

        return $records;
    }
}