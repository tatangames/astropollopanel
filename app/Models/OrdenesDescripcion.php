<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesDescripcion extends Model
{
    use HasFactory;
    protected $table = 'ordenes_descripcion';
    public $timestamps = false;
}
