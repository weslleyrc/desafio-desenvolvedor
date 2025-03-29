<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\FileContentController;

// Definindo a rota para o upload
Route::post('/upload', [FileUploadController::class, 'upload']);

//Definindo a rota para o historico de uploads
Route::get('/upload/history', [HistoryController::class, 'gethistory']);

//Definindo a rota para a busca de parametros
Route::get('/file/search', [FileContentController::class, 'searchContent']);