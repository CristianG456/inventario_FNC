<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Tomar el archivo xlsx más reciente
$files = glob('/var/www/storage/framework/cache/laravel-excel/*.xlsx');
usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });

foreach ($files as $idx => $file) {
    echo "=== ARCHIVO #$idx: " . basename($file) . " (modificado: " . date('Y-m-d H:i:s', filemtime($file)) . ") ===\n";
    $spreadsheet = IOFactory::load($file);
    
    // Mostrar todas las hojas
    $sheetNames = $spreadsheet->getSheetNames();
    echo "Hojas: " . implode(', ', $sheetNames) . "\n";
    
    $worksheet = $spreadsheet->getActiveSheet();
    echo "Hoja activa: " . $worksheet->getTitle() . "\n\n";
    
    // Leer filas 1, 2 y 3
    for ($rowIdx = 1; $rowIdx <= 3; $rowIdx++) {
        echo "--- FILA $rowIdx ---\n";
        $cellIterator = $worksheet->getRowIterator($rowIdx, $rowIdx)->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $colIdx = 0;
        foreach ($cellIterator as $cell) {
            $val = $cell->getValue();
            $coord = $cell->getCoordinate();
            if ($val !== null && $val !== '') {
                echo "  $coord: " . json_encode($val, JSON_UNESCAPED_UNICODE) . "\n";
            }
            $colIdx++;
            if ($colIdx > 50) break; // Limitar a 50 columnas
        }
    }
    echo "\n\n";
    if ($idx >= 1) break; // Solo ver los 2 más recientes
}
