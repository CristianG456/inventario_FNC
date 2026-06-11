<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Let's find the uploaded file or CMDB file
$files = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\app\\livewire-tmp\\*');
$files2 = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*');
$allFiles = array_merge($files, $files2);

$excelFiles = array_filter($allFiles, function($f) {
    return strpos($f, '.xlsx') !== false || strpos($f, '.xls') !== false;
});

if (empty($excelFiles)) {
    echo "NO EXCEL FILES FOUND.\n";
    exit;
}

usort($excelFiles, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$latest = $excelFiles[0];
echo "LATEST FILE: $latest\n";

$spreadsheet = IOFactory::load($latest);
$worksheet = $spreadsheet->getActiveSheet();

// Let's find the column index for names
$nameCol = null;
$cedulaCol = null;

// Scan first 5 rows to find the header
for ($r = 1; $r <= 5; $r++) {
    foreach ($worksheet->getColumnIterator() as $col) {
        $val = strtolower($worksheet->getCell($col->getColumnIndex() . $r)->getCalculatedValue() ?? '');
        if (strpos($val, 'nombre') !== false && strpos($val, 'apellido') !== false) {
            $nameCol = $col->getColumnIndex();
        }
        if (strpos($val, 'cédula') !== false || strpos($val, 'cedula') !== false) {
            $cedulaCol = $col->getColumnIndex();
        }
    }
    if ($nameCol && $cedulaCol) break;
}

echo "Name Col: $nameCol, Cedula Col: $cedulaCol\n";

if (!$nameCol || !$cedulaCol) {
    echo "Could not find Name or Cedula column headers.\n";
    exit;
}

// Dump 20 rows of names and cedulas
$count = 0;
foreach ($worksheet->getRowIterator() as $row) {
    $ced = $worksheet->getCell($cedulaCol . $row->getRowIndex())->getCalculatedValue();
    $name = $worksheet->getCell($nameCol . $row->getRowIndex())->getCalculatedValue();
    
    if (!empty($ced) && $ced != 'CÉDULA DEL FUNCIONARIO/CONTRATISTA') {
        echo "Row " . $row->getRowIndex() . " -> Cedula: $ced | Name: $name\n";
        $count++;
    }
    if ($count > 20) break;
}
