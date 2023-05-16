<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesDirecciones extends Model
{
    use HasFactory;
    protected $table = 'ordenes_direcciones';
    public $timestamps = false;
}
