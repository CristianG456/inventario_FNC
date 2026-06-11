<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('usuario_asignados')->truncate();
DB::table('equipos')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "TABLAS DE EQUIPOS VACIADAS CON EXITO\n";
