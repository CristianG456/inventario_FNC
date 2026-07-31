<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config(['database.connections.mysql.host' => '127.0.0.1']);
config(['database.connections.mysql.port' => '3307']);

try {
    $campos = \App\Models\CampoPersonalizado::select('id', 'nombre', 'modulo', 'mostrar_en_grilla', 'participa_exportacion_cmdb', 'exportar_excel_despues_de')->get();
    foreach ($campos as $campo) {
        echo "ID: {$campo->id} | Nombre: {$campo->nombre} | Modulo: {$campo->modulo} | Grilla: {$campo->mostrar_en_grilla} | ExportCMDB: {$campo->participa_exportacion_cmdb} | ExportDespues: {$campo->exportar_excel_despues_de}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
