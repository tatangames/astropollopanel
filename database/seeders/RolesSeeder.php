<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // administrador con todos los permisos
        $role1 = Role::create(['name' => 'Super-Admin']);

        Permission::create(['name' => 'seccion.estadisticas', 'description' => 'Vista para estadisticas de la App'])->syncRoles($role1);

        // roles y permisos
        Permission::create(['name' => 'seccion.permisos', 'description' => 'Vista para permisos'])->syncRoles($role1);

    }
}
