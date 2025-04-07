<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class FileUpload extends Eloquent
{
    // Conecta com MongoDB(Z)
    protected $connection = 'mongodb'; 

    // Nome da coleção no banco
    protected $collection = 'uploads'; 

    // Como enviarei as informaçoes para o dbz
    protected $guarded = [];
}