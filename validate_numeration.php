<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Equipo;
use App\Models\Funcionario;
use App\Models\UsuarioAsignado;
use App\Models\AsignacionResponsabilidad;

$totalEquipos = Equipo::whereNull('deleted_at')->count();
$totalFuncionarios = Funcionario::count();
$totalAsignaciones = UsuarioAsignado::count();
$totalResponsabilidades = AsignacionResponsabilidad::count();

echo "--- VALIDACIÓN POST-CAMBIO ---\n";
echo "Equipos activos: {$totalEquipos}\n";
echo "Funcionarios: {$totalFuncionarios}\n";
echo "UsuarioAsignado: {$totalAsignaciones}\n";
echo "AsignacionesResponsabilidad: {$totalResponsabilidades}\n";

$primerEquipo = Equipo::orderBy('id', 'asc')->first();
$ultimoEquipo = Equipo::orderBy('id', 'desc')->first();

echo "\n--- VALIDACIÓN DE CÓDIGOS ---\n";
echo "Primer Equipo (ID {$primerEquipo->id}): " . $primerEquipo->identificador_interno . "\n";
echo "Último Equipo (ID {$ultimoEquipo->id}): " . $ultimoEquipo->identificador_interno . "\n";

// Sample list of first 5 to verify numbering
$top5 = Equipo::orderBy('id', 'asc')->limit(5)->get();
echo "\nPrimeros 5 equipos:\n";
foreach($top5 as $eq) {
    echo "ID: {$eq->id} | Código: {$eq->identificador_interno} | Consecutivo: {$eq->consecutivo}\n";
}

// Check latest ones
$last5 = Equipo::orderBy('id', 'desc')->limit(5)->get();
echo "\nÚltimos 5 equipos:\n";
foreach($last5 as $eq) {
    echo "ID: {$eq->id} | Código: {$eq->identificador_interno} | Consecutivo: {$eq->consecutivo}\n";
}
