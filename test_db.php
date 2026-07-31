<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
// Force local DB connection if needed
config(['database.connections.mysql.host' => '127.0.0.1']); 

try {
    echo json_encode(\App\Models\CampoPersonalizado::select('id', 'nombre', 'mostrar_en_grilla', 'participa_exportacion_cmdb', 'exportar_excel_despues_de')->get());
} catch (\Exception $e) {
    echo $e->getMessage();
}
