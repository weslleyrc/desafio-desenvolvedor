<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\FileUploadRepository;

class FileContentController extends Controller
{
    protected $fileUploadRepository;

    public function __construct(FileUploadRepository $fileUploadRepository)
    {
        $this->fileUploadRepository = $fileUploadRepository;
    }

    public function searchContent(Request $request)
    {
        $tckrSymb = $request->query('TckrSymb'); //1 parametro de busca
        $rptDt = $request->query('RptDt'); //2 parametro de busca

        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $result = $this->fileUploadRepository->searchFileContent($tckrSymb, $rptDt, $page, $perPage);


        if($result->isEmpty()){
            return response()->json(['message' => 'Nenhum conteúdo encontrado.'], 404);
        }

        return response()->json([
            'message' => 'Resultados encontrados',
            'data' => $result
        ]);

    }
}
