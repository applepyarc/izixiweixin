<?php

header("Content-Type: text/html; charset=utf8");

define("TOKEN", "weixin");

/*
$s=new SaeStorage();
if ($s->fileExists('logstorage', 'log') == true){
    $s->write('logstorage', 'log', 'begin');
}
else {
    echo 'faild to find log file';
}

if ($s->fileExists('logstorage', 'data_2013_2.cvs')){
  //echo 'load data';
    $s->read('logstorage', 'data_2013_2.cvs');
}
else {
    echo 'faild to find data';
}
*/

$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid('#now');
$wechatObj->responseMsg(); 
class wechatCallbackapiTest
{
    public function valid($request)
    {
      //$echoStr = $_GET["echostr"]; 
      //if($this->checkSignature()){
          {
            
          //$searchStr=$this->getCurrentSearch();
          //$searchStr=$this->getSearch("星期二，第4，5节");
          //echo $searchStr . "\n";
          //$room=$this->getRoom($searchStr);
            
          //echo date("今天是Y年m月d日星期N，当前时间：H:i。",time()) . "当前空闲教室为：\n" . $room . "\n" . "请输入您想查询的时间段来获取教室列表，例如：星期一，第1，2节";
/*
            $s=new SaeStorage();
            $dataStr=$s->read('logstorage', 'data_2013_2.cvs');
          //echo $dataStr;
            $room='';
            do
            {
            $index1=strpos($dataStr, $searchStr);
            echo 'index1=' . $index1;
            $index2=strpos($dataStr, ',', $index1+strlen($searchStr));
            echo 'index2=' . $index2;
            $room=$room . substr($dataStr, $index1+strlen($searchStr), $index2-$index1+strlen($searchStr));
            
            $dataStr=substr($dataStr, $index2);
            }while(substr_count($dataStr, $searchStr) > 0);
            
            echo $room;
            
            $tmp2=$tmp1;
            $tmp2=$tmp2 . 'week=' . $week;
            $s->write('logstorage', 'log', $tmp2);
*/
            //$request="#today";
            $reply="";
            
            $tmpChinese=$this->checkChineseCharacter($request);
            if ($tmpChinese != '')
            {
                return $tmpChinese;
            }
            
            $signal=$this->checkSignal($request);
            echo $signal;
            if($signal == 1)
            {            
              //echo "signal == 1";
                $tmp=$this->getSearchReq($request);
                if($tmp != "")
                {
                    $reply=$tmp;
                }
                else
                {
                    $reply="当前为非上课时间，没人的教室都可上自习，注意劳逸结合哦^_^！";
                }
            }
            else if($signal == 2)
            {
              //echo "signal == 2";
                $reply=$this->getDirection($request);
            }
            else if($signal == 0)
            {
              $reply="感谢您的反馈，客服妹纸会尽快给您答复:)";
            }
            if ($reply=="")
            {
              $reply="不明白你要去哪自习，发送＂?＂获得快速上手技巧，发送＂??＂获得进阶使用技巧，发送＂???＂获得高阶使用技巧吧:)";
            }
            
            return $reply;
            
          //$searchStr=$this->getSearchStr();
          //echo $echoStr;
          //exit;
        }
      
    }

    private function checkChineseCharacter($req)
    {
        //echo "checkChineseCharacter";
        $count=mb_substr_count($req, "？");
        //echo "?count=" . $count;
        $result='';
        if(($count != 0) && (mb_strpos($req, "？") !== 0))
        {
            return "感谢您的反馈，客服妹纸会尽快给您答复:)";
        }
        switch($count)
        {
        case 1:
        $result="欢迎使用西政i自习 微信助手，你可输入＂#now＂查询当前可用自习室，输入＂#toady＂查询今天的全天自习室；如想了解进阶使用技巧，请输入两个问号＂??＂。";
        break;
        case 2;
        $result="你可输入＂#+一到两位数字＂查询特定课时的自习教室安排，如输入＂#1＂则表示查询周一全天自习室安排；如输入＂#13＂则表示查询周一，第三节课的时候有哪些自习室，＂#36＂表示查询周三，第六节时有哪些自习室。想了解高阶使用技巧，请输入三个问号＂???＂";
        break;
        case 3:
        $result="如果你想将自习室结果限定在笃行楼（三教）或博学楼（四教），可在进阶命令后添加3（表示笃行楼）或4（表示博学楼）。如＂#234＂，表示周二，第三节课时，博学楼（四教）的自习室安排；#423，表示周四，第二节课时，笃行楼（三教）的自习室安排。如尚有疑问，可直接输入你的问题，我们会在24小时内回答你的问题。";
        break;
        }
        return $result;
    }
    
