<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Populares extends Model
{
    use HasFactory;
    protected $table = 'populares';
    public $timestamps = false;


    protected $fillable = [
        'posicion'
    ];
}
