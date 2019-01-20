<?php
//获得参数 
	$nonce = $_GET['nonce'];
	$token = 'tianqi';
	$timestamp = $_GET['timestamp'];
	$echostr = $_GET['echostr'];
	$signature =$_GET['signature'];
	//形成数组
	$array = array();
	$array = array($nonce,$timestamp,$token);
	sort($array);
	$str=sha1(implode($array));
	if( $str == $signature && $echostr ){
		//第一次接入weixin api接口的时候
		echo  $echostr;
		exit;
	}else{
		$wechatObj = new wechatCallbackapiTest();
		$wechatObj->responseMsg();
	}

	class wechatCallbackapiTest
	{

	    public function responseMsg()
	    {
	        //get post data, May be due to the different environments
	        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

	          //extract post data
	        if (!empty($postStr)){
	                
	                  $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	                $RX_TYPE = trim($postObj->MsgType);

	                switch($RX_TYPE)
	                {
	                    case "text":
	                        $resultStr = $this->handleText($postObj);
	                        break;
	                    case "event":
	                        $resultStr = $this->handleEvent($postObj);
	                        break;
	                    default:
	                        $resultStr = "Unknow msg type: ".$RX_TYPE;
	                        break;
	                }
	                echo $resultStr;
	        }else {
	            echo "";
	            exit;
	        }
	    }

	    public function handleText($postObj)
	    {
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
	                    <FuncFlag>0</FuncFlag>
	                    </xml>";             
	        if(!empty( $keyword ))
	        {
	            $msgType = "text";

	            //天气
	            $str = mb_substr($keyword,-2,2,"UTF-8");
	            $str_key = mb_substr($keyword,0,-2,"UTF-8");
	            if($str == '天气' && !empty($str_key)){
                    $contentStr  = $this->getWeather($str_key);
            
	            } else {
	                $contentStr = "";
	            }

	            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
	            echo $resultStr;
	        }else{
	            echo "Input something...";
	        }
	    }
	
      
	    public function handleEvent($object)
	    {
	        $contentStr = "";
	        switch ($object->Event)
	        {
	            case "subscribe":
	                $contentStr = "欢迎关注我们的微信公众账号PKU天气预报";
	                break;
	            default :
	                $contentStr = "Unknow Event: ".$object->Event;
	                break;
	        }
	        $resultStr = $this->responseText($object, $contentStr);
	        return $resultStr;
	    }
	    
	    public function getWeather($city){
          $url = "http://192.144.143.57/city/".$city;
          $res = file_get_contents($url); 
      	  $data = json_decode($res,true);
          if($data){
          	$code = $data["city_code"];
            $url_city = "http://192.144.143.57/weather/".$code;
            $res = file_get_contents($url_city);
            return $res;
			$data = json_decode($res,true);
          }
          return $data;
        }
	    public function responseText($object, $content, $flag=0)
	    {
	        $textTpl = "<xml>
	                    <ToUserName><![CDATA[%s]]></ToUserName>
	                    <FromUserName><![CDATA[%s]]></FromUserName>
	                    <CreateTime>%s</CreateTime>
	                    <MsgType><![CDATA[text]]></MsgType>
	                    <Content><![CDATA[%s]]></Content>
	                    <FuncFlag>%d</FuncFlag>
	                    </xml>";
	        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
	        return $resultStr;
	    }



	 
	}

?>
