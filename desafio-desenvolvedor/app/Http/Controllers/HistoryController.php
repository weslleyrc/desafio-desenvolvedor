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
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        //se a validação valhar ele chama o bad request (bad romanceeeee)
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()]. 400);
        }

        // Busca os arquivos no histórico (procurando nemo!)
        $query = FileUpload::select('filename', 'uploaded_at');

        //Se positivo vai adicionar ao filtro
        if($request->filled('filename')){
            $query->where('filename', 'like', '%' . $request->input('filename') . '%');
        }

        //Realizando a query e retornando o resultado (aleluia senhor!!!)
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $uploads = $query->orderBy('uploaded_at', 'desc')
                         ->paginate($perPage, ['*'], 'page', $page);

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
