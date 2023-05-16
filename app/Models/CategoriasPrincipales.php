<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriasPrincipales extends Model
{
    use HasFactory;

    protected $table = 'categorias_principales';
    public $timestamps = false;

    protected $fillable = [
        'posicion'
    ];

}
