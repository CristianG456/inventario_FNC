<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$s = new \App\Imports\EquiposImportSelector('storage/app/temp_imports/cmdb_1786630440_12 AGOSTO 2026 CMDB USUARIO FINAL (3).xlsx', 'admin');
$i = $s->getImport();
if ($i) {
    print_r(array_keys($i->getMapper()->getMappings()));
    echo "\n\nSample mapped row:\n";
    $mapper = $i->getMapper();
    // read the first row of data
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile('storage/app/temp_imports/cmdb_1786630440_12 AGOSTO 2026 CMDB USUARIO FINAL (3).xlsx');
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load('storage/app/temp_imports/cmdb_1786630440_12 AGOSTO 2026 CMDB USUARIO FINAL (3).xlsx');
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    
    // row 0 is header
    $header = $rows[0];
    $mapper->initialize(array_combine($header, $rows[1]));
    
    echo "Is Responsibility Row (Row 1): " . ($mapper->isResponsibilityRow(array_combine($header, $rows[1])) ? 'YES' : 'NO') . "\n";
    print_r(array_combine($header, $rows[1]));
}
