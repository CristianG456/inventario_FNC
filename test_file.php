<?php
// Let's test the importer on the actual file!
require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$files = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\app\\livewire-tmp\\*');
if (empty($files)) {
    echo "No files in livewire-tmp\n";
} else {
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    $file = $files[0];
    echo "Reading $file\n";
    $spreadsheet = IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    
    // Dump row 2 (Headers) and row 3 (Data)
    $headers = [];
    foreach ($worksheet->getColumnIterator() as $col) {
        $cell = $worksheet->getCell($col->getColumnIndex() . '2');
        $headers[$col->getColumnIndex()] = $cell->getCalculatedValue();
    }
    echo "Headers:\n";
    print_r(array_filter($headers));
    
    echo "\nData Row 3:\n";
    foreach ($worksheet->getColumnIterator() as $col) {
        $cell = $worksheet->getCell($col->getColumnIndex() . '3');
        $val = $cell->getCalculatedValue();
        if (!empty($val)) {
            echo $headers[$col->getColumnIndex()] . ": " . $val . "\n";
        }
    }
}
