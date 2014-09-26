<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=100%, initial-scale=1"  charset="UTF-8" />
</head>
<body>
<div align = "center">
<video width ="384px"  controls="controls" autoplay="autoplay">
<?php
  $path = "/v/".$_GET["d"]."/v.mp4";	
  $oggpath = "/v/".$_GET["d"]."/v.ogv";	
  echo "<source src=\"$oggpath\" type=\"video/ogg\">";
  echo "<source src=\"$path\" type=\"video/mp4\">";
  echo "您的浏览器不支持 video 标签。"; 	
?>
</video>
</div>
</body>
</html>
