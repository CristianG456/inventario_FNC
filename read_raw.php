<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\laravel-excel-Qmu4KXNzIxQSPwEImTaEyEDuxNbucYu9.xlsx';
$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();

for ($row = 1; $row <= 4; $row++) {
    echo "ROW $row:\n";
    foreach ($worksheet->getColumnIterator() as $col) {
        $cell = $worksheet->getCell($col->getColumnIndex() . $row);
        $val = $cell->getCalculatedValue();
        if ((string)$val !== '') {
            echo "  " . $col->getColumnIndex() . ": " . $val . "\n";
        }
    }
}
