<?
// 변수 지정
if($channel=='osearch') {
	$name = isset($_POST['name']) ? $_POST['name']:$_GET['name'];
	$order_num = isset($_POST['order_num']) ? $_POST['order_num']:$_GET['order_num'];
	$name = addslashes($name);
	$order_num = addslashes(trim($order_num));

	if(!$name || !$order_num) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');
	
	$sql = "SELECT count(*) FROM mall_order_info WHERE name1 = '{$name}' && order_num = '{$order_num}'";
	if($mysql->get_one($sql)==0) alert('주문내역이 존재 하지 않습니다. \\n 주문자명과 주문번호를 다시 한번 확인 해 보시기 바랍니다.','back');
	
	$tpl->define("main","{$skin}/customer_osearch.html");
}
else {
	if(!$my_id) alert('먼저 로그인을 하시기 바랍니다.','back');
	$order_num = isset($_POST['order_num']) ? $_POST['order_num']:$_GET['order_num'];
	if(!$order_num) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');

	$sql = "SELECT count(*) FROM mall_order_info WHERE order_num = '{$order_num}' && id='{$my_id}'";
	if($mysql->get_one($sql)==0) alert("주문이 삭제되었거나 존재하지 않습니다. 다시 확인 해 보시기 바랍니다.","back");

	$page	= isset($_GET['page']) ? $_GET['page'] : 1;
	$sdate1	= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
	$sdate2	= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
	$status	= isset($_GET['status']) ? $_GET['status'] : $_POST['status'];

	$addstring = "&amp;page={$page}";
	if($status) {
		$addstring .= "&amp;status={$status}";
	}
	if($sdate1 && $sdate2) {	
		if($sdate1 > $sdate2) {$sdate1 = $tmp; $sdate1 = $sdate2; $sdate2 = $tmp;}
		$addstring .= "&amp;sdate1=$sdate1&amp;sdate2=$sdate2";	
	}  

	
	$tpl->define("main","{$skin}/mypage_order_detail.html");
}
$tpl->scan_area("main");

$sql	= "SELECT code FROM mall_design WHERE mode='T'";
$tmp	= $mysql->get_one($sql);
$etc_info = explode("|",$tmp);
$EDATE = isset($etc_info[3]) ? $etc_info[3] : 7;
unset($etc_info);

$sql = "SELECT order_status FROM mall_order_info WHERE order_num='{$order_num}'";
$def_status = $mysql->get_one($sql);

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
	
    if($MY_SALE>0) {		
		$TTL = $my_type1;
		$tpl->parse("is_my_sale","1");
	}
	if($MY_POINT>0) {
		$TTL = $my_type2;
		$tpl->parse("is_my_point","1");
	}

	if($gData['carr']) {
		if($gData['carr']=='F') { 
			$my_carr = 'Y';
			$tpl->parse("is_my_free","1");
		}
		else { 
			$CARR = number_format($gData['carr']);
			$tpl->parse("is_my_carr","1");	
		}
	}	
	
	for($i=0,$cnt=count($gData['op_sec_vls']);$i<$cnt;$i++){
		if($gData['op_sec_vls'][$i]) {	
			$OP_SEC_VLS = $gData['op_sec_vls'][$i];
			$tpl->parse('loop_op');
			$tpl->parse('loop_op2');
		}		
	}

	if($data['order_status']=='X' || $data['order_status']=='Y' || $data['order_status']=='Z') $STEP = $status_arr2[$data['order_status'].$data['order_status2']];
	else $STEP = $status_arr[$data['order_status']];

    $LTIME = substr($data['status_date'],0,16);
	$LTIME = str_replace(" ","<br />",$LTIME);

	if($data['order_status']=='Y' && $data['order_status2']=='D') {
		$tmps = explode("|",$data['carr_info']);
		$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
		$tmp = $mysql->one_row($sql);
		$CLINKY = $tmp['code'];
		$CNUMY = $tmps[1];
		$tpl->parse("is_carrY");
	}

	if($def_status!=$data['order_status'] || $def_status=='Z') {
		$DISA = "disabled";
		$ck_cancel = 'Y';
	}
	else $DISA = "";

	$tpl->parse('loop_cart');	   
	$tpl->parse('loop_op','2');
	$tpl->parse("is_my_sale","2");
	$tpl->parse("is_my_point","2");
	$tpl->parse("is_my_carr","2");
	$tpl->parse("is_my_free","2");
	$tpl->parse("is_carrY","2");
	
	if(($data['order_status']!='Z' && $data['order_status']!='X') || $def_status=='Z' || $data['order_status2']=='A') {
		$total += $gData['sum'];
		$trese += $gData['reserve'];
		$tsale += $gData['sale'];
		$TCNT++;
	}
}

if($def_status=='A' || $def_status=='B' || $def_status=='C') $tpl->parse("is_statusZ");
if($def_status=='D' || $def_status=='E') {
	$tpl->parse("is_statusX");
	$tpl->parse("is_statusY");
}

//if($TCNT<1) alert("주문한 내역이 없습니다. \\n 다시 확인 해 보시고 이상이 있으시면 관리자에게 연락 주시기 바랍니다.",'back');

$sql = "SELECT * FROM mall_order_info WHERE order_num = '$order_num'";
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
}

if($info['cupon']) {	
	$stype_arr = Array('P'=>'%','W'=>'원');
	$CUPON = number_format($info['use_cupon'],$ckFloatCnt);
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



$TOTAL	= number_format($total,$ckFloatCnt);
$TRESE	= number_format($trese,$ckFloatCnt);
if($tsale>0) {
	$TSALE	= number_format($tsale,$ckFloatCnt);
	$tpl->parse("is_tsale");
}

if($info['cash_sale']) {
	$cashdc = round(($total * $info['cash_sale'])/100,$ckFloatCnt);
	if($ckFloatCnt==0) $cashdc = numberLimit($cashdc,1);
	$CASHDC = number_format($cashdc,$ckFloatCnt);
	$CASHDC2 = $info['cash_sale'];
	$tpl->parse("is_cash_dc");
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

$tpl->parse("is_list");

$ONAME = stripslashes($info['name1']); 
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

		$sql = "SELECT * FROM mall_order_cash WHERE order_num='{$order_num}'";		
		$cash_row = $mysql->one_row($sql);

		if($cash_row['status'] || $info['cash_info']!='') {	
			if($cash_row['status']=='A'  || (!$cash_row['status'] && $info['cash_info']!='')) {
				if($info['cash_info']!='') {
					if(substr($info['cash_info'],0,1)==1) $cash_info = "소득공제용 : ".substr($info['cash_info'],2);
					else $cash_info = "지출증빙용 : ".substr($info['cash_info'],2);
				}
				else {
					if($cash_row['cash_type']=='A') $cash_info = "소득공제용 : {$cash_row['auth_number']}";
					else $cash_info = "지출증빙용 : {$cash_row['auth_number']}";
				}
				$CASH_INFO = "현금영수증 발급요청 중 ({$cash_info})";
			}
			else if($cash_row['status']=='B') {				
				$CASH_INFO = "현금영수증 발급완료";
				$SHOP_ID = $cash[3];
				$receipt_no = $cash_row['receipt_no'];
				$tpl->parse("is_cash_print");
			}
			else if($cash_row['status']=='C') {
				$CASH_INFO = "현금영수증 발급취소";
			}	
			$tpl->parse("is_bank_cash");
		}
		else if($info['order_status']!='Z') {			
			$sql = "SELECT code FROM mall_design WHERE mode='O'";
			$code = $mysql->get_one($sql);
			if($code) {
				$code = explode("|",stripslashes($code));
				if($code[0]==1 && $code[1]==2) {
					if($info['signdate'] >= date("Y-m-d H:i:s",time()-(86400*$code[2]))) {
						$tpl->parse("is_bank_cash2");
						$tpl->parse("is_bank_cash3");
					}
				}
			}
		}
		$tpl->parse("is_bank");
	break;
	case "C" :
		$CASH_TYPE .= "신용카드({$pay_arr[$info[pay_status]]})";
		$CARD_INFO = stripslashes($info['pay_info']);
		if($info['pay_number'] && $info['pay_status']=='B') {
			$tno = $info['pay_number'];
			$tpl->parse("is_card_print");
		}
		$tpl->parse("is_card");			
	break;
	case "R" :
		$CASH_TYPE .= "실시간 계좌이체({$pay_arr[$info[pay_status]]})";
		$CARD_INFO = stripslashes($info['pay_info']);
		if($info['pay_number'] && $info['pay_status']=='B') {
			$tno = $info['pay_number'];
			$tpl->parse("is_rbank_print");
		}
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

$STATUS = $status_arr[$info['order_status']];
if($info['order_status']=='D') {
	if($info['carr_info']) {
		$sql = "SELECT uid,code FROM mall_design WHERE mode='Z'";
		$mysql->query($sql);
		while($row = $mysql->fetch_array()){
			$DELI_ARR[$row['uid']] = $row['code'];
		}

		$tmps = explode("|",$info['carr_info']);
		$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
		$tmp = $mysql->one_row($sql);
		$CLINK = $tmp['code'];
		$CNUM = $tmps[1];
		$tpl->parse("is_carr");
		$CINFO = "<a href='{$DELI_ARR[$tmps[0]]}{$tmps[1]}' target='blank' title='배송조회하기'>{$tmp['name']} 송장번호 : {$CNUM}</a>";
	}
}

if(!$CINFO) $CINFO = "배송준비중이거나 택배회사 송장정보를 등록하지 않았습니다.";

if($info['order_status']=="A") {
	
	if($info['pay_type']=='B' && ($cash[1]=='1' || $cash[17]=='1' || $cash[18]=='1' || $cash[19]=='1')) {
		$O_TOTAL = $C_TOTAL-$info['use_reserve']-$info['use_cupon'];
		$CARDUSE1 = $cash[4];
		$CARDUSE2 = number_format($cash[4],$ckFloatCnt);
		if($cash[1]=='1') $tpl->parse("is_card1");
		if($cash[17]=='1') $tpl->parse("is_card2");
		if($cash[18]=='1') $tpl->parse("is_card3");
		if($cash[19]=='1') $tpl->parse("is_card4");	
		if($cash[16]==1) $tpl->parse("is_test");	
		$tpl->parse("is_pay_change1");	
		$tpl->parse("is_pay_change2");	
		$tpl->parse("is_pay_change4");
	}

	if($info['pay_type']!='B' && $info['pay_status']!='B') {
		$RELINK = "{$Main}?channel=card_pay&amp;cash_type={$info['pay_type']}&amp;order_num={$order_num}";
		$tpl->parse("is_pay_change3");
	}

	if($ck_cancel!='Y') $tpl->parse("is_cancel");
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>