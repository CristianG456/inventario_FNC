<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $filePath = 'storage/app/temp_imports/cmdb_1786630711_12 AGOSTO 2026 CMDB USUARIO FINAL (3).xlsx';
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($filePath);
    $sheetNames = $spreadsheet->getSheetNames();
    
    foreach ($sheetNames as $name) {
        echo "Sheet: $name\n";
        $sheet = $spreadsheet->getSheetByName($name);
        $rows = $sheet->toArray();
        if (count($rows) > 0) {
            echo "Headers:\n";
            print_r($rows[0]);
            echo "\nFirst Row:\n";
            print_r($rows[1]);
            
            $mapper = new \App\Services\Importadores\CMDBMapperService();
            $header = $rows[0];
            $data = array_combine($header, $rows[1]);
            echo "isResponsibilityRow for row 1: " . ($mapper->isResponsibilityRow($data) ? 'YES' : 'NO') . "\n";
            break; // Just need first sheet usually
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
