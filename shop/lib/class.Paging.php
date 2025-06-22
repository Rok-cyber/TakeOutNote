<?
/******************************************************************
* =======================================================
* 클래스: 쇼링몰을 위한 페이징 클래스
*
* 제 작: gubok kim (email : previl@previl.net,  homepage : http://previl.net)
*
* 제작일: 2003.02.12 , 최종 수정일: 2008.03.22
*
* =======================================================
******************************************************************/


// require나 include시 중복선언 방지를 위한 부분 

if(!isset($__PREVIL_PAGING__))   
{ 
  $__PREVIL_PAGING__ = 1; 

class paging {
		
		var $page;                 // 현재페이지
		var $page_record_num;      // 한페이지에 보여줄 레코드 수
		var $page_link_num;        // 한페이지에 보여줄 페이지 수
		var $total_record;         // 총 레코드 수
		var $total_page;           // 총 페이지 수
		var $total_block;          // 총 블럭 수
		var $block = 1;            // 현재블럭
		var $page_start;           // 화면에 뿌려질 페이지 숫자의 첫 페이지 숫자
        var $page_end;             // 화면에 뿌려질 페이지 숫자의 마지막 페이지 숫자
		var $prev_page;            // 이전페이지
		var $next_page;            // 다음페이지
		var $url;                  // 문서정보
        var $qstr;                // HTTP에서 GET으로 넘길 쿼리문자

/*
####################################################################
     ::: 생성자 :::          
     새롭게 인자를 받지 않으면 Conf에 저장되어 있는 정보를 가지고 접속한다.
####################################################################
*/

		function paging($total_record,$page="1",$page_record_num="",$page_link_num="") {
              global $PGConf,$Main;                

              $this->total_record = $total_record;
    		  $this->page = $page;
			  if($page_record_num)   $this->page_record_num = $page_record_num;
              else        $this->page_record_num = $PGConf['page_record_num'];
			  if($page_link_num)   $this->page_link_num = $page_link_num;
              else        $this->page_link_num = $PGConf['page_link_num'];
		  	  if($Main) $this->url ="";
			  else $this->url = $_SERVER[PHP_SELF];
              
			  $this->make_page();
		}
       

/*
####################################################################
     :::  :::          
####################################################################
*/

         function make_page() {
		      
			    $this->total_page = ceil($this->total_record/$this->page_record_num);		
			    $this->total_block = ceil($this->total_page/$this->page_link_num);
       		    if($this->page < 1) $this->page = 1;
		        if($this->page > $this->total_page) $this->page = $this->total_page;
		        $this->block = ceil($this->page/$this->page_link_num);        // 현재블럭 설정
                $this->page_end = ceil($this->block*$this->page_link_num);        // 페이지출력 종료루프 변수
		        $this->page_start = ($this->page_end-$this->page_link_num)+1;       // 페이지출력 시작루프 변수
       		    $this->prev_page = ($this->block*$this->page_link_num)-$this->page_link_num; // 이전블럭 번호 설정
		        $this->next_block = $this->block+1; // 다음블럭 번호 설정
		
	}


/*
####################################################################
     ::: 추가 변수 :::          
     HTTP에서 GET으로 넘길 쿼리변수를 입력받는다. (&name1=value1&name2=value2)
####################################################################
*/
        
        function addQueryString($qstr) {

                $this->qstr = $qstr;
        
		}

	
/*
####################################################################
     ::: 페이징 출력 :::          
     페이징을 출력한다. 
####################################################################
*/

function print_page($type="",$separator="",$img_path="") {

	if($type=="box" && $this->total_page>6 ) {
		$ckBlock1 = $ckBlock2 = 0;

		$this->page_start = intVal($this->page) - 3;
		$this->page_end = intVal($this->page) + 3;			
		if($this->page<4) {
			$this->page_start = 1;
			$this->page_end = 7;
		}
		if($this->page_end>$this->total_page) {
			$this->page_start = intVal($this->total_page) - 6;
			$this->page_end = $this->total_page;								
		}
		if($this->page>4) $ckBlock1 = 1;
		if($this->page_end==$this->total_page) $ckBlock2 = 1;
	}
	
	if($this->block > 1) {		

		switch($type) {
			case "img" :
				$paging = "<a href='{$this->url}{$this->qstr}&page=1'><img src='{$img_path}/btn_first.gif' border='0' align='absmiddle' alt='첫 페이지' title='첫 페이지' /></a>&nbsp;<a href='{$this->url}{$this->qstr}&page={$this->prev_page}'><img src='{$img_path}/btn_prev.gif' border='0' alt='이전 목록' title='이전 목록' /></a>&nbsp;";
			break;

			case "box" :
				$paging = "<a href='{$this->url}{$this->qstr}&page=1'><span class='num default' onmouseover='this.className=\"num defaultOver\"' onmouseout='this.className=\"num default\"'>1</span></a><span class='num'>...</span>";
				break;
			
			default :
				$paging = "<a href='{$this->url}{$this->qstr}&page={$this->prev_page}'><span id='prevPage'  title='이전 목록'>이전</span></a>";
				$paging .= "<span class='numbox'>";
				$paging .= "<a href='{$this->url}{$this->qstr}&page=1'><span class='num2' title='첫 페이지'>1</span></a><span class='num'>...</span>";
			break;
		}		
	}
	else {
		switch($type) {
			case "img" : case "box" : break;
			default :			
				$paging .= "<span class='numbox'>";
		}			
	}
		
	if($this->block > 1) {		

		switch($type) {
			case "img" :
				$paging = "<a href='{$this->url}{$this->qstr}&page=1'><img src='{$img_path}/btn_first.gif' border='0' align='absmiddle' alt='첫 페이지' title='첫 페이지' /></a>&nbsp;<a href='{$this->url}{$this->qstr}&page={$this->prev_page}'><img src='{$img_path}/btn_prev.gif' border='0' alt='이전 목록' title='이전 목록' /></a>&nbsp;";
			break;

			case "box" :
				$paging = "<a href='{$this->url}{$this->qstr}&page=1'><span class='num default' onmouseover='this.className=\"num defaultOver\"' onmouseout='this.className=\"num default\"'>1</span></a><span class='num'>...</span>";
			break;

			case "box2" :
				$paging = "<a href='{$this->url}{$this->qstr}&page=1'><span class='small btnFY'><font style='font-size:10px'><</font>&nbsp;&nbsp;맨앞</span></a>&nbsp;&nbsp;&nbsp;&nbsp;";
				$paging .= "<a href='{$this->url}{$this->qstr}&page={$this->prev_page}'><span class='small btnFY'><font style='font-size:10px'><</font>&nbsp;&nbsp;이전</span></a>&nbsp;&nbsp;&nbsp;&nbsp;";
			break;
			
			default :
				$paging = "<a href='{$this->url}{$this->qstr}&page={$this->prev_page}'><span id='prevPage'  title='이전 목록'>이전</span></a>";
				$paging .= "<span class='numbox'>";
				$paging .= "<a href='{$this->url}{$this->qstr}&page=1'><span class='num2' title='첫 페이지'>1</span></a><span class='num'>...</span>";
			break;
		}		
	}
	else {
		switch($type) {
			case "img" : case "box" : break;

			case "box2" : 
				if($this->page>1) {
					$paging = "<a href='{$this->url}{$this->qstr}&page=1'><span class='small btnFY'><font style='font-size:10px'><</font>&nbsp;&nbsp;맨앞</span></a>&nbsp;&nbsp;&nbsp;&nbsp;";
					$paging .= "<span class='small btnFN'><font style='font-size:10px'><</font>&nbsp;&nbsp;이전</span>&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				else {
					$paging = "<span class='small btnFN'><font style='font-size:10px'><</font>&nbsp;&nbsp;맨앞</span>&nbsp;&nbsp;&nbsp;&nbsp;";
					$paging .= "<span class='small btnFN'><font style='font-size:10px'><</font>&nbsp;&nbsp;이전</span>&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			break; 

			default :			
				$paging .= "<span class='numbox'>";
		}			
	}
		
	if($this->block >= 1) {		
		
		for($i=$this->page_start; $i<=$this->page_end; $i++) {
			if($i!=$this->page_start) $pseparator = $separator;
			if($this->page==$i) { 				
				switch($type) {
					case "img" :
						$paging .= "{$pseparator}<a class='inum'><span class='selected'>{$i}</span></a>";				
					break;

					case "box" :
						$paging .= "<span class='selected num'>{$i}</span>";				
					break;

					case "box2" :
						$paging .= "<span class='selected2 small'>{$i}</span>&nbsp;&nbsp;";				
					break;

					default :
						$paging .= "<a class='num'><span class='selected'>{$i}</span></a>";				
				}
			} 
			else {
				switch($type) {					
					case "img" :
						$paging .= "{$pseparator}<a href='{$this->url}{$this->qstr}&page={$i}'><span class='inum'>{$i}</span></a>";
					break;

					case "box" :
						$paging .= "<a href='{$this->url}{$this->qstr}&page={$i}'><span class='num default' onmouseover='this.className=\"num defaultOver\"' onmouseout='this.className=\"num default\"'>{$i}</span></a>";
					break;

					case "box2" :
						$paging .= "<a href='{$this->url}{$this->qstr}&page={$i}'><span class='small default2' onmouseover='this.className=\"small defaultOver2\"' onmouseout='this.className=\"small default2\"'>{$i}</span></a>&nbsp;&nbsp;";
					break;

					default :
						$paging .= "<a href='{$this->url}{$this->qstr}&page={$i}'><span class='num'>{$i}</span></a>";
				}
			}
			
			$this->next_page = $i+1;			
			if($this->next_page==$this->total_page+1) {
				break;
			}
		}
	}
    
	switch($type) {
		case "img" : case "box" : break;

		default :
			$paging .= "</span>";
	}

	if($this->block < $this->total_block) {
		switch($type) {
			case "img" :
				$paging .= "&nbsp;<a href='{$this->url}{$this->qstr}&page={$this->next_page}'><img src='{$img_path}/btn_next.gif' border='0' align='absmiddle' alt='다음 목록' title='다음 목록' /></a>&nbsp;<a href='{$this->url}{$this->qstr}&page={$this->total_page}'><img src='{$img_path}/btn_last.gif' border='0' align='absmiddle'  alt='마지막 페이지' title='마지막 페이지' /></a>";
			break;

			case "box" :
				$paging .= "<span class='num'>...</span><a href='{$this->url}{$this->qstr}&page={$this->total_page}'><span class='num default' onmouseover='this.className=\"num defaultOver\"' onmouseout='this.className=\"num default\"'>{$this->total_page}</span></a>&nbsp;";					
			break;

			case "box2" :
				$paging .= "&nbsp;&nbsp;<a href='{$this->url}{$this->qstr}&page={$this->next_page}'><span class='small btnFY'>다음&nbsp;&nbsp;<font style='font-size:10px'>></font></span></a>&nbsp;&nbsp;&nbsp;&nbsp;";
				$paging .= "<a href='{$this->url}{$this->qstr}&page={$this->total_page}'><span class='small btnFY'>맨끝&nbsp;&nbsp;<font style='font-size:10px;'>></font></span></a>";
			break;

			default :
				$paging .= "<span class='num2'>...</span><a href='{$this->url}{$this->qstr}&page={$this->total_page}'><span class='num' title='마지막 페이지'>{$this->total_page}</span></a> <a href='{$this->url}{$this->qstr}&page={$this->next_page}'><span id='nextPage' title='다음 목록'>다음</span></a>";
		}			
	}
	else {
		switch($type) {
			case "box2" :
				if($this->page!=$this->total_page) {
					$paging .= "&nbsp;&nbsp;<span class='small btnFN'>다음&nbsp;&nbsp;<font style='font-size:10px'>></font></span>&nbsp;&nbsp;&nbsp;&nbsp;";
					$paging .= "<a href='{$this->url}{$this->qstr}&page={$this->total_page}'><span class='small btnFY'>맨끝&nbsp;&nbsp;<font style='font-size:10px;'>></font></span></a>";
				}
				else {
					$paging .= "&nbsp;&nbsp;<span class='small btnFN'>다음&nbsp;&nbsp;<font style='font-size:10px'>></font></span>&nbsp;&nbsp;&nbsp;&nbsp;";
					$paging .= "<span class='small btnFN'>맨끝&nbsp;&nbsp;<font style='font-size:10px;'>></font></span>";
				}		
			break;
		}
	}
		
	return $paging;
}


/*
####################################################################
     ::: 페이징 close :::          
     class의 사용한 메모리를 지운다
####################################################################
*/
	
	function close() {

       unset($this->qstr);

    }


}  // End Class


} // End of if(!isset($__PREVIL_PAGING__)) 

?>
