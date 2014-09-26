<?php
#$path = "/usr/local/rheadisk/test/";
$postdata = file_get_contents("php://input"); 
$http_header = getallheaders(); 

$rhea_subdir="/tmp/rhea/";
if(!empty($http_header["Datetime"]))
{ 
	$filename = $http_header["Datetime"];		
	$filename = trim($filename);
	$rhea_imgpath=$rhea_subdir.substr($filename,0,8);
}
else
{	
	$rhea_imgpath=$rhea_subdir.date("Ymd");
}

if(!is_dir($rhea_imgpath))
{
	mkdir($rhea_imgpath);	
}
$path = $rhea_imgpath."/";
$defaultname = date("YmdHi").".jpg";

if(!empty($http_header["Datetime"]))
{
	$filename = $http_header["Datetime"];	
	$filename = trim($filename);
	$filename = substr($filename,0,12);
	$filename = $path.$filename.".jpg";

}
else
{
	$filename = $path.$defaultname;
}
		
$fp = fopen($filename,"wb");
$ret = fwrite($fp,$postdata,strlen($postdata));
fclose($fp);
echo "OK\n";
?>

