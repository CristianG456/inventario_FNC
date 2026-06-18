<?php
$pngFile = 'public/imagenes/federacion cafeteros logo.png';
$jpgFile = 'public/imagenes/federacion_cafeteros_logo.jpg';

if (file_exists($pngFile)) {
    $image = imagecreatefrompng($pngFile);
    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagealphablending($bg, TRUE);
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    imagejpeg($bg, $jpgFile, 100);
    imagedestroy($bg);
    imagedestroy($image);
    echo "Converted PNG to JPG: " . $jpgFile;
} else {
    echo "PNG file not found!";
}
