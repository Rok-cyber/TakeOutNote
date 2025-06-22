<?
######################## lib include
include "../ad_init.php";

require "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

$item = $_POST['item'];
if(!$item) alert("정보가 넘어오지 못했습니다.","back");

$item = "'".join("','",$item)."'";
$where .= " && order_num IN({$item}) ";

$skin = ".";

$tpl = new classTemplate;
$tpl->define("main","order_print.html");
$tpl->scan_area("main");

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

/*********************************** QUERY **********************************/
$query = "SELECT order_num,order_status FROM mall_order_info WHERE uid>0 {$where} ORDER BY order_num ASC";
$mysql->query($query);		
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
$cks = 0;
while ($rows=$mysql->fetch_array()){
	
	$def_status = $rows['order_status'];
	$order_num = $rows['order_num'];

	if(!$order_num) continue;
	
	/**************************** GOODS LIST**************************/

	$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' ORDER BY p_cate ASC";
	$mysql->query2($sql);

	$TCNT = $total = $carr = $trese = 0;
	$G_NAME = $G_OP = array();
	while($data = $mysql->fetch_array(2)){

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
		
		$Main = $SMain;
		$gData	= getDisplayOrder2($data);		// 디스플레이 정보 가공 후 가져오기

		$LINK	= $gData['link'];
		$IMAGE	= "<img src='../../{$gData['simage']}' border='0' width='80' height='80'>";
		$NAME	= $gData['name'];	
		$ICON	= $gData['icon'];	
		$OPTION = $gData['option'];
		$PRICE	= $gData['p_price'];
		$OP_PRICE	= $gData['p_op_price'];
		$UID	= $data['uid']; 
		$UID2	= $gData['uid']; 
		$QTY	= $data['p_qty'];
		$UNIT	= $gData['unit'];
		$RESE	= $gData['p_reserve'];
		$SUM	= $gData['p_sum'];
		$LOC = getLocation($data[p_cate],'1');

		$G_NAME[$UID] = $NAME;
		
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
				if($G_OP[$UID]) $G_OP[$UID] .= ", ".$OP_SEC_VLS;			
				else $G_OP[$UID] = $OP_SEC_VLS;			
				$tpl->parse('loop_op');
				$tpl->parse('loop_op2');
			}		
		}
		
		if($data['order_status']=='X' || $data['order_status']=='Y' || $data['order_status']=='Z') $STEP = $status_arr2[$data['order_status'].$data['order_status2']];
		else $STEP = $status_arr[$data['order_status']];

		if($data['order_status']=='Y' && $data['order_status2']=='D') {
			$tmps = explode("|",$data['carr_info']);
			$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
			$tmp = $mysql->one_row($sql);
			$STEP .= "<br />{$tmp['name']}{$tmps[1]}";	
		}

		$LTIME = substr($data['status_date'],0,16);
		$LTIME = str_replace(" ","<br />",$LTIME);

		$tpl->parse('loop_cart');	   
		$tpl->parse('loop_op','2');
		$tpl->parse("is_my_sale","2");
		$tpl->parse("is_my_point","2");
		$tpl->parse("is_my_carr","2");
		$tpl->parse("is_my_free","2");
		
		if(($data['order_status']!='Z' && $data['order_status']!='X' ) || $def_status=='Z' || $data['order_status2']=='A') {	
			$total += $gData['sum'];
			$trese += $gData['reserve'];
			$tsale += $gData['sale'];
			$TCNT++;
		}
		
	}

	$sql = "SELECT * FROM mall_order_info WHERE order_num = '{$order_num}'";
	$info = $mysql->one_row($sql);

	$TIME = $info['signdate'];
	$NUMBER = $order_num;

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

	$TOTAL	= number_format($total,$ckFloatCnt)."원";
	$TRESE	= number_format($trese,$ckFloatCnt)."원";
	if($tsale>0) {
		$TSALE	= number_format($tsale,$ckFloatCnt)."원";
		$tpl->parse("is_tsale");
	}

	if($info['cash_sale']) {
		$cashdc = round(($total * $info['cash_sale'])/100,$ckFloatCnt);
		if($ckFloatCnt==0) $cashdc = numberLimit($cashdc,1);
		$CASHDC = number_format($cashdc,$ckFloatCnt)."원";
		$CASHDC2 = $info['cash_sale'];
		$tpl->parse("is_cash_dc");
	}

	$C_TOTAL= $total+$carr;
	$TOTAL2 = number_format($C_TOTAL,$ckFloatCnt);
	$C_TOTAL2 = $total+$carr-$cashdc;
	$TOTAL3 = number_format($C_TOTAL2-$info['use_reserve']-$info['use_cupon'],$ckFloatCnt)."원";

	if($MY_CARR=='Y') $tpl->parse("is_mcarr");

	if(($C_TOTAL2-$info['use_reserve']-$info['use_cupon']) != $info['pay_total']) {
		$TOTAL3 .= "&nbsp;&nbsp;<font class='orange'>결제금액과 총 합계금액이 다릅니다. 확인 해 보시기 바랍니다!</font>";
	}

	for($i=0,$cnt=strlen($TOTAL2);$i<$cnt;$i++){
		if($TOTAL2[$i]==',') $IMG_TOTAL .= "<img src='img/shop/star_jum2.gif' />";
		else $IMG_TOTAL .= "<img src='img/shop/star_num{$TOTAL2[$i]}.gif' />";
	}

	$TOTAL2 = $TOTAL2."원";


	if(!$pop) {
		$tpl->parse("is_pop_hide2");
	}

	$tpl->parse("is_list");
		
	$TIME = $info['signdate'];
	$ORDER_NUM = $info['order_num']; 
	$NAME1 = stripslashes($info['name1']);
	$TEL= stripslashes($info['tel1']);
	$PHONE = stripslashes($info['hphone1']);
	$EMAIL = stripslashes($info['email']);
	$EMAIL = "<a href='../member/mail_form.html?m_to={$EMAIL}' onfocus='this.blur();'>{$EMAIL}</a>";
	$NAME2 = stripslashes($info['name2']);
	$TEL2= stripslashes($info['tel2']);
	$PHONE2 = stripslashes($info['hphone2']);
	$ADDRESS = "[".$info['zipcode']."] ".stripslashes($info['address']);
	$MESSAGE = nl2br(stripslashes($info['message']));
	$ADMESS = htmlspecialchars(stripslashes($info['admess']));
	$tmps = htmlspecialchars(stripslashes($info['carr_info']));
	$tmps = explode("|",$tmps);
	$THIS_STEP = $status_arr[$info['order_status']];

	$CARR_NUM = $tmps[1];
	
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

	if($info['order_status']=='D' || $info['order_status']=='E') {
		if($info['carr_info']) {
			$tmps = explode("|",$info['carr_info']);
			$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
			$tmp = $mysql->one_row($sql);
			$CLINK = $tmp['code'];
			$CNUM = $tmps[1];
			$tpl->parse("is_carr");		
		}
	}

	if($info['affiliate']) {
		$AFFILIATE = stripslashes($info['affiliate']);
		$AFFILI_COMMI = number_format((($info['pay_total'] + $info['use_reserve'] - $info['carriage'])*$info['a_commi'])/100,$ckFloatCnt)."원 ({$info['a_commi']}%)";
		$tpl->parse("is_affiliate");
	}

	if($cks>0) {
		$tpl->parse("is_sepa1");
		$tpl->parse("is_sepa2");
	}

	$cks++;

	$tpl->parse("loop_orders");
	$tpl->parse("is_reserve","2");
	$tpl->parse("is_cupon","2");
	$tpl->parse("is_cash_dc","2");
	$tpl->parse("is_mcarr","2");
	$tpl->parse("is_bank_cash","2");
	$tpl->parse("is_bank","2");
	$tpl->parse("is_card","2");		
	$tpl->parse("is_id","2");		
	$tpl->parse("is_sepa1","2");
	$tpl->parse("is_sepa2","2");
	$tpl->parse("loop_cart","2");
	$tpl->parse("is_affiliate","2");

}


/*********************************** LOOP  ***********************************/


$tpl->parse("main");
$tpl->tprint("main");
?>