<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CampoPersonalizado;
use Illuminate\Support\Facades\DB;

// Clean up previous tests
CampoPersonalizado::where('nombre', 'like', 'Test %')->delete();

$testFields = [
    [
        'modulo' => 'equipos',
        'nombre' => 'Test Texto',
        'tipo' => 'texto',
        'obligatorio' => false,
        'activo' => true,
        'mostrar_en_grilla' => true,
        'posicion_grilla_despues_de' => 'marca',
        'participa_exportacion_cmdb' => true,
        'exportar_excel_despues_de' => 'cmdb_modelo'
    ],
    [
        'modulo' => 'equipos',
        'nombre' => 'Test Numero',
        'tipo' => 'numero',
        'obligatorio' => false,
        'activo' => true,
        'mostrar_en_grilla' => true,
        'posicion_grilla_despues_de' => 'activo_fijo',
        'participa_exportacion_cmdb' => true,
        'exportar_excel_despues_de' => 'cmdb_placa'
    ],
    [
        'modulo' => 'equipos',
        'nombre' => 'Test Fecha',
        'tipo' => 'fecha',
        'obligatorio' => false,
        'activo' => true,
        'mostrar_en_grilla' => false,
        'participa_exportacion_cmdb' => true,
        'exportar_excel_despues_de' => 'cmdb_fecha_compra'
    ],
    [
        'modulo' => 'equipos',
        'nombre' => 'Test Select',
        'tipo' => 'select',
        'obligatorio' => false,
        'activo' => true,
        'mostrar_en_grilla' => true,
        'posicion_grilla_despues_de' => 'estado_operativo',
        'participa_exportacion_cmdb' => true,
        'exportar_excel_despues_de' => 'cmdb_estado_operativo'
    ],
    [
        'modulo' => 'equipos',
        'nombre' => 'Test MultiSelect',
        'tipo' => 'multiselect',
        'obligatorio' => false,
        'activo' => true,
        'mostrar_en_grilla' => false,
        'participa_exportacion_cmdb' => false
    ]
];

foreach ($testFields as $index => $fieldData) {
    $fieldData['orden'] = $index;
    $campo = CampoPersonalizado::create($fieldData);
    if (in_array($campo->tipo, ['select', 'multiselect'])) {
        $campo->opciones()->createMany([
            ['valor' => 'Opcion A', 'orden' => 0],
            ['valor' => 'Opcion B', 'orden' => 1],
        ]);
    }
}

echo "Campos de prueba creados.\n";

// Asignar un valor a un equipo para testear renderizado
$equipo = \App\Models\Equipo::first();
if ($equipo) {
    $campos = CampoPersonalizado::where('nombre', 'like', 'Test %')->get();
    foreach($campos as $campo) {
        $valor = 'TestValue';
        if ($campo->tipo === 'select') $valor = 'Opcion A';
        if ($campo->tipo === 'multiselect') $valor = json_encode(['Opcion A', 'Opcion B']);
        if ($campo->tipo === 'numero') $valor = '123';
        if ($campo->tipo === 'fecha') $valor = '2025-01-01';

        \App\Models\CampoPersonalizadoValor::updateOrCreate(
            ['entidad_id' => $equipo->id, 'campo_personalizado_id' => $campo->id],
            ['valor' => $valor]
        );
    }
    echo "Valores de prueba asignados al equipo ID {$equipo->id}.\n";
}

// Ahora testear que mapearCmdbAGrilla funciona
echo "Mapeo de cmdb_serial a grilla: " . \App\Http\Controllers\CampoPersonalizadoController::mapearCmdbAGrilla('cmdb_serial') . "\n";
echo "Mapeo de cmdb_modelo a grilla: " . \App\Http\Controllers\CampoPersonalizadoController::mapearCmdbAGrilla('cmdb_modelo') . "\n";

echo "SUCCESS";
