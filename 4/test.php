<?php
$datestr="20130615";
echo strtotime($datestr);
echo "<br>";
echo $datastr[4-5];



$result = date("Y-m-d H:i:s",strtotime($datestr."235959"));
echo $result;

$str = "kangjw";
echo "<br>";
echo md5($str);


?>