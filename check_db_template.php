<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$plantilla = App\Models\PlantillaPdf::where('activa', 1)->where('tipo', 'acta_entrega')->first();
if ($plantilla) {
    echo "Found active template ID: " . $plantilla->id . "\n";
    // Also read the new blade content
    $content = file_get_contents('resources/views/pdf/acta_entrega.blade.php');
    
    // We only want the HTML part of the signatures, wait, the DB template usually has EVERYTHING inside <body>.
    // Let's just output if there is one.
    echo "Content length: " . strlen($plantilla->contenido) . "\n";
} else {
    echo "No active template found in DB.\n";
}
