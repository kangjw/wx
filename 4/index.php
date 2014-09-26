<?php
echo 'Hello World ';

$SaeLocationObj = new SaeLocation();
$drive_route_arr = array('begin_coordinate'=>'116.317245,39.981437','end_coordinate'=>'116.328422,40.077796');
$drive_route = $SaeLocationObj->getDriveRoute($drive_route_arr);
echo 'drive_rote: ';
print($drive_route);
echo '</br>';



?>