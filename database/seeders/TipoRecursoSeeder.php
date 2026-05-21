<?php

namespace Database\Seeders;

use App\Models\TipoRecurso;
use Illuminate\Database\Seeder;

class TipoRecursoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Laptop',
            'PC Escritorio',
            'Tablet',
            'Servidor',
            'Monitor',
            'Impresora',
            'Teléfono IP',
            'Switch',
            'Router',
            'UPS',
        ];

        foreach ($tipos as $nombre) {
            TipoRecurso::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
