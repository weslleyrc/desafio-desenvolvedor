<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $connection = 'mongodb'; // Define a conexão com MongoDB
    protected $collection = 'uploads'; // Nome da coleção no banco

    protected $fillable = [
        'filename',
        'uploaded_at',
        'content'
    ];
}
