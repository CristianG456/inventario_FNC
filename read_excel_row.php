<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\laravel-excel-Qmu4KXNzIxQSPwEImTaEyEDuxNbucYu9.xlsx';
$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();

// Dump headers (Row 2)
$headers = [];
foreach ($worksheet->getColumnIterator() as $col) {
    $val = $worksheet->getCell($col->getColumnIndex() . '2')->getCalculatedValue();
    $headers[$col->getColumnIndex()] = $val;
}

echo "Looking for cedula 1110553049...\n";

foreach ($worksheet->getRowIterator() as $row) {
    if ($row->getRowIndex() < 3) continue;
    
    $rowData = [];
    $isMatch = false;
    foreach ($worksheet->getColumnIterator() as $col) {
        $val = $worksheet->getCell($col->getColumnIndex() . $row->getRowIndex())->getCalculatedValue();
        $header = $headers[$col->getColumnIndex()] ?? 'Col_'.$col->getColumnIndex();
        $rowData[$header] = $val;
        if ((string)$val === '1110553049' || (string)$val === 'R52WA02SGRT') {
            $isMatch = true;
        }
    }
    
    if ($isMatch) {
        echo "Found Row " . $row->getRowIndex() . ":\n";
        print_r($rowData);
        break;
    }
}