    private function checkSignal($req)
    {
        if(substr_count($req, "#") != 0)
        {
          //echo "#1";
            return 1;
        }
        else if(substr_count($req, "?") != 0)
        {
            return 2;
        }
        else
        {
            return 0;
        }           
    }
    
    private function getSearchReq($req)
    {
      //echo $req;
        $index=strpos($req, "#");
        $tmp2=substr($req, $index, 6);
        if($tmp2 == "#today")
            {                
              //echo "today getAllDaySearch";
                return $this->getAllDaySearch();
            }
        $tmp=substr($req, $index);
        if(strlen($tmp) >= 4)
        {
            $tmp=substr($tmp, 0, 4);
            
            if($tmp == "#now")
            {
                //echo "now";
                return $this->getCurrentSearch();
            }
        }
        
      //echo $tmp;
        switch(strlen($tmp))
        {
          case 2:
          return $this->getSearch1($tmp);
          //return "由于教务处未公布全天自习室名单，请输入至少两位数字，查询可用自习室。";
          break;
          case 3:
          return $this->getSearch2($tmp);
          break;
          case 4:
          return $this->getSearch3($tmp);
          break;
          default:
          return "不明白你要去哪自习，发送＂?＂获得快速上手技巧，发送＂??＂获得进阶使用技巧，发送＂???＂获得高阶使用技巧吧:)";
        }
        
    }
    
    private function getSearch1($req)
    {
      
        $file = fopen("data_2013_2_allday", "r");
        $result='';
        $weeklen=0;
        $weekCh='';
        $bit1=substr($req, 1, 1);
      //echo substr($req, 0, 1);
        switch($bit1)
         {
         case 1:
         $weeklen=strlen('Monday');
         $weekCh='周一';
         break;
         case 2:
         $weeklen=strlen('Tuesday');
         $weekCh='周二';
         break;
         case 3:
         $weeklen=strlen('Wednesday');
         $weekCh='周三';
         break;
         case 4:
         $weeklen=strlen('Thursday');
         $weekCh='周四';
         break;
         case 5:
         $weeklen=strlen('Friday');
         $weekCh='周五';
         break;
         default:
         return "不明白你要去哪自习，你可输入#now 找到当前可用自习室。发送问号＂?＂获得快速上手技巧，发送两个问号＂??＂获得进阶使用技巧，发送三个问号＂???＂获得高阶使用技巧。";
         break;
         }
        $count=1;
        while(! feof($file))
        {
            if ($count != $bit1)
            {
              //echo $count;
                $count=$count+1;
                fgets($file);
            }
            else
            {
              //echo "bingo" . $count;
                $result = fgets($file);
              //echo "restul" . $result;
                break;
            }            
        }
        fclose($file);
        
        $result=substr($result, $weeklen+1);
        
        return $weekCh . "的全天自习教室有：" . $result; 
    }
    
    private function getSearch2($req)
    {
        $bit1=substr($req, 1, 1);
        $bit2=substr($req, 2, 1);
        
        $weekStr="";        
        switch($bit1)
        {
          case 1:
          $weekStr='Monday';
          break;
          case 2:
          $weekStr='Tuesday';
          break;
          case 3:
          $weekStr='Wednesday';
          break;
          case 4:
          $weekStr='Thursday';
          break;
          case 5:
          $weekStr='Friday';
          break;
          default:
          return "不明白你要去哪自习，发送＂?＂获得快速上手技巧，发送＂??＂获得进阶使用技巧，发送＂???＂获得高阶使用技巧吧:)";
        }
        
        $timeStr="";
        switch($bit2)
        {
          case 1:
          case 2:
          $timeStr="1-2";
          break;
          case 3:
          case 4:
          $timeStr="3-4";
          break;
          case 5:
          case 6:
          $timeStr="5-6";
          break;
          case 7:
          case 8:
          $timeStr="7-8";
          break;
          default:
          return "不明白你要去哪自习，发送＂?＂获得快速上手技巧，发送＂??＂获得进阶使用技巧，发送＂???＂获得高阶使用技巧吧:)";
        }
        
        $searchStr=$weekStr . ',' . $timeStr . ',';
        
        return $this->getRoom($searchStr);
    }
    
