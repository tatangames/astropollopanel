<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuponDescuentoDinero extends Model
{
    use HasFactory;
    protected $table = 'c_descuento_dinero';
    public $timestamps = false;
}
