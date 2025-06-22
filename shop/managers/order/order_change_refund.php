<?
######################## lib include
include "../ad_init.php";
include "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

$skin = ".";

$order_num = $_GET['order_num'];
$uid = $_GET['uid'];

if(!$order_num || !$uid) alert("정보가 넘어오지 못했습니다.","close5");

$sql = "SELECT * FROM mall_order_change WHERE uid='{$uid}' && order_num='{$order_num}'";
$row = $mysql->one_row($sql);
if(!$row) alert("취소/반품 내역이 존재 하지 않습니다","close5");
$sgoods	= $row['sgoods'];
$bank_info = explode(" ",$row['bank_info']);
$rbank = $bank_info[0];
$rbank_num = $bank_info[1];
$rbank_name = $bank_info[2];
$memo = $row['message'];

unset($row);

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","order_change_refund.html");
$tpl->scan_area('main');

$sql = "SELECT * FROM mall_order_info WHERE order_num = '{$order_num}'";
$info = $mysql->one_row($sql);

$TIME = $info['signdate'];
$NUMBER = $order_num;
$NAME1 = stripslashes($info['name1']);


if($info['carriage'] && $info['carriage'] >0 ) {
	$TCARR = number_format($info['carriage'],$ckFloatCnt)."원";
    $carr = $info['carriage'];
} 
else $TCARR = "0원";

if($info['use_reserve']>0) { 
	$RESERVE = number_format($info['use_reserve'],$ckFloatCnt)."원";	
	$tpl->parse("is_reserve");
}

if($info['id'] && $info['id']!='guest' && $info['id']!='del') {
	$MID = $info['id'];
	$tpl->parse("is_id");
}

if($info['cupon']) {	
	$stype_arr = Array('P'=>'%','W'=>'원');
	$CUPON = number_format($info['use_cupon'],$ckFloatCnt)."원";
	$tmp_cupon = explode(",",$info['cupon']);

	$C_LINK = "../member/cupon_down_list.php?field=id&word={$info['id']}";

	for($c=0;$c<count($tmp_cupon);$c++) {
		$sql = "SELECT a.gid, b.name, b.sale, b.stype FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$info['id']}' && a.pid=b.uid && a.uid='{$tmp_cupon[$c]}'";
		$tmps = $mysql->one_row($sql);

		$C_NAME	= stripslashes($tmps['name']);
		$C_SALE	= number_format($tmps['sale'],$ckFloatCnt);
		$C_STYPE = $stype_arr[$tmps['stype']];

		if($tmps['gid']) {
			$sql = "SELECT count(*) FROM mall_order_goods WHERE p_number='{$tmps['gid']}' && order_num='{$order_num}' && !(order_status='Z' && order_status!='A') && !(order_status='X' && order_status!='A')";
			if($mysql->get_one($sql)==0) {
				$C_NAME = $C_NAME."[주문 부분 취소에 따른 쿠폰 사용취소]"; 	
			}
		}
		else {
			if(($info['use_cupon'] - goodsCuponUse($order_num))==0){
				$C_NAME = $C_NAME."[주문 부분 취소에 따른 쿠폰 사용취소]"; 	
			}
		}		
		$tpl->parse("loop_cupon");
	}	
	$tpl->parse("is_cupon");
}


$total = $info['pay_total'];
$TOTAL3 = number_format($total,$ckFloatCnt)."원";

if($MY_CARR=='Y') $tpl->parse("is_mcarr");
$TOTAL2 = $TOTAL2."원";

$tmps = htmlspecialchars(stripslashes($info['carr_info']));
$tmps = explode("|",$tmps);

$CARR_NUM = $tmps[1];
$sql = "SELECT uid,name,code FROM mall_design WHERE mode='Z'  ORDER BY uid ASC";
$mysql->query($sql);
for($i=0; $row = $mysql->fetch_object(); $i++) {
	if($tmps[0]==$row->uid) $DELIVERY .= "<option value='{$row->uid}' selected>".stripslashes($row->name)."</option>\n";
	else $DELIVERY .= "<option value='{$row->uid}'>".stripslashes($row->name)."</option>\n";
}


$pay_arr = Array('A'=>'결제미완료','B'=>'결제성공','C'=>'결제실패');
$pay_arr2 = Array('A'=>'계좌발급완료','B'=>'계좌입금완료','C'=>'결제실패');

