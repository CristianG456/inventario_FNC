<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$b64 = file_get_contents('logo_small_b64.txt');
$b64Html = '<html><body><h1>Test Logo B64 with Styles</h1><img src="data:image/jpeg;base64,' . $b64 . '" alt="Logo" width="90" height="60" style="object-fit: contain;"></body></html>';
$pdf2 = Pdf::loadHTML($b64Html)->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
$pdf2->save('public/test_logo_styles.pdf');

echo "PDF generated. Size: " . filesize('public/test_logo_styles.pdf');
