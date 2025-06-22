<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

$skin = ".";

###################### 변수 정의 ##########################
$field		= isset($_REQUEST['field']) ? $_REQUEST['field'] : 'name';
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$sectype	= isset($_GET['sectype']) ? $_GET['sectype'] : $_POST['sectype'];
$ststus		= isset($_GET['ststus']) ? $_GET['ststus'] : $_POST['ststus'];
$pid		= isset($_GET['pid']) ? $_GET['pid'] : $_POST['pid'];


// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./cupon_down_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");


##################### addstring ############################
if($pid) {
	$addstring .="&pid={$pid}";
	$where .= " && pid = '{$pid}' ";

	$type_arr = Array('','관리자발급','회원가입발급','상품페이지다운','링크다운');
	$stype_arr = Array('P'=>'%','W'=>'원');

	$sql= "SELECT * FROM mall_cupon_manager WHERE uid='{$pid}'";
    $row = $mysql->one_row($sql);

	if(!$row) alert("등록되지않은 쿠폰이거나 삭제된 쿠폰 입니다.","back");
	
	$NAME	= "<a href='cupon_write.php?mode=modify&uid={$pid}' title='수정하기'>".stripslashes($row['name'])."</a>";
	$TYPE	= $type_arr[$row['type']];
	$SALE	= number_format($row['sale']);
	$STYPE	= $stype_arr[$row['stype']];

	if($row['sdate'] && $row['edate'] && !$row['days']) {
		$DATES = substr($row['sdate'],0,10)." ~ ".substr($row['edate'],0,10);
	}
	else {
		$DATES = "발급 후 {$row['days']}일";
	}

	$DATE = date("Y-m-d",$row['signdate']);

	if($row['sqty']==1) {
		$QTY = "<font class='eng'>".number_format($row['qty'])."개";
	}
	else $QTY = "무제한";

	$sql = "SELECT count(*) FROM mall_cupon WHERE pid='{$pid}' && status!='D'";
	$CNTS = number_format($mysql->get_one($sql));

	$tpl->parse("is_pid");

	if($row['type']==1) {
		$sql = "SELECT name, code FROM mall_design WHERE mode='L' && name!='10' ORDER BY name ASC";
		$mysql->query($sql);

		for($i=2;$i<9;$i++) {
			$row = $mysql->fetch_array();
			while($row['name']!=$i) {
				$LEVEL .= "<option value='{$i}'>LV{$i}</option>";
				if($i==8) break;
				$i++;
			}
			if($row['name']==$i) {
				$tmps = explode("|",$row['code']);
				$LEVEL .= "<option value='{$i}'>".stripslashes($tmps[0])."</option>";
			}
		}

		$LEVEL .= "<option value='9'>부관리자</option>";
		$LEVEL .= "<option value='10'>관리자</option>";

		$tpl->parse("is_pid2");
	}

}

if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	if($field=='name') $where .= "&& INSTR(b.{$field},'{$word}') ";
	else $where .= "&& INSTR(a.{$field},'{$word}') ";
} else $field = "name";

if($order) $addstring .="&order={$order}";	
else $order = "a.uid DESC";

if($status) {
	$addstring .= "&status={$status}";
	$where .= " && a.status='{$status}'";
}

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


$sql = "SELECT COUNT(*) FROM mall_cupon a, mall_cupon_manager b WHERE a.uid != '0' && a.pid=b.uid {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$limit);	
$v_num = $total_record - (($page-1) * $limit);
/*********************************** @LIMIT  CONFIGURATION ***********************************/

if($total_record > 0) {

$status_arr = Array("A"=>"쿠폰발급완료","B"=>"<font class='blue'>쿠폰사용완료</font>","C"=>"쿠폰기간만료","D"=>"쿠폰발급실패");
/*********************************** QUERY **********************************/
	$query = "SELECT a.*, b.name FROM mall_cupon a, mall_cupon_manager b WHERE a.uid != '0' && a.pid=b.uid {$where} ORDER BY {$order} LIMIT {$Pstart},{$limit}";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$UID	= $row['uid'];
		$DEL	= "<input type='checkbox' value='{$UID}' name='item[]' onfocus='blur();'>";
		$ID		= $row['id'];
		$CNAME	= stripslashes($row['name']);
		$CNAME = "<a href='cupon_write.php?mode=modify&uid={$row['pid']}' title='쿠폰수정하기'>{$CNAME}</a>";

		$sql = "SELECT name FROM pboard_member WHERE id='{$ID}'";
		$NAME = stripslashes($mysql->get_one($sql));

		$STATUS	= $status_arr[$row['status']];
		if($row['usedate']) $UDATE	= date("Y-m-d",$row['usedate']);
		else $UDATE = "";
		
		if($row['signdate'])  $DATE	= date("Y-m-d",$row['signdate']);
		else $DATE = '';
		
		$tpl->parse("is_man2","1");         
		$tpl->parse("loop");
		$v_num--;
	}

} else { $tpl->parse("is_loop"); 	}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$C_ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$PAGE = "{$page}/{$total_page}";

$pg = new paging($total_record,$page);
$pg->addQueryString("?".$addstring2); 
$PAGING = $pg->print_page();  //페이징 
$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML