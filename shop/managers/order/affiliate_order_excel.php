<?
$file_name = "itsMallAffiliateOrderList_".date("Ymd",time());
header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-Disposition: attachment; filename={$file_name}.xls" ); 
header( "Content-Description: Gamza Excel Data" ); 
header( "Content-type: application/vnd.ms-excel;charset=utf-8" ); 

######################## lib include
include "../ad_init.php";

require "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

$type = $_GET['type'];

if($type!='check') {
	###################### 변수 정의 ##########################
	$order_num	= $_GET['order_num'];
	$gs			= $_GET['gs'];
	$field		= $_GET['field'];
	$word		= $_GET['word'];
	$sdate1		= $_GET['sdate1'];
	$sdate2		= $_GET['sdate2'];
	$page		= $_GET['page'];
	$limit		= $_GET['limit'];
	$type		= $_GET['type'];
	$status		= $_GET['status'];
	$affiliates	= $_GET['affiliates'];

	if($gs) {
		$where .= "&& b.order_status='{$gs}'";
	}

	if($field && $word) {
		if($field=="multi") {
			$where .= "&& (INSTR(a.order_num,'{$word}') || INSTR(a.id,'{$word}') || INSTR(a.name1,'{$word}') || INSTR(a.name2,'{$word}') || INSTR(a.pay_name,'{$word}') || INSTR(b.p_name,'{$word}'))";
		}
		else if($field=="p_name") $where .= "&& INSTR(b.{$field},'{$word}')";
		else $where .= "&& INSTR(a.{$field},'{$word}')";
	}  else $field = "order_num";

	if($sdate1 && $sdate2) {	
		if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
		if($sdate1==$sdate2) $where .= "&& INSTR(a.signdate,'{$sdate1}') ";
		else $where .= "&& (a.signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(a.signdate,'{$sdate2}'))";		
	}  

	if($type) {
		if($type=='E') $where .= " && b.escrow='Y' ";
		else $where .= " && b.pay_type='{$type}' ";
	}

	if($status) {
		$where .= " && a.pay_status='{$status}' ";
	}

	if($affiliates) {
		$where .= " && a.affiliate = '{$affiliates}'";
		$where2 .= " && affiliate = '{$affiliates}'";	
	}
	else {
		$where .= " && a.affiliate !=''";
		$where2 .= " && affiliate !=''";
	}
}

$skin = ".";
$mysql = new  mysqlClass(); //디비 클래스

$tpl = new classTemplate;
$tpl->define("main","affiliate_order_excel.html");
$tpl->scan_area("main");

/*********************************** QUERY **********************************/
if($type=='check') {
	$item = $_POST['item'];
	if(!$item) exit;
	
	for($i=0,$cnt=count($item);$i<$cnt;$i++){
		if($i==0) $where3 = " a.order_num = '{$item[$i]}' ";
		else $where3 .= " || a.order_num = '{$item[$i]}' ";
	}

	if($where3) $where3 = " && ({$where3}) ";
	
	$query = "SELECT a.order_num, a.p_name, a.p_qty, a.p_price, a.op_price, a.sale_price, a.order_status as status, a.carr_info FROM mall_order_goods as a, mall_order_info as b WHERE a.order_num=b.order_num {$where3} ORDER BY a.uid DESC";
}
else {
	if(eregi("b.",$where)) {
		$query = "SELECT a.order_num, a.p_name, a.p_qty, a.p_price, a.op_price, a.sale_price, a.order_status as status, a.carr_info FROM mall_order_goods as a, mall_order_info as b WHERE a.order_num=b.order_num {$where} ORDER BY a.uid DESC";
	}
	else {
		$query = "SELECT a.order_num, a.p_name, a.p_qty, a.p_price, a.op_price, a.sale_price, a.order_status as status, a.carr_info FROM mall_order_goods as a WHERE a.uid!='' {$where} ORDER BY a.uid DESC";
	}
	
}
$mysql->query($query);		
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/

