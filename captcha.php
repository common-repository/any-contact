<?php

if(!$_SESSION) session_start();

header ('Content-Type: image/png');

$width = 225;
$height = 60;

$image = @imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');
$background = imagecolorallocate ($image, 255, 255, 255);
imagefilledrectangle($image,0,0,$width,$height,$background);

$text_color = imagecolorallocate($image, 20, 20, 20);
$ttf = '/captcha/XFILES.TTF';
$ttfsize = 25;
$angle = mt_rand(-5,5); 
$t_x = mt_rand(5,20); 
$t_y = $height/2+10;
$random_string = randomString(9);
$_SESSION['captcha_code'] = $random_string;

imagettftext($image, $ttfsize, $angle, $t_x, $t_y, $text_color, $ttf, $random_string); 
imagepng($image); 
imagedestroy($image);


function randomString($length = 8) {
  $possible = "ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";
  $str = "";
  while(strlen($str)<$length) {
    $str .= substr($possible,(mt_rand()%(strlen($possible))),1);
  }
  return $str;
} 

?>