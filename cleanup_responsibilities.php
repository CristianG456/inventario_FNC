<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find responsibilities that are actually just the same user 
// with no separate responsable, which were incorrectly created.

$falsasResponsabilidades = \App\Models\AsignacionResponsabilidad::where('estado', 'activa')
    ->where(function($q) {
        // No separate responsable cedula OR it's the exact same as the assigned document
        $q->whereNull('responsable_cedula')
          ->orWhereColumn('responsable_cedula', 'documento');
    })->get();

$count = 0;
foreach($falsasResponsabilidades as $resp) {
    // Verificar si el equipo realmente tiene un usuario_asignado idéntico
    $eq = $resp->equipo;
    if ($eq && $eq->usuarioAsignado && $eq->usuarioAsignado->cedula === $resp->documento) {
        $resp->update([
            'estado' => 'finalizada',
            'fecha_final_real' => now()->toDateString(),
            'motivo_finalizacion' => 'Limpieza de asignación normal (falsa responsabilidad)'
        ]);
        $count++;
    }
}

echo "Se limpiaron {$count} falsas responsabilidades en la base de datos.\n";
