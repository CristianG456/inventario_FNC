<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config(['database.connections.mysql.host' => '127.0.0.1']);
config(['database.connections.mysql.port' => '3307']);

try {
    $columnasPersonalizadas = \App\Models\CampoPersonalizado::where('modulo', 'equipos')
        ->where('mostrar_en_grilla', 1)
        ->where('participa_exportacion_cmdb', 1)
        ->pluck('id')
        ->toArray();
        
    $camposInfo = \App\Models\CampoPersonalizado::whereIn('id', $columnasPersonalizadas)->orderBy('orden')->get();
    
    $col = 'cmdb_modelo';
    $campos = $camposInfo->where('exportar_excel_despues_de', $col);
    
    echo "Found fields directly: " . $campos->count() . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
