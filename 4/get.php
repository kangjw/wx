<?php
$x = $_GET['x'];
$y = $_GET['y'];
$type = $_GET['type'];

//echo "x=$x\n";
//echo "y=$y\n";

if($x == 0 || $y == 0 || strchr($x,'e')!= false || strchr($y,'e')!= false)
{
    //ignore the value.
	echo "123\n";
	return;
}

$sql_insert = "INSERT INTO `app_rhea`.`kangjw_20130613` (`id`, `x`, `y`, `type`, `time`) VALUES (NULL, $x, $y, 0, CURRENT_TIMESTAMP);";

//echo $sql_insert;

 $link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
 if(!$link)
 {
	echo "ERROR:001\n";
 }
 else
 {
	$result = mysql_query($sql_insert);
	if($result)
	{
		echo "OK\n";
	}
	else
	{
		$error_no = mysql_errno();
		//$error_no = mysql_error();
		echo "ERROR:$error_no\n";
	}
 }
 mysql_close($link);

?>