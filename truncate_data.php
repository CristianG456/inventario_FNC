<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tablesToTruncate = [
    'actas_firmadas',
    'actas_firmadas_versions',
    'activo_complementos',
    'asignaciones',
    'asignaciones_responsabilidad',
    'audit_logs',
    'autorizaciones_activos',
    'campo_personalizado_valores',
    'checklists',
    'equipos',
    'funcionarios',
    'historial_administrativos',
    'historial_complementos',
    'historial_tecnicos',
    'licencia_asignaciones',
    'licencia_historial',
    'licencia_seriales',
    'licencias',
    'perifericos',
    'prestamos',
    'seguimientos',
    'solicitudes_cambio_password',
    'suscripcion_asignaciones',
    'suscripcion_historiales',
    'suscripciones',
    'tickets',
    'usuario_asignados',
    'vitalicia_asignaciones',
    'vitalicia_historiales',
    'vitalicias'
];

Schema::disableForeignKeyConstraints();

foreach ($tablesToTruncate as $table) {
    if (Schema::hasTable($table)) {
        DB::table($table)->truncate();
        echo "Truncated table: $table\n";
    }
}

Schema::enableForeignKeyConstraints();
echo "Done!\n";
