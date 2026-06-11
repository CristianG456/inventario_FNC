<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('usuario_asignados')->truncate();
DB::table('equipos')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// Borrar categorias basura que se crearon por error
DB::table('tipo_recursos')->whereIn('nombre', ['PAC', 'Recurso Propio', 'PROYECTO', 'RECURSO PROPIO'])->delete();

echo "TABLAS VACIADAS Y CATEGORIAS BASURA ELIMINADAS\n";
