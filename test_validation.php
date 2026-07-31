<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = new \App\Http\Requests\EquipoRequest();
$rules = $request->rules();
print_r($rules['estado_operativo']);
