<?php
require __DIR__.'/vendor/autoload.php';

use App\Imports\EquiposImport;
use Maatwebsite\Excel\Facades\Excel;

$file = 'c:\\xampp\\htdocs\\inventario_equipos\\storage\\framework\\cache\\laravel-excel\\laravel-excel-Qmu4KXNzIxQSPwEImTaEyEDuxNbucYu9.xlsx';

class TestImport implements \Maatwebsite\Excel\Concerns\ToModel, \Maatwebsite\Excel\Concerns\WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        echo json_encode($row) . "\n";
        exit;
    }
}

Excel::import(new TestImport(), $file);
