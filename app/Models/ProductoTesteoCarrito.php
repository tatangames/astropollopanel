<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoTesteoCarrito extends Model
{
    use HasFactory;
    protected $table = 'producto_testeo_carrito';
    public $timestamps = false;
}