$pay_arr = Array('A'=>'결제미완료','B'=>'결제성공','C'=>'결제실패','D'=>'카드취소');
$pay_arr2 = Array('A'=>'계좌발금완료','B'=>'계좌입금완료','C'=>'입금실패','D'=>'환불');
$pay_arr3 = Array('A'=>'미입금','B'=>'입금완료','C'=>'입금실패','D'=>'환불');

$tmp_order_num = '';
$NUM = 1;
while ($row=$mysql->fetch_array()){
	
	if($tmp_order_num!=$row['order_num']) {
		$sql = "SELECT uid, order_num, id, name1, pay_type, pay_status, order_status, use_cupon, affiliate, a_commi, use_reserve, carriage, pay_total, signdate FROM mall_order_info WHERE order_num='{$row['order_num']}'";
		$row2 = $mysql->one_row($sql);
		
		$LIST1 = $row2['order_num'];
		$LIST2 = substr($row2['signdate'],0,16); 		
		$LIST3 = stripslashes($row2['affiliate']);
		$LIST4 = stripslashes($row2['name1']);
		$LIST42 = stripslashes($row2['id']);
		if($LIST42) $LIST4 .= " ({$LIST42})";

		$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$row['order_num']}' {$where2}";
		$goods_cnt = $mysql->get_one($sql);
		if($goods_cnt>1) $ROWSPAN = "rowspan='{$goods_cnt}'";
		else $ROWSPAN='';

		switch($row2['pay_type']) {
			case "B" :
				if($row2['order_status']=='A') $pay_status = "입금대기";
				else if($row2['order_status']=='O' || $row2['order_status']=='P') $pay_status = "미입금/환불";
				else $pay_status = "입금완료";
				$LIST11 = "무통장({$pay_status})";
			break;
			case "C" :
				if(($row2['order_status']=='O' || $row2['order_status']=='P') && $row2['pay_status']=='B') $row2['pay_status'] = 'D';
				$LIST11 = "신용카드 ({$pay_arr[$row2[pay_status]]})";
			break;
			case "R" :
				if(($row2['order_status']=='O' || $row2['order_status']=='P') && $row2['pay_status']=='B') $row2['pay_status'] = 'D';
				$LIST11 = "실시간 계좌이체 ({$pay_arr3[$row2[pay_status]]})";
			break;
			case "V" :
				if(($row2['order_status']=='O' || $row2['order_status']=='P') && $row2['pay_status']=='B') $row2['pay_status'] = 'D';
				$LIST11 = "가상 계좌이체 ({$pay_arr2[$row2[pay_status]]})";
			break;
			case "H" :
				if($row2['order_status']=='O' || $row2['order_status']=='P') $row2['pay_status'] = 'D';
				$LIST11 = "핸드폰 ({$pay_arr[$row2[pay_status]]})";
			break;

		}
		if($row['escrow']=='Y') $LIST11 .= "&nbsp;(에스크로)";
		
		$LIST9 = number_format($row2['use_cupon'],$ckFloatCnt);
		if($row2['a_commi']>0) {
			$LIST10 = number_format((($row2['pay_total'] + $row2['use_reserve'] - $row2['carriage'])*$row2['a_commi'])/100,$ckFloatCnt)."원 ({$row2['a_commi']}%)";
		}
		else $LIST10 = 0;
	}		

	$LIST5 = stripslashes($row['p_name']);	
	$LIST6 = number_format($row['p_qty']);	
	$LIST7 = number_format((($row['p_price'] + $row['op_price']) * $row['p_qty']) - $row['sale_price'],$ckFloatCnt);
	if($row['status2']) {
		$LIST8 = $status_arr2[$row['status'].$row['status2']]; 
	}
	else $LIST8 = $status_arr[$row['status']]; 
	
	if($tmp_order_num!=$row['order_num']) {			
		$tpl->parse("type1");
		$tmp_order_num = $row['order_num'];					
		$NUM++;
	}
	else {			
		$tpl->parse("type2");
	}

	$tpl->parse("loop");				
	$tpl->parse("type1","2");		
	$tpl->parse("type2","2");			
}

$tpl->parse("main");
$tpl->tprint("main");
?>