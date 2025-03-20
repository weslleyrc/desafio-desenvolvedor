<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class FileUpload extends Eloquent
{
    // Define a conexão com MongoDB
    protected $connection = 'mongodb'; 

    // Nome da coleção no banco
    protected $collection = 'uploads'; 

    // Campos que podem ser preenchidos
    protected $fillable = [
        'filename',
        'uploaded_at',
        'content'
    ];
}