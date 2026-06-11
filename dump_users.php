<?php
$users = App\Models\UsuarioAsignado::whereNotNull('equipo_id')->limit(5)->get()->toArray();
echo json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
