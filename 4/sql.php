<?php

 $sql_insert = "INSERT INTO `app_rhea`.`kangjw_20130613` (`id`, `x`, `y`, `type`, `time`) VALUES (NULL, \'116.404\', \'39.925\', \'0\', CURRENT_TIMESTAMP);";

 $link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
 if(!$link)
 {
	print "Error DB";
 }
 else
 {
    mysql_select_db(SAE_MYSQL_DB,$link);
  
    print "Db connect is OK\n";
	
	$result = mysql_query("SELECT * FROM kangjw_20130613");
	if($result)
	{
		print "Db select is OK\n";
		
	}
	else
	{
		$error_name = mysql_errno();
		print $error_name;
		print "\n";
		
		if($error_name == 1146)
		{
			
		}
		
		
	}
    //your code goes here
    mysql_close($link);
 }
?>