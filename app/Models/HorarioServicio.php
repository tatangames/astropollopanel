<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioServicio extends Model
{
    use HasFactory;
    protected $table = 'horario_servicio';
    public $timestamps = false;
}
