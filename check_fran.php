<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$eqs = \App\Models\Equipo::whereHas('usuarioAsignado', function($q) {
    $q->where('nombre', 'like', '%FRANCISCO JAVIER NU%');
})->get();

foreach($eqs as $eq) {
    echo "Equipo ID: " . $eq->id . " | Serial: " . $eq->serial . " | razon_estado: " . $eq->razon_estado . PHP_EOL;
    $ar = $eq->asignacionesResponsabilidad()->where('estado', 'activa')->first();
    if ($ar) {
        echo "  --> AR exists: " . $ar->nombre_usuario . " | " . $ar->documento . PHP_EOL;
    } else {
        echo "  --> NO AR" . PHP_EOL;
    }
}
