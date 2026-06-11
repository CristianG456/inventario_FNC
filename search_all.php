<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$files = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*.xlsx');

foreach ($files as $file) {
    echo "Searching in $file...\n";
    try {
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $found = false;
        foreach ($worksheet->getRowIterator() as $row) {
            foreach ($worksheet->getColumnIterator() as $col) {
                $val = strtolower($worksheet->getCell($col->getColumnIndex() . $row->getRowIndex())->getCalculatedValue() ?? '');
                if (strpos($val, 'sara') !== false) {
                    echo "  Found SARA in row " . $row->getRowIndex() . "\n";
                    echo "  Row data: \n";
                    foreach ($worksheet->getColumnIterator() as $c) {
                        $v = $worksheet->getCell($c->getColumnIndex() . $row->getRowIndex())->getCalculatedValue();
                        echo "    " . $c->getColumnIndex() . ": $v\n";
                    }
                    $found = true;
                    break;
                }
            }
            if ($found) break;
        }
    } catch (\Exception $e) {
        echo "  Error reading file.\n";
    }
}
