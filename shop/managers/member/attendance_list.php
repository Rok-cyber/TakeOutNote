<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

$skin = ".";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$status		= isset($_GET['status']) ? $_GET['status'] : $_POST['status'];
$type		= isset($_GET['type']) ? $_GET['type'] : $_POST['type'];
$stype		= isset($_GET['stype']) ? $_GET['stype'] : $_POST['stype'];
$method		= isset($_GET['method']) ? $_GET['method'] : $_POST['method'];

##################### addstring ############################
if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	$where .= "&& INSTR({$field},'{$word}') ";
} else $field = "name";

if($status) {
	$addstring .="&status={$status}";
	if($status==1) $where .= "&& s_date > '".date("Y-m-d")."' && s_date !='0000-00-00'";
	else if($status==2) $where .= "&& s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."'";
	else $where .= "&& e_date <'".date("Y-m-d")."' && e_date !='0000-00-00'";			
}

if($type) {
	$addstring .="&type={$type}";
	$where .= " && type = '{$type}' ";
}

if($stype) {
	$addstring .="&stype={$stype}";
	$where .= " && stype = '{$stype}' ";
}

if($method) {
	$addstring .="&method={$method}";
	$where .= " && method = '{$method}' ";
}

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


$PAGE_LINK = "http://{$_SERVER["SERVER_NAME"]}{$ShopPath}/index.php?channel=attendance";

$sql = "SELECT COUNT(uid) FROM mall_attendance WHERE uid != '0' {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$limit);	
$v_num = $total_record - (($page-1) * $limit);
/*********************************** @LIMIT  CONFIGURATION ***********************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./attendance_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	

$type_arr = array('A'=>'연속','T'=>'횟수','D'=>'매일');
$stype_arr = array('P'=>'수동','A'=>'자동');
$method_arr = array('S'=>'스템프','R'=>'댓글','L'=>'로그인');


/*********************************** QUERY **********************************/
	$query = "SELECT * FROM mall_attendance WHERE uid != '0' {$where} ORDER BY {$order} LIMIT {$Pstart},{$limit}";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$UID	= $row['uid'];
		$DEL	= "<input type='checkbox' value='{$UID}' name='item[]' onfocus='blur();'>";
		$NAME	= "<a href='attendance_write.php?mode=modify&uid={$UID}{$addstring}' title='수정하기'>".stripslashes($row['title'])."</a>";
		$SDATE  = substr($row['s_date'],0,10);
		$EDATE  = substr($row['e_date'],0,10);
		$TYPE	= $type_arr[$row['type']];
		$STYPE	= $stype_arr[$row['stype']];
		$METHOD	= $method_arr[$row['method']];
		if($row['stype']!='D') $CONDI = " (<font class='eng'>".number_format($row['condi'])."</font>일)";
		else $CONDI = '';
		if($row['point']) $POINT = " (<font class='eng'>".number_format($row['point'])."P</font>지급)";
		else $POINT = '';

		if($SDATE<=date("Y-m-d")) {
			if($EDATE>=date("Y-m-d")) {
				$STATUS = "<font class='blue small'>진행중</font>";
			}
			else $STATUS = "종료";
		}
		else $STATUS = "준비중";

		$DATE = date("Y-m-d",$row['signdate']);

		$sql = "SELECT count(DISTINCT id) FROM mall_attendance_check WHERE puid='{$UID}'";
		$CNTS = number_format($mysql->get_one($sql));

		$tpl->parse("is_man2","1");         
		$tpl->parse("loop");
		$v_num--;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?".$addstring2); 
	$PAGING = $pg->print_page();  //페이징 

} else { $tpl->parse("is_loop"); 	}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$C_ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$PAGE = "{$page}/{$total_page}";
$LINK = "./attendance_write.php?{$addstring}";    //  상품등록 링크

$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML