<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    //dd($request->file('file')->getMimeType());
    // Validação do Arquivo
    $file = $request->file('file');

    dd($request->file('file')->getMimeType());

    $validator = Validator::make($request->all(), [
        'file' => 'required|mimes:csv,txt,text/plain,text/csv|max:1024000'
    ]);

    if ($validator->fails()){
        return response()->json(['error' => $validator->errors()], 400);
    }
    //chama o metodo de upload
    $result = $this->fileUploadService->uploadFile($request->file('file'));

    //caso retorne erro
    if (isset($result['error'])) {
        return response()->json($result, 400);
    }

     // Retorna a resposta de sucesso com mais informações, como o nome do arquivo
     dd('papai');
     return response()->json([
        'message' => 'Upload realizado com sucesso!', 
        'data' => [
            'filename' => $result['filename'], // Supondo que o resultado contenha o nome do arquivo
            'file_path' => $result['file_path'], // Caso você armazene o caminho do arquivo
        ]
    ], 201);
}
}
