<?phpsession_start();require_once('util.php');//echo $_SESSION['name'];if(!isset($_SESSION['name'])){	header("location:main_login.php");}?><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><style type="text/css">body, html,div#container{width:1000px}div#header {background-color:#99bbbb;}div#menu {background-color:#ffff99; height:600px; width:100px; float:left; }div#allmap {height:600px; width:900px; float:left; }#header h1 {margin-bottom:0;}#menu h2 {margin:4;height:60px;width:100%;font-size:18px; float:left;}</style><script type="text/javascript" src="http://api.map.baidu.com/api?v=1.5&ak=EA61ea9e02e404588d4bf46970ddcfd9"></script><script type="text/javascript" src="http://developer.baidu.com/map/jsdemo/demo/convertor.js"></script><title>Hello, World</title></head><?phprequire_once('util.php');$data_total =0;$data_x = array();$data_y = array();#$table_name = $_SESSION['name'].'_'.$_SESSION['datetime'];$table_name = rhea_get_table($_SESSION['name']);$url_baidu = "http://api.map.baidu.com/ag/coord/convert?from=0&to=4";//daytimeif($_GET['d']){	$datestr = $_GET['d'];	$date_start = date("Y-m-d H:i:s",strtotime($datestr));	$date_end = date("Y-m-d H:i:s",strtotime($datestr."235959"));	$select_sql="SELECT * FROM $table_name where time>='$date_start' AND time<='$date_end' ORDER BY id";	$check_sql="SELECT * FROM $table_name where time>='$date_start' AND time<='$date_end' AND x1=0";}else if($_GET['b']){   	$date_start = date("Y-m-d H:i:s",mktime(0, 0, 0, date("m"),date("d")-$_GET['b'], date("Y")));	$date_end = date("Y-m-d H:i:s",mktime(23, 59, 59, date("m"),date("d")-$_GET['b'], date("Y")));	$select_sql="SELECT * FROM $table_name where time>='$date_start' AND time<='$date_end' ORDER BY id";	$check_sql="SELECT * FROM $table_name where time>='$date_start' AND time<='$date_end' AND x1=0";}else{	$rhea_select_day = $rhea_today;  // today	$select_sql="SELECT * FROM $table_name where time>='$rhea_select_day' ORDER BY id";	$check_sql="SELECT * FROM $table_name where time>='$rhea_select_day' AND x1=0";}echo  $select_sql;echo "<BR>"; $link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);if(!$link){	echo "Error DB";}else{    mysql_select_db(SAE_MYSQL_DB,$link);		// auto convert the GPS to China GPS data;	$check_result = mysql_query($check_sql);	if($check_result)	{		$check_total=mysql_num_rows($check_result);		for ($i=0; $i< $check_total; $i++)		{			$check_data = mysql_fetch_row($check_result);						// 4&x=116.254615&y=29.814476			$response =  file_get_contents($url_baidu."&x="."$check_data[1]"."&y="."$check_data[2]");			if($response)			{				//echo $response;				//{"error":0,"x":"MTE2LjI2MTA5OTEyMjE=","y":"MjkuODIwNTYwODc0ODQ2"}				$result = json_decode($response,true);				if($result["error"] == 0)				{					$x = base64_decode($result["x"]);					$y = base64_decode($result["y"]);					//echo "<br>";					//echo $x ;					//echo "<br>";					//echo $y;					$update_sql = "update $table_name set x1=$x,y1=$y where id = $check_data[0]"; 					mysql_query($update_sql);					//echo $update_sql;					//echo "<br>";				}			}					}	}	    $result = mysql_query($select_sql);	if($result)	{		$data_total=mysql_num_rows($result);				echo "<script type=\"text/javascript\">";		echo "var mapx = new Array();";		echo "var mapy = new Array();";		echo "var map_max =$data_total;";		for ($i=0; $i< $data_total; $i++)		{			$data = mysql_fetch_row($result);			//echo $data_x."\n";			//echo $data_y;			//echo "\n";			echo "mapx[$i]=$data[3];";			echo "mapy[$i]=$data[4];";		}				echo "</script>";		//echo "Db select is OK\n";			//echo mapx;		//echo "\n";		//echo mapy;	}	else	{		$error_name = mysql_errno();		echo $error_name;		echo "\n";		}    //your code goes here    mysql_close($link);}?><body><div id="container"><div id="header"> <h1 align="center">This is a test APP!</h1></div><div id="menu"> 	<h2>MENU</h2>	<h2><a href="dp.php">Now</a></h2>	<h2><a href="dp.php?b=1">LastD1</a></h2>	<h2><a href="dp.php?b=2">LastD2</a></h2>	<h2><a href="logout.php">Logout</a></h2></div><div id="allmap"></div><script type="text/javascript">var map = new BMap.Map("allmap");            // ??Map?}var gpsPoint = new BMap.Point(mapx[0], mapy[0]);    // ?????var gpsIndex = 0;map.centerAndZoom(gpsPoint,15);                     // ?????,???��????????cmap.enableScrollWheelZoom();   var mypath = new Array();for(var i = 0; i < map_max; i++){	mypath[i] = new BMap.Point(mapx[i],mapy[i]);}//draw line.	var curve = new BMap.Polyline(mypath, {strokeColor:"blue", strokeWeight:3, strokeOpacity:0.5});map.addOverlay(curve); //���ӵ���ͼ��//draw end port;var newpoint = new BMap.Point(mapx[map_max-1],mapy[map_max-1]);	var mycircle = new BMap.Circle(newpoint,100);mycircle.setFillColor("#0000ff");mycircle.setFillOpacity(0.2);map.addOverlay(mycircle);	var mycircle1 = new BMap.Circle(newpoint,10);mycircle.setStrokeColor("#ff0000");map.addOverlay(mycircle1);	</script></div></body></html>