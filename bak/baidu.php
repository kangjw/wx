<?php

//echo rhea_get_baidu_weather("上海天气");

function rhea_get_baidu_weather($city)
{
	$page = rhea_get_baidu_page($city);
	$str_wt = rhea_wt_get_div($page);
	return $city.":\n".$str_wt;
}


function rhea_get_baidu_page($keyword)
{
	$baidu_url = "http://zhidao.baidu.com/search?lm=0&rn=10&pn=0&fr=search&ie=gbk&word=";
	$page = file_get_contents($baidu_url.$keyword);
	return $page;
}	

function rhea_wt_get_div($page)
{
	$tag_start = "<div class=\"weather-al-list\">";
	$tag_end = "</div>";

	$str_start = strstr($page,$tag_start);
	if(!$str_start)
		return "亲,没有找到！";
	$end = strpos($str_start,$tag_end);
	$str_wt = substr($str_start,0,$end);	
	
	$str_wt = iconv('GB2312', 'UTF-8',$str_wt);
	
        $link_str = strstr($str_wt,"http://www.weather.com.cn");
	$link_end = strpos($link_str,"\"");
	$link_str = substr($link_str,0,$link_end);
	preg_match("/\d{8,}/",$link_str,$link_ret);

	$keywords = preg_split("/[\f\n\r\t\v]+/",strip_tags($str_wt));

	$str_wt = "";
	for($i = 1; $i < count($keywords)-1;$i++)
	{
		if($i%4 == 1)
			$str_wt = $str_wt."【".$keywords[$i]."】"."\n";
		else
			$str_wt = $str_wt.$keywords[$i]."\n";
	}
	
	return $str_wt."\n【更详细，请点击:】\n"."http://mobile.weather.com.cn/city/".$link_ret[0].".html";
}



?>
