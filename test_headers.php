<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$files = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*.xlsx');
usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
$file = $files[0];

$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();
$rows = [];
$i = 0;
foreach ($worksheet->getRowIterator() as $row) {
    if ($i++ > 3) break;
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $rowData = [];
    foreach ($cellIterator as $cell) {
        $rowData[] = $cell->getValue();
    }
    $rows[] = $rowData;
}

echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
