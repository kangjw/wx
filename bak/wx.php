<?php
/**
  * wechat php test
  */

//define your token
require("main.php");
require_once("tc.php");
require_once("weather.php");


define("TOKEN", "kangjw");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	        //extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
			    <ToUserName><![CDATA[%s]]></ToUserName>
			    <FromUserName><![CDATA[%s]]></FromUserName>
			    <CreateTime>%s</CreateTime>
			    <MsgType><![CDATA[%s]]></MsgType>
			    <Content><![CDATA[%s]]></Content>
			    </xml>";             
		if(!empty( $postObj->MsgType ))
                {
              		$msgType = "text";
                	//$contentStr = "欢迎你来到瑞亚乐园!";	
			//$contentStr = rhea_main_menu($keyword);
			$contentStr = rhea_main_msg($postObj);
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
			//echo $fromUsername;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "eeor";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
