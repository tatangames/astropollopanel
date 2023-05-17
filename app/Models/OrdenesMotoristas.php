<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesMotoristas extends Model
{
    use HasFactory;
    protected $table = 'ordenes_motoristas';
    public $timestamps = false;
}
