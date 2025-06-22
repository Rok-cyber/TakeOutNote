<?
$code   = $_GET['code'];
$limit	= isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$word	= isset($_POST['word'])	? urldecode(trim($_POST['word'])) : urldecode(trim($_GET['word']));
$seccate= isset($_POST['seccate'])	? $_POST['seccate'] : $_GET['seccate'];
if(!$limit) $limit = 12;

$sql  = "SELECT * FROM pboard_manager WHERE name = '{$code}'";
if(!$main_data = $mysql->one_row($sql)) alert("삭제된 게시판 이거나 존재하지 않습니다");

$tpl = new classTemplate;	// 템플릿 생성
$tpl->define('main',"{$skin}/board_{$code}.html");
$tpl->scan_area('main');


$PGConf['page_record_num']	= $main_data['inpage'];
$PGConf['page_link_num']	= $main_data['pagelink'];
$options = explode("||",$main_data['options']);

$B_NAME = $main_data['title'];

if($word) {
	$addstring = "&amp;field={$field}&amp;word=".urlencode($word);
	$where .= " && (INSTR(b.comment,'{$word}') || INSTR(m.subject,'{$word}') || INSTR(m.name,'{$word}'))";
}
$catestring =$addstring; 
if($seccate) { 
	$addstring .= "&amp;seccate={$seccate}"; 
	$where .= " && m.cate = '{$seccate}' ";
}
$pagestring = $addstring;
if($page) $addstring .="&amp;page={$page}";

$page_record_num = $limit;

##################### 출력할 리스트 계산 ################################
if($where) {	// 검색일경우
    
	############# 전체글 수 ########### 	
	$sql = "SELECT comment FROM pboard_{$code}_body where no=1";	
	$data			= $mysql->one_row($sql);	
	$record_arr		= explode("|",$data[comment]);
	$total_record	= $record_arr[0];
	
	########## 게시물수에 따른 2중페이징 분할건수(만건) #################
	if($total_record <300000) $sepa_idx = 10;
    else if($total_record <1000000) $sepa_idx = 8;
	else if($total_record <2000000) $sepa_idx = 6;
	else if($total_record <3000000) $sepa_idx = 4;
    else     $sepa_idx = 2;
    
	$h_record=$i=$sepa_start_idx=$total_record=0;
    $record_arr='';
	$sepa_last_idx =999;
	
	if($field=='comment') $sepa_idx=($sepa_idx/2);
   
	if(!$spage) {
		$sql = "SELECT depth FROM pboard_{$code} WHERE no=1";
	    $slast_idx = $mysql->get_one($sql);
	    if($slast_idx < (999-$sepa_idx))  $spage=1;
	} 
	if($spage && $slast_idx) {
        $sepa_start_idx = ($slast_idx + ($sepa_idx * ($spage-1)))-1;
        $sepa_last_idx	= $sepa_start_idx + ($sepa_idx+1);
		$spagestring	= $pagestring."&slast_idx={$slast_idx}";;
     	$addstring		.= "&slast_idx={$slast_idx}&spage={$spage}";
	    $pagestring		.= "&slast_idx={$slast_idx}&spage={$spage}";
	    if($sepa_last_idx>998) $sepa_last_idx = 999;
	}

	$sql = "SELECT count(*) as cnt, m.idx FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no=b.no && m.idx < {$sepa_last_idx} && m.idx > {$sepa_start_idx} {$where} GROUP BY m.idx";	
	$mysql->query($sql);
	  
	while($data=$mysql->fetch_array()){
		$record_arr[$i] = $data[cnt];
		$total_record += $data[cnt];
		$tmp_idx = $data['idx'];
		$i++;
	}
} 
else {

	$sql = "SELECT comment,file FROM pboard_{$code}_body where no=1";
	$data			= $mysql->one_row($sql);

	$record_arr		= explode("|", $data['comment']);
	$nrecord		= explode("|", $data['file']);
	$total_record	= $record_arr[0]+$nrecord[0];	
}	
##################### 출력할 리스트 계산 ################################    
   	
##################### 카데고리 #####################
if($options[1]=='Y' && $category[0]>0){
	$CATE		= "<option value=''>전체</option>\n";	
	for($i=1,$ck2=1;$i<=$category[0];$i++) {
		$C_NAME = stripslashes($category[$i]);		
		if($seccate==$i) $CSEC2 = "selected";
		else $CSEC2 = "";
				
		$CATE .="<option value='{$i}' {$CSEC2}>{$C_NAME}</option>";		
		$tpl->parse("loop_cate");
	}	    
    $tpl->parse('is_cate');
}

if($total_record > 0) {	
	$Pstart = $start_recode;
	$TOTAL = $total_record;      //토탈수 
	$TOTAL_PAGE = ceil($total_record/$page_record_num);	
	$TOPAGE = ceil($total_record/$PGConf['page_record_num']);
	$PAGE = $page;
} 
else $tpl->parse("no_content");

$tpl->parse('main');
$tpl->parse('close');
?>