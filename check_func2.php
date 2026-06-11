<?php
$f = App\Models\Funcionario::where('identificacion', '1110553049')->first();
echo json_encode($f, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
