<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.connections.mysql.host' => '127.0.0.1', 'database.connections.mysql.port' => '3307']);

echo "--- EXPORTAR COMPLETA (Providers) HEADINGS ---\n";
$export1 = new \App\Exports\EquiposExport(['COMPLETA'], [1], []);
print_r($export1->headings());
