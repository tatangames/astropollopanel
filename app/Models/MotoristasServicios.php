<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotoristasServicios extends Model
{
    use HasFactory;
    protected $table = 'motoristas_servicios';
    public $timestamps = false;
}
