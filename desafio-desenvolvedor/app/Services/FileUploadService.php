<?php

namespace App\Services;

use App\Repositories\FileUploadRepository;
use MongoDB\Client;
use League\Csv\Reader;
use Maatwebsite\Excel\Facades\Excel;

class FileUploadService
{
    protected $fileUploadRepository;
    protected $bucket;

    public function __construct(FileUploadRepository $fileUploadRepository)
    {
        $this->fileUploadRepository = $fileUploadRepository;

         // Conecta com o GridFS do MongoDB
         $client = new Client(
            env('MONGO_DSN', 'mongodb://' . env('DB_USERNAME') . ':' . env('DB_PASSWORD') . '@' . env('DB_HOST') . ':' . env('DB_PORT'))
        );
        $this->bucket = $client->selectDatabase(env('DB_DATABASE', 'desafio'))->selectGridFSBucket();
    }

    public function uploadFile($file)
    {
        $filename = $file->getClientOriginalName();

        // Verifica se o arquivo já foi enviado para o DBZ
         if ($this->fileUploadRepository->findByFilename($filename)) {
            return ['error' => 'O arquivo já foi enviado anteriormente.'];
        }

        // Salva o arquivo no GridFS
        $stream = fopen($file->getRealPath(), 'rb');
        $fileId = $this->bucket->uploadFromStream($filename, $stream);
        fclose($stream);

        // Lendo o conteúdo do arquivo
        $content = $this->readFile($file);

        // Salva as informações no MongoDB
        return $this->fileUploadRepository->store([
            'filename' => $filename, //nome do arquivo
            'uploaded_at' => now(),  //data de upload
            'file_id' => (string) $fileId, //id do arquivo no GridFS
        ]);
        
    }

    private function readFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $content = [];

        if($extension === 'csv') {
            $reader = Reader::createFromPath($file->getRealPath(), 'r');
            $reader->setHeaderOffset(0);
            foreach ($reader as $row){
                $content[] = $row;
            }
        } elseif ($extension === 'xlsx'){
            $content = Excel::toArray([], $file)[0];
        }

        return $content;
    }
}