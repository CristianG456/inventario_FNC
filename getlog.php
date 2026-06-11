<?php
$lines = file('/var/www/storage/logs/laravel.log');
foreach(array_slice($lines, -300) as $l) {
    if(strpos($l, 'AUDITORIA') !== false || strpos($l, 'Lista completa') !== false || strpos($l, 'Nombre exacto') !== false || strpos($l, 'Valores extraídos') !== false) {
        echo $l;
    }
}
