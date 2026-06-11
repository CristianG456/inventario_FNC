<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Encontrar el archivo CMDB más reciente
$files = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*');
echo "=== Archivos en caché ===\n";
foreach ($files as $f) {
    echo basename($f) . " (" . date('Y-m-d H:i:s', filemtime($f)) . ") - " . filesize($f) . " bytes\n";
}

// Leer las primeras 3 filas del archivo más reciente con extensión xlsx o xls
$xlsFiles = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*.{xlsx,xls}', GLOB_BRACE);
if (empty($xlsFiles)) {
    echo "\nNo hay archivos Excel en caché.\n";
    exit;
}
usort($xlsFiles, function($a, $b) { return filemtime($b) - filemtime($a); });
$file = $xlsFiles[0];
echo "\n=== Leyendo: " . basename($file) . " ===\n\n";

$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();

for ($rowIdx = 1; $rowIdx <= 3; $rowIdx++) {
    echo "=== FILA $rowIdx ===\n";
    $cellIterator = $worksheet->getRowIterator($rowIdx, $rowIdx)->current()->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $col = 'A';
    foreach ($cellIterator as $cell) {
        $val = $cell->getValue();
        if ($val !== null && $val !== '') {
            echo "  COL $col: " . json_encode($val, JSON_UNESCAPED_UNICODE) . "\n";
        }
        $col++;
    }
}
