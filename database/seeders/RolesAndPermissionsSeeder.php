<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'dashboard.ver',
            
            // Equipos
            'equipos.ver', 'equipos.crear', 'equipos.editar', 'equipos.eliminar', 'equipos.exportar', 'equipos.importar',
            
            // Usuarios Asignados
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            
            // Checklists
            'checklist.ver', 'checklist.crear', 'checklist.editar', 'checklist.eliminar',
            
            // Licencias
            'licencias.ver', 'licencias.crear', 'licencias.editar', 'licencias.eliminar',
            
            // Mesa de Ayuda
            'mesaayuda.ver', 'mesaayuda.crear', 'mesaayuda.editar', 'mesaayuda.cerrar',
            
            // Historiales
            'historial.ver', 'historial.exportar',
            
            // Configuración
            'configuracion.ver', 'configuracion.editar',
            
            // Roles
            'roles.ver', 'roles.crear', 'roles.editar', 'roles.eliminar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign created permissions

        // 1. Administrador (acceso total)
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $roleAdmin->syncPermissions(Permission::all());

        // 2. Consulta (solo lectura)
        $roleConsulta = Role::firstOrCreate(['name' => 'Consulta']);
        $roleConsulta->syncPermissions([
            'dashboard.ver',
            'equipos.ver',
            'usuarios.ver',
            'checklist.ver',
            'licencias.ver',
            'mesaayuda.ver',
            'historial.ver',
            'configuracion.ver',
            'roles.ver'
        ]);

        // 3. Inventarios (Dashboard, Equipos, Checklists)
        $roleInventarios = Role::firstOrCreate(['name' => 'Inventarios']);
        $roleInventarios->syncPermissions([
            'dashboard.ver',
            'equipos.ver', 'equipos.crear', 'equipos.editar', 'equipos.eliminar', 'equipos.exportar', 'equipos.importar',
            'checklist.ver', 'checklist.crear', 'checklist.editar', 'checklist.eliminar'
        ]);

        // 4. Mesa de Ayuda (Mesa de Ayuda, Equipos, Historiales)
        $roleMesaAyuda = Role::firstOrCreate(['name' => 'Mesa de Ayuda']);
        $roleMesaAyuda->syncPermissions([
            'mesaayuda.ver', 'mesaayuda.crear', 'mesaayuda.editar', 'mesaayuda.cerrar',
            'equipos.ver', 'equipos.crear', 'equipos.editar', 'equipos.eliminar', 'equipos.exportar', 'equipos.importar',
            'historial.ver', 'historial.exportar'
        ]);
        
        // 5. Otros roles mencionados en el requerimiento
        Role::firstOrCreate(['name' => 'Soporte TI']);
        Role::firstOrCreate(['name' => 'Auditor']);
        Role::firstOrCreate(['name' => 'Licencias']);
    }
}
