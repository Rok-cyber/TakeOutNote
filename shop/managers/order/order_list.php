<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
include "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode(trim($_GET['word'])) : urldecode(trim($_POST['word']));
$smoney1	= isset($_GET['smoney1']) ? $_GET['smoney1'] : $_POST['smoney1'];
$smoney2	= isset($_GET['smoney2']) ? $_GET['smoney2'] : $_POST['smoney2'];
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$type		= isset($_GET['type']) ? $_GET['type'] : $_POST['type'];
$status		= isset($_GET['status']) ? $_GET['status'] : $_POST['status'];
$mobile		= isset($_GET['mobile']) ? $_GET['mobile'] : $_POST['mobile'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$gs			= isset($_GET['gs']) ? $_GET['gs'] : $_POST['gs'];
$affiliates	= isset($_GET['affiliates']) ? $_GET['affiliates'] : $_POST['affiliates'];
$refer		= $_SERVER['HTTP_HOST'];

$skin = ".";

if($gs) {
	$addstring2 = "gs={$gs}";
	$where .= "&& a.order_status='{$gs}'";
}

if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	if($field=="multi") {
		$where .= "&& (INSTR(a.order_num,'{$word}') || INSTR(a.id,'{$word}') || INSTR(a.name1,'{$word}') || INSTR(a.name2,'{$word}') || INSTR(a.pay_name,'{$word}') || INSTR(b.p_name,'{$word}') || INSTR(b.p_number,'{$word}'))";
	}
	else if($field=="p_name" || $field=="p_number") $where .= "&& INSTR(b.{$field},'{$word}')";
	else $where .= "&& INSTR(a.{$field},'{$word}')";
}  else $field = "name1";


if($smoney1 && $smoney2) {
    if($smoney1 > $smoney2) {$smoney1 = $tmp; $smoney1 = $smoney2; $smoney2 = $tmp;}
	$addstring .= "&smoney1=$smoney1&smoney2=$smoney2";
	if($smoney1==$smoney2) $where .= "&& INSTR(a.pay_total,'{$smoney1}') ";
	else $where .= "&& a.pay_total BETWEEN '{$smoney1}' AND '{$smoney2}' ";
}

if($sdate1=='today') $sdate1 = date("Y-m-d");
if($sdate1 && !$sdate2) $sdate2 = $sdate1;

if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&sdate1=$sdate1&sdate2=$sdate2";	
	if($sdate1==$sdate2) $where .= "&& INSTR(a.signdate,'{$sdate1}') ";
	else $where .= "&& (a.signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(a.signdate,'{$sdate2}'))";		
}  

if($type) {
	$addstring .="&type={$type}";	
	if($type=='E') $where .= " && a.escrow='Y' ";
	else $where .= " && a.pay_type='{$type}' ";
}

if($status) {
	$addstring .="&status={$status}";	
	$where .= " && a.pay_status='{$status}' && pay_type!='B' ";
}

if($affiliates) {
	$addstring .= "&affiliates={$affiliates}";
	$where .= " && a.affiliate = '{$affiliates}'";	
}

if($mobile) {
	$addstring .="&mobile={$mobile}";	
	$where .= " && a.mobile='{$mobile}'";
}

if($limit) $addstring .="&limit={$limit}";	
else $limit = "10";

$addstring3 = $addstring2.$addstring;
if($page>1) $addstring .="&page={$page}";

$PGConf['page_record_num'] = $limit;
$PGConf['page_link_num']='10';
$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];


$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-3 MONTH', time()));
$DAY6 = date('Y-m-d', strtotime('-6 MONTH', time()));

######################## Affiliate 설정 ############################
$sql = "SELECT * FROM mall_affiliate ORDER BY uid ASC";
$mysql->query($sql);

$AFFILI_LIST = '';
while($row=$mysql->fetch_array()){
	if($row['id'] == $affiliates) $sec = 'selected';
	else $sec='';
	$AFFILI_LIST .= "<option value='{$row['id']}' {$sec}>{$row['id']}</option>\n";
}	


