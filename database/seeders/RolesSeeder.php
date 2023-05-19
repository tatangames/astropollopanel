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

        // ADMINISTRADOR PARA MANEJAR LOS ROLES Y PERMISOS
        $roleAdmin = Role::create(['name' => 'Admin']);

        // REALIZA TODAS LAS FUNCIONES MENOS LOS ROLES Y PERMISOS
        $roleEditor = Role::create(['name' => 'Editor']);

        // MANEJA CALL CENTER
        $roleColaborador = Role::create(['name' => 'Colaborador']);


        // Roles y Permisos
        Permission::create(['name' => 'sidebar.roles.y.permisos', 'description' => 'Vista para permisos'])->syncRoles($roleAdmin);


        Permission::create(['name' => 'sidebar.zonas', 'description' => 'Vista para creacion de zonas'])->syncRoles($roleEditor);

        Permission::create(['name' => 'sidebar.ordenes', 'description' => 'Vista para ver ordenes'])->syncRoles($roleEditor);

        Permission::create(['name' => 'sidebar.usuarios', 'description' => 'Vista para ver usuarios de todos'])->syncRoles($roleEditor);













    }
}
