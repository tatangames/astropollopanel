<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesPremio extends Model
{
    use HasFactory;
    protected $table = 'ordenes_premio';
    public $timestamps = false;
}
