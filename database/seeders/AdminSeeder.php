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
            'activo' => '1',
            'correo' => 'tatangamess@gmail.com',
            'token_correo' => null,
            'token_fecha' => null
        ])->assignRole('Admin');

        Administrador::create([
            'nombre' => 'Editor',
            'usuario' => 'editor',
            'password' => bcrypt('1234'),
            'activo' => '1',
            'correo' => 'tatangamess@gmail.com',
            'token_correo' => null,
            'token_fecha' => null
        ])->assignRole('Editor');

        Administrador::create([
            'nombre' => 'Colaborador',
            'usuario' => 'colaborador',
            'password' => bcrypt('1234'),
            'activo' => '1',
            'correo' => '',
            'token_correo' => null,
            'token_fecha' => null
        ])->assignRole('Colaborador');





    }
}
