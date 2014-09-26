<?php
// Create a 100*30 image
require_once("config.php");
if(!isset($_GET["d"]))
{
	$DIR = $RHEA_IMGPATH.'/';
}
else
{
	$DIR = $RHEA_IMGROOT.'/'.$_GET['d'].'/';
}
#echo $DIR;

if(isset($_GET["f"]))
{
	$fname = $_GET['f'];
	$filename = $DIR.$fname.".jpg";
	if(!isset($_GET["n"]))
		rhea_img_thumb($filename);
	else
		rhea_img_normal($filename);
}

function rhea_img_thumb($filename)
{
	header('Content-type: image/jpg');
	$destimg=resizeImage($filename, 240);
	imagejpeg($destimg);
	imagedestroy($destimg);
}

function rhea_img_normal($filename)
{
	$destimg = ImageCreateFromJPEG($filename);
	// Output the image
	header('Content-type: image/jpeg');
	imagejpeg($destimg);
	imagedestroy($destimg);
}


function img_test()
{

	$im = imagecreate(200, 30);

	// White background and blue text
	$bg = imagecolorallocate($im, 255, 255, 255);
	$textcolor = imagecolorallocate($im, 255, 255, 0);

	imagecolortransparent($im, $bg);

	// Write the string at the top left

	$today = date("Y-m-d H:i:s");   

	imagestring($im, 5, 0, 0, $today, $textcolor);

	list($width, $height, $type, $attr) = getimagesize("/tmp/test1.jpg");
	$destimg = ImageCreateFromJPEG("/tmp/test1.jpg");

	imagecopymerge($destimg, $im, $width-200,$height-30 , 0, 0, 200, 30,50);

	// Output the image
	header('Content-type: image/png');

	imagepng($destimg);
	imagedestroy($destimg);
}

function resizeImage($filename, $max_width)
{
	list($orig_width, $orig_height) = getimagesize($filename);
	$width = $orig_width;
	$height = $orig_height;

# taller
#if ($height > $max_height) {
#    $width = ($max_height / $height) * $width;
#    $height = $max_height;
#}

# wider
	if ($width > $max_width) 
	{
		$height = ($max_width / $width) * $height;
		$width = $max_width;
	}
	$image_p = imagecreatetruecolor($width, $height);
	$image = imagecreatefromjpeg($filename);
	imagecopyresampled($image_p,$image, 0, 0, 0, 0, 
			$width, $height, $orig_width, $orig_height);
	return $image_p;
}


?>


