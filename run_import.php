<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\EquiposImportSelector;
use Maatwebsite\Excel\Facades\Excel;

$filePath = storage_path('app/temp_imports/cmdb_1786630711_12 AGOSTO 2026 CMDB USUARIO FINAL (3).xlsx');

echo "Iniciando importación desde: {$filePath}\n";

try {
    $importador = new EquiposImportSelector($filePath, 'FNC');
    Excel::import($importador, $filePath);
    echo "Importación completada con éxito.\n";
} catch (\Exception $e) {
    echo "Error en importación: " . $e->getMessage() . "\n";
}
