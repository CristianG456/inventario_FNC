<?php
$web = file_get_contents('routes/web.php');
$web = preg_replace('/<img src="data:image\/png;base64,[^"]+"/i', '<img src="{{logo_fnc}}"', $web);
file_put_contents('routes/web.php', $web);
echo "Restored.";
