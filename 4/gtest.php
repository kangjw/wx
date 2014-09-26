<?php

$postdata_test2 = "{
 \"homeMobileCountryCode\": 460,  
 \"homeMobileNetworkCode\": 3,  
 \"radioType\": \"cdma\",
 \"carrier\": \"China Telecom\",
 \"cellTowers\": [
  {
   \"cellId\": 449076,
   \"locationAreaCode\": 952,
   \"mobileCountryCode\": 460,
   \"mobileNetworkCode\": 3,
   \"age\": 0,
   \"signalStrength\": -100
  }
 ],
 \"wifiAccessPoints\": [
  {
   \"macAddress\": \"0c:72:2C:A8:4C:54\",
   \"signalStrength\": 8,
   \"age\": 0,
   \"signalToNoiseRatio\": -65,
   \"channel\": 8
  },
  {
   \"macAddress\": \"E5:05:C5:2E:EA:8C\",
   \"signalStrength\": 4,
   \"age\": 0
  },
  {
   \"macAddress\": \"00:23:CD:53:C4:1A\",
   \"signalStrength\": 4,
   \"age\": 0
  },  
  {
   \"macAddress\": \"E0:05:C5:2E:EA:8C\",
   \"signalStrength\": 7,
   \"age\": 0
  }, 
  {
   \"macAddress\": \"BC:D1:77:16:91:F0\",
   \"signalStrength\": 7,
   \"age\": 0
  },    
  {
   \"macAddress\": \"98:F5:37:41:EB:A0\",
   \"signalStrength\": 4,
   \"age\": 0
  } 
 ]
}";

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $postdata_test2
    )
);

$context = stream_context_create($opts);

$google_url = "https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyAsdI_hO6YDIiD_diGJxNdOpqgI-mk1ius";

//$result = file_get_contents($google_url, false, $context);
$result = "{ \"location\": { \"lat\": 31.2020257, \"lng\": 121.54077830457891 }, \"accuracy\": 45.0 }";
echo $result;
echo "<br>";
$location_result = json_decode($result,true);

if($location_result["location"])
{
	echo $location_result["location"]["lat"];
	echo "<br>";
	echo $location_result["location"]["lng"];
	echo "<br>";
	echo $location_result["accuracy"];
	echo "<br>";
	//insert gps table status code:201; 	
}
else if($location_result["error"]) 
{
	echo $location_result["error"]["code"];
	// insert gps db result. 400...
}


?>