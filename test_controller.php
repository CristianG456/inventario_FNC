<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config(['database.connections.mysql.host' => '127.0.0.1']);
config(['database.connections.mysql.port' => '3307']);

try {
    $request = \Illuminate\Http\Request::create('/equipos/exportar', 'GET', ['modo_exportacion' => 'cmdb_principal']);
    $controller = $app->make(\App\Http\Controllers\EquipoController::class);
    $response = $controller->exportar($request);
    echo "Controller ran successfully. Returned instance of: " . get_class($response) . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
