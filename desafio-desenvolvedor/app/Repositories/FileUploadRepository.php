<?php

namespace App\Repositories;

use App\Models\FileUpload;

class FileUploadRepository
{
    public function findByFilename($filename)
    {
        // Verifica se o arquivo já existe no MongoDB(z) pelo nome
        return FileUpload::where('filename', $filename)->first();
    }

    public function store(array $data)
    {
        // Armazena os dados no MongoDB(z)
        return FileUpload::create($data);
    }

    public function searchFileContent($tckrSymb = null, $rptDt = null, $page = 1, $perPage = 10)
    {
        $query = FileUpload::query();

        if($tckrSymb){
            $query->where('content.TckrSymb', $tckrSymb);
        }

        if($rptDt){
            $query->where('content.RptDt', $rptDt);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}