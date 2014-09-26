<?php
require_once('util.php');
$name = $_GET['n'];
$pwdmd5 = $_GET['p'];
$x = $_GET['x'];
$y = $_GET['y'];
$type = $_GET['type'];
$mode = $_GEY['mode'];
$utype = $_GET['utype'];
//test
/*
$name = "kangjw";
$pwdmd5 = md5("kangjw1");
$x = 3.1;
$y = 3.2;
$type = 0;
*/
//echo "x=$x\n";
//echo "y=$y\n";

$sql_finduser = "SELECT * FROM rhea_user WHERE username='$name'";

//echo $sql_insert;

 $link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
 if(!$link)
 {
	echo "ERROR:001\n";
 }
 else
 {
    // check user name & pwd
	mysql_select_db(SAE_MYSQL_DB)or die("ERROR:002\n");
	$result=mysql_query($sql_finduser);
	$count = 0;
	// Mysql_num_row is counting table row	
	if($result)
	{		
		$count=mysql_num_rows($result);
	}
	if($count==1)
	{
		$data = mysql_fetch_row($result);
		if(md5($data[2]) == $pwdmd5)
		{
			//$tablename = $name.'_'.date_format(new DateTime($data[3]), 'Ymd');
			$tablename = rhea_get_table($name);
			$sql_insert = "INSERT INTO $tablename (`id`, `x`, `y`, `type`, `time`) VALUES (NULL, $x, $y, 0, CURRENT_TIMESTAMP);";
			$result = mysql_query($sql_insert);
			if($result)
			{
				echo "OK\n";
			}
			else
			{
				//$error_no = mysql_errno();
				//$error_no = mysql_error();
				echo "ERROR:004\n";
			}
		}
		else  //password is wrong.
		{
			echo "ERROR:005\n";
		}
	}
	else
	{

		if($utype == 1)  //IMEI type
		{

					   //INSERT INTO `app_rhea`.`rhea_user` (`id`, `username`, `passwd`, `time`) VALUES (NULL, 'test', 'test', CURRENT_TIMESTAMP); 
			$mypassword = substr($name,-6);
			$tablename = rhea_get_table($name);
			$sql="insert into $RHEA_TB_USER (`username`, `passwd`) VALUES ('$name', '$mypassword')";
			
			
			$result=mysql_query($sql);
			if($result)
			{
				$tablename = rhea_get_table($name);
				$sql_createtbl  = "CREATE TABLE $tablename(";
				$sql_createtbl = $sql_createtbl."`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
				$sql_createtbl = $sql_createtbl."`x` DOUBLE NOT NULL , `y` DOUBLE NOT NULL ,";
				$sql_createtbl = $sql_createtbl."`x1` DOUBLE NOT NULL DEFAULT  '0', `y1` DOUBLE NOT NULL DEFAULT  '0',";
				$sql_createtbl = $sql_createtbl."`type` TINYINT( 4 ) NOT NULL DEFAULT  '0',";
				$sql_createtbl = $sql_createtbl."`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)";
				$result=mysql_query($sql_createtbl);
				
				if($result)
				{
					$sql_insert = "INSERT INTO $tablename (`id`, `x`, `y`, `type`, `time`) VALUES (NULL, $x, $y, 0, CURRENT_TIMESTAMP);";
					$result = mysql_query($sql_insert);
					if($result)
					{
						echo "OK\n";
					}
					else
					{
						echo "ERROR:004\n";
					}
				}
				else
				{
					echo "ERROR:003\n";
				}

			}
			else
			{
				echo "ERROR:003\n";
			}
		}
		else
			echo "ERROR:003\n";
	}
 }
 // close the DB connection.
 if($link)
	mysql_close($link);




?>