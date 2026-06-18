<?php
$pngFile = 'public/imagenes/federacion cafeteros logo.png';
$jpgFile = 'public/imagenes/federacion_cafeteros_logo_small.jpg';

if (file_exists($pngFile)) {
    $image = imagecreatefrompng($pngFile);
    $bg = imagecreatetruecolor(180, 120);
    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagecopyresampled($bg, $image, 0, 0, 0, 0, 180, 120, imagesx($image), imagesy($image));
    imagejpeg($bg, $jpgFile, 85);
    imagedestroy($bg);
    imagedestroy($image);
    
    $b64 = base64_encode(file_get_contents($jpgFile));
    echo "Length: " . strlen($b64);
    file_put_contents('logo_small_b64.txt', $b64);
} else {
    echo "PNG file not found!";
}
