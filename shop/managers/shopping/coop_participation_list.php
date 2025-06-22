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
$gid		= isset($_GET['gid']) ? $_GET['gid'] : $_POST['gid'];


// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./coop_participation_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");


##################### addstring ############################
if($gid) {
	$addstring .="&gid={$gid}";

	$today = date("Y-m-d H:i");
	$img_path	= "../../image/goods_img";

	$sql = "SELECT * FROM mall_goods WHERE uid = '{$gid}'";
    $row = $mysql->one_row($sql);
	
	$sql = "SELECT price, qty FROM mall_goods_cooper WHERE guid='{$row['uid']}' ORDER BY qty ASC LIMIT 1";
	$row2 = $mysql->one_row($sql);

	$MLINK	= "coop_goods_write.php?mode=modify&uid={$gid}";
	$IMAGE = "<img src='{$img_path}{$row['image4']}' border=0 width=50 height=50>";
	$NAME = "<a href='{$MLINK}' onfocus='this.blur();'>".stripslashes($row['name'])."</a>";
	$PRICE = number_format($row['price'],$ckFloatCnt); 
	
	if($row['coop_pay']=='Y') {
		$COOP_PRICE = number_format($row2['price'],$ckFloatCnt);
		$STATUS3 = "<font class='small orange'>선주문</font>";
	}
	else {
		if($row['coop_price']==0) $COOP_PRICE = $PRICE;
		else $COOP_PRICE = number_format($row['coop_price'],$ckFloatCnt); 
		$STATUS3 = "<font class='small blue'>성립후주문</font>";
	}
	$CNT = number_format($row['coop_cnt']); 
	$ORDER = number_format($row['o_cnt']); 

	switch($row['s_qty']){
		case "1" : $QTY = "무제한";
		break;
		case "4" : 
			$QTY = "<font class='eng'>{$row['qty']}</font>";
		break;
	}
	if($row['type']=='B') $QTY = "<font style='color:#3366CC'>분류<br />상품숨김</font>";
		
	/************************* 적립금 관련 ***********************/
	$reserve = explode("|",$LIST5);
	if($reserve[0] =='2') { //쇼핑몰 정책일때
		if($cash[6] =='1') { 
			$RESERVE = number_format(($row['coop_price'] * $cash[8])/100,$ckFloatCnt);
		} else $RESERVE = 0;
	} 
	else if($reserve[0] =='3') { //별도 책정일때
		$RESERVE = number_format(($row['coop_price'] * $reserve[1])/100,$ckFloatCnt);
	}		
	else $RESERVE = 0;
	/************************* 적립금 관련 ***********************/

	/************************* 아이콘 관련 ***********************/
	if($row['icon']){
		$ICON = '';
		$icon = explode("|",$row['icon']);
		for($j=1,$cnt2=count($icon);$j<$cnt2;$j++) {
		   $ICON .= "<img src='../../image/icon/{$icon[$j]}' border='0' align='absmiddle' />";
		}
	}	
	/************************* 아이콘 관련 ***********************/
	
	$SDATE = substr($row['coop_sdate'],0,16);	
	$EDATE = substr($row['coop_edate'],0,16);	
		
	if(strtotime($SDATE) > time()) $STATUS = "<font class='small blue'>공구 준비중</font>";
	else if(strtotime($EDATE) > time()) $STATUS = "<font class='small orange'>공구 진행중</font>";
	else $STATUS = "<font class='small green'>공구 마감</font>";

	if($row2['qty'] <= $row['coop_cnt']) $STATUS2 = "<font class='small orange'>공구 성립</font>";
	else $STATUS2 = "<font class='small'>공구 미성립</font>";
	
	$SCNT = $row2['qty'];
		
	$tpl->parse("is_gid");

	$where = " && guid='{$gid}' ";
}
else $OP_GNAME = "<option value='gname'>상품명</option>";

