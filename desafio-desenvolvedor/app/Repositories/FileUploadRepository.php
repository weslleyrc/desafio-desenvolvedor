<?php

namespace App\Repositories;

use App\Models\FileUpload;

class FileUploadRepository
{
    public function findByFilename($filename)
    {
        // Verifica se o arquivo já existe no MongoDB pelo nome
        return FileUpload::where('filename', $filename)->first();
    }

    public function store(array $data)
    {
        // Armazena os dados no MongoDB
        return FileUpload::create($data);
    }
}