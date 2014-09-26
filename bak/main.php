<?php

require_once("baidu.php");
require_once("tc.php");
require_once("songci.php");
//echo rhea_main_menu("test");



function rhea_main_msg($msgObj)
{
	$msgtype = $msgObj->MsgType;
	if(0 == strcmp($msgtype,"event"))
	{
		$reply_msg = rhea_main_event($msgObj);		
	}
	else if(0 == strcmp($msgtype,"text"))
	{	
		$keycode = $msgObj->Content;
		$reply_msg = rhea_main_menu($keycode);	
	} 
	else
	{
		$keycode = $msgObj->Content;
		$reply_msg = rhea_main_menu($keycode);	
	}
	return $reply_msg;
		
}

function rhea_main_event($msgObj)
{
	$event = $msgObj->Event;
	if(0 == strcmp($event,"subscribe"))
	{
		$reply_msg = rhea_welcome();
	}
	else if(0 == strcmp($event,"unsubscribe"))
	{
		$reply_msg ="抱歉，没有能够给老板提供东莞式的标准服务，让你受累了！\n";
	}
	else
	{		
		$reply_msg = rhea_get_help();
	}
	return $reply_msg;
}



function rhea_main_menu($keycode)
{
	$resp = "没有发现";
	
	if(strncmp($keycode,'A') == 0 || strncmp($keycode,'a') == 0)
	{
		$resp = "最新的实景图片，请点击:\nhttp://v.rheatelecom.com/v.htm";
	}
	else if(strstr($keycode,"天气"))
	{
		$resp = rhea_get_baidu_weather($keycode);
	}
	else if(strstr($keycode,"诗"))
	{
		$resp = rhea_get_tangshi($keycode);
	}

	else if(strstr($keycode,"词"))
	{
		$resp = rhea_get_songci($keycode);
	}
	if(strstr($resp,"没有发现"))
	{
		$resp = rhea_get_help();	
	}
	return $resp;
}	



function rhea_get_help()
{
	$t="【亲】，我不懂你的输入！\n";
	$t1="客官，我们只提供下面服务:\n\n";
	$h1="【天气服务】:\n例如输入“北京天气“\n\n";
	$h2="【唐诗300首】:\n例如输入“唐诗 李白 明月光”\n";
	$h21="例如输入“诗 120“\n\n";
	$h3="【宋词300首】:\n例如”词 明月几时有“\n\n";
        $end="客官，这个只是个学习项目!\n";

	return $t.$t1.$h1.$h2.$h21.$h3.$end;		
}


function rhea_welcome()
{
	$t="欢迎老板关顾瑞亚！\n";
	$t1="老板，我们只提供下面服务:\n";
	$h1="【天气服务】:\n例如“北京天气“\n";
	$h2="【唐诗300首】:\n例如“唐诗 李白 明月光”\n";
	$h21="例如输入“诗 120“\n";
	$h3="【宋词300首】:\n例如”词 明月几时有“\n";
        $end="\n学习微信API!\n";

	return $t.$t1.$h1.$h2.$h21.$h3.$end;		
}

?>
