<?php

namespace Database\Seeders;

use App\Models\TipoCupon;
use Illuminate\Database\Seeder;

class TipoCuponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoCupon::create([
            'nombre' => 'Producto Gratis',
        ]);

        TipoCupon::create([
            'nombre' => 'Descuento Dinero',
        ]);

        TipoCupon::create([
            'nombre' => 'Descuento Porcentaje',
        ]);
    }
}
