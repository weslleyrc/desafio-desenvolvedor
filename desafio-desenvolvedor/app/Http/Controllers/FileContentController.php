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
        $tckrSymb = $request->query('TckrSymb'); //Código do ativo
        $rptDt = $request->query('RptDt'); //Data do ativo

        $page = $request->query('page', 1); //Pagina que vai iniciar
        $perPage = $request->query('per_page', 10);//itens por pagina

        $result = $this->fileUploadRepository->searchFileContent($tckrSymb, $rptDt, $page, $perPage); //O cara que vai no banco buscar as info

        //Se nao encontrar nada, retorna com 404 (NINGUEM EM CASA kkk)
        if($result->isEmpty()){
            return response()->json(['message' => 'Nenhum conteúdo encontrado.'], 404);
        }

        //Se encontrar retorna com um JSON bonitão
        return response()->json([
            'message' => 'Resultados encontrados',
            'data' => $result->items(), // se estiver paginando
        ]);

    }
}
