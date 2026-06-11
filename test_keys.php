<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$files = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*.xlsx');
usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
$file = $files[0];

class TestImport implements \Maatwebsite\Excel\Concerns\ToCollection, \Maatwebsite\Excel\Concerns\WithHeadingRow
{
    public function headingRow(): int { return 2; }
    public function collection(\Illuminate\Support\Collection $rows) {
        $r = $rows->first();
        echo "Keys extracted by Laravel Excel:\n";
        print_r(array_keys($r->toArray()));
        echo "\nRow data:\n";
        print_r($r->toArray());
        die();
    }
}

\Maatwebsite\Excel\Facades\Excel::import(new TestImport, $file);
