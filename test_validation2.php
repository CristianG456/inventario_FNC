<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = new \App\Http\Requests\StoreCampoPersonalizadoRequest();
$request->merge([
    'tipo' => 'select',
    'modo_asignacion_masiva' => 'solo_vacios',
    'asignar_valor_inicial' => '1', // Checkbox sends "1" or "on" usually. In Laravel it's cast by boolean rule.
    'valor_inicial_masivo' => 'Opcion A',
]);

$reflection = new \ReflectionClass($request);
$method = $reflection->getMethod('prepareForValidation');
$method->setAccessible(true);
$method->invoke($request);

$rules = $request->rules();
$validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

if ($validator->fails()) {
    echo "Fails:\n";
    print_r($validator->errors()->toArray());
} else {
    echo "Passes!\n";
}
