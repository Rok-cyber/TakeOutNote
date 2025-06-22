<?
/******************************************************************
* =======================================================
* 클래스: miniRSS Reader
*
* 제 작: gubok kim (email : previl@previl.net,  homepage : http://dev.previl.net)
*
* 제작일: 2005.12.12 
*
* 해당 RSS페이지를 불러 파싱 후 간략 정보를 불러옵니다.
* iconv 가 설치되지 않은 곳에서는 한글이 제대로 나오지 않습니다.
* 사용방법은 minirss.php 파일을 참고 하시기 바랍니다.
*
* =======================================================
******************************************************************/



// require나 include시 중복선언 방지를 위한 부분 
if( !$__PREVIL_RSS__ )   
{ 
  $__PREVIL_RSS__ = 1; 

class classMRss 
{ 
       var $Url;	//Rss 주소  
	   var $ArticleNum;		//가져올 글 수
	   var $Title;		//타이틀
	   var $Link;		//링크주소
	   var $Items = Array();	//글정보	   	   
	   var $DecodeType;

/*
####################################################################
     ::: 생성자 :::          
####################################################################
*/
	function classMRss($url,$cnt='',$decode='euc-kr') {
		
		if(!$url) $this->errorMsg("Rss채널 주소를 입력 하시기 바람니다.");

		if(!eregi("http://",$url)) $url="http://".$url;

		$this->Url = $url;	
		$this->ArticleNum = $cnt;
		$this->DecodeType = $decode;	
	}



/*
####################################################################
     ::: RSS 채널 재정의 :::          
####################################################################
*/
	function setUrl($url,$cnt='') {
		
		if(!$url) $this->errorMsg("Rss채널 주소를 입력 하시기 바람니다.");

		if(!eregi("http://",$url)) $url="http://".$url;

		$this->Url = $url;	
		$this->ArticleNum = $cnt;	
	
	}



/*
####################################################################
     ::: 파싱 함수 :::          
####################################################################
*/

	function parse() {
		
		$buffer = getUrlPage($this->Url);	

		if(!$buffer) {
			echo "<table height=96% width=100% valign=middle align=center bgcolor='#EFEFEF'><tr><td><font style='font-family:돋움,돋움체; color:#2579CF;font-size:8pt;letter-spacing:-1;text-decoration:non'>해당서버의 접속이 원활하지 않거나, 잘못된 주소입니다.<br><br><br>다시한번 확인 해 보시기 바랍니다.</font></td></tr></table>"; 
			exit;
		}
		
		$pos_offset = 0;
        $pos=strpos($buffer, "?>", $pos_offset);
        $var = substr($buffer, 0, $pos);	
		preg_match('/encoding=\"([^"]*)\"/',$var, $matches);
		if(!$Encode = $matches[1]) {
			preg_match('/encoding=\'([^"]*)\'/',$var, $matches);
            $Encode = $matches[1];
        }        
        	
		$pos=strpos($buffer, "<channel", $pos_offset)+9;
		$endpos = strpos($buffer, "<item", $pos); 
		$var = substr($buffer, $pos, $endpos-$pos);			
		
		if($Encode=='euc-kr' || $Encode =='ks_c_5601-1987' || $Encode==$this->DecodeType) {
			$this->Title = $this->getData('title',$var);
			$this->Link = $this->getData('link',$var);			
		} else {
			$this->Title = iconv($Encode, $this->DecodeType,$this->getData('title',$var));
			$this->Link = iconv($Encode, $this->DecodeType,$this->getData('link',$var));			
        } 
		
		$cnt=0;

        $pos_offset = $endpos;
		while(is_long($pos=strpos($buffer, "<item", $pos_offset))) { 
            $pos=strpos($buffer, "<title", $pos);
			$endpos = strpos($buffer, "</item>", $pos); 
			$var = substr($buffer, $pos, $endpos-$pos);						
			
			if($Encode=='euc-kr' || $Encode =='ks_c_5601-1987' || $Encode==$this->DecodeType) $this->Items[] = $this->getArticle($var);
			else $this->Items[] =  iconv($Encode, $this->DecodeType,$this->getArticle($var)); 			
			$cnt++;
			$pos_offset = $endpos;
            if($this->ArticleNum==$cnt && $this->ArticleNum) break;
		}

	}



/*
####################################################################
     ::: Article 뽑아오기 :::          
####################################################################
*/

	function getArticle($var) {

		if(!$var) return false;

		$title = $this->getData('title',$var);
		$description = $this->getData('description',$var);
		$link = $this->getData('link',$var);
		
		return $title."|".$description."|".$link;
	}



/*
####################################################################
     ::: data 뽑아오기 :::          
####################################################################
*/

	function getData($pattern,$var) {

		if(!$var) return false;
		
        $pos=strpos($var, "<$pattern", $pos_offset);
        $endpos = strpos($var, "</$pattern>", $pos)+strlen("</$pattern>");
		
		$var = substr($var, $pos, $endpos-$pos);	

		$var = str_replace("\"","|'|",$var);
		$var = str_replace("<![CDATA[","",$var);
		$var = str_replace("]]>","",$var);

 		if(substr($var,6,1)==">"){
			$pattern = '/<'.$pattern.'>([^"]*)<\/'.$pattern.'>/';
			preg_match($pattern, $var, $matches);
			$matches = $matches[1];
	    } else {
			$pattern = '/<'.$pattern.'([^"]*)>([^"]*)<\/'.$pattern.'>/';
			preg_match($pattern, $var, $matches);
			$matches = $matches[2];
        }
		$matches = str_replace("|'|","\"",$matches);
		
		return $matches;
	}




/*
####################################################################
     ::: Item 리턴 :::          
####################################################################
*/

	function Items() {

		return $this->Items;
		
	}


/*

####################################################################
     ::: Title 리턴 :::          
####################################################################
*/

	function Title() {

		return $this->Title;

	}


/*
####################################################################
     ::: Link 리턴 :::          
####################################################################
*/

	function Link() {

		return $this->Link;

	}




/*
####################################################################
     ::: 에러 메세지 :::          
     에러 메세지 출력후 종료
####################################################################
*/

    function errorMsg($str) {
              
        	 echo "<style type='text/css'>
					  <!-- 
						font {font-family:굴림; font-size: 9.3pt;}
						font.1 {font-weight:bold;}
						.submit {background-color:rgb(232,232,232); color:#1E1F20; border-width:1; border-color:rgb(130,132,131); border-style:solid; font-family:굴림,Verdana; font-size:9pt; height=20px}</style>
						-->
						</style>";

	          echo ("<br><br><br><br><br>
					  <table width='400' border='0' cellspacing='0' cellpadding='0' align=center style='border-width:2; border-color:gray; border-style:solid;'> 
					  <tr><td width ='400' height = '25' bgcolor=#A8A9A8 align='center'>
					  <font class=1> ERROR </font>
					  </td></tr>
					  <tr><td width ='400' height = '100' align=center valign=middle> 
					  <font>$str</font>
					  <td><tr></table><br>
					  <center><input class=submit type=button value=' Move Back ' onclick=history.back()>
					  ");                
			   exit;        //종료


     }


/*
####################################################################
     ::: 변수삭제 :::          
####################################################################
*/

	function close() {
		
	    unset($this->Url);
	    unset($this->ArticleNum);
		unset($this->Title);
		unset($this->DecodeType);
	    
		$keys = array_keys($this->Items);

		foreach($keys as $key) {
			unset($this->Items[$key]);
		
		}
	}



}  //end of class


}


/*
###############################################
     ::: file() 기능함수 :::          
    사용방법 : getURLPage("사이트주소");
    ex) $test =  getURLPage("http://previl.net");
###############################################
*/

function getURLPage($url) { 

	if(!$tmp = parse_url($url)) return 0; 
	if($tmp['scheme'] != "http") return 0; 
	$host = $tmp['host']; 
	$port = ($tmp['port']) ? $tmp['port'] : 80; 
	$path = ($tmp['path']) ? $tmp['path'] : "/"; 
	$para = ($tmp['query']) ? "?".$tmp['query'] : ""; 

	$fp = @fsockopen($host, $port, &$errno, &$errstr, 10); 
	if(!$fp) return 0;

	$header = "GET ".$path.$para." HTTP/1.0\r\n"; 	
	$header .= "Host: ".$host."\r\n"; 
	$header .= "User-agent: Mozilla\r\n"; 
	$header .= "\r\n"; 

	fputs($fp,$header); 
    
	$ret='';
	while(!feof($fp)) { 
		$ctr=fgets($fp, 1024); 		
		if ($ok) $ret .= $ctr; 
		if (strstr($ctr, "HTTP/1.1 404") && !$ok) break; 
		if (strstr($ctr, "Content-Type:")) $ok=1; 
	} 

	fclose($fp); 

	return $ret ; 

} 
?> 
