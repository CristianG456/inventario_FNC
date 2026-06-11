<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\laravel-excel-Qmu4KXNzIxQSPwEImTaEyEDuxNbucYu9.xlsx';
$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();

$found = false;
foreach ($worksheet->getRowIterator() as $row) {
    foreach ($worksheet->getColumnIterator() as $col) {
        $cell = $worksheet->getCell($col->getColumnIndex() . $row->getRowIndex());
        $val = $cell->getCalculatedValue();
        if ((string)$val === '1110553049') {
            echo "Found 1110553049 in Row " . $row->getRowIndex() . " Col " . $col->getColumnIndex() . "\n";
            
            // Dump the whole row
            foreach ($worksheet->getColumnIterator() as $c) {
                $v = $worksheet->getCell($c->getColumnIndex() . $row->getRowIndex())->getCalculatedValue();
                echo "  " . $c->getColumnIndex() . ": $v\n";
            }
            $found = true;
            break;
        }
    }
}
if (!$found) echo "Not found!\n";
