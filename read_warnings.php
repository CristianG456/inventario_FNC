<?php
$lines = file('c:\\xampp\\htdocs\\inventario_equipos\\storage\\logs\\laravel.log');
$warnings = array_filter($lines, function($line) {
    return strpos($line, 'rechazada') !== false || strpos($line, '[IMPORT]') !== false;
});
echo implode("", array_slice($warnings, -20));
