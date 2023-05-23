<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallCenterCliente extends Model
{
    use HasFactory;
    protected $table = 'callcenter_cliente';
    public $timestamps = false;
}
