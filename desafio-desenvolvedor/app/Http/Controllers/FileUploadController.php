<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
   protected $fileUploadService;

   public function __construct(FileUploadService $fileUploadService)
   {
        $this->fileUploadService = $fileUploadService;
   }

   public function upload(Request $request)
{
    /*
    // ESSA MERDA NAO FUNCIONA
    $validator = Validator::make($request->all(), [
        "file" => "required|mimes:csv,xlsx,text/plain,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:1024000"
    ]);

    // Se a validação falhar, retorna erro BAD REQUEST (SO TEM DADO ISSO, RETIREI ESSE CARALHA)
    if ($validator->fails()){
        return response()->json(['error' => $validator->errors()], 400);
    }
    }*/

    // Realiza o upload do arquivo
    $result = $this->fileUploadService->uploadFile($request->file('file'));

    // Se tiver erro no upload, retorna o erro
    if (isset($result['error'])) {
        return response()->json($result, 400);
    }

    // Retorna a resposta de sucesso
    return response()->json([
        'message' => 'Upload realizado com sucesso!',
        'data' => [
            'filename' => $result['filename'], // Nome do arquivo
            'uploaded_at' => $result['uploaded_at'] //Data do upload
        ]
    ], 201);
}
}