<?
$tpl->define("main","{$skin}/order_03.html");
$tpl->scan_area("main");

$order_num = $_GET['order_num'];
if(!$order_num) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');
/**************************** GOOD LIST**************************/
$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}'";
$mysql->query($sql);

$TCNT = $total = $carr = $trese = 0;
while($data = $mysql->fetch_array()){
	
	if($data['sale_vls']){
		$tmps = explode("|",$data['sale_vls']);
		$MY_SALE = $tmps[0];
		$MY_POINT = $tmps[1];
		$my_carr = $tmps[2];
		$my_type1 = $tmps[3];
		$my_type2 = $tmps[4];
	}
	else {
		$MY_SALE = $MY_POINT = 0;
		$my_carr = 'N';
	}

	$gData	= getDisplayOrder2($data);		// 디스플레이 정보 가공 후 가져오기

	$LINK	= $gData['link'];
	$IMAGE	= $gData['image'];
	$OIMAGE	= $gData['simage'];
	$NAME	= $gData['name'];	
	$ICON	= $gData['icon'];	
	$OPTION = $gData['option'];
	$PRICE	= $gData['p_price'];
	$OP_PRICE	= $gData['p_op_price'];
	$UID	= $data['uid']; 
	$QTY	= $data['p_qty'];
	$UNIT	= $gData['unit'];
	$RESE	= $gData['p_reserve'];
	$SUM	= $gData['p_sum'];
	$LOC = getLocation($data[p_cate],'1');
	$total = $total+($gData['sum']);

	$trese += $gData['reserve'];
	$tsale += $gData['sale'];

    if($MY_SALE>0) {		
		$TTL = $my_type1;
		$tpl->parse("is_my_sale","1");
		$tpl->parse("is_my_sale2","1");
	}
	if($MY_POINT>0) {
		$TTL = $my_type2;
		$tpl->parse("is_my_point","1");
		$tpl->parse("is_my_point2","1");
	}

	if($gData['carr']) {
		if($gData['carr']=='F') { 
			$my_carr = 'Y';
			$tpl->parse("is_my_free","1");
			$tpl->parse("is_my_free2","1");
		}
		else { 
			$CARR = number_format($gData['carr']);
			$tpl->parse("is_my_carr","1");
			$tpl->parse("is_my_carr2","1");
		}
	}	
	
	for($i=0,$cnt=count($gData['op_sec_vls']);$i<$cnt;$i++){
		if($gData['op_sec_vls'][$i]) {	
			$OP_SEC_VLS = $gData['op_sec_vls'][$i];
			$tpl->parse('loop_op');
			$tpl->parse('loop_op2');
		}		
	}

	$tpl->parse('loop_cart');	   
	$tpl->parse('loop_op','2');
	$tpl->parse("is_my_sale","2");
	$tpl->parse("is_my_point","2");
	$tpl->parse("is_my_carr","2");
	$tpl->parse("is_my_free","2");
	$tpl->parse('loop_cart2');	  
	$tpl->parse("is_my_free","2");
	$tpl->parse("is_my_free2","2");
	$tpl->parse('loop_op2','2');
	$tpl->parse("is_my_sale2","2");
	$tpl->parse("is_my_point2","2");
	$tpl->parse("is_my_carr2","2");
	$tpl->parse("is_my_free2","2");
	$TCNT++;
}

if($TCNT<1) alert("주문한 내역이 없습니다. \\n 다시 확인 해 보시고 이상이 있으시면 관리자에게 연락 주시기 바랍니다.",'back');

$sql = "SELECT * FROM mall_order_info WHERE order_num = '{$order_num}'";
$info = $mysql->one_row($sql);

$TIME = $info['signdate'];
$NUMBER = $order_num;

if($info['carriage'] && $info['carriage'] >0 ) {
	$TCARR = number_format($info['carriage'],$ckFloatCnt);
    $carr = $info['carriage'];	
} 
else $TCARR = "0";

if($info['use_reserve']>0) { 
	$RESERVE = number_format($info['use_reserve'],$ckFloatCnt);
	$tpl->parse("is_reserve");
	$tpl->parse("is_reserve2");
}

if($info['use_cupon']>0) { 
	$stype_arr = Array('P'=>'%','W'=>'원');
	$CUPON = number_format($info['use_cupon'],$ckFloatCnt);

	$tmp_cupon = explode(",",$info['cupon']);
	for($c=0;$c<count($tmp_cupon);$c++) {
		$sql = "SELECT a.gid, b.name, b.sale, b.stype FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$my_id}' && a.pid=b.uid && a.uid='{$tmp_cupon[$c]}'";
		$tmps = $mysql->one_row($sql);

		if($c>0) $C_NAME	= ", ".stripslashes($tmps['name']);
		else $C_NAME	= stripslashes($tmps['name']);
		$C_SALE	= number_format($tmps['sale'],$ckFloatCnt);
		$C_STYPE = $stype_arr[$tmps['stype']];
		$tpl->parse("loop_cupon");
		$tpl->parse("loop_cupon2");
	}	
	$tpl->parse("is_cupon");
	$tpl->parse("is_cupon2");
}

$TOTAL	= number_format($total,$ckFloatCnt);
$TRESE	= number_format($trese,$ckFloatCnt);
if($tsale>0) {
	$TSALE	= number_format($tsale,$ckFloatCnt);
	$tpl->parse("is_tsale");
	$tpl->parse("is_tsale2");
}

if($info['cash_sale']) {
	$cashdc = round(($total * $info['cash_sale'])/100,$ckFloatCnt);
	if($ckFloatCnt==0) $cashdc = numberLimit($cashdc,1);
	$CASHDC = number_format($cashdc,$ckFloatCnt);
	$CASHDC2 = $info['cash_sale'];
	$tpl->parse("is_cash_dc");
	$tpl->parse("is_cash_dc2");
}

$C_TOTAL= $total+$carr;
$TOTAL2 = number_format($C_TOTAL,$ckFloatCnt);
$C_TOTAL2 = $total+$carr-$cashdc;
$TOTAL3 = number_format($C_TOTAL2-$info['use_reserve']-$info['use_cupon'],$ckFloatCnt);

for($i=0,$cnt=strlen($TOTAL2);$i<$cnt;$i++){
	if($TOTAL2[$i]==',') $IMG_TOTAL .= "<img src='img/shop/star_jum2.gif' />";
	else $IMG_TOTAL .= "<img src='img/shop/star_num{$TOTAL2[$i]}.gif' />";
}

$TOTAL2 = $TOTAL2;

$name = stripslashes($info['name1']);
$NAME = stripslashes($info['name2']);
$TEL = stripslashes($info['tel2']);
$PHONE = stripslashes($info['hphone2']);
$ZIPCODE = $info['zipcode'];
$ADDRESS = stripslashes($info['address']);
$MESSAGE = stripslashes($info['message']);

$pay_arr = Array('A'=>'결제미완료','B'=>'결제성공','C'=>'결제실패');
$pay_arr2 = Array('A'=>'계좌발급완료','B'=>'계좌입급완료','C'=>'결제실패');

switch($info['pay_type']) {
	case "B" :
		$CASH_TYPE = "무통장 입금";
		$bank_info = explode(",",$info['bank_name']);
		$BANK_NAME ="{$bank_info[0]} : {$bank_info[1]} , 예금주 : {$bank_info[2]}";
		$PAY_NAME = stripslashes($info['pay_name']);
		$PAY_DATE = stripslashes($info['pay_date']);

		if($info['cash_info']!='') {
			if(substr($info['cash_info'],0,1)==1) $cash_info = "소득공제용 : ".substr($info['cash_info'],2);
			else $cash_info = "지출증빙용 : ".substr($info['cash_info'],2);
			$CASH_INFO = "현금영수증 발급신청 ({$cash_info})";
			$tpl->parse("is_bank_cash");
			$tpl->parse("is_bank_cash2");
		}

		$tpl->parse("is_bank");
		$tpl->parse("is_bank2");
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
		$tpl->parse("is_card2");	
	break;
	case "V" :
		$CASH_TYPE .= "가상 계좌이체({$pay_arr2[$info[pay_status]]})";
		$CARD_INFO = stripslashes($info['pay_info']);
		$tpl->parse("is_card");	
		$tpl->parse("is_card2");	
	break;
	case "H" :
		$CASH_TYPE .= "핸드폰({$pay_arr[$info[pay_status]]})";
		$CARD_INFO = stripslashes($info['pay_info']);
		$tpl->parse("is_card");	
		$tpl->parse("is_card2");	
	break;
}

if($info['escrow']=='Y') {
	$CASH_TYPE .= " [에스크로]";
}


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();


if($info['send_ok']=='N') {
	socketPost("http://".$_SERVER["HTTP_HOST"]."/".$ShopPath."php/order_mail.php?order_num={$order_num}");
}

?>