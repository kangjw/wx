<?php 


$date1 = date('Ymd');

echo $date1;
echo "<br>";
echo substr($date1,0,-2);


if(strncmp($date1,"20140327",8)==0 )
{
	echo "find\n";
}
else 
	echo "no find\n";

#echo rhea_baidu_find("将进酒");


function rhea_baidu_find($keycode)
{	
	$url1 = "http://www.baidu.com/s?ie=utf-8&mod=0&isid=8891b3df001ff4e2&pstg=0&wd=";
	$url2  = "&rsv_sid=5694_1452_5223_5722_5461_4261_5568_4759_5659&f4s=1&_cr1=8851";
	$url = $url1.$keycode.$url2;
	
	$page = rhea_get_baidu($url);
	$page_left =  rhea_get_http_section($page,"<div id=\"content_left\">");
	$context =  rhea_get_http_section($page_left,"<div class=\"result");
	
	$span = rhea_get_last_span($context);
	$urllink = rhea_get_more_url($context);
	return $span.$urllink;
}	


function rhea_get_baidu($url)
{
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_REFERER, "www.baidu.com"); 
	$cookid = "BAIDUID=F6A939D7F5C5B1A68E34173C79581BF6:FG=1; H_PS_TIPFLAG=O; H_PS_TIPCOUNT=4; BD_CK_SAM=1; BDRCVFR[feWj1Vr5u3D]=I67x6TjHwwYf0; H_PS_PSSID=5695_5507_1469_5223_5722_4261_5567_4760_5453";
	curl_setopt($ch,CURLOPT_COOKIE, $cookid);  
	curl_setopt($ch,CURLOPT_HEADER,false);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$context = curl_exec($ch);
	curl_close($ch);
	return $context;
}

function rhea_get_last_span($page)
{
	$span_tag = "c-span-last";
	$start_page = strstr($page,$span_tag);
	if($start_page == false)
		return "没有发现";	
        $start_page = "<div class=\"".$start_page;
	$span = rhea_get_http_section($start_page,"<div");
	$span_str = strip_tags($span);

	$findmore = strpos($span_str,"...");
	if($findmore == false)
		return $span_str;
	$span = substr($span_str,0,$findmore);
	return $span."...";
}


function rhea_get_more_url($page)
{
	$t_tag="<h3 class=\"t\">";
	$start_page = strstr($page,$t_tag);
	if($start_page == false)
		return " ";

	$start_page = strstr($start_page,"http");
	$end_page = strpos($start_page,"\"");
	$url = substr($start_page,0,$end_page);
	return $url;
}




function rhea_get_http_section($page,$start)
{
			
	$startpage = strstr($page,$start);

	if($startpage == false)
		return "什么也没有发现！";

	$length = strlen($startpage);
	

	$findcount = 0;	
	for($i= 0; $i < $length;$i++)
	{
		$start_str = substr($startpage,$i);
		//echo $start_str."KANGJW<BR>";
		
		if(strncmp($start_str,"<div",4) == 0)
		{
			
			//echo "K1JW".$findcount."<BR>";			
			$findcount++;
		}
		else if(strncmp($start_str,"</div>",6) == 0)	
		{
			//echo "K2JW".$findcount."<br>";
			$findcount--;
			if($findcount == 0)
			{
				$find_str = substr($startpage,0,$i);
				return $find_str;
			}
		}
	}
	return "没有发现";	
}

?>

