<?php
/*
CREATE TABLE  `app_rhea`.`test` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`x` DOUBLE NOT NULL ,
`y` DOUBLE NOT NULL ,
`x1` DOUBLE NOT NULL DEFAULT  '0',
`y1` DOUBLE NOT NULL DEFAULT  '0',
`type` TINYINT( 4 ) NOT NULL DEFAULT  '0',
`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ;
*/
require_once('util.php');

$myusername=$_POST['myusername']; 
$mypassword=$_POST['mypassword'];

if($myusername == null || $mypassword == null)
{
	echo "Name is Null";
	return;
}

// Connect to server and select databse.
$con_ret=mysql_connect("$RHEA_DB_HOST", "$RHEA_DB_USER", "$RHEA_DB_PASS");
if(!$con_ret)
{
	die("DB login connection error\n");
}
mysql_select_db("$RHEA_DB_NAME")or die("cannot select DB");

// username and password sent from form 
$count=0;

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);

$sql="SELECT * FROM $RHEA_TB_USER WHERE username='$myusername'";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
if($result)
{
	$count=mysql_num_rows($result);
}

//echo "test\n";
// If result matched $myusername and $mypassword, table row must be 1 row
if($count==1)
{
	
	echo "<BR>";
	echo "username ".$myusername." is exist, please register again!";
	echo "<br>";
	
}
else
{
    //INSERT INTO `app_rhea`.`rhea_user` (`id`, `username`, `passwd`, `time`) VALUES (NULL, 'test', 'test', CURRENT_TIMESTAMP); 
	$sql="insert into $RHEA_TB_USER (`username`, `passwd`) VALUES ('$myusername', '$mypassword')";
	
	$result=mysql_query($sql);
	if($result)
	{
		$tablename = rhea_get_table($myusername);
		$sql_createtbl  = "CREATE TABLE $tablename(";
		$sql_createtbl = $sql_createtbl."`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
		$sql_createtbl = $sql_createtbl."`x` DOUBLE NOT NULL , `y` DOUBLE NOT NULL ,";
		$sql_createtbl = $sql_createtbl."`x1` DOUBLE NOT NULL DEFAULT  '0', `y1` DOUBLE NOT NULL DEFAULT  '0',";
		$sql_createtbl = $sql_createtbl."`type` TINYINT( 4 ) NOT NULL DEFAULT  '0',";
		$sql_createtbl = $sql_createtbl."`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)";
		$result=mysql_query($sql_createtbl);
		
		if($result)
		{
			echo "Add user ".$myusername." OK!";
		}
		else
		{
			die(mysql_error());
			echo "Add user ".$myusername." table fail!";
		}

	}
	else
	{
		echo "Add user ".$myusername." fail!";
	}
}
mysql_close($con_ret);
?>