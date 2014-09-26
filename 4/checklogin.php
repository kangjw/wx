<?php
session_start();
require_once('util.php');
// Connect to server and select databse.
$con_ret=mysql_connect("$RHEA_DB_HOST", "$RHEA_DB_USER", "$RHEA_DB_PASS");
if(!$con_ret)
{
	die("DB login connection error\n");
}
mysql_select_db("$RHEA_DB_NAME")or die("cannot select DB");

// username and password sent from form 
$myusername=$_POST['myusername']; 
$mypassword=$_POST['mypassword']; 
$count=0;

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);

$sql="SELECT * FROM $RHEA_TB_USER WHERE username='$myusername' and passwd='$mypassword'";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
if($result)
{
	$count=mysql_num_rows($result);
}

//echo "test\n";
// If result matched $myusername and $mypassword, table row must be 1 row
if($count==1){

// Register $myusername, $mypassword and redirect to file "login_success.php"
//session_register("myusername");
//session_register("mypassword");
$data = mysql_fetch_row($result);

$_SESSION['name']=$myusername; 
$_SESSION['passwd']=$mypassword;
$_SESSION['datetime'] = date_format(new DateTime($data[3]), 'Ymd');
//echo $_SESSION['datetime'];
mysql_close($con_ret);
header("location:login_success.php");
}
else 
{
	if($con_ret)
		mysql_close($con_ret);
	echo "Wrong Username or Password";
}
?>