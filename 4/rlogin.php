<?php
$name = $_GET['n'];
$pwdmd5 = $_GET['p'];
//$pwdmd5 = md5("kangjw");

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
	mysql_select_db(SAE_MYSQL_DB)or die("ERROR:002");
	$result=mysql_query($sql_finduser);
	// Mysql_num_row is counting table row	
	if($result)
	{		
		$count=mysql_num_rows($result);
		if($count==1)
		{
			$data = mysql_fetch_row($result);
			if(md5($data[2]) == $pwdmd5)
			{
				
				echo "OK";
			}
			else  //password is wrong.
			{
				echo "ERROR:003".$data[1].md5($data[2]);
			}
		}
		else
		{
			echo "ERROR:003";
		}
	}
	else
	{
		echo "ERROR:003";
	}
}
 // close the DB connection.
 if($link)
	mysql_close($link);

?>