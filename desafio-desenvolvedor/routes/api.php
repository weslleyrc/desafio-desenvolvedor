<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;

//Definindo a rota para o upload
Route::post('/upload', [FileUploadController::class, 'upload'])->middleware('admin');