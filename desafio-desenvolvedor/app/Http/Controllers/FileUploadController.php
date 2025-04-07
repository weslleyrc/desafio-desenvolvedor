<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploadService;

class FileUploadController extends Controller
{
    protected $fileUploadService;

    //recebe o service (assim esperamos)
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    //Recebe o arquivo, passa para o service validar, salva e insere no DBZ
    public function upload(Request $request)
    {
        return $this->fileUploadService->uploadFile($request);
    }
}