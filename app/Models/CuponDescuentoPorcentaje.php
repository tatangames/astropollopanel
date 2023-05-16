<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuponDescuentoPorcentaje extends Model
{
    use HasFactory;
    protected $table = 'c_descuento_porcentaje';
    public $timestamps = false;
}
