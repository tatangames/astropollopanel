<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteProblema extends Model
{
    use HasFactory;
    protected $table = 'reporte_problemas';
    public $timestamps = false;
}
