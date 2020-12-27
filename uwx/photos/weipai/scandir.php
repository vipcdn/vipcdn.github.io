<?php
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('PRC');
$dbhost = 'localhost';
$dbuser = 'uwx_mysql';
$dbpwd  = 'uwx_mypwd20';
$dbname = 'uwuxiu_db';
$tablepre = 'uwx_';
$dbcharset = null;
$pconnect = '';
$db = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);
$db->query("SET NAMES utf8");	
if ($db->connect_errno) {
	printf("Connect failed: %s\n", $db->connect_error);
	exit();
}


function getDir($path){
 global $db;
  if(is_dir($path)){

    $dir = scandir($path);
	$i=1;
    foreach ($dir as $value){
		
      $sub_path =$path .'/'.$value;
      if($value == '.' || $value == '..'){
        continue;
      }else if(is_dir($sub_path)){
        echo '目录名:'.$value .'<br/>';
		
        getDir($sub_path);
      }else{
        //.$path 可以省略，直接输出文件名
		//$value = iconv('GB2312', 'UTF-8',$value);
		if(empty($_SESSION['xsvid'])){$_SESSION['xsvid'] = 1;}
		
        echo $_SESSION['xsvid'].' 最底层文件: '.$path. ':'.$value.' <hr/>';
		$title  = "微拍No.".$_SESSION['xsvid'];
		
		//$i= $i+1;
		$videoname = str_replace(".jpg","",$value);
		$video = "/videos/".$path."/".$videoname;
		///$date = date("Ymd");
		$rand = mt_rand(10,99);
		$newvalue = "wp".$rand.$_SESSION['xsvid'].".jpg";
		
		$file =  __DIR__.'/'.$path.'/'.$value;
		$new_file_name =  __DIR__.'/'.$path.'/'.$newvalue;
		$tag ="微拍";
		$img = "/photos/".$path."/".$newvalue;
		$FabNum = mt_rand(100,999);
		if($new_file_name != ""){
			
			$sql = "update `uwx_videos_new` SET `img` ='$img',`title` ='$title',`tag` ='$tag', `FabNum` ='$FabNum',`murl` ='$videoname' where `video` = '$video'";
			$query = $db->query($sql);
			//echo $sql;
			rename($file, $new_file_name);
		}
		
		
		//$npath = "/var/www/html/dgxz/";
		//$old_file_name="/var/www/html/".$path.'/'.$value;
		//$rand = mt_rand(1,99999);
		//$murl = str_replace("dgxz/","",$path);
		//$name = $murl.'_'.$rand.".mp4";
		//$new_file_name="/var/www/html/dgxz/".$name;
		//var_dump($old_file_name);
		//var_dump($new_file_name);
		
		//重命名 写入数据库
		//$orderID = $path;
		
		
		//if(!empty($title)){
			//$sql = "INSERT INTO `uwuxiu_db`.`uwx_svideos` (`cat_id` ,`murl` ,`img` ,`svideo` ,`title` ,`tag`) VALUES ('$cat_id',  '$murl' , '$img', '$svideo','$title', '$tag')";
			//$query = $db->query($sql);
	
			//rename($old_file_name, $new_file_name);
		//}
		
		
		$_SESSION['xsvid'] = $_SESSION['xsvid']+1;
      }
	  
    }
	
  }
}
$path = '2018';
getDir($path); 

//加密解密
/** 
* 返回一字符串，十进制 number 以 radix 进制的表示。 
* @param dec       需要转换的数字 
* @param toRadix    输出进制。当不在转换范围内时，此参数会被设定为 2，以便及时发现。 
* @return    指定输出进制的数字 
*/ 
function urlShort($url){	
    $url= crc32($url);	
    $result= sprintf("%u", $url);
    $sUrl= '';
    while($result>0){
        $s= $result%62;
        if($s>35){
            $s= chr($s+61);
        } elseif($s>9 && $s<=35){
            $s= chr($s+ 55);
        }
        $sUrl.= $s;
        $result= floor($result/62);
    }
	//取前四位
    return substr($sUrl,0,4);
}
function encryptNum($dec, $toRadix='52') {
	//获取最后一位作为干扰码
	$inf = substr($dec,-1);
    $MIN_RADIX = 2; 
    $MAX_RADIX = 62; 
    $num62 = 'xLszHMmqUcKiGupaXAvYRBrhZnSjIJCektVbQWOFNldDoTEwfPgy'; 
    if ($toRadix < $MIN_RADIX || $toRadix > $MAX_RADIX) { 
        $toRadix = 2; 
    } 
	 if ($toRadix == 10) { 
        return $dec; 
    } 
    // -Long.MIN_VALUE 转换为 2 进制时长度为65 
    $buf = array(); 
    $charPos = 64; 
    $isNegative = $dec < 0; //(bccomp($dec, 0) < 0); 
    if (!$isNegative) { 
        $dec = -$dec; // bcsub(0, $dec); 
    }

    while (bccomp($dec, -$toRadix) <= 0) { 
        $buf[$charPos--] = $num62[-bcmod($dec, $toRadix)]; 
        $dec = bcdiv($dec, $toRadix); 
    } 
    $buf[$charPos] = $num62[-$dec]; 
    if ($isNegative) { 
        $buf[--$charPos] = '-'; 
    } 
    $_any = ''; 
    for ($i = $charPos; $i < 65; $i++) { 
        $_any .= $buf[$i]; 
	}	
    return $_any.$inf; 
}