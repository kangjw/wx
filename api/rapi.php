<?php

if(!isset($_GET['c'])||!isset($_GET['u']))
{
	echo "{\"error\":\"parameter\"}";
	return;
}

if($_GET['c']== 1)
{
	$USER="/tmp/";
	$path = $USER.$_GET['u'];
	$dirs = scandir($path,1);
	if($dirs == FALSE)
	{
		echo "{\"error\":\"no user\"}";
		return;
	}

	for($i=0;$i<count($dirs)-2;$i++)
	{
		$files[$i]=$dirs[$i];
	}
	$jdata['cmd'] = 1;
	$jdata['user'] = "rhea";
	$jdata['total'] = count($dirs)-2;
	$jdata['list'] = $files; 
	$ret = json_encode($jdata);
	echo $ret;
}
else if($_GET['c'] == 2)
{
	if(!isset($_GET['d']))
	{	
		echo "{\"error\":\"parameter\"}";
		return;
	}
	$date = $_GET['d'];
	$user = $_GET['u'];
	
	$path = "/tmp/".$user."/".$date;
	$dirs = scandir($path,1);

	if($dirs == FALSE)
	{
		echo "{\"error\":\"no img\"}";
		return;
	}
	for($i=0;$i<count($dirs)-2;$i++)
	{
		$files[$i]=$dirs[$i];
	}

	$jdata['cmd'] =2;
	$jdata['user'] = "rhea";
	$jdata['total'] = count($dirs)-2;
	$jdata['img'] = $files; 
	$imgfile = json_encode($jdata);
	echo $imgfile;
}
else if($_GET['c'] == 3)
{
	if(!isset($_GET['f'])||!isset($_GET['d']))
	{
		echo "{\"error\":\"parameter\"}";
		return;
	}
	$filename="/tmp/".$_GET['u']."/".$_GET['d']."/".$_GET['f'];
	rhea_img_thumb($filename);
}

else if($_GET['c'] == 4)
{
	if(!isset($_GET['f'])||!isset($_GET['d']))
	{
		echo "{\"error\":\"parameter\"}";
		return;
	}
	$filename="/tmp/".$_GET['u']."/".$_GET['d']."/".$_GET['f'];
	rhea_img_normal($filename);
}
else
{
	echo "{\"error\":\"unknown\"}";
	return;
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
