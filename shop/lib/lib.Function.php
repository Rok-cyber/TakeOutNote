<?

/******************************************************************************
 * previl library 
 *
 * 마지막 수정일자 : 2003. 02. 8
 * 이 파일내의 모든 함수는 원하시는대로 사용하셔도 됩니다.
 *
 * by previl (previl@previl.net)
 *
 ******************************************************************************/

// require나 include시 중복선언 방지를 위한 부분 

if(!isset($__PREVIL_LIB__))   
{ 
  $__PREVIL_LIB__ = 1; 

/*
##############################################
    ::: 에러 표시 :::          
    사용방법 : Error('메세지'); 
##############################################
*/

function Error($str){
header("Content-Type: text/html; charset=utf-8");
echo "<style type='text/css'>
<!-- 
.eng {font-family:verdana; font-size:11px; color:#000;letter-spacing:0px;}
.submit {background-color:#dadada; color:#1E1F20; border:1px solid #999; font-family:verdana; font-size:11px; height:24px}
-->
</style>";

echo ("<br><br><br><br><br>
      <table width='400' border='0' cellspacing='0' cellpadding='0' align=center style='border-width:2; border-color:gray; border-style:solid;'> 
      <tr><td width ='400' height = '25' bgcolor=#A8A9A8 align='center'>
	  <font class=eng> ERROR </font>
	  </td></tr>
	  <tr><td width ='400' height = '100' align='center' valign='middle' bgcolor='#ffffff' style='padding:10px;'> 
      <font class=eng>{$str}</font>
	  <td><tr></table><br>
 	  <center><input class=submit type=button value=' Move Back ' onclick='history.back();'>
	  "); 
exit;
}


/*
##############################################
    ::: 이미지 사이즈 조절 :::          
    사용방법 : imgSizeCh('이미지 경로','이미지파일','고정사이즈','세로 맥스사이즈'); 
##############################################
*/

function imgSizeCh($dir,$file,$size_factor="",$y_size="",$x_size="",$title=""){
    if(getExtension($dir.urlencode($file)) == 'swf') $fla=1;
   	if($title) $title = "alt='{$title}' title='{$title}'";
	else $title = "alt=''";
	if($size_factor==0 && !$y_size && !$x_size) {
		if($fla==1) {
			$size=@GetImageSize($dir.$file);
			return "
				<script  type='text/javascript'> 
					setem = new setEmbed(); 
					setem.init('flash','".$dir.urlencode($file)."','{$size[0]}','{$size[1]}');
					setem.parameter('wmode','transparent');
					setem.show(); 
				</script>
			";
		}
		else return "<img src='$dir".urlencode($file)."' border='0' {$title} />"; 
    }
	if(!$size=@GetImageSize($dir.$file)) return false; 
    if($size[0] == 0 ) $size[0]=1; 
    if($size[1] == 0 ) $size[1]=1; 
    
	if($x_size && $y_size) {
		$size[0] = $x_size;
		$size[1] = $y_size;
		$per = 1;
	}
	else if($y_size) {
		if($size[1] > $y_size) $per = $y_size/$size[1];
		else $per=1;
    } 
	else if($x_size) {
		if($size[0] > $x_size) $per = $x_size/$size[0];
		else $per=1;
	}
	else {
		if($size[0] > $size_factor || $size[1] > $size_factor){
			if($size[0]>$size[1]) { $per=$size_factor / $size[0]; }  
			else { $per=$size_factor / $size[1]; } 
		} 
		else  $per=1;    
    }
	$x_size=intVal($size[0]*$per); 
    $y_size=intVal($size[1]*$per); 

	if($fla==1) { 
		return "
			<script  type='text/javascript'> 
				setem = new setEmbed(); 
				setem.init('flash','".$dir.urlencode($file)."','{$x_size}','{$y_size}');				
				setem.show(); 
			</script>
		";
	}
	else return "<img src='".$dir.urlencode($file)."' width='{$x_size}' height='{$y_size}' border='0' {$title} />"; 	
}

function getImgSize($dir,$file,$size_factor="",$y_size=""){
	if($size=@GetImageSize($dir.$file)) {
		if($size[0] > $size_factor) $per=$size_factor / $size[0];
		else  $per = 1;        
		
		$rsize[0] = intval($size[0]*$per); 
		if($y_size) $rsize[1] = $y_size;
		else $rsize[1] = intval($size[1]*$per); 
	}
	return $rsize;
}

/*
##############################################
     ::: 한글 자르기 :::          
    사용방법 : hanCut('문자열','자를길이'); 
    ex) $str =  hanCut('$str','40');
##############################################
*/
/*
function hanCut ($str, $cut, $fix='...') {
    if (!$str || strlen($str)<=(int)$cut*2.3) return $str;
    $han = $eng=0;	  
	for($i=0;$i<$cut*2;$i++) {
	    if(ord($str[$i])>127) $han++;     		
		else $eng++;
    }	
	$cut = $han+$eng+(int)$eng*0.23;
	if (strlen($str)<=$cut) return $str;
    return preg_replace("/(([\x80-\xff].)*)[\x80-\xff]?$/", "\\1", substr($str,0,$cut)).$fix;
}
*/

function hanCut($str, $len, $tail='...',$checkmb=false) { 
  preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match); 
  $m    = $match[0]; 
  $slen = strlen($str);  // length of source string 
  $tlen = strlen($tail); // length of tail string 
  $mlen = count($m);    // length of matched characters 

  if ($slen <= $len) return $str; 
  if (!$checkmb && $mlen <= $len) return $str; 
  
  $ret  = array(); 
  $count = 0; 
  
  for ($i=0; $i < $len; $i++) { 
    $count += ($checkmb && strlen($m[$i]) > 1)?2:1; 
    if ($count + $tlen > $len) break; 
    $ret[] = $m[$i]; 
  } 
  return join('', $ret).$tail; 
} 



/*
###############################################
     ::: 랜덤수 구하기 :::          
    사용방법 : getCode('자리수'); 
    ex) $rand = getCode('5') ;
###############################################
*/

function getCode($len) {
    $SID = md5(uniqid(rand()));
    $code = substr($SID, 0, $len);
    return $code;
}


/*
###############################################
     ::: alert 창 함수 :::          
    사용방법 : alert('메세지','이동url'); 
    ex) alert('오류~','back');
###############################################
*/

function alert($msg,$location,$url="") {
    echo "<script>
    alert('\\n $msg \\n');";
    if($location=="back") { echo"history.back();";}
	else if($location=="close") { echo"self.close();";}
	else if($location=="close2") { echo"opener.location.href='$url';self.close();";}
	else if($location=="close3") { echo"opener.location.reload();self.close();";}
	else if($location=="close4") { echo"parent.location.reload();self.close();";}
	else if($location=="close5") { echo"parent.pLightBox.hide();";}
    else { echo "location.href='$location'"; }
    echo "</script>";
	exit;
}


/*
###############################################
     ::: 사이트이동 함수 :::          
    사용방법 : movePage('이동url'); 
    ex) movePage('http://previl.net'); 
###############################################
*/

function movePage($url) {
	 echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	 exit;
}

function movePage2($url) {
	 echo"<script>parent.location.href='{$url}';</script>";
	 exit;
}


/*
###############################################
     ::: 공백문자열 검사함수 :::          
    사용방법 : chrtrim('문자l'); 
    ex) chrtrim($chr); 
###############################################
*/

function chrtrim($chr) { 
	$chr = trim($chr);
	$chr_length = strlen($chr);
	if($chr_length <= 0) alert('올바른문자를 입력해주세요!','back');
}


/*
###############################################
     ::: 속도계산 함수 :::          
    사용방법 : getMicotime('처음시간','다음시간'); 
###############################################
*/

function getMicrotime($old, $new) {
    $old = explode(" ", $old);  //주어진 문자열을 나눔 (sec, msec으로 나누어짐)
    $new = explode(" ", $new);
    $time[msec] = $new[0] - $old[0];
    $time[sec]  = $new[1] - $old[1];
    if($time[msec] < 0) {
      $time[msec] = 1.0 + $time[msec];
      $time[sec]--;
    }

    $time = sprintf("%.3f", $time[sec] + $time[msec]);

    return $time;
}


/*
###############################################
     ::: 단위절사  :::          
    사용방법 : numberLimit('금액','절사단위'); 
    ex) $price = numberLimit('1001','1'); -> 1000;
###############################################
*/

function numberLimit($num,$lmt) { 
    $num = round($num/(10*$lmt));
	$num = $num*(10*$lmt);
    return $num;
} 


/*
###############################################
     ::: 확장자 빼오기 (소문자로 치환) :::          
    사용방법 : getExtension('파일이름'); 
    ex) $last_name = getExtension('test.php');
###############################################
*/

function getExtension($filename) { 
    $filename = trim($filename); 
    $right = strrchr($filename, "."); 
    return strtolower(substr($right,1)); 
} 


/*
###############################################
     ::: 문자열에서 url을 찾아내어 링크를 시킨다.(http) :::          
    사용방법 : makeLink("문자열"); 
###############################################
*/

function makeLink($str) { 
	// URL 치환
	$homepage_pattern = "/([^\"\=\>])(mms|http|HTTP|ftp|FTP|telnet|TELNET)\:\/\/(.[^ \n\<\"]+)/";
	$str = preg_replace($homepage_pattern,"\\1<a href=\\2://\\3 target=_blank>\\2://\\3</a>", " ".$str);

	// 메일 치환
	$email_pattern = "/([ \n]+)([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)/";
	$str = preg_replace($email_pattern,"\\1<a href=mailto:\\2@\\3>\\2@\\3</a>", " ".$str);

	return $str;
}


/*
###############################################
     ::: 파일사이즈 변환 (kb,mb..) :::          
    사용방법 : getFilesize('파일사이즈'); 
    ex) $filesize = getFilesize('$size');
###############################################
*/

function getFilesize($filename) {
        if(!file_exists($filename)) return "0 Byte";
		$size = filesize($filename);		
		if(!$size) return "0 Byte";
		if($size<1024) { 
			return ($size."Byte");
		} elseif($size >1024 && $size< 1024 *1024)  {
			return sprintf("%0.1fKB",$size / 1024);
		}
		else return sprintf("%0.2fMB",$size / (1024*1024));
}

/*
###############################################
     ::: 파일 저장함수 :::          
    사용방법 : writeFile('파일명');   //파일생성
	사용방법 : writeFile('파일명','데이타');   //파일 저장
	ex) writeFile('test.dat');
###############################################
*/

function writeFile($filename,$data="0") {	       
	 $fp=fopen($filename,'w');
	 fwrite($fp,$data);
	 fclose($fp);
}


/*
###############################################
     ::: 파일 읽기함수 :::          
    사용방법 : readFiles('파일명');  
	ex) $buffer = readFiles('test.dat');
###############################################
*/

function readFiles($filename) {	       
	 if(!file_exists($filename)) return '';
	 $fp=fopen($filename,'r');
	 $str = fread($fp, filesize($filename));
	 fclose($fp);
     return $str;
}



/*
###############################################
     ::: 파일 삭제함수 :::          
    사용방법 : delFile('파일명');  
	ex) delFile('test.dat');
###############################################
*/

function delFile($filename) {
		@chmod($filename,0777);
		@unlink($filename);
		if(@file_exists($filename)) {
			@chmod($filename,0775);
			@unlink($filename);
		}
}

/*
###############################################
     ::: 파일 중복체크  함수 :::          
    사용방법 : proc_file_dup($savedir, $filename);  
    ex) file_name = proc_file_dup('./files', 'test.gif');  
###############################################
*/

function proc_file_dup($savedir, $filename) {
  global $incNum;
    $filename = preg_replace("/ /i", "_", $filename);
    // 먼저 들어온 파일명의 앞부분과 확장자 부분을 분리한다. 
    $lenStr= strlen($filename);                         // 파일 길이 
    $extension = strrchr($filename, ".");           // 파일의 확장자.(. 포함) 
    $hyPos = strrpos($filename, "_");              // 맨 마지막 _의 위치 
	$dotPos = strrpos($filename, ".");              // 맨 마지막 도트의 위치 
    $file = substr($filename, 0, $dotPos);      // 확장자와 점을 뺀 파일명 
  	if($hyPos AND $incNum) {
		$ea = $dotPos - $hyPos;
		$incNum = substr($filename, $hyPos+1,$ea-1);
		$file = substr($filename,0,$hyPos);	
	} else 	$incNum = 0;                                            // 중복 파일명이 계속 들어올 때마다 하나씩 확장되는 숫자 
    
    $incNum++; 

	$filename = $file . "_" . $incNum . "$extension"; 
        
    if (file_exists("$savedir/$filename"))      // recursive call 
    { 
        $filename = proc_file_dup($savedir, $filename); 
        return $filename; 
    } 
    else 
        return $filename; 
} 



/*
###############################################
     ::: 파일 업로드 함수 :::          
    사용방법 : upFile($userfile,$save_dir,'최대업용량');
    ex) $upfile = upFile($userfile,$save_dir,'20000');
###############################################
*/

function upFile($userfile,$userfile_name,$savedir,$max_size="",$img="",$save_name=""){
   
	// 확장자 검사
    if(!eregi("\.jpg|\.jpeg|\.gif|\.png|\.swf|\.bmp",$userfile_name) && $img) {
		Error("이미지 파일만 등록 하실 수 있습니다.");
    }

	if($img=='true' && !exif_imagetype($userfile)) {
		Error("정상적인 이미지 파일이 아닙니다.");
	}

	if(eregi("\.php|\.inc|\.htm|\.phtm|\.shtm|\.ztx|\.dot|\.cgi|\.pl|\.asp|\.jsp",$userfile_name)) {
		 
		 // 먼저 들어온 파일명의 앞부분과 확장자 부분을 분리한다. 
	 	 $lenStr= strlen($userfile_name);                         // 파일 길이 
		 $dotPos = strrpos($userfile_name, ".");              // 맨 마지막 도트의 위치 
		 $userfile_name = substr($userfile_name, 0, $dotPos).".phps";      // 확장자와 점을 뺀 파일명
		// Error ("Html, PHP 관련파일은 업로드 할 수 없습니다");
	}
    
    // 파일용량제한 
	$file_size = filesize($userfile);
	if(!$file_size){
		 Error("파일 제한용량을 초과 했습니다!");
	}
  
	if(!$save_name) {
		// 파일 중복시 파일병 변경
		if(@is_file("$savedir/$userfile_name"))   {   
			 $filename = $userfile_name;
			 $userfile_name = proc_file_dup($savedir, $filename); 
		}
    } else {
		$userfile_name=$save_name.".".getExtension($userfile_name);
    }

	//파일 업로드
	if(!move_uploaded_file($userfile, $savedir."/".$userfile_name)){
		 Error("파일을 저장한 디렉토리에 복사하는데 실패했습니다.");
	} 
     
    return $userfile_name;

}
	
/*
###############################################
     :::  암호화 :::          
    사용방법 : previlEncode('원문'); 
    ex) $encode_str = previlEncode('previl') ;  한글도 가능
###############################################
*/

function previlEncode($og_string){
    if(!$og_string) return false;
	$len_string = strlen($og_string);
	$rand_str = getCode(21);

	//echo "원문 : $og_string";

	for($h=$h2=0;$h < $len_string;$h++){      //원문과 랜덤문을 썩는다.
		$h_str1 = substr($rand_str,$h2,1);
		$h_str2 = substr($og_string,$h,1);
		$string .= $h_str1.$h_str2;				
		if($h2==20) $h2=0;
		else $h2++;
	}
	$h_str1 = substr($rand_str,-1,1);
	$string .= $h_str1;

	$ba_string = base64_encode($string); // 1차 base64로 암호화
	$str_length=strlen($ba_string);

	$lens=0;
	$cnt=1;
	while($lens < $str_length){      // p re vil 1,2,3,4,5,6,7,8,9 1,2.. 글자식 짤라 배열로저장	  
	  $en_str[] = substr($ba_string,$lens,$cnt);
	  $lens+=$cnt;
	  if($cnt==9) $cnt=1;
	  else $cnt++;
	}
         
	for($k=0;$k<count($en_str);$k++){         // 리버스 시킴
	  $en_str[$k] = strrev($en_str[$k]);
	}

	$k2=2;
	for($k=0;$k<count($en_str);$k++){         // 2차 base64로 암호화
	  $en_str[$k] = base64_encode($en_str[$k]);	 
	  //echo $en_str[$k]."<br>";
	  if($k2==2) $en_str[$k] = str_replace("==","",$en_str[$k]);
	  else if($k2==1) $en_str[$k] = str_replace("=","",$en_str[$k]);
	  
	  if($k2==0) $k2=2;
	  else $k2--;
	}

	$encode_string = join("",$en_str);   //조인

	//echo "<br>암호문 : $encode_str";       

	return $encode_string;

}

/*
###############################################
     :::  복호화 :::          
    사용방법 : previlDecode('암호문'); 
    ex) $decode_str = previlDecode('sdlkglsdgf') ;   
###############################################
*/

function previlDecode($encode_string){
    if(!$encode_string) return false;
	$de_length = strlen($encode_string);
    
	$k_total=0;	
	$k1=2;
	$k2=2;
	
	for($k=0;$de_length > $k_total;$k++){         // 배열로 저장	  
	  $de_str[$k] = substr($encode_string,$k_total,$k1);
	  $k_total = $k_total + $k1;	  
	  if($k2==0) $k1++;
	  else if($k2==2 && ($de_length > $k_total)) $de_str[$k] .= "==";
	  else if($k2==1 && ($de_length > $k_total)) $de_str[$k] .= "=";	  
	  //echo $de_str[$k]."<br>";
	  $de_str[$k] =  base64_decode($de_str[$k]);  //복호화
	  if($k1==13) $k1=2;
	  else $k1++;	  
      if($k2==0) $k2=2;
	  else $k2--;
	}

	for($i=0;$i<$k;$i++){         // 리버스 시킴
	  $de_str[$i] = strrev($de_str[$i]);
	}

	$decode_str = join("",$de_str);   //조인
	$decode_str = base64_decode($decode_str);   // 복호화

	$len_string2 = strlen($decode_str);

	for($h=1;$h < $len_string2;$h+=2){
		$h_str3 = substr($decode_str,$h,1);		
		$decode_string .= $h_str3;
	}

	//echo "<br> 복호문 : $decode_string";
    return $decode_string;
}



/*
###############################################
     :::  파일다운로드 함수 :::          
    사용방법 : fileDown('풀경로','파일이름')
    ex) fileDown($filename,$dfilename);   
###############################################
*/

function fileDown($filename,$dfilename) {

	if(eregi("(MSIE 5.0|MSIE 5.1|MSIE 5.5|MSIE 6.0)", $HTTP_USER_AGENT))
	{ 
	  if(strstr($HTTP_USER_AGENT, "MSIE 5.5")) 
	  { 
		header("Content-Type: doesn/matter"); 
		header("Content-disposition: filename=$dfilename"); 
		header("Content-Transfer-Encoding: binary"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
	  } 

	  if(strstr($HTTP_USER_AGENT, "MSIE 5.0")) 
	  { 
		Header("Content-type: file/unknown"); 
		header("Content-Disposition: attachment; filename=$dfilename"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
	  } 

	  if(strstr($HTTP_USER_AGENT, "MSIE 5.1")) 
	  { 
		Header("Content-type: file/unknown"); 
		header("Content-Disposition: attachment; filename=$dfilename"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
	  } 
	  
	  if(strstr($HTTP_USER_AGENT, "MSIE 6.0"))
	  {
		Header("Content-type: application/x-msdownload"); 
		Header("Content-Length: ".(string)(filesize("$filename")));
		Header("Content-Disposition: attachment; filename=$dfilename");   
		Header("Content-Transfer-Encoding: binary");   
		Header("Pragma: no-cache");   
		Header("Expires: 0");   
	  }
	} else { 
	  Header("Content-type: file/unknown");     
	  Header("Content-Length: ".(string)(filesize("$filename"))); 
	  Header("Content-Disposition: attachment; filename=$dfilename"); 
	  Header("Pragma: no-cache"); 
	  Header("Expires: 0"); 
	} 

	if (is_file("$filename")) { 
	  
	  $fp = fopen("$filename", "rb");
      if(!fpassthru($fp)) fclose($fp);

	} else { 
	  alert("파일을 찾을 수 없습니다.",'back'); 
	} 
}


/*
###############################################
     :::  메일체크 함수 :::          
    사용방법 : mailCheck($email)
    참이면 true 거짓이면 false 리턴
###############################################
*/

function mailCheck($str) {

		if(!eregi("([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)", $str) ) return false;
		list($user, $host) = explode("@", $str);
		if(!function_exists('checkdnsrr')) return true;
		if (checkdnsrr($host, "MX") or checkdnsrr($host, "A")) return true;
		else $return = false;
	
}


/*
###############################################
     ::: 브라우저 체크함수 :::          
    사용방법 : ckBrowser();  
	ex) $browser = ckBrowser();
###############################################
*/

function ckBrowser() {
	
	if(!$agent=getenv("HTTP_USER_AGENT")) return 'unknown';
    
	if(eregi( 'MSIE', $agent)) { 
		preg_match("/MSIE ([0-9][.][0-9]{0,2})/i",$agent,$match);
		return "MS-Explorer {$match[1]}"; 
	} 
	if(eregi( 'Netscape', $agent)) {
		$temp=substr($agent,strrpos($agent,'Netscape'));
		$temp = preg_replace("/[^0-9+.]/","",$temp);
		return "Netscape {$temp}"; 
	} 	
	if(eregi( 'Opera', $agent)) { 
		$temp=substr($agent,strrpos($agent,'Opera'));
		$temp = preg_replace("/[^0-9+.]/","",$temp);
		return "Opera {$temp}"; 
	}
	if(eregi( 'Firefox', $agent)) { 
		$temp=substr($agent,strrpos($agent,'Firefox'));
		$temp = preg_replace("/[^0-9+.]/","",$temp);
		return "Firefox {$temp}"; 
	}
	if(eregi( 'Mozilla', $agent)) { 
		if(eregi('rv',$agent)){
			preg_match_all("/rv:(.*)\)/i",$agent,$match,PREG_SET_ORDER);
			return "Mozilla {$match[0][1]}"; 
		}
	}
    if (eregi('Safari', $agent)) return "Safari";
	if (eregi('Lynx', $agent)) return "Lynx";
	if (eregi('LibWWW', $agent)) return "LibWWW";
	if (eregi('Konqueror', $agent)) return "Konqueror";
	if (eregi('Internet Ninja', $agent)) return "Internet Ninja";
	if (eregi('Download Ninja', $agent)) return "Download Ninja";
	if (eregi('WebCapture', $agent)) return "WebCapture";
	if (eregi('LTH', $agent)) return "LTH";
	if (eregi('Gecko', $agent)) return "Gecko";
	if (eregi('wget', $agent)) return "Wget command";

	if (eregi('PSP', $agent)) return "PlayStation Portable";
	if (eregi('Symbian', $agent)) return "Symbian PDA";
	if (eregi('Nokia', $agent)) return "Nokia PDA";
	if (eregi('LGT', $agent)) return "LG Mobile";
	if (eregi('iPhone', $agent)) return "iPhone Mobile";
	if (eregi('htc', $agent)) return "HTC Mobile";
	if (eregi('samsung', $agent)) return "SAMSUNG Mobile";
	if (eregi('mobile', $agent)) return "ETC Mobile";

	if (eregi('Googlebot', $agent)) return "GoogleBot";
	if (eregi('OmniExplorer', $agent)) return "OmniExplorerBot";
	if (eregi('MJ12bot', $agent)) return "majestic12Bot";
	if (eregi('ia_archiver', $agent)) return "Alexa(IA Archiver)";
	if (eregi('Yandex', $agent)) return "Yandex bot";
	if (eregi('Inktomi', $agent)) return "Inktomi Slurp";
	if (eregi('Giga', $agent)) return "GigaBot";
	if (eregi('Jeeves', $agent)) return "Jeeves bot";
	if (eregi('Planetwide', $agent)) return "IBM Planetwide bot";
	if (eregi('bot', $agent) || eregi('Crawler', $agent) || eregi('library', $agent)) return "ETC Robot";

	return 'unknown';
}


/*
###############################################
     ::: OS 체크함수 :::          
    사용방법 : ckOs();  
	ex) $os = ckOs();
###############################################
*/

function ckOs() {
    	
	if(!$agent=getenv("HTTP_USER_AGENT")) return 'unknown';
						
    if (eregi('win95', $agent) || eregi('windows 95', $agent)) return "Windows 95";
    if (eregi('Windows 9x', $agent) || eregi('Win 9x 4.90', $agent) || eregi('Windows Me', $agent)) return "Windows ME";
	if (eregi('Win98', $agent) || eregi( 'Windows 98', $agent)) return "Windows 98";	
	if (eregi('Windows NT 5.1', $agent) || eregi('Windows XP', $agent)) return "Windows XP";	
	if (eregi('Windows NT 5.0', $agent) || eregi('Windows 2000', $agent)) return "Windows 2000";    
    if (eregi('windows NT 5.2', $agent) || eregi('Windows 2003', $agent)) return "Windows 2003";
	if (eregi('windows NT 6.0', $agent)) return "Windows Vista";
	if (eregi('windows NT 6.1', $agent)) return "Windows 7";
	if (eregi('Winnt', $agent) || eregi('Windows NT', $agent)) return "Windows NT";
	if (eregi('windows', $agent)) return "ETC Windows";
    if (eregi('Mac', $agent )) {
		if(eregi('PowerPC' , $agent)) return "Mac PowerPC";
		if(eregi('Macintosh' , $agent)) return "Mac Macintosh";
		if(eregi('PowerPC' , $agent)) return "Mac OS X";
		if(eregi('iPhone' , $agent)) return "iPhone OS";
		return "ETC Mac";
	}	
	if (eregi('Os2', $agent)) return "OS2";
	if (eregi('Linux', $agent) || eregi('Wget', $agent)) return "Linux";
	if (eregi('Unix', $agent)) return "Unix";
	if (eregi('Freebsd', $agent)) return "Freebsd";

	if (eregi('PSP', $agent)) return "PlayStation Portable";
	if (eregi('Symbian', $agent)) return "Symbian PDA";
	if (eregi('Nokia', $agent)) return "Nokia PDA";
	if (eregi('LGT', $agent)) return "LG Mobile";
	if (eregi('mobile', $agent)) return "ETC Mobile";

	if (eregi('Googlebot', $agent)) return "GoogleBot";
	if (eregi('OmniExplorer', $agent)) return "OmniExplorerBot";
	if (eregi('MJ12bot', $agent)) return "majestic12Bot";
	if (eregi('ia_archiver', $agent)) return "Alexa(IA Archiver)";
	if (eregi('Yandex', $agent)) return "Yandex bot";
	if (eregi('Inktomi', $agent)) return "Inktomi Slurp";
	if (eregi('Giga', $agent)) return "GigaBot";
	if (eregi('Jeeves', $agent)) return "Jeeves bot";
	if (eregi('Planetwide', $agent)) return "IBM Planetwide bot";
	if (eregi('bot', $agent) || eregi('Crawler', $agent) || eregi('library', $agent)) return "ETC Robot";
	
	return 'unknown';
}


/*
###############################################
     :::  html 태그 제거 함수 :::          
    사용방법 : html2txt($document)
    html태그를 제거하고 택스트형태로 리턴
###############################################
*/

function html2txt($document){
	$search =	array('@<script[^>]*?>.*?</script>@si',	// Strip out javascript
					'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
					'@<[\/\!]*?[^<>]*?>@si',			// Strip out HTML tags
					'@<![\s\S]*?--[ \t\n\r]*>@'			// Strip multi-line comments including CDATA
				);
	$text = preg_replace($search, '', $document);
	return $text;
}

/*
###############################################
     ::: IE hack 방지 함수 :::          
    사용방법 : ieHackCheck($document)
    IE 해킹에 이용될 수있는 js 제거
###############################################
*/

function ieHackCheck($html){
	$html = @preg_replace_callback('/(href|src)[\t\s\r\n]*=[\t\s\r\n]*((["\']).*?(?<!\x5c)\3)/is', 'iehack_escape', $html); 
	$html = preg_replace('/((href|src)=.?)(j(ava)?)?script/i', '\1javaworker', $html); 

	if(!function_exists('iehack_escape')){
		function iehack_escape($matches){ 
			return $matches[1].'='.preg_replace('/(&#(x0*[da]|0*1[03]);|[\r\n])/i', '', $matches[2]); 
		}
	}
	return $html;
}



/*
###############################################
     ::: 트랙백 핑을 보내는 함수  :::          
    
    
###############################################
*/

function sendTb($t_url,$url,$title,$blog_name,$excerpt) {
	global $tb_error_str;

	function Errors() {
		$tb_error_str = "올바른 트랙백 URL이 아닙니다.";
		return false;
	}
		
	if(!$tmp = parse_url($t_url)) return false;
	
	$host = $tmp['host']; 
	$port = ($tmp['port']) ? $tmp['port'] : 80; 
	$path = ($tmp['path']) ? $tmp['path'] : "/"; 

	$title	= strip_tags($title);
	$excerpt= strip_tags($excerpt);
	
	$para = "url=".urlencode($url)."&title=".urlencode($title)."&blog_name=".urlencode($blog_name)."&excerpt=".urlencode($excerpt);
	
	$fp = @fsockopen($host, $port, &$errno, &$errstr, 10); 
	if(!$fp) return false;

	$header = "POST ".$path." HTTP/1.0\r\n"; 	
	$header .= "Host: ".$host."\r\n"; 
	$header .= "User-agent: PHP/HTTP_CLASS\r\n"; 
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-length: ".strlen($para)."\r\n";
	$header .= "Connection: Close\r\n";
	$header .= "\r\n"; 
	$header .= $para."\r\n";

	fputs($fp,$header); 

	while(!feof($fp)) $response .= fgets($fp,128);
	fclose($fp);
	if(!strstr($response,"<response>")) return false;

	$response = strchr($response,"<?");
	$response = substr($response,0,strpos($response,"</response>"));

	if(strstr($response,"<error>0</error>")) return true;
	else {
		//$tb_error_str = strchr($response,"<message>");
		//$tb_error_str = substr($tb_error_str,0,strpos($tb_error_str,"</message>"));
		//$tb_error_str = str_replace("<message>","",$tb_error_str);
		//$tb_error_str = "트랙백 전송중 오류가 발생했습니다: $tb_error_str";
		return false;
	}
}


/*
###############################################
     ::: Tree  :::          
    
    
###############################################
*/

function delTree($path) {
    if (is_dir($path)) {
		if (version_compare(PHP_VERSION, '5.0.0') < 0) {
			$entries = array();
			if ($handle = opendir($path)) {
				while (false !== ($file = readdir($handle))) $entries[] = $file;

				closedir($handle);
			}
			} else {
			$entries = scandir($path);
			if ($entries === false) $entries = array(); // just in case scandir fail...
		}

		foreach ($entries as $entry) {
			if ($entry != '.' && $entry != '..') {
				deltree($path.'/'.$entry);
			}
		}

		return rmdir($path);
	} 
	else return @unlink($path);
}


function copyTree( $source, $target ) {
    if ( is_dir( $source ) ) {
        @mkdir( $target );
            
        $d = dir( $source );
            
        while ( FALSE !== ( $entry = $d->read() ) ) {
            if ( $entry == '.' || $entry == '..' )continue;
                
            $Entry = $source . '/' . $entry;            
            if ( is_dir( $Entry ) ) {
                copyTree( $Entry, $target . '/' . $entry );
                continue;
            }
            @copy( $Entry, $target . '/' . $entry );
        }
            
        $d->close();
    }
	else @copy( $source, $target );        
}



/*
###############################################
     ::: 내장 함수 체크   :::         
###############################################
*/

if ( ! function_exists( 'exif_imagetype' ) ) {
    function exif_imagetype ( $filename ) {
        if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
            return $type;
        }
    return false;
    }
} 


/*
###############################################
     ::: 우편번호 업데이트   :::         
###############################################
*/

function getZip($size) {
	$url = "http://itsmall.kr/update/zipcode.php?size={$size}&un=1&refer=".$_SERVER['HTTP_HOST'];
	
	$url_parsed = parse_url($url);
    $host = $url_parsed["host"];
    $port = $url_parsed["port"];
    if($port==0) $port = 80;
    $path = $url_parsed["path"];

    if(empty($path)) $path = "/";
    if(empty($host)) return false;

    if($url_parsed["query"] != "") $path .= "?".$url_parsed["query"];
    $out = "GET ".$path." HTTP/1.0\r\nHost: ".$host."\r\n\r\n";
	
    $fp = fsockopen($host, $port, $errno, $errstr, 30);
    usleep(50);
    if($fp) {
        socket_set_timeout($fp, 30);
        fwrite($fp, $out);
        $body = false;
        while(!feof($fp)) {
            $buffer = fgets($fp, 128);
            if($body) $content .= $buffer;
            if($buffer=="\r\n")    $body = true;
        }
        fclose($fp);
    }
	else {
        return false;
    }
    return $content;
}

function socketPost($url) { 

    if(!$tmp = parse_url($url)) return 0; 
    if($tmp['scheme'] != "http" && $tmp['scheme'] != "https") return 0; 
	
	$host = $tmp['host']; 	
	if($tmp['scheme'] == "https") $host2 = "ssl://{$host}";
	else $host2 = $host;

	$port = ($tmp['port']) ? $tmp['port'] : 80; 
    $path = ($tmp['path']) ? $tmp['path'] : "/"; 
    $para = ($tmp['query']) ? "?".$tmp['query'] : ""; 

    $fp = @fsockopen($host2, $port, &$errno, &$errstr, 10); 
    if(!$fp) return 0;

    $header = "GET ".$path.$para." HTTP/1.0\r\n"; 
    $header .= "Host: ".$host."\r\n"; 
	$header .= "Referer:".$_SERVER["HTTP_HOST"]."\r\n";
    $header .= "User-agent: PHP/HTTP_CLASS\r\n"; 
    $header .= "\r\n"; 

    fputs($fp,$header); 
    $ret = Array(); 
    while(!feof($fp)) { 
        $ctr=fgets($fp, 1024); 		
		if ($ok) $ret[]= $ctr; 	
        if (strstr($ctr, "HTTP/1.1 404") && !$ok) break; 
        if (strstr($ctr, "Content-Type: text/html")) $ok=1; 
    } 
	fclose($fp); 
    return $ret ;
}


function getURLimg($url) {

    $url_parsed = parse_url($url);
    $host = $url_parsed["host"];
    $port = $url_parsed["port"];
    if($port==0) $port = 80;
    $path = $url_parsed["path"];

    if(empty($path)) $path = "/";
    if(empty($host)) return false;

    if($url_parsed["query"] != "") $path .= "?".$url_parsed["query"];
    $out = "GET ".$path." HTTP/1.0\r\nHost: ".$host."\r\n\r\n";
	
    $fp = fsockopen($host, $port, $errno, $errstr, 30);
    usleep(50);
    if($fp) {
        socket_set_timeout($fp, 30);
        fwrite($fp, $out);
        $body = false;
        while(!feof($fp)) {
            $buffer = fgets($fp, 128);
            if($body) $content .= $buffer;
            if($buffer=="\r\n")    $body = true;
        }
        fclose($fp);
    }else {
        return false;
    }
    return $content;
}

/*
###############################################
     ::: add_escape_string   :::         
###############################################
*/
function add_escape_string($str){
	global $cook_rand;
	if($str[0]!='') return $str; 
	$str = eregi_replace("union", "u1111{$cook_rand}110;ion", $str);
	$str = eregi_replace(";","1111{$cook_rand}59;",$str); 	
	$str = eregi_replace("=","1111{$cook_rand}61;",$str); 	
	$str = eregi_replace("#","1111{$cook_rand}35;",$str); 
	$str = eregi_replace("1111{$cook_rand}","&#",$str); 
	return $str;
}

function countDown($times){
	$lastTime = $times - time();	
	
	if($lastTime>0) {
		$day = floor($lastTime/86400);
		$lastTime -= $day * 86400;
		$hour = floor($lastTime/3600);
		$lastTime -=  $hour * 3600;
		$min  = floor($lastTime/60);
		$lastTime -=  $min * 60;
		$sec = floor($lastTime); 
		
		$day	= ($day>9) ? $day : '0'.$day;
		$hour	= ($hour>9) ? $hour : '0'.$hour;
		$min	= ($min>9) ? $min : '0'.$min;
		$sec	= ($sec>9) ? $sec : '0'.$sec;

		return "{$day}:{$hour}:{$min}:{$sec}";
	}
	else return "00:00:00:00";
}

} // End of if(!isset($__PREVIL_LIB__)) 
?>