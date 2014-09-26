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
position: relative;
left: 60px;
font-size:16px;
color:#FFFFFF;
background-color:#98bf21;
padding:2px 10px 2px 10px;
text-decoration:none;
}



</style>
</head>

<body>
<div style="background-color:#4169E1; width: 100%">
<h1>实景图</h1>
</div>

<?php
require_once("config.php");
session_start();

$STEP = 2;
// store session data
if(!isset($_GET['m']))
	$forward = 0;
else
	$forward = $_GET['m'];


$FPAGE='前一页';
$BPAGE='后一页';
$FIRST='首页';

$FILEPATH = $RHEA_IMGPATH;

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

		$dsfiles = array_slice($files,0,$STEP);
		foreach($dsfiles as $file)
		{
			#echo "<div class=\"r2\">";
			$filename = date("YmdHi",$file);
			echo "<h3>".date("Y-m-d H:i",$file)."<a class=\"r2\" href=\"img.php?f=$filename&n=1\">大图</a></h3>";
			echo "<img src=\"img.php?f=$filename\" alt=\"Picture\">";
			echo "<br>";
		}
		#echo "<br>";
		echo "\n<a class=\"r1\" href=\"file.php?m=1\">".$FPAGE."</a>";
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

	if($index >=count($files))
	{
		$index = $_SESSION['INDEX'] - $STEP;
		$_SESSION['INDEX'] = $index;
	}

	$dsfiles = array_slice($files,$index,$STEP);
	foreach($dsfiles as $file)
	{
		$filename = date("YmdHi",$file);
		echo "<h3>".date("Y-m-d H:i",$file)."<a class=\"r2\" href=\"img.php?$filename&n=1\">大图</a></h3>";
		echo "<img src=\"img.php?f=$filename\" alt=\"Picture\">";
		echo "<br>";
	}
	echo "<div>";
	if($index + $STEP <= count($files))
		echo "<a class=\"r1\" href=\"file.php?m=1\">$FPAGE</a>";	
	echo "<a class=\"r1\" href=\"file.php?m=2\">$BPAGE</a>";
	echo "</div>";	
}	

else if($forward == '2')
{
	$_SESSION['INDEX'] = $_SESSION['INDEX']- $STEP;
	$index = $_SESSION['INDEX'];
	$files = $_SESSION['FILES'];

	if($index <= 0)
	{
		$index = 0;
		$_SESSION['INDEX'] = 0;
	}

	$dsfiles = array_slice($files,$index,$STEP);
	foreach($dsfiles as $file)
	{
		$filename = date("YmdHi",$file);
		echo "<h3>".date("Y-m-d H:i",$file)."<a class=\"r2\" href=\"img.php?f=$filename&n=1\">大图</a></h3>";;
		echo "<img src=\"img.php?f=$filename\" alt=\"Picture\">";
		echo "<br>";
	}
	echo "<div>";
	echo "<a  class=\"r1\" href=\"file.php?m=1\">$FPAGE</a>";	
	if($index-$STEP >=0)
		echo "<a class=\"r1\" href=\"file.php?m=2\">$BPAGE</a>";
	else
		echo "<a class=\"r1\" href=\"file.php\">$FIRST</a>";
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
?>

</body>
</html>


