<?php
require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Find the latest excel file in storage/app/public or storage/app/temp or somewhere
$files = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\app\\*\\*.*');
$excelFiles = array_filter($files, function($f) {
    return strpos($f, '.xlsx') !== false || strpos($f, '.xls') !== false;
});

if (empty($excelFiles)) {
    // Try other directories
    $files = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\testing\\*.*');
    $excelFiles = array_filter($files, function($f) {
        return strpos($f, '.xlsx') !== false || strpos($f, '.xls') !== false;
    });
}

if (empty($excelFiles)) {
    echo "No excel files found.\n";
} else {
    // Sort by modification time to get the latest
    usort($excelFiles, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    $latest = $excelFiles[0];
    echo "Reading file: $latest\n";
    
    $spreadsheet = IOFactory::load($latest);
    $worksheet = $spreadsheet->getActiveSheet();
    
    // Dump first 3 rows
    for ($row = 1; $row <= 3; $row++) {
        echo "ROW $row:\n";
        $colIterator = $worksheet->getColumnIterator();
        foreach ($colIterator as $col) {
            $cell = $worksheet->getCell($col->getColumnIndex() . $row);
            $val = $cell->getCalculatedValue();
            if (!empty($val)) {
                echo "  Col " . $col->getColumnIndex() . ": " . $val . "\n";
            }
        }
    }
}
