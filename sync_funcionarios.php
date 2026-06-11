<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UsuarioAsignado;
use App\Models\Funcionario;

$asignados = UsuarioAsignado::whereNotNull('cedula')
    ->where('cedula', '!=', 'Sin Asignar')
    ->get();

$count = 0;
foreach ($asignados as $asig) {
    if ($asig->nombre && $asig->nombre !== 'Sin Asignar') {
        Funcionario::updateOrCreate(
            ['identificacion' => $asig->cedula],
            [
                'nombres' => $asig->nombre,
                'apellidos' => '',
                'cargo' => $asig->cargo,
                'area' => $asig->area,
                'departamento' => $asig->departamento,
                'ciudad' => $asig->ciudad,
                'empresa_funcionario' => $asig->empresa_funcionario,
                'tipo_vinculacion' => $asig->tipo_vinculacion,
                'estado' => 'Activo'
            ]
        );
        $count++;
    }
}

echo "Sincronizados {$count} funcionarios.\n";
