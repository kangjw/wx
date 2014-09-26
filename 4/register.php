<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<style type="text/css">
body, 
html,
div#container{width:1000px}
div#header {background-color:#99bbbb;}
div#menu {background-color:#ffff99; height:600px; width:100px; float:left; }
div#allmap {height:600px; width:900px; float:left; }
#header h1 {margin-bottom:0;}
#menu h2 {margin:4;height:60px;width:100%;font-size:18px; float:left;}
</style>

<title>Register User</title>
</head>


<body>
<div id="container">
<div id="header"> <h1 align="center">注册用户!</h1></div>
<div id="menu"> 
	<h2>MENU</h2>
	<h2><a href="main_login.php">登陆</a></h2>
	<h2><a href="logout.php">退出</a></h2>
</div>
<div id="adduser">

<table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
<tr>
<form name="form1" method="post" action="adduser.php">
<td>
<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
<tr>
<td>&nbsp</td>
<td>&nbsp</td>
<td>&nbsp</td>
<td>&nbsp</td>
</tr>
<tr>
<td width="200">用户名</td>
<td width="20">:</td>
<td width="294"><input name="myusername" type="text" id="myusername"></td>
</tr>
<tr>
<td>密码</td>
<td>:</td>
<td><input name="mypassword" type="text" id="mypassword"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td><input type="submit" name="Submit" value="注册"></td>
</tr>
</table>
</td>
</form>
</tr>
</table>

</div>
</body>
</html>

