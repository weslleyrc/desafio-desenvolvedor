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
        
        // Verifica se o arquivo já foi enviado antes
        if (FileUpload::where('filename', $filename)->exists()) {
            return response()->json(['error' => 'Arquivo já enviado anteriormente.'], 400);
        }
    
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ['csv', 'xlsx'])) {
            return response()->json(['error' => 'Formato de arquivo não suportado.'], 400);
        }
    
        // Processa o arquivo e retorna os dados como array de documentos
        $data = $this->processFile($file, $extension);
    
        if (!$data) {
            return response()->json(['error' => 'Erro ao processar o arquivo.'], 500);
        }
    
        // Insere os documentos em lotes no MongoDB (batch insert)
        $batchSize = 1000; // Define o tamanho do lote
        $batches = array_chunk($data, $batchSize);
        
        foreach ($batches as $batch) {
            FileUpload::insert($batch);
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
    
        // Lê o conteúdo original do arquivo
        $content = file_get_contents($file->getRealPath());
    
        // Converte para UTF-8
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');
    
        // Quebra o conteúdo por linha
        $lines = preg_split("/\r\n|\n|\r/", $content);
    
        // Verifica se a primeira linha contém "Status do Arquivo"
        if (isset($lines[0]) && str_contains($lines[0], 'Status do Arquivo')) {
            array_shift($lines); // Remove a primeira linha
        }
    
        // Remove linhas vazias
        $lines = array_filter($lines, fn($line) => trim($line) !== '');
    
        // Junta novamente
        $content = implode(PHP_EOL, $lines);
    
        // Salva em um arquivo temporário
        $tmpPath = storage_path('app/tmp_upload.csv');
        file_put_contents($tmpPath, $content);
    
        // Agora lê com o League\Csv
        $csv = Reader::createFromPath($tmpPath, 'r');
        $csv->setDelimiter(";"); // Delimitador correto
        $csv->setHeaderOffset(0); // Primeira linha como cabeçalho
    
        // Adiciona os campos extras em cada registro
        $records = [];
        foreach ($csv->getRecords() as $record) {
            $record['filename'] = $filename;
            $record['uploaded_at'] = $uploadedAt;
            $records[] = $record;
        }
    
        return $records;
    }

    private function processExcel($file)
    {
        $data = Excel::toArray([], $file);
        if (empty($data) || empty($data[0])) {
            return null;
        }
    
        $header = array_shift($data[0]); // Primeira linha como cabeçalho
        $records = [];
    
        foreach ($data[0] as $row) {
            $document = array_combine($header, $row);
    
            foreach ($document as $key => $value) {
                $document[$key] = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
            }
    
            $document['filename'] = request()->file('file')->getClientOriginalName();
            $document['uploaded_at'] = now();
    
            $records[] = $document;
        }
    
        return $records;
    }
}