if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	if($field=='gname') {		
		$where = " && a.guid=b.uid && INSTR(b.name,'{$word}') ";
		$where2 = ", mall_goods b ";
		$where3 = ", b.name, b.coop_price, b.coop_sale";
	} 
	else $where .= "&& INSTR(a.{$field},'{$word}') ";

} 
else $field = "id";

if($order) $addstring .="&order={$order}";	
else $order = "uid DESC";

if($status) {
	$addstring .= "&status={$status}";
	$where .= " && a.status='{$status}'";
}

if(!$limit) {	
	$limit = 20;
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


$sql = "SELECT COUNT(*) FROM mall_cooperate a {$where2} WHERE a.uid != '0' {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$limit);	
$v_num = $total_record - (($page-1) * $limit);
/*********************************** @LIMIT  CONFIGURATION ***********************************/

if($total_record > 0) {

	$status_arr = Array("A"=>"신청완료","B"=>"<font class='small blue'>주문완료</font>","C"=>"<font class='small green'>주문취소","D"=>"신청취소");
/*********************************** QUERY **********************************/
	$query = "SELECT a.* {$where3} FROM mall_cooperate a {$where2} WHERE a.uid != '0' {$where} ORDER BY {$order} LIMIT {$Pstart},{$limit}";
	$mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$UID	= $row['uid'];
		$GUID	= $row['guid'];
		$DEL	= "<input type='checkbox' value='{$UID}' name='item[]' onfocus='blur();'>";
		$ID		= $row['id'];
		
		$sql = "SELECT name FROM pboard_member WHERE id='{$ID}'";
		$NAME = stripslashes($mysql->get_one($sql));
		
		if(!$row['name']) {
			$sql = "SELECT uid, name, coop_price, coop_pay, price FROM mall_goods WHERE uid='{$row['guid']}'";
			$tmps = $mysql->one_row($sql);
		}
		else $tmps = $row;
		$GNAME = stripslashes($tmps['name']);

		if($tmps['coop_pay']=='Y'){
			$sql = "SELECT price FROM mall_goods_cooper WHERE guid='{$tmps['uid']}' ORDER BY qty ASC LIMIT 1";
			$nowprice = $mysql->get_one($sql);
			$GPRICE = number_format($nowprice,$ckFloatCnt);
			$GSALE = 100 - round((100*$nowprice)/$tmps['price']);
		}
		else {
			$GPRICE = number_format($tmps['coop_price'],$ckFloatCnt);
			$GSALE = number_format($tmps['coop_sale']);
		}

		$CELL = "<a href='../member/sms_send.html?cell={$row['cell']}' target='_blank' title='sms 발송'>{$row['cell']}</a>";
		$EMAIL = "<a href='../member/mail_form.html?m_to={$row['email']}' target='_blank' title='메일보내기'>{$row['email']}</a>";
		$QTY = $row['qty'];

		$STATUS	= $status_arr[$row['status']];
		$DATE	= date("Y-m-d H:i",$row['signdate']);
		
		if($row['order_num']) {
			$ORDER_NUM = $row['order_num'];
			$tpl->parse("is_order1","1");
			$tpl->parse("is_order2","1");
		}
				
		$tpl->parse("is_man2","1");         
		if(!$gid) $tpl->parse("loop1");
		else $tpl->parse("loop2");
		$tpl->parse("is_order1","2");
		$tpl->parse("is_order2","2");
		$v_num--;
	}

	if(!$gid) $tpl->parse("is_type1");
	else $tpl->parse("is_type2");

} else { 
	if(!$gid) $tpl->parse("is_type1");
	else $tpl->parse("is_type2");
	
	$tpl->parse("is_loop"); 	
}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$C_ACTION = "{$PHP_SELF}?{$addstring2}";
$PAGE = "{$page}/{$total_page}";

$pg = new paging($total_record,$page);
$pg->addQueryString("?".$addstring2); 
$PAGING = $pg->print_page();  //페이징 
$ACTION = $PHP_SELF;   //검색 경로
$CANCEL = $PHP_SELF."?gid={$gid}";

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML