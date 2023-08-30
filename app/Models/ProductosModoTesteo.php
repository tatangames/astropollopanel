<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosModoTesteo extends Model
{
    use HasFactory;
    protected $table = 'productos_modotesteo';
    public $timestamps = false;

    protected $fillable = [
        'posicion'
    ];
}