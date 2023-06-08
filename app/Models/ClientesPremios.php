<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientesPremios extends Model
{
    use HasFactory;
    protected $table = 'clientes_premios';
    public $timestamps = false;

}
