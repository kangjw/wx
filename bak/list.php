<html>
<head>
<meta name="viewport" content="width=100%, initial-scale=1"  charset="UTF-8" />
<style type="text/css">
a.r1:link,a.r1:visited
{
display:block;
font-weight:bold;
font-size:20px;
font-family:Verdana, Arial, Helvetica, sans-serif;
color:#FFFFFF;
background-color:#98bf21;
width:120px;
text-align:center;
padding:10px;
text-decoration:none;
float: left;
margin: 10px 20px 10px 0px;
}
a.r1:hover,a.r1:active
{
background-color:#7A991A;
}

a.r2
{
position: relative;
left: 60px;
font-size:16px;
color:#FFFFFF;
background-color:#98bf21;
padding:2px 10px 2px 10px;
text-decoration:none;
}

ul {margin:0px;padding:0px}
li {clear:both;
  font-size:18px;
  font-weight: bold;
  list-style:none;
  height:30px;}

a.r3
{
float:left;
margin: 10px 20px 10px 0px;
}

</style>
</head>

<body>
<div style="background-color:#4169E1; width: 100%">
<h1>实景图</h1>
</div>

<?php

$dirlist=scandir("/tmp/rhea",1);
if($dirlist)
{
	echo "<ul>";
	for($i = 0 ; $i < count($dirlist)-2;$i++)
	{
		echo "<li>";
		echo "<a class=\"r3\" href=\"file.php?d=$dirlist[$i]\"> $dirlist[$i]</a>";
		if(file_exists("v/$dirlist[$i]/v.mp4"))
			echo "<a class=\"r3\" href=\"video.php?d=$dirlist[$i]\">视频合成</a> ";
		else	
			echo "<a class=\"r3\" href=\"list.php\">暂无</a> ";
		echo "</li>";
	}
	echo "</ul>";
}

include "foot.htm";

?>


</body>
</html>

