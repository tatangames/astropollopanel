<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DireccionesRestaurante extends Model
{
    use HasFactory;
    protected $table = 'direcciones_restaurante';
    public $timestamps = false;
}
