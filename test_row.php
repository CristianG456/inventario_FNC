<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$files2 = glob('c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\*');
$excelFiles = array_filter($files2, function($f) { return strpos($f, '.xlsx') !== false; });
usort($excelFiles, function($a, $b) { return filemtime($b) - filemtime($a); });

$latest = $excelFiles[0];
echo "FILE: $latest\n";

class TestImport implements \Maatwebsite\Excel\Concerns\ToModel, \Maatwebsite\Excel\Concerns\WithHeadingRow
{
    public function headingRow(): int { return 1; }
    public function model(array $row)
    {
        echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        exit;
    }
}

\Maatwebsite\Excel\Facades\Excel::import(new TestImport(), $latest);
