<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario administrador por defecto
        User::factory()->create([
            'name'  => 'Administrador',
            'email' => 'admin@inventario.com',
        ]);

        // Sembrar tipos de recursos tecnológicos
        $this->call(TipoRecursoSeeder::class);
    }
}
