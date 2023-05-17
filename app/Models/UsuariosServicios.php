<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuariosServicios extends Model
{
    use HasFactory;
    protected $table = 'usuarios_servicios';
    public $timestamps = false;
}