    private function getSearch3($req)
    {
        $bit1=substr($req, 1, 1);
        $bit2=substr($req, 2, 1);
        $bit3=substr($req, 3, 1);
        
        $weekStr="";        
        switch($bit1)
        {
          case 1:
          $weekStr='Monday';
          break;
          case 2:
          $weekStr='Tuesday';
          break;
          case 3:
          $weekStr='Wednesday';
          break;
          case 4:
          $weekStr='Thursday';
          break;
          case 5:
          $weekStr='Friday';
          break;
          default:
          return "不明白你要去哪自习，发送＂?＂获得快速上手技巧，发送＂??＂获得进阶使用技巧，发送＂???＂获得高阶使用技巧吧:)";
        }
        
        $timeStr="";
        switch($bit2)
        {
          case 1:
          case 2:
          $timeStr="1-2";
          break;
          case 3:
          case 4:
          $timeStr="3-4";
          break;
          case 5:
          case 6:
          $timeStr="5-6";
          break;
          case 7:
          case 8:
          $timeStr="7-8";
          break;
          default:
          return "不明白你要去哪自习，发送＂?＂获得快速上手技巧，发送＂??＂获得进阶使用技巧，发送＂???＂获得高阶使用技巧吧:)";
        }
        
        if($bit3 != "3" && $bit3 != "4")
        {          
          return "不明白你要去哪自习，发送＂?＂获得快速上手技巧，发送＂??＂获得进阶使用技巧，发送＂???＂获得高阶使用技巧吧:)";
        }
        
        $searchStr=$weekStr . ',' . $timeStr . ',' . $bit3;
        
        return $this->getRoom2($searchStr);
        
    }
    
    private function getRoom2($req)
    {
        $dataStr = file_get_contents("data_2013_2.cvs");
        $tmp = $dataStr;
        do {
    	$index1 = stripos($tmp, $req);
        if ($index1 == FALSE)
        {
            break;
        }

    	$index2 = strpos($tmp, ',', $index1+strlen($req));
    	if ($index2 == FALSE) {
            break;
    	}
    		
    	if ($hint != "") {
    		if ($count % 5 == 0) {
        		$hint = $hint . "\n";
        		$count = 1;
        	}
        	else {
        		$hint = $hint . ", ";
        		$count=$count+1;
        	}
    	}
   		
    	$hint = $hint . substr($tmp, $index1+strlen($req)-1, $index2-$index1-strlen($req)+1);

    	$tmp = substr($tmp, $index2);
        }while (strpos($tmp, $req) != FALSE);
        
        return $hint;
    }
    
    private function getDirection($req)
    {
        $index=strpos($req, "?");
        $tmp=substr($req, $index);
        if(strlen($tmp) > 3)
        {
            $req=substr($tmp, 0, 3);
        }
        
        $count=substr_count($req, "?");
        
        switch($count)
        {
          case 1://?
          return "欢迎使用西政i自习 微信助手，你可输入＂#now＂查询当前可用自习室，输入＂#toady＂查询今天的全天自习室；如想了解进阶使用技巧，请输入两个问号＂??＂。";
          break;
          case 2://??
          if(substr($req, 0, 2) == "??")
          {
              return "你可输入＂#+一到两位数字＂查询特定课时的自习教室安排，如输入＂#1＂则表示查询周一全天自习室安排；如输入＂#13＂则表示查询周一，第三节课的时候有哪些自习室，＂#36＂表示查询周三，第六节时有哪些自习室。想了解高阶使用技巧，请输入三个问号＂???＂";
          }
          else
          {
              return "";
          }
          break;
          case 3://???
          return "如果你想将自习室结果限定在笃行楼（三教）或博学楼（四教），可在进阶命令后添加3（表示笃行楼）或4（表示博学楼）。如＂#234＂，表示周二，第三节课时，博学楼（四教）的自习室安排；#423，表示周四，第二节课时，笃行楼（三教）的自习室安排。如尚有疑问，可直接输入你的问题，我们会在24小时内回答你的问题。";
          break;
          default:
          return "";
          
        }
    }
                
    
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
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
            <FuncFlag>0<FuncFlag>
            </xml>";
          /*            
            $s=new SaeStorage();
            if ($s->fileExists('logstorage', 'log') == true){
                $s->write('logstorage', 'log', $keyword);
            }
            $week=date("N", time());
            */
            if(!empty( $keyword ))
            {
                $msgType = "text";
              //$contentStr = 'hello world';
                $contentStr=$this->valid($keyword);
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                
            }else{
                echo 'test...';
            } 
        }else {
            echo 'testing...';
            exit;
        }
    }

    private function getRoom($req)
    {
        $dataStr = file_get_contents("data_2013_2.cvs");
        $tmp = $dataStr;
        do {
    	$index1 = stripos($tmp, $req);
        if ($index1 == FALSE)
        {
            break;
        }

    	$index2 = strpos($tmp, ',', $index1+strlen($req));
    	if ($index2 == FALSE) {
            break;
    	}
    		
    	if ($hint != "") {
    		if ($count % 5 == 0) {
        		$hint = $hint . "\n";
        		$count = 1;
        	}
        	else {
        		$hint = $hint . ", ";
        		$count=$count+1;
        	}
    	}
/*
        echo "len = ". strlen($req) . "\n";
        echo "index1 = " . $index1 . "\n";
        echo "index2 = " . $index2 ."\n";
        echo "index3 = " . ($index2-$index1-strlen($req)) . "\n";
*/
        
        
    		
    	$hint = $hint . substr($tmp, $index1+strlen($req), $index2-$index1-strlen($req));
        //echo $hint . "\n";
    	$tmp = substr($tmp, $index2);
        }while (strpos($tmp, $req) != FALSE);
        
        //echo $hint . '\n';
        return $hint;
    }
    
    private function getCurrentSearch()
    {
        $week=date("N", time());
        $hour=date("H", time());
        $minute=date("i", time());
        echo 'week=' . $week . 'hour=' . $hour . 'minute=' . $minute;
        $weekStr='';
        $weekCh='';
        switch ($week)
        {
          case 1:
          $weekStr='Monday';
          $weekCh='一';
          break;
          case 2:
          $weekStr='Tuesday';
          $weekCh='二';
          break;
          case 3:
          $weekStr='Wednesday';
          $weekCh='三';
          break;
          case 4:
          $weekStr='Thursday';
          $weekCh='四';
          break;
          case 5:
          $weekStr='Friday';
          $weekCh='五';
          break;
        }
        $timeStr='';
      //echo 'hour=' . $hour . 'minute' . $minute;
/*
        if (($hour >= 8 && $minute <= 30) && ($hour <= 10 && $minute <= 10)) {
                $timeStr='1-2';
        }
        
        else if (($hour >= 10 && $minute <= 30) && ($hour <= 12 && $minute <= 10)) {
                $timeStr='3-4';
        }
        
        else if (($hour >= 14 && $minute <= 00) && ($hour <= 15 && $minute <= 40)) {
                $timeStr='5-6';
        }
        
        else if (($hour >= 16 && $minute <= 10) && ($hour <= 17 && $minute <= 50)) {
                $timeStr='7-8';
}*/
        if (($hour == 8 && $minute >= 30)) {
                $timeStr='1-2';
        }
        else if (($hour == 9)) {
                $timeStr='1-2';
        }
        else if (($hour == 10 ) && ($minute <= 10 )) {
                $timeStr='1-2';
        }        
        else if (($hour == 10 && $minute >= 30)) {
                $timeStr='3-4';
        }
        else if (($hour == 11)) {
                $timeStr='3-4';
        }
        else if (($hour == 12 ) && ($minute <= 10)) {
                $timeStr='3-4';
        }        
        else if (($hour == 14)) {
                $timeStr='5-6';
        }
        else if (($hour == 15) && ($minute <= 40)) {
                $timeStr='5-6';
        }        
        else if (($hour == 16 && $minute >= 10)) {
                $timeStr='7-8';
        }
        else if (($hour == 17 && $minute <= 50)) {
                $timeStr='7-8';
        } 
        
      //echo 'week=' . $weekStr . "time=" . $timeStr;
        if ($weekStr == '' || $timeStr == '')
        {
            return "";
        }
        
        $searchStr=$weekStr . ',' . $timeStr . ',';
      //echo 'searchStr=' . $searchStr . '\n';  
        
        $room=$this->getRoom($searchStr);
        
        return date("今天是Y年m月d日星期",time()) . $weekCh . "，当前空闲教室为：\n" . $room;
    }

    private function getAllDaySearch()
    {
        $week=date("N", time());
      //echo "week". $week;
        $weekStr='';
        $weekCh='';
        switch ($week)
        {
          case 1:
          $weekStr='Monday';
          $weekCh='一';
          break;
          case 2:
          $weekStr='Tuesday';
          $weekCh='二';
          break;
          case 3:
          $weekStr='Wednesday';
          $weekCh='三';
          break;
          case 4:
          $weekStr='Thursday';
          $weekCh='四';
          break;
          case 5:
          $weekStr='Friday';
          $weekCh='五';
          break;
        }
        if ($weekStr == '')
        {
            return '';
        }

        $room=$this->getAllDayRoom($week);
      //echo "room=" . $room;

        if($room == '')
        {
            return '';
        }

        return date("今天是Y年m月d日星期",time()) . $weekCh . "，今天的全天自习室是：\n" . $room;
    }

    private function getAllDayRoom($week)
    {
        //$dataStr = file_get_contents("data_2013_2_allday");
        $file = fopen("data_2013_2_allday", "r");
        $result='';
        $weeklen=0;
        switch($week)
         {
         case 1:
         $weeklen=strlen('Monday');
         break;
         case 2:
         $weeklen=strlen('Tuesday');
         break;
         case 3:
         $weeklen=strlen('Wednesday');
         break;
         case 4:
         $weeklen=strlen('Thursday');
         break;
         case 5:
         $weeklen=strlen('Friday');
         break;
         }
        $count=1;
        while(! feof($file))
        {
            if ($count != $week)
            {
              //echo $count;
                $count=$count+1;
                fgets($file);
            }
            else
            {
              //echo "bingo" . $count;
                $result = fgets($file);
              //echo "restul" . $result;
                break;
            }            
        }
        fclose($file);
        
        return substr($result, $weeklen+1);
    }
/*    
    private function getSearch($txt)
    {

    $result='';
    $week=substr($txt, strpos($txt, "星期")+strlen("星期"), 1);
    echo "txt=" . $txt;
    echo strlen(+strlen("星期")) . "ddd";
    echo strpos($txt, "星期") . "ssss";
    echo strpos($txt, "二");
    echo "week=" . $week;
    $weekStr="";
    $timeStr="";
    switch ($week)
    {
        case 1:
        $weekStr='Monday';
        break;
        case 2:
        $weekStr='Tuesday';
        break;
        case 3:
        $weekStr='Wednesday';
        break;
        case 4:
        $weekStr='Thursday';
        break;
        case 5:
        $weekStr='Friday';
        break;
    }
    
    switch ($week)
    {
        case "一":
        $weekStr='Monday';
        break;
        case "二":
        $weekStr='Tuesday';
        break;
        case "三":
        $weekStr='Wednesday';
        break;
        case "四":
        $weekStr='Thursday';
        break;
        case "五":
        $weekStr='Friday';
        break;
    }
    
    if(strpos($txt, "第") != FALSE)
    {
        $time=substr($txt, strpos($txt, "第"), 1);
        
        switch($time)
        {
          case 1:
          $timeStr="1-2";
          break;
          case 3:
          $timeStr="3-4";
          break;
          case 5:
          $timeStr="5-6";
          break;
          case 7:
          $timeStr="7-8";
          break;
        }
        
        switch($time)
        {
          case "一":
          $timeStr="1-2";
          break;
          case "三":
          $timeStr="3-4";
          break;
          case "五":
          $timeStr="5-6";
          break;
          case "七":
          $timeStr="7-8";
          break;
        }
    }
    
    echo $weekStr;
    echo $timeStr;
    
    if($weekStr != "" && $timeStr != "")
    {
      //$result=$weekStr . "," . $timeStr ",";
    }    
    
    return $result;

    }
*/
 
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token =TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        return true;
 
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
?>
