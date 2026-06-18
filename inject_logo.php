<?php
$b64 = file_get_contents('logo_small_b64.txt');
$file = file_get_contents('app/Services/PdfService.php');
$replacement = "    private function obtenerLogoPath(): string\n    {\n        return 'data:image/jpeg;base64," . $b64 . "';\n    }";
$file = preg_replace('/    private function obtenerLogoPath\(\): string\s+\{.*?\n    \}/s', $replacement, $file);
file_put_contents('app/Services/PdfService.php', $file);
echo "Injected base64 into PdfService.php";
