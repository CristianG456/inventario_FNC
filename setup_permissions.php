<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
Permission::firstOrCreate(['name' => 'campos_personalizados.ver']);
Permission::firstOrCreate(['name' => 'campos_personalizados.crear']);
Permission::firstOrCreate(['name' => 'campos_personalizados.editar']);
Permission::firstOrCreate(['name' => 'campos_personalizados.eliminar']);
$role = Role::where('name', 'Administrador')->first();
if($role) {
    $role->givePermissionTo(['campos_personalizados.ver', 'campos_personalizados.crear', 'campos_personalizados.editar', 'campos_personalizados.eliminar']);
}
echo "OK\n";
