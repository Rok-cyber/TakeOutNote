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
$sectype	= isset($_GET['sectype']) ? $_GET['sectype'] : $_POST['sectype'];

##################### addstring ############################
if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	$where .= "&& INSTR({$field},'{$word}') ";
} else $field = "name";

if($sectype) {
	$addstring .="&sectype={$sectype}";
	$where .= " && type = '{$sectype}' ";
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


$sql = "SELECT COUNT(uid) FROM mall_cupon_manager WHERE uid != '0' {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$limit);	
$v_num = $total_record - (($page-1) * $limit);
/*********************************** @LIMIT  CONFIGURATION ***********************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./cupon_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	
$type_arr = Array('','관리자발급','회원가입발급','상품페이지다운','링크다운');
$stype_arr = Array('P'=>'%','W'=>'원');

/*********************************** QUERY **********************************/
	$query = "SELECT * FROM mall_cupon_manager WHERE uid != '0' {$where} ORDER BY {$order} LIMIT {$Pstart},{$limit}";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$UID	= $row['uid'];
		$DEL	= "<input type='checkbox' value='{$UID}' name='item[]' onfocus='blur();'>";
		$NAME	= "<a href='cupon_write.php?mode=modify&uid={$UID}{$addstring}' title='수정하기'>".stripslashes($row['name'])."</a>";
		if($row['type']==1) {
			$TYPE = "<span id='nextIcon' class=\"hand small\" onclick=\"window.location.href='cupon_down_list.php?pid={$UID}'\">관리자발급</span>";
		}
		else $TYPE	= $type_arr[$row['type']];
		$SALE	= number_format($row['sale'],$ckFloatCnt);
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

		$sql = "SELECT count(*) FROM mall_cupon WHERE pid='{$UID}' && status!='D'";
		$CNTS = number_format($mysql->get_one($sql));
		$CNTS = "<a href='cupon_down_list.php?pid={$UID}' title='발급쿠폰리스트보기'>{$CNTS}</a>";
			
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
$LINK = "./cupon_write.php?{$addstring}";    //  상품등록 링크

$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML