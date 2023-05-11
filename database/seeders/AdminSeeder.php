<?php

namespace Database\Seeders;

use App\Models\Administrador;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Administrador::create([
            'nombre' => 'Administrador',
            'usuario' => 'admin',
            'password' => bcrypt('1234'),
            'activo' => '1'
        ])->assignRole('Super-Admin');
    }
}
