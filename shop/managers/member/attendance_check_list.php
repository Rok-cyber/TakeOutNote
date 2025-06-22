<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

$skin = ".";

$type_arr = array('A'=>'연속','T'=>'횟수','D'=>'매일');
$stype_arr = array('P'=>'수동','A'=>'자동');
$method_arr = array('S'=>'스템프','R'=>'댓글','L'=>'로그인');

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$field2		= isset($_GET['field2']) ? $_GET['field2'] : $_POST['field2'];
$word2		= isset($_GET['word2']) ? urldecode($_GET['word2']) : urldecode($_POST['word2']);
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$status1	= isset($_GET['status1']) ? $_GET['status1'] : $_POST['status1'];
$status2	= isset($_GET['status2']) ? $_GET['status2'] : $_POST['status2'];


// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./attendance_check_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");



if(!$uid) {
	$sql = "SELECT uid FROM mall_attendance WHERE s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."' ORDER BY s_date ASC LIMIT 1";
	$uid = $mysql->get_one($sql);
}

$sql = "SELECT * FROM mall_attendance WHERE uid='{$uid}'";
if($data = $mysql->one_row($sql)) {

	$NAME	= stripslashes($data['title']);
	$SDATE  = substr($data['s_date'],0,10);
	$EDATE  = substr($data['e_date'],0,10);
	$TYPE	= $type_arr[$data['type']];
	$STYPE	= $stype_arr[$data['stype']];
	$METHOD	= $method_arr[$data['method']];
	if($data['stype']!='D') $CONDI = " (<font class='eng'>".number_format($data['condi'])."</font>일)";
	else $CONDI = '';
	if($data['point']) $POINT = " (<font class='eng'>".number_format($data['point'])."P</font>지급)";
	else $POINT = '';

	if($SDATE<=date("Y-m-d")) {
		if($EDATE>=date("Y-m-d")) {
			$STATUS = "<font class='blue small'>진행중</font>";
		}
		else $STATUS = "종료";
	}
	else $STATUS = "준비중";
	$tpl->parse("is_infos");
}

$addstring = "&uid={$uid}";
##################### addstring ############################
if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	$where .= "&& INSTR({$field},'{$word}') ";
} 
else $field = "name";

if($field2 && $word2) {
	$addstring .= "&field2={$field2}&word2=".urlencode($word2);
	$where .= "&& {$field2} >= '{$word2}' ";
} 

if($status1) {
	$addstring .= "&status1={$status1}";
	if($data['type']=='A') $where .= " && continuity >= {$data['condi']}";
	else if($data['type']=='T') $where .= " && total >= {$data['condi']}";
	$status11 = "checked";
}

if($status2) {
	$addstring .= "&status2={$status2}";
	$where .= " && reserve>0";
	$status21 = "checked";
}

if(!$order) $order = "total DESC";
if($order) $addstring .="&order={$order}";	
else $order = "uid DESC";

if(!$limit) {	
	$limit = 10;
	$PGConf['page_record_num'] = 10;
}
else {
	$addstring .="&limit={$limit}";	
	$PGConf['page_record_num'] = $limit;
}
$PGConf['page_link_num'] = 10;

$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];

$addstring2= $addstring;
if($page) $addstring .="&page=$page";


$sql = "SELECT count(DISTINCT id) FROM mall_attendance_check WHERE puid = '{$uid}' {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$limit);	
$v_num = $total_record - (($page-1) * $limit);
/*********************************** @LIMIT  CONFIGURATION ***********************************/


if($total_record > 0) {
	
/*********************************** QUERY **********************************/
	$query = "SELECT *, MAX(total) as total, MAX(continuity) as conti, MAX(reserve) as reserve  FROM mall_attendance_check WHERE puid='{$uid}' {$where} GROUP BY id ORDER BY {$order} LIMIT {$Pstart},{$limit}";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$C_ID = $row['id'];
		
		if($row['reserve']!=0) $disable = "disabled";
		else $disable = "";

		$DEL	= "<input type='checkbox' value='{$C_ID}' name='item[]' onfocus='blur();' {$disable} />";

		$sql = "SELECT * FROM pboard_member WHERE id='{$C_ID}'";
		$row2 = $mysql->one_row($sql);

		$C_NAME = stripslashes($row2['name']);
		$C_CNTS = number_format($row['total']);
		$C_MCNTS = number_format($row['conti']);

		switch($data['type']) {
			case "A" : 
				if($data['condi']<=$row['conti']) $C_STATUS1 = "<font class='small orange'>예</font>";
				else $C_STATUS1 = "아니요";
			break;

			case "T" : 
				if($data['total']<=$row['conti']) $C_STATUS1 = "<font class='small orange'>예</font>";
				else $C_STATUS1 = "아니요";
			break;

			case "D" :
				$C_STATUS1 = "예";
			break;
		}

		if($row['reserve']!=0) $C_STATUS2 = "<a href='../multi_board/board.php?code=reserve&field=id&word={$C_ID}' title='적립내역보기'><font class='small orange'>지급완료</font></a>";
		else if($C_STATUS1!="아니요")  $C_STATUS2 = "<font class='small blue'>수동지급요청</font>";
		else $C_STATUS2 = "대상아님";
				
		$tpl->parse("is_man2","1");         
		$tpl->parse("loop");
		$v_num--;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?".$addstring2); 
	$PAGING = $pg->print_page();  //페이징 

} 
else { $tpl->parse("is_loop"); 	}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$C_ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$PAGE = "{$page}/{$total_page}";

$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML