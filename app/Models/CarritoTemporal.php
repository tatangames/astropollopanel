<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoTemporal extends Model
{
    use HasFactory;
    protected $table = 'carrito_temporal';
    public $timestamps = false;
}
