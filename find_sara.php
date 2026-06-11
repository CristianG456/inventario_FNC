<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$files2 = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*');
$excelFiles = array_filter($files2, function($f) { return strpos($f, '.xlsx') !== false; });
usort($excelFiles, function($a, $b) { return filemtime($b) - filemtime($a); });

$latest = $excelFiles[0];
$spreadsheet = IOFactory::load($latest);
$worksheet = $spreadsheet->getActiveSheet();

$found = false;
foreach ($worksheet->getRowIterator() as $row) {
    foreach ($worksheet->getColumnIterator() as $col) {
        $val = strtolower($worksheet->getCell($col->getColumnIndex() . $row->getRowIndex())->getCalculatedValue() ?? '');
        if (strpos($val, 'sara') !== false) {
            echo "Found SARA in row " . $row->getRowIndex() . "\n";
            echo "  Cedula (Col F): " . $worksheet->getCell('F' . $row->getRowIndex())->getCalculatedValue() . "\n";
            echo "  Name (Col H): " . $worksheet->getCell('H' . $row->getRowIndex())->getCalculatedValue() . "\n";
            $found = true;
        }
    }
}
if (!$found) echo "Sara not found in the Excel file.\n";
