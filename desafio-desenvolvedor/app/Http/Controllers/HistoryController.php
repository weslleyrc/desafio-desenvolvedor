<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Validator;

class HistoryController extends Controller
{
    public function getHistory(Request $request)
    {
        //validando (essa merda) os parametros de busca
        $validator = Validator::make($request->all(),[
            'filename' => 'nullable|string', //nome do arquivo
            'uploaded_at' => 'nullable|date_format:Y-m-d',//data do arquivo
        ]);

        //se a validação valhar ele chama o bad request (bad romanceeeee)
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()]. 400);
        }

        // Busca os arquivos no histórico (procurando nemo!)
        $query = FileUpload::query();

        //Se positivo vai adicionar ao filtro
        if($request->has('uploaded_at')){
            $query->whereDate('uploaded_at', '=', $request->input('uploaded_at'));
        }

        //Realizando a query e retornando o resultado (aleluia senhor!!!)
        $uploads = $query->get();

        //Se não encontrar nenhum upload chama o 404 (tem ninguem em casa!!)
        if($uploads->isEmpty()){
            return response()->json(['message' => 'Nenhum histórico encontrado'], 404);
        }

        //Retorna os uploads encontrados (Vitóriaaaaa ayrton senna em primeiro na linha de chegada)
        return response()->json([
            'message' => 'Histórico de uploads encontrado.',
            'data' => $uploads
        ],200);
    }
}
