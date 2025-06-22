<?
/******************************************************************
* =======================================================
* 클래스: 쇼핑몰을 위한 템플릿 클래스
*
* 제  작: 베어템플릿 (http://cafe.naver.com/hopegiver.cafe)
*
* 수  정: gubok kim (email : previl@previl.net,  homepage : http://previl.net)
*
* 수정일: 2003.05.24 
*
* =======================================================
******************************************************************/

// require나 include시 중복선언 방지를 위한 부분 

if(!isset($__PREVIL_TEMP__))   
{ 
  $__PREVIL_TEMP__ = 1; 

class classTemplate {

	var $Src;			//템플릿 원본 내용
	var $Var;			//치환될 변수들이 저장
	var $Root;			//템플릿 디렉토리 경로
	var $separator;
	var $pg_string;    // 페이징연동용
	var $lt_string;    // 상품분류위치출력연동용

/*
####################################################################
     ::: 생성자 :::          
####################################################################
*/
	function classTemplate($path=".") {
		$this->Src = array();
		$this->Var = &$GLOBALS;		//치환변수를 글로벌 변수로 초기화함.
		$this->Root = preg_replace("/[\/]*$/", "", $path);	//템플릿 디렉토리 초기경로 보정
		$this->separator = array("{{", "}}");
	}

 
/*
####################################################################
     ::: 템플릿 영역을 정의하는 함수 :::          
####################################################################
*/
	function define($var, $parent="") {
       
		if(!preg_match("/^[a-z0-9_-]+$/i", $parent)) return $this->define_file($var, $parent);
		else return $this->define_area($var, $parent);
		
	}


/*
####################################################################
     ::: 파일을 읽어 템플릿을 정의하는 함수 :::          
####################################################################
*/

	function define_file($var, $filename) {
		global $skin;
		$path = $this->Root."/".$filename;

		if(!is_file($path)) $this->errorMsg("템플릿 파일을 찾을 수 없습니다.<br> 파일이름 : $filename");

		$fp = fopen($path,"r");
		$buffer = fread($fp,filesize($path));
		fclose($fp);
		$this->Src[$var] = $buffer;
		return true;
		
     }


/*
####################################################################
     ::: 내부 다이나믹 영역을 정의하는 함수 :::          
####################################################################
*/

	function define_area($var, $parent) {
		$buffer = $this->Src[$parent];
		$buff = explode("<!-- DYNAMIC @$var@ -->", $buffer);
		if(count($buff) == 3) {
			$this->Src[$var] = $buff[1];
			$this->Src[$parent] = $buff[0].$this->separator[0].$var.$this->separator[1].$buff[2];
			return true;
		} else {
			return false;
		}
	}


/*
####################################################################
     ::: 페이징 연동 페이징 형태 정의하는 함수 :::          
####################################################################
*/

	function define_paging($var,$parent) {
		$buffer = $this->Src[$parent];
		$buff = explode("<!-- DYNAMIC @$var@ -->", $buffer);
		if(count($buff) == 3) {
			$this->Src[$var] = "{{PAGING}}";
			preg_match('/PAGING\(([^"]*)\)/',$buff[1], $matches);
			$this->pg_string = $matches[1];
			$this->Src[$parent] = $buff[0].$this->separator[0].$var.$this->separator[1].$buff[2];
			return true;
		} else {
			return false;
		}
	}

	function getPgstring() {
		return $this->pg_string;
	}

/*
####################################################################
     ::: 분류상품 연동 정의하는 함수 :::          
####################################################################
*/

	function define_catelist($var,$parent) {
		$buffer = $this->Src[$parent];
		$buff = explode("<!-- DYNAMIC @$var@ -->", $buffer);
		if(count($buff) == 3) {
			preg_match('/CATELIST\(([^"]*)\)/',$buff[1], $matches);
			$this->cl_string = $matches[1];
			$this->Src[$parent] = $buff[0].$this->separator[0].$var.$this->separator[1].$buff[2];
			return true;
		} else {
			return false;
		}
	}

	function getClstring() {
		return $this->cl_string;
	}

/*
####################################################################
     ::: 게시판 최근글 연동 정의하는 함수 :::          
####################################################################
*/

	function define_boardlist($var,$parent) {
		$buffer = $this->Src[$parent];
		$buff = explode("<!-- DYNAMIC @$var@ -->", $buffer);
		if(count($buff) == 3) {
			preg_match('/BOARDLIST\(([^"]*)\)/',$buff[1], $matches);
			$this->bl_string = $matches[1];
			$this->Src[$parent] = $buff[0].$this->separator[0].$var.$this->separator[1].$buff[2];
			return true;
		} else {
			return false;
		}
	}

	function getBlstring() {
		return $this->bl_string;
	}


/*
####################################################################
     ::: 상품 분류 위치 출력 형태 정의하는 함수 :::          
####################################################################
*/

	function define_location($var,$parent) {
		$buffer = $this->Src[$parent];
		$buff = explode("<!-- DYNAMIC @$var@ -->", $buffer);
		if(count($buff) == 3) {
			$this->Src[$var] = "{{LOCATION}}";
			preg_match('/LOCATION\(([^"]*)\)/',$buff[1], $matches);
			$this->lt_string = $matches[1];
			$this->Src[$parent] = $buff[0].$this->separator[0].$var.$this->separator[1].$buff[2];
			return true;
		} else {
			return false;
		}
	}

	function getLtstring() {
		return $this->lt_string;
	}


/*
####################################################################
     ::: 내부 다이나믹 영역을 검색하는 재귀 함수 :::          
####################################################################
*/

	function scan_area($parent) {

		$buffer = &$this->Src[$parent];
		$pos_offset = 0;
		while(is_long($pos = strpos($buffer, "<!-- DYNAMIC @", $pos_offset))) { 
			$pos += 14; 
			$endpos = strpos($buffer, "@ -->", $pos); 
			$child = substr($buffer, $pos, $endpos-$pos);
 			if($child=="define_catelist") {
				$this->define_catelist($child,$parent);
			} 
			else if($child=="define_boardlist") {
				$this->define_boardlist($child,$parent);
			}
			else if($child=="define_pg") {
				$this->define_paging($child,$parent);
			}
			else if($child=="define_location") {
				$this->define_location($child,$parent);
			}
			else if($this->define_area($child, $parent)) {
				$pos_offset = $pos + strlen($child) - 14;
				$this->scan_area($child); // 재귀호출				
			} 
			else {
				$pos_offset = $endpos + 5;
			}
		}
	}

/*
####################################################################
     ::: 파싱 함수 :::          
####################################################################
*/

	function parse($var,$ctl="") {  
		if(!$buffer = $this->Src[$var]) return false;
		    $buff1 = explode($this->separator[0], $buffer);
			$arr[] = $buff1[0];
			for($i=1; $i<count($buff1); $i++) {
				$buff2 = explode($this->separator[1], $buff1[$i]);
				if(count($buff2) == 2 && preg_match("/^[a-z0-9_-]+$/i", $buff2[0])) {
					$key = $buff2[0];
					$arr[] = &$this->Var[$key]; $arr[] = $buff2[1];
				} else {
					$arr[] = ""; $arr[] = $buff1[$i];
				}
			}
		if($ctl=='1') $this->Var[$var] = implode("", $arr);
		else if($ctl=='2') $this->Var[$var] = "";
		else $this->Var[$var] .= implode("", $arr);
				
	}


/*
####################################################################
     ::: 출력 함수 :::          
####################################################################
*/

	function tprint($var,$re="") {
	 global $skin;	
		$buffer = $this->Var[$var];
		$org = "./img|img/|images/|swf/";
		$org = str_replace("/", "\/",$org);       //이미지 경로 보정
		$buffer = preg_replace("/([='\"])($org)/i", "\\1$skin/\\2", $buffer);
		$buffer = preg_replace("/([=(])($org)/i", "\\1$skin/\\2", $buffer);
		//$buffer = preg_replace("/''/i", "'", $buffer);
		if($re) return $buffer;
		else print($buffer);
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

	function close($var="") {
		
		$keys = array();

		if(is_array($var)) $keys = $var;
		elseif($var != "") $keys[0] = $var; 
		else $keys = array_keys($this->Src);

		foreach($keys as $key) {
			unset($this->Var[$key]);
			unset($this->Src[$key]);
		}
	}

}    // End of class


} // End of if(!isset($__PREVIL_TEMP__)) 