<?php
$b64 = file_get_contents('logo_b64.txt');
$web = file_get_contents('routes/web.php');
$web = str_replace('{{logo_fnc}}', 'data:image/png;base64,' . $b64, $web);
file_put_contents('routes/web.php', $web);
echo "Replaced.";
