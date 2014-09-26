<html>
<head>
<meta name="viewport" content="width=100%, initial-scale=1"  charset="UTF-8" />
<style type="text/css">
a.r1:link,a.r1:visited
{
display:block;
font-weight:bold;
font-size:20px;
font-family:Verdana, Arial, Helvetica, sans-serif;
color:#FFFFFF;
background-color:#98bf21;
width:120px;
text-align:center;
padding:10px;
text-decoration:none;
float: left;
margin: 10px 20px 10px 0px;
}
a.r1:hover,a.r1:active
{
background-color:#7A991A;
}

a.r2
{
margin-left: 20px;
font-size:16px;
color:#FFFFFF;
background-color:#98bf21;
padding:2px 10px 2px 10px;
text-decoration:none;
}

div#pic {float:left; width:300px}
div#button {clear:both}

</style>
</head>

<body>
<div style="background-color:#99CC99; width: 100%">
<h1>实景图</h1>
</div>
<div>
<h3><a href="list.php">历史记录</a>&nbsp;&nbsp;&nbsp <a href="file.php">当天</a></h3>
</div>

<div>
<?php
require_once("config.php");
session_start();

$STEP = 8;
// store session data
if(!isset($_GET['m']))
	$forward = 0;
else
	$forward = $_GET['m'];




$FPAGE='前一页';
$BPAGE='后一页';
$FIRST='首页';

if(!isset($_GET['d']))
{
	$FILEPATH = $RHEA_IMGPATH;
	$date = date("Ymd"); 
}
else
{
	$FILEPATH = $RHEA_IMGROOT.'/'.$_GET['d'];
	$date = $_GET['d'];
}

if(!isset($_SESSION['INDEX'])||!isset($_SESSION['DATE']))
{
	$forward = 0;
	$date = date("Ymd");
	$FILEPATH = $RHEA_IMGPATH;	
}


if($forward == '0')
{
	session_destroy();
	session_start();

	$files = rhea_get_files($FILEPATH);
	if($files)
	{	
		#echo date("Y-m-n H:i",$files[0]);
		$_SESSION['INDEX']=0;
		$_SESSION['FILES']=$files;
		$_SESSION['DATE']=$date; 

		$dsfiles = array_slice($files,0,$STEP);
		foreach($dsfiles as $file)
		{
			#echo "<div class=\"r2\">";
			$filename = date("YmdHi",$file);
			echo "<div id=\"pic\">";
			echo "<h3>".date("Y-m-d H:i",$file)."<a class=\"r2\" href=\"img.php?d=$date&f=$filename&n=1\">大图</a></h3>";
			echo "<img src=\"img.php?d=$date&f=$filename\" alt=\"Picture\">";
			echo "</div>";
			#echo "<br>\n";
		}
		#echo "<br>";
		echo "<div id=\"button\">";
		echo "<a class=\"r1\" href=\"file.php?d=$date&m=1\">".$FPAGE."</a>";
		echo "</div>";
	}
	else
	{
		echo "No found\n";
	}

}
else if($forward == '1')
{
	$_SESSION['INDEX'] = $_SESSION['INDEX']+$STEP;
	$index = $_SESSION['INDEX'];
	$files = $_SESSION['FILES'];
	$date = $_SESSION['DATE'];

	if($index >=count($files))
	{
		$index = $_SESSION['INDEX'] - $STEP;
		$_SESSION['INDEX'] = $index;
	}

	$dsfiles = array_slice($files,$index,$STEP);
	foreach($dsfiles as $file)
	{
		$filename = date("YmdHi",$file);
		echo "<div id=\"pic\">";
		echo "<h3>".date("Y-m-d H:i",$file)."<a class=\"r2\" href=\"img.php?d=$date&f=$filename&n=1\">大图</a></h3>";
		echo "<img src=\"img.php?d=$date&f=$filename\" alt=\"Picture\">";
		echo "</div>";
		echo "\n";
	}
	echo "<div id=\"button\">";
	if($index + $STEP <= count($files))
		echo "<a class=\"r1\" href=\"file.php?d=$date&m=1\">$FPAGE</a>";	
	echo "<a class=\"r1\" href=\"file.php?d=$date&m=2\">$BPAGE</a>";
	echo "</div>";	
}	

else if($forward == '2')
{
	$_SESSION['INDEX'] = $_SESSION['INDEX']- $STEP;
	$index = $_SESSION['INDEX'];
	$files = $_SESSION['FILES'];
	$date = $_SESSION['DATE'];

	if($index <= 0)
	{
		$index = 0;
		$_SESSION['INDEX'] = 0;
	}

	$dsfiles = array_slice($files,$index,$STEP);
	foreach($dsfiles as $file)
	{
		$filename = date("YmdHi",$file);
		echo "<div id=\"pic\">";
		echo "<h3>".date("Y-m-d H:i",$file)."<a class=\"r2\" href=\"img.php?d=$date&f=$filename&n=1\">大图</a></h3>";;
		echo "<img src=\"img.php?d=$date&f=$filename\" alt=\"Picture\">";
		echo "</div>";
		echo "\n";
	}
	echo "<div id=\"button\">";
	echo "<a  class=\"r1\" href=\"file.php?d=$date&m=1\">$FPAGE</a>";	
	if($index-$STEP >=0)
		echo "<a class=\"r1\" href=\"file.php?d=$date&m=2\">$BPAGE</a>";
	else
		echo "<a class=\"r1\" href=\"file.php?d=$date\">$FIRST</a>";
	echo "</div>";	
}




function rhea_get_files($dir)
{
#$dir = "/tmp/rhea_20140327";

	if(!is_dir($dir))
		return false;

	$files = scandir($dir);
	if(!$files || count($files) == 2)	
		return false;

	for($i = 2;$i<count($files); $i++)
	{
		$length = strpos($files[$i],".jpg");
		$filedate[$i-2] = substr($files[$i],0,$length); 
	}
	for($i=0; $i<count($filedate);$i++)
	{
		$date = date_parse_from_format("YmdHi", $filedate[$i]);
		$datestr =$date['year'].'-'.$date['month'].'-'.$date['day'];
		$datestr =$datestr.' '.$date['hour'].':'.$date['minute'].':00'; 
		$utime[$i] = strtotime($datestr);
		//echo date( "Y-m-d H:i:s",$utime[$i]);
		//echo "<br>";
	}
	rsort($utime);
	return $utime;
}
echo "\n";
echo "</div>";
echo "\n";
include "foot.htm";
?>

</body>
</html>


