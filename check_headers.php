<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$files2 = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*');
$excelFiles = array_filter($files2, function($f) { return strpos($f, '.xlsx') !== false; });
usort($excelFiles, function($a, $b) { return filemtime($b) - filemtime($a); });

$latest = $excelFiles[0];
$spreadsheet = IOFactory::load($latest);
$worksheet = $spreadsheet->getActiveSheet();

echo "ROW 1 HEADERS:\n";
foreach ($worksheet->getColumnIterator() as $col) {
    $val = $worksheet->getCell($col->getColumnIndex() . '1')->getCalculatedValue();
    echo $col->getColumnIndex() . ": $val\n";
}

echo "\nROW 2:\n";
foreach ($worksheet->getColumnIterator() as $col) {
    $val = $worksheet->getCell($col->getColumnIndex() . '2')->getCalculatedValue();
    echo $col->getColumnIndex() . ": $val\n";
}
