<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\Funcionario;
use App\Models\UsuarioAsignado;
use App\Models\AsignacionResponsabilidad;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

Equipo::truncate();
Funcionario::truncate();
UsuarioAsignado::truncate();
AsignacionResponsabilidad::truncate();

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Limpieza completada.\n";
