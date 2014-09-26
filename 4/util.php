<?php
$RHEA_DB_HOST=SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT;
$RHEA_DB_USER=SAE_MYSQL_USER;
$RHEA_DB_PASS=SAE_MYSQL_PASS;
$RHEA_DB_NAME=SAE_MYSQL_DB;
$RHEA_TB_USER=rhea_user;

// data
$rhea_today = date("Y-m-d").' 00:00:00';  
$rhea_yesterday = date("Y-m-d H:i:s",mktime(0, 0, 0, date("m"),date("d")-1, date("Y")));

 
function rhea_last_date($before)
{ 
  $retDAY = date("Y-m-d H:i:s",mktime(0, 0, 0, date("m"),date("d")-$before, date("Y")));; 
  return $retDAY; 
}

function rhea_date_start($datestr)
{ 
	$result = date("Y-m-d H:i:s",strtotime($datestr));
	return $result;
}

function rhea_date_end($datestr)
{
	$result = date("Y-m-d H:i:s",strtotime($datestr."235959"));
	return $result;	
}

function rhea_get_table($username)
{
	$tbl_name = $username.'_'.substr(md5($username) , 0 , 8);
	return $tbl_name;
}

?>