<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

include "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";
require "{$lib_path}/class.Paging.php";

$skin = ".";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$status		= isset($_GET['status']) ? $_GET['status'] : $_POST['status'];

if(!$field) $field = "name";

if($word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	if($field=="multi") $where .= "&& (INSTR(name,'{$word}') || INSTR(id,'{$word}') || INSTR(tel,'{$word}') || INSTR(hphone,'{$word}') || INSTR(email,'{$word}') || INSTR(address,'{$word}'))";
	else $where .= "&& INSTR({$field},'{$word}')";
}

if($sdate1 && $sdate2) {		
    if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}&dates={$dates}";
	$dates = "status_date";
	if($sdate1==$sdate2) $where .= "&& INSTR({$dates},'{$sdate1}') ";
	else $where .= "&& ({$dates} BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR({$dates},'{$sdate2}'))";		
}  

if($status) {
	$addstring .= "&status={$status}";
	$where .= " && status='{$status}'";
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

if($order) {
	$addstring .= "&order={$order}";
}
else $order = "signdate DESC";

$pagestring	= $addstring;
$addstring .= "&page={$page}";

$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-3 MONTH', time()));
$DAY6 = date('Y-m-d', strtotime('-6 MONTH', time()));

$sql = "SELECT COUNT(uid) FROM mall_order_cash WHERE uid>0 {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$record_num);	
$v_num = $total_record - (($page-1) * $record_num);
/*********************************** @LIMIT CONFIGURATION ***********************************/

/*********************** 페이지 계산 **************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","cash_tax_list.html");
$tpl->scan_area('main');

if($total_record > 0) {
	
	$status_arr2 = array('A'=>'<font class="green">발급요청</font>','B'=>'<font class="orange">발급완료</font>','C'=>'발급취소');
	$rstatus_arr = Array("NTRW"=>"KCP 등록 완료","NTNW"=>"국세청 등록 대기","NTNC"=>"국세청 등록 완료","NTNE"=>"국세청 등록 오류");

	$sql = "SELECT code FROM mall_design WHERE mode='B'";
	$tmp_cash = $mysql->get_one($sql);
	$cash = explode("|*|",stripslashes($tmp_cash));
	$SHOP_ID = $cash[3];	

	/*********************************** QUERY ***********************************/
    $query = "SELECT * FROM mall_order_cash WHERE uid>0 {$where} ORDER BY {$order} LIMIT {$Pstart},{$record_num}";
    $mysql->query($query);
	/*********************************** QUERY ***********************************/

	/*********************************** LOOP ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#efefef";
		else $BGCOLOR = "#ffffff";
	    
		if($row['order_num']) { 			
			if(!eregi("CASH_",$row['order_num'])) {
				$LIST1 = "<a href='#' onclick=\"pLightBox.show('order_view.php?pop=1&order_num={$row['order_num']}','iframe','840','580','■ 주문내역 상세보기',10);return false;\" title='주문내역상세보기'>{$row['order_num']}</a";
				$sql = "SELECT order_status FROM mall_order_info WHERE order_num='{$row['order_num']}'";
				$ostatus = $mysql->get_one($sql);
				$LIST5 = $status_arr[$ostatus];
			}
			else {
				$LIST1 = $row['order_num'];
				$LIST5 = "개별발급";
			}
		}
		else {
			$LIST1 = "발급후생성"; 
			$LIST5 = "개별발급";
		}

		$LIST2 = stripslashes($row['name']);
		$LIST3 = stripslashes($row['goods_name']);
		$LIST4 = number_format($row['price']);
		
		$LIST6 = $status_arr2[$row['status']];
		if(substr($row['status_date'],0,4)!='0000') {
			$LIST7 = substr($row['status_date'],0,16);
		}
		else $LIST7 = '';

		if($row['status']=='A') {
			$ALINK = "cash_tax_post.php?mode=apply&uid={$row['uid']}{$addstring}";
			$ACT_TITLE = "발급";
			if($row['receipt_error']) $LIST6 .= "<br /><font class='small' title='{$row['receipt_error']}'>발급실패</font>";
			$tpl->parse("is_action");
		}
		else if($row['status']=='B') {
			//$LIST6 .= "<br /><font class='small'>(".$rstatus_arr[$row['receipt_status']].")</font>";
			$LIST6 = "<font title='승인번호 : {$row['receipt_no']}'>{$LIST6}</font>";
			if($row['receipt_error']) $LIST6 .= "<br /><font class='small' title='{$row['receipt_error']}'>취소실패</font>";
			
			$ALINK = "cash_tax_post.php?mode=cancel&uid={$row['uid']}{$addstring}";
			$ACT_TITLE = "발급취소";			

			$PRINT = "window.open('https://admin.kcp.co.kr/Modules/Service/Cash/Cash_Bill_Common_View.jsp?term_id=PGNW{$SHOP_ID}&orderid={$row['order_num']}&bill_yn=N&authno={$row['receipt_no']}','cash','width=420, height=670');return false;";
			$tpl->parse("is_action");
			$tpl->parse("is_print");
		}
		
		if($row['status']=='C' || ($row['status']=='A' && !$row['order_num'])) {
			$UID = $row['uid'];
			$tpl->parse("is_delete");			
		}

		$tpl->parse("loop");
		$tpl->parse("is_action","2");
		$tpl->parse("is_delete","2");			
		$tpl->parse("is_print","2");			

		$v_num--;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?{$pagestring}");
	$PAGING = $pg->print_page();  //페이징 

} 
else $tpl->parse("noloop");
/*********************************** LOOP ***********************************/

$TOTAL = $total_record;      //토탈수 
$PAGE = "$page/$total_page";
$LINK1 = "";    // 목록보기 링크 

$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("main");
$tpl->tprint("main");


include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
?>





	