switch($info['pay_type']) {
	case "B" :
		$CASH_TYPE = "무통장 입금";
		$bank_info = explode(",",$info['bank_name']);
		$BANK_NAME ="{$bank_info[0]} : {$bank_info[1]} , 예금주 : {$bank_info[2]}";
		$PAY_NAME = stripslashes($info['pay_name']);
		$PAY_DATE = stripslashes($info['pay_date']);

		if($info['cash_info']!='') {
			if(substr($info['cash_info'],0,1)==1){
				$cash_info = "소득공제용 : ".substr($info['cash_info'],2);
			}
			else {
				$cash_info = "지출증빙용 : ".substr($info['cash_info'],2);
			}
			if($info['order_status']=='A') {
				$CASH_INFO = "현금영수증 발급신청 [{$cash_info}]";
			}
			else {
				$sql = "SELECT * FROM mall_order_cash WHERE order_num='{$order_num}'";
				$cash_row = $mysql->one_row($sql);
				if($cash_row['status']=='A') {
					$CASH_INFO = "현금영수증 발급실패 [{$cash_row['receipt_error']}]";
				}
				else if($cash_row['status']=='B') {
					$CASH_INFO = "현금영수증 발급완료 [승인번호 : <a href='cash_tax_list.php?field=order_num&word={$order_num}' title='현금영수증발급/조회'>{$cash_row['receipt_no']}</a>]";
				}
				else if($cash_row['status']=='C') {
					$CASH_INFO = "현금영수증 발급취소 [승인번호 : <a href='cash_tax_list.php?field=order_num&word={$order_num}' title='현금영수증발급/조회'>{$cash_row['receipt_no']}</a>]";
				}
			}
			if($CASH_INFO) $tpl->parse("is_bank_cash");
		}
		else {
			$sql = "SELECT * FROM mall_order_cash WHERE order_num='{$order_num}'";
			$cash_row = $mysql->one_row($sql);
			
			if($cash_row['status']=='A') {
				if($cash_row['cash_type']=='A') $cash_info = "소득공제용 : {$cash_row['auth_number']}";
				else $cash_info = "지출증빙용 : {$cash_row['auth_number']}";
				$CASH_INFO = "현금영수증 발급신청 [{$cash_info}]";
			}
			else if($cash_row['status']=='B') {
				$CASH_INFO = "현금영수증 발급완료 [승인번호 : <a href='cash_tax_list.php?field=order_num&word={$order_num}' title='현금영수증발급/조회'>{$cash_row['receipt_no']}</a>]";
			}
			else if($cash_row['status']=='C') {
				$CASH_INFO = "현금영수증 발급취소 [승인번호 : <a href='cash_tax_list.php?field=order_num&word={$order_num}' title='현금영수증발급/조회'>{$cash_row['receipt_no']}</a>]";
			}

			if($CASH_INFO) $tpl->parse("is_bank_cash");
		}

		$tpl->parse("is_bank");
	break;
	case "C" :
		$CASH_TYPE .= "신용카드({$pay_arr[$info[pay_status]]})";
		$CARD_INFO = stripslashes($info['pay_info']);
		$tpl->parse("is_card");		
		$tpl->parse("is_card2");				
	break;
	case "R" :
		$CASH_TYPE .= "실시간 계좌이체({$pay_arr[$info[pay_status]]})";
		$CARD_INFO = stripslashes($info['pay_info']);
		$tpl->parse("is_card");			
	break;
	case "V" :
		$CASH_TYPE .= "가상 계좌이체({$pay_arr2[$info[pay_status]]})";
		$CARD_INFO = stripslashes($info['pay_info']);
		$tpl->parse("is_card");			
	break;
	case "H" :
		$CASH_TYPE .= "핸드폰({$pay_arr[$info[pay_status]]})";
		$CARD_INFO = stripslashes($info['pay_info']);
		$tpl->parse("is_card");			
	break;
}

if($info['escrow']=='Y') {
	$CASH_TYPE .= " [에스크로]";
}


$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$sgoods})";
$mysql->query($sql);

$ii = 1;
$PAY_SUM = $CARR_SUM = 0;
while($data = $mysql->fetch_array()){

	if($data['sale_vls']){
		$tmps = explode("|",$data['sale_vls']);
		$MY_SALE = $tmps[0];
		$MY_POINT = $tmps[1];
		$MY_CARR = $tmps[2];
		$my_type1 = $tmps[3];
		$my_type2 = $tmps[4];
	}
	else {
		$MY_SALE = $MY_POINT = 0;
		$MY_CARR = 'N';
	}
	
	$gData	= getDisplayOrder2($data);	
	$NAME	= $gData['name'];		
	$QTY	= $data['p_qty'];
	$SUM	= $gData['p_sum'];
	if($gData['carr']=='F') $gData['carr'] = 0;
	$CARR	= number_format($gData['carr']*$data['p_qty']);	
	$CARR_SUM += ($gData['carr']*$data['p_qty']);
	$PAY_SUM += $gData['sum'];
	
	
	$OP_SEC_VLS = '';
	for($i=0,$cnt=count($gData['op_sec_vls']);$i<$cnt;$i++){
		if($gData['op_sec_vls'][$i]) {	
			$OP_SEC_VLS .= $gData['op_sec_vls'][$i];
		}		
	}
	$STATUS = $status_arr2[$data['order_status'].$data['order_status2']];
	$tpl->parse("loop_change");
	
	$ii++;
}

$PAY_SUM2 = number_format($PAY_SUM);
$CARR_SUM2 = number_format($CARR_SUM);
$PAY_TOTAL = $CARR_SUM+$PAY_SUM;
$PAY_SUM3 = number_format($PAY_TOTAL);

$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$order_num}' && uid NOT IN({$sgoods})";
$empty = $mysql->get_one($sql);

if($empty==0) {
	$tmps = str_replace(array(",","원"),"",$TOTAL3);
	$TOTALS = "(".number_format($tmps + $PAY_TOTAL)." - ".number_format($PAY_TOTAL).")";

	if($CARR_SUM>0) {
		$tmps = str_replace(array(",","원"),"",$TCARR);
		$TCARRS = "(".number_format($tmps + $CARR_SUM)." - ".number_format($CARR_SUM).")";
	}
}

$tpl->parse("main");
$tpl->tprint("main");
?>