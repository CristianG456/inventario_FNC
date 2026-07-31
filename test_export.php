<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exports\EquiposExport;

$columnasEstandarInput = ['marca', 'modelo', 'serial', 'fin_garantia', 'periferico_mouse', 'periferico_camara'];
$columnasPersonalizadas = [1]; // Fake custom field ID

$columnasEstandar = array_values(array_unique(array_merge(
    EquiposExport::columnasCmdbPrincipal(),
    $columnasEstandarInput
)));

$export = new EquiposExport($columnasEstandar, $columnasPersonalizadas, []);

echo "--- HEADINGS ---\n";
print_r($export->headings());

$equipo = \App\Models\Equipo::first();
echo "\n--- MAP ---\n";
if ($equipo) {
    print_r($export->map($equipo));
} else {
    echo "No equipos found\n";
}
