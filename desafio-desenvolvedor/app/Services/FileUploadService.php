<?php

namespace App\Services;

use App\Repositories\FileUploadRepository;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Maatwebsite\Excel\Facades\Excel;

class FileUploadService
{
    protected $fileUploadRepository;

    public function __construct(FileUploadRepository $fileUploadRepository)
    {
        $this->fileUploadRepository = $fileUploadRepository;
    }

    public function uploadFile($file)
    {
        $filename = $file->getClientOriginalName();

        //verifica se o arquivo ja foi enviado
        if ($this->fileUploadRepository->findByFilename($filename)) {
            return ['error' => 'O arquivo ja foi enviado anteriormente.'];
        }

        //salva o arquivo no storage
        $path = $file->store('uploads');

        //lê o conteúdo do arquivo
        $content = $this->readFile($file);

        //Salva no MongoDB
        return $this->fileUploadRepository->store([
            'filename' => $filename,
            'uploaded_at' => now(),
            'content' => $content
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