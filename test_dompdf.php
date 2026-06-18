<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$path = public_path('imagenes/federacion_cafeteros_logo.jpg');
$path = str_replace('\\', '/', $path);

$html = '<html><body><h1>Test Logo</h1><img src="' . $path . '" width="200"></body></html>';

$pdf = Pdf::loadHTML($html)->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
$pdf->save('public/test_logo_path.pdf');

$b64Html = '<html><body><h1>Test Logo B64</h1><img src="data:image/jpeg;base64,' . base64_encode(file_get_contents($path)) . '" width="200"></body></html>';
$pdf2 = Pdf::loadHTML($b64Html)->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
$pdf2->save('public/test_logo_b64.pdf');

echo "PDFs generated. Check public/test_logo_path.pdf and public/test_logo_b64.pdf";
