<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class FileUpload extends Eloquent
{
    // Conecta com MongoDB(Z)
    protected $connection = 'mongodb'; 

    // Nome da coleção no banco
    protected $collection = 'uploads'; 

    // Informaçoes para o banco
    protected $guarded = [];
}