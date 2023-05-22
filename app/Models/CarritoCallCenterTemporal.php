<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoCallCenterTemporal extends Model
{
    use HasFactory;
    protected $table = 'carrito_callcenter_tempo';
    public $timestamps = false;
}