if(eregi("b.",$where)) {
	$sql = "SELECT COUNT(*) FROM mall_order_info as a, mall_order_goods as b WHERE a.order_num=b.order_num {$where} group by b.order_num";

	$mysql->query($sql);
	$total_record = 0;
	while($mysql->fetch_array()){
	   $total_record++;
	}
	$join_use = 'Y';
}
else {
	$sql = "SELECT COUNT(*) FROM mall_order_info as a WHERE a.uid>0 {$where}";
	$total_record = $mysql->get_one($sql);
	$join_use = 'N';	
}

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$record_num);	
$v_num = $total_record - (($page-1) * $record_num);
if($v_num<1 && $page>1) {
	$page = 1;
	$Pstart = $record_num*($page-1);
	$v_num = $total_record - (($page-1) * $record_num);
}
/*********************************** @LIMIT  CONFIGURATION ***********************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./order_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

$arr1 = Array('전체주문','입금대기중','결제완료','배송준비중','배송중','배송완료','취소완료');
$arr2 = Array("","A","B","C","D","E","Z");

for($i=0;$i<7;$i++){	
	if($gs==$arr2[$i]) $tabs = "tab_on";
	else $tabs = "tab_off";
	if($arr2[$i]) $LNS = "?gs=".$arr2[$i];
	else $LNS = '';
	$TTL = $arr1[$i];
	$tpl->parse("loop_tab");
}

if($total_record > 0) {

	$sql = "SELECT uid,code FROM mall_design WHERE mode='Z'";
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){
		$DELI_ARR[$row['uid']] = $row['code'];
	}
	
	/*********************************** QUERY **********************************/
    if($join_use=='Y') {
		$query = "SELECT a.uid as uid, a.order_num as order_num, a.id as id, a.name1, a.pay_type, a.pay_status, a.signdate as signdate, a.status_date, a.bank_name, a.use_reserve, a.carriage, a.use_cupon, count(*) as cnt, a.pay_total as sum, a.order_status as status, a.carr_info, a.escrow, a.mobile FROM mall_order_info as a, mall_order_goods as b WHERE a.order_num=b.order_num {$where} group by b.order_num ORDER BY a.uid DESC LIMIT $Pstart,$record_num";
	}
	else{
		$query = "SELECT a.uid as uid, a.order_num as order_num, a.id as id, a.name1, a.pay_type, a.pay_status, a.signdate as signdate, a.status_date, a.bank_name, a.use_reserve, a.carriage, a.use_cupon, a.pay_total as sum, a.order_status as status, a.carr_info, a.escrow, a.mobile FROM mall_order_info as a WHERE a.uid>0 {$where} ORDER BY a.uid DESC LIMIT $Pstart,$record_num";
	}
    $mysql->query($query);		
	/*********************************** QUERY  ***********************************/

	/*********************************** LOOP  ***********************************/
    //사용 배열정의 
	$MTlist = Array(8,'uid','order_num','id','name1','cnt','sum','pay_type','signdate');	
	$pay_arr = Array('A'=>'결제미완료','B'=>'결제성공','C'=>'결제실패','D'=>'카드취소');
	$pay_arr2 = Array('A'=>'계좌발금완료','B'=>'계좌입금완료','C'=>'입금실패','D'=>'환불');
	$pay_arr3 = Array('A'=>'미입금','B'=>'입금완료','C'=>'입금실패','D'=>'환불');

	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;

		if($v_num%2 ==0) $BGCOLOR = "#efefef";
		else $BGCOLOR = "#ffffff";

		$DEL = "<input type='checkbox' value='$row[order_num]' name='item[]'  onfocus='blur();'>";
		for($i=2;$i<9;$i++){  
			$fd = $MTlist[$i];
			if($i==2) ${"LIST".$i} = "<a href='order_view.php?{$addstring2}&order_num=$row[order_num]{$addstring}' onfocus='this.blur();' title='상세내역보기'><font class=eng>$row[$fd]</font></a>"; 
			else ${"LIST".$i} = $row[$fd];      		 
		}
	 
		$ORDER_NUM = $row['order_num'];

		if($row['status']=='Z') {
			$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$ORDER_NUM}'";
		}
		else {
			$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$ORDER_NUM}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D')";
		}
		$LIST5 = $mysql->get_one($sql);	
		$LIST6 = number_format($LIST6,$ckFloatCnt);	 

		switch($LIST7) {
			case "B" :
				$bank_name = explode(",",$row['bank_name']);
				$LIST7 = "무통장<br /><font class='small'>($bank_name[0])</font>";
			break;
			case "C" :
				$LIST7 = "신용카드 <br /><font class='small'>({$pay_arr[$row[pay_status]]})</font>";
			break;
			case "R" :
				$LIST7 = "실시간 계좌이체 <br /><font class='small'>({$pay_arr[$row[pay_status]]})</font>";
			break;
			case "V" :
				$LIST7 = "가상 계좌이체 <br /><font class='small'>({$pay_arr2[$row[pay_status]]})</font>";
			break;
			case "H" :
				$LIST7 = "핸드폰 <br /><font class='small'>({$pay_arr[$row[pay_status]]})</font>";
			break;

		}

		if($row['escrow']=='Y') $LIST7 .= "&nbsp;<img src='img/icon_escrow.gif' border=0 align='absmiddle' alt='에스크로' />";
		if($row['mobile']=='Y') $LIST7 .= "&nbsp;<img src='img/icon_mobile.gif' border=0 align='absmiddle' alt='모바일' />";

		$LIST8 = substr($LIST8,0,16);
		
		if(substr($row['status_date'],0,4)!='0000') {
			$SDATE = "<br />".substr($row['status_date'],0,16);
		}
		else $SDATE = '';
	  	  
		$LIST10 = $status_arr[$row['status']]; 

		if($row['carr_info']) {
			$tmps = explode("|",$row['carr_info']);			
			$CARR_INFO = "<br /><a href='{$DELI_ARR[$tmps[0]]}{$tmps[1]}' target='blank' title='배송조회하기'><font class='eng'>{$tmps[1]}</font></a>";
		} 
		else $CARR_INFO = "";

		$TSUM = number_format($row['sum'] + $row['use_reserve'] + $row['use_cupon'],$ckFloatCnt);

		if($row['use_reserve']>0) $RESERVE = "<br>({$TSUM} - <img src='../shopping/img/icon_point.gif' align='absmiddle' style='margin-top:2px;'>".number_format($row['use_reserve'],$ckFloatCnt);
		else $RESERVE = "";

		if($row['use_cupon']>0) {
			if($RESERVE) $RESERVE .= " - <img src='../shopping/img/icon_cupon.gif' align='absmiddle' style='margin-top:2px;'>".number_format($row['use_cupon'],$ckFloatCnt).")";
			else $RESERVE = "<br>({$TSUM} - <img src='../shopping/img/icon_cupon.gif' align='absmiddle' style='margin-top:2px;'>".number_format($row['use_cupon'],$ckFloatCnt);
		}
		else if($RESERVE) $RESERVE .= ")";
		
		if($LIST3 && $LIST3!='guest' && $LIST3!='del') $tpl->parse("is_crm","1");      
		$tpl->parse("is_man2","1");
		$tpl->parse("loop");
		$tpl->parse("is_crm","2");
		$v_num--;
	}

	$GS_LIST = "선택";
	foreach ($status_arr as $k => $v) {
		if($k=='DEL') continue; 
		$GS_LIST .= "<option value='{$k}'>{$v}</option>";
	}
	$tpl->parse("is_sec");

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?".$addstring3); 
	$PAGING = $pg->print_page();  //페이징 
} 
else $tpl->parse("is_loop");
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 
	
$PAGE = "$page/$total_page";
$ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";   //검색 경로
$CANCEL = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

 include "../html/bottom_inc.html"; // 하단 HTML?>