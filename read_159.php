<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\laravel-excel-Qmu4KXNzIxQSPwEImTaEyEDuxNbucYu9.xlsx';
$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();

// Get headers from Row 1
$headers = [];
foreach ($worksheet->getColumnIterator() as $col) {
    $val = $worksheet->getCell($col->getColumnIndex() . '1')->getCalculatedValue();
    $headers[$col->getColumnIndex()] = $val;
}

foreach ($worksheet->getRowIterator() as $row) {
    if ($row->getRowIndex() == 159) {
        echo "ROW 159:\n";
        foreach ($worksheet->getColumnIterator() as $col) {
            $val = $worksheet->getCell($col->getColumnIndex() . $row->getRowIndex())->getCalculatedValue();
            $header = $headers[$col->getColumnIndex()];
            echo "  [$header] => $val\n";
        }
        break;
    }
}
