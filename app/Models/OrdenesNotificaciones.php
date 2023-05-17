<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesNotificaciones extends Model
{
    use HasFactory;
    protected $table = 'ordenes_notificacion';
    public $timestamps = false;

}
