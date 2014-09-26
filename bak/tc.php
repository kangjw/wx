<?php

//$keycode = "诗 200";
//echo get_de_tangshi();
//echo rhea_get_tangshi($keycode);

function rhea_get_tangshi($keycode)
{
	$keycodes = preg_split("/[\s,]+/",$keycode);
	if(is_numeric($keycodes[1]))
	{
		$index = intval($keycodes[1]);
		return get_tangshi($index);	
	}
	else if(!empty($keycodes[1]) || !empty($keycodes[2]))
	{
		return rhea_search_tangshi($keycode);
	}
	return get_default_tangshi();
			
}	

function get_tangshi($index)
{

$filename = "tangshi.dat";
$handle = fopen($filename,"r");
$lines = 0;
$cfind = 5;
$tc_array = "";

if($handle)
{
	while(!feof($handle))
	{
		$buffer = fgets($handle);
		$buffer = trim($buffer);
		if($buffer[0] >='0' && $buffer[0] <='9')
		{	
			if($lines == $index)
			{
				fclose($handle);
				return $tc_array;	
			}
		
			$tc_array = $buffer;
			$lines++;
		}
		else
		{		
			$tc_array = $tc_array."\n".$buffer;
				
		}
		
	}
	fclose($handle);
	return "没有发现";
	//fclose($handle);
}
return "没有发现";
}


function rhea_search_tangshi($keycode)
{

$filename = "tangshi.dat";
$handle = fopen($filename,"r");
$lines = 0;
$cfind = 5;
$tc_array = "";

$keycodes = preg_split("/[\s,]+/",$keycode);

if($handle)
{
	while(!feof($handle))
	{
		$buffer = fgets($handle);
		$buffer = trim($buffer);
		if($buffer[0] >='0' && $buffer[0] <='9')
		{	
			if(!empty($keycodes[1]) && !empty($keycodes[2]))
			{
				$find1 = strstr($tc_array,$keycodes[1]);
				$find2 = strstr($tc_array,$keycodes[2]);					
				if( $find1 && $find2)
				{
					fclose($handle);
					return $tc_array;
				}	
			}
			else if(!empty($keycodes[1]) && empty($keycodes[2]))
			{
				
				if(strstr($tc_array,$keycodes[1]))
				{
					fclose($handle);
					return $tc_array;
				}	
			}
			$tc_array = $buffer;
			$lines++;
		}
		else
		{		
			$tc_array = $tc_array."\n".$buffer;
				
		}
		
	}
	fclose($handle);
	return "没有发现";
	//fclose($handle);
}
return "没有发现";
}

function get_default_tangshi()
{
	$index = rand(0,321);
	$ts_str = get_tangshi($index);
	return $ts_str;		
}



?>
