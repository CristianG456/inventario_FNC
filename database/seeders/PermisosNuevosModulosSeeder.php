<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermisosNuevosModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            'suscripciones.ver', 'suscripciones.crear', 'suscripciones.editar', 'suscripciones.eliminar', 'suscripciones.exportar',
            'vitalicias.ver', 'vitalicias.crear', 'vitalicias.editar', 'vitalicias.eliminar', 'vitalicias.exportar'
        ];

        foreach ($permisos as $permiso) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permiso]);
        }

        $rolAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Administrador']);
        $rolAdmin->givePermissionTo($permisos);
    }
}
