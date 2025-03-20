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
    // Validação do Arquivo
    $validator = Validator::make($request->all(), [
        'file' => 'required|mimes:csv,xlsx|max:51200'
    ]);

    if ($validator->fails()){
        return response()->json(['error' => $validator->errors()], 400);
    }
    $result = $this->fileUploadService->uploadFile($request->file('file'));

    if (isset($result['error'])) {
        return response()->json($result, 400);
    }

    return response()->json(['message' => 'Upload realizado com sucesso!', 'data' => $result], 201);

   }
}
