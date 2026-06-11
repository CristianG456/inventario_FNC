<?php
$e1 = App\Models\Equipo::with('usuarioAsignado')->where('serial', 'R52WA02SGRT')->first();
$e2 = App\Models\Equipo::with('usuarioAsignado')->where('serial', '96WJS93')->first();

echo "Equipo 1:\n";
echo json_encode($e1, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
echo "Equipo 2:\n";
echo json_encode($e2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
