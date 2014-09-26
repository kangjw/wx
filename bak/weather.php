<?php


$testcity="北京天气";

rhea_get_weather($testcity);

function rhea_get_weather($keycode)
{
	$weather_url="http://www.weather.com.cn/data/cityinfo/";
	$weather_html=".html";


	$weather_future="亲,查看未来7天预报,猛击:\nhttp://mobile.weather.com.cn/city/";
	$weather_fhtml=".html?data=7d";


	$citycode = rhea_get_citycode($keycode);	
	if($citycode)
	{
		$urllink = $weather_url.trim($citycode).$weather_html;
 		$response = file_get_contents($urllink);
		$wobj = json_decode($response);			

		$w_city = $wobj->weatherinfo->city;
		$w_weather = $wobj->weatherinfo->weather;
		$w_temp = $wobj->weatherinfo->temp1."~".$wobj->weatherinfo->temp2;	
		$today = date("Ymd");  
	
		$moreinfo = $weather_future.trim($citycode).$weather_fhtml;
		$result = "[".$today."  ".$w_city."天气]"."\n".$w_weather."\n".$w_temp."\n".$moreinfo;		
		//echo $result;
		return $result;	
	}
	else
	{
		return "亲,没有发现你说的地方";
	}
}


function rhea_get_citycode($name)
{
	$city = substr($name,0,stripos($name,"天气"));
	//echo $city;	
	
	$fp = fopen("weathercity.dat","r");
	while(!feof($fp))
	{
		$data = fgets($fp);
		if(strstr($data,$city))
		{
			$citycode = strtok($data,",");
			$citycode = strtok(",");		
			break;		
		}
	}
	//echo $citycode;
	fclose($fp);	
	return $citycode; 
}




?>
