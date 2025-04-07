<?php

namespace App\Repositories;

use App\Models\FileUpload;

class FileUploadRepository
{   //esse cara aqui vai me ajudar na busca do segundo endpoint e na paginação do mesmo
    public function searchFileContent($tckrSymb, $rptDt, $page = 1, $perPage = 10)
    {
        $query = FileUpload::query();

        if ($tckrSymb) {
            $query->where('TckrSymb', $tckrSymb);
        }

        if ($rptDt) {
            $query->where('RptDt', $rptDt);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}