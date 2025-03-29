<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
//use App\Http\Middleware\AdminMiddleware;

// Definindo a rota para o upload
Route::post('/upload', [FileUploadController::class, 'upload']);

//Definindo a rota para o historico de uploads
Route::post('/upload/history', [HistoryController::class, 'gethistory']);