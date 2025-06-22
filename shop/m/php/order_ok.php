<?
header("Cache-Control: no-store");
header("Pragma: no-cache");

include "sub_init.php";

$order_cookie_vars = "{$_POST['name1']}|{$_POST['tel11']}|{$_POST['tel12']}|{$_POST['tel13']}|{$_POST['phone11']}|{$_POST['phone12']}|{$_POST['phone13']}|{$_POST['email']}|{$_POST['name2']}|{$_POST['tel21']}|{$_POST['tel22']}|{$_POST['tel23']}|{$_POST['phone21']}|{$_POST['phone22']}|{$_POST['phone23']}|{$_POST['zip1']}|{$_POST['zip2']}|{$_POST['addr']}|{$_POST['message']}|{$_POST['direct']}";
SetCookie("order_cookie","Y",0,"/"); 
SetCookie("order_cookie_vars",$order_cookie_vars,0,"/"); 

if(eregi(":",$_SERVER['HTTP_HOST'])) {
	$tmps = explode(":",$_SERVER['HTTP_HOST']);
	$_SERVER['HTTP_HOST'] = $tmps[0];
	unset($tmps);
}
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if($_SERVER['REQUEST_METHOD']=='GET') alert('정상적으로 등록하세요!','back');

########################### 장바구니 상품 재고수량 체크 ########################
if($_POST['direct']=='Y') {
	$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}'";
	if($mysql->get_one($sql)>0) $cwhere = " && p_direct = 'Y'";
	else alert("주문정보가 일치하지 않습니다. 다시 주문 하시기 바랍니다.","back");
}
else $cwhere = "";

$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' {$cwhere}";
$mysql->query($sql);

while($tmps = $mysql->fetch_array()){
	$sql = "SELECT name, s_qty, qty, coop_pay, type FROM mall_goods WHERE uid = '{$tmps['p_number']}'";
	$tmps2 = $mysql->one_row($sql);

	if(!$tmps2) {
		$sql = "DELETE FROM mall_cart WHERE uid='{$tmps['uid']}'";
		$mysql->query2($sql);
		alert("삭제된 상품이 있어 장바구니에서 삭제 되었습니다. 확인 후 다시 주문해 주시기 바랍니다.","back");
	}

	if($tmps2['s_qty']==2 || $tmps2['type']!='A' ) {
		$sql = "DELETE FROM mall_cart WHERE uid='{$tmps['uid']}'";
		$msg = "[{$tmps2['name']}] 상품이 품절되어 장바구니에서 삭제 되었습니다!";
		$mysql->query2($sql);
		alert($msg,"back");
	}
	
	if($tmps2['s_qty']==4 && substr($tmps['p_cate'],0,3)!='999') {
		if($tmps2['qty']<$tmps['p_qty']) { 
			if($tmps2['qty']==0) {
				$sql = "DELETE FROM mall_cart WHERE uid='{$tmps['uid']}'";
				$msg = "[{$tmps2['name']}] 상품이 품절되어 장바구니에서 삭제 되었습니다!";
			}
			else {
				$sql = "UPDATE mall_cart SET p_qty = '{$tmps2['qty']}' WHERE uid='{$tmps['uid']}'";
				$msg = "[{$tmps2['name']}] 상품의 재고수량(현재:{$tmps2['qty']}개)이 부족하여 주문수량이 변경 되었습니다!";
			}
			$mysql->query2($sql);
			alert($msg,"back");
		}

		if($tmps['p_option']) {
			$p_option2 = explode("|",$tmps['p_option']);
			for($i=0,$cnt=count($p_option2);$i<$cnt;$i++) {
				$sql = "SELECT option1, option2, price, qty FROM mall_goods_option WHERE uid='{$p_option2[$i]}'";
				$tmps3 = $mysql->one_row($sql);
				if(!$tmps3['option1']) continue;
				if($tmps3['qty']==0) {
					array_splice($p_option2,$i,1);
					$p_option = explode("|",$p_opotion2);
					$sql = "UPDATE mall_cart SET  p_option='{$p_option}' WHERE uid='{$tmps['uid']}'";
					$msg = "[{$tmps2['name']}] 옵션상품이 품절되었습니다. 다른 옵션을 선택하시기 바랍니다."; 
					$mysql->query2($sql);
					alert($msg,"../{$Main}?channel=cart");
				}
				else if($tmps3['qty']<$tmps['p_qty']) {
					$sql = "UPDATE mall_cart SET p_qty = '{$tmps3['qty']}' WHERE uid='{$tmps['uid']}'";
					$msg = "[{$tmps2['name']}] 상품의 재고수량(현재:{$tmps3['qty']}개)이 부족하여 주문수량이 변경 되었습니다!";
					$mysql->query2($sql);
					alert($msg,"back");
				}			
			}
		}
	}
	if(substr($tmps['p_cate'],0,3)=='999') {
		$coop_pay = $tmps2['coop_pay'];
		$sql = "SELECT count(*) FROM mall_cooperate WHERE id='{$my_id}' && guid='{$tmps['p_number']}' && status='A'";
		if($mysql->get_one($sql)==0 && $coop_pay!='Y') alert("공동구매 신청내역이 없어 주문하실 수 없습니다","back");
	}
}
########################### 장바구니 상품 재고수량 체크 ########################


$name1	= addslashes($_POST['name1']);
$tel1	= addslashes($_POST['tel11']." - ".$_POST['tel12']." - ".$_POST['tel13']);
$phone1 = addslashes($_POST['phone11']." - ".$_POST['phone12']." - ".$_POST['phone13']);
$email	= addslashes($_POST['email']);

$name2	= addslashes($_POST['name2']);
$tel2	= addslashes($_POST['tel21']." - ".$_POST['tel22']." - ".$_POST['tel23']);
$phone2 = addslashes($_POST['phone21']." - ".$_POST['phone22']." - ".$_POST['phone23']);
$zipcode	= addslashes($_POST['zip1']." - ".$_POST['zip2']);
$addr	= addslashes($_POST['addr']);

$cash_type	= addslashes($_POST['cash_type']);
$bank_name	= addslashes($_POST['bank_name']);
$pay_name	= addslashes($_POST['pay_name']);

$myear	= addslashes($_POST['myear']);
$mmonth	= addslashes($_POST['mmonth']);
$mday	= addslashes($_POST['mday']);
$pay_date	= "$myear $mmonth $mday";

$message	= addslashes($_POST['message']);
$use_reserve= !empty($_POST['use_reserve']) ? addslashes($_POST['use_reserve']) : 0;
$use_cupon	= !empty($_POST['use_cupon']) ? addslashes($_POST['use_cupon']) : 0;
$cupon		= addslashes($_POST['cupon']);

$cash_total	= addslashes($_POST['cash_total']);
$carriage	= addslashes($_POST['carriage']);
$tsale		= addslashes($_POST['tsale']);

$cash_info = '';
if($_POST['cash_ctype']=='A') {
	if($_POST['pay_type']=='A') {
		$auth_number = $_POST['cell1'].$_POST['cell2'].$_POST['cell3'];
		if(strlen($auth_number)==10 || strlen($auth_number)==11) {
			$cash_info = "1|{$auth_number}";
		}
	}
	else if($_POST['pay_type']=='B') {
		$auth_number = $_POST['jumin1'].$_POST['jumin2'];
		if(strlen($auth_number)==13) {
			$cash_info = "1|{$auth_number}";
		}
	}
}
else {
	$auth_number = $_POST['cnum1'].$_POST['cnum2'].$_POST['cnum3'];
	if(strlen($auth_number)==10) {
		$cash_info = "2|{$auth_number}";
	}
}

if(!$name1 || !$addr || !$cash_type || strlen($cash_total)==0) alert('정보가 제대로 넘어오지 못했습니다.\\n 다시 시도해 주세요!','back');

if($email){
	if(!mailCheck($email)) alert("입력하신 {$email} 은 존재하지 않는 메일주소입니다.\\n다시 한번 확인하여 주시기 바랍니다.",'back');
}

######################### 결제 정보 유효성 체크 ######################
$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));

if($my_id) {
	$sql = "SELECT reserve FROM pboard_member WHERE id = '{$my_id}'";
	$cks_reserve = $mysql->get_one($sql);

	if($use_reserve > $cks_reserve) alert("적립금 사용금액을 초과 했습니다.","back");
}

$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' {$cwhere}";
$mysql->query($sql);

$TCNT = $cks_total = $cks_carr = 0;
$ck_carr_only = '';
$goods_carriage = array();
while($data = $mysql->fetch_array()){	
	$goods_carriage[$data['uid']] = 0;
	$MY_SALE = $my_sale;
	$MY_POINT = $my_point;

	$gData	= getDisplayOrder($data);		// 디스플레이 정보 가공 후 가져오기

	$cks_total = $cks_total+($gData['sum']);	
	
    if($gData['carr']) {
		if($gData['carr']=='F') { 
			//$cks_carr = 0;
			$my_carr = 'Y';
			$goods_carriage[$data['uid']] = '99999';
		}
		else { 
			$cks_carr += $gData['carr'];
			$goods_carriage[$data['uid']] = $gData['ocarr'];
			if(!$ck_carr_only) $ck_carr_only = 'Y';
		}
	}	
	else if($ck_carr_only=='Y') $ck_carr_only = 'N';
	$TCNT++;
}

if($TCNT==0) alert("장바구니 상품이 없습니다. 다시 주문 하시기 바랍니다","back");

if($cash[10] =='1' && $my_carr!='Y' && $ck_carr_only !='Y') { 
	if($cks_total < $cash[11]) $cks_carr += $cash[12];
} 

if($cash[13] && trim($cash[14])) {
	$cks_tmps1 = explode("|",$cash[14]);
	$cks_tmps3 = explode("|",$cash[13]);

	for($i=0;$i<count($cks_tmps1);$i++) {	
		$cks_tmps2 = explode(",",$cks_tmps1[$i]);
		for($j=0;$j<count($cks_tmps2);$j++) {		
			if(!$cks_tmps2[$j]) continue;
			if(eregi($cks_tmps2[$j],$addr)) {			
				$cks_carr += $cks_tmps3[$i];
				break;
			}
		}
	}
}

if($use_cupon && $cupon && $my_id) {
	$tmp_cupon = explode(",",$cupon);
	$C_PRICE = 0;

	for($c=0;$c<count($tmp_cupon);$c++) {
	
		$sql = "SELECT a.status, a.signdate as dates, a.gid, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$my_id}' && a.pid=b.uid && a.status ='A' && a.uid='{$tmp_cupon[$c]}'";
		
		if(!$row = $mysql->one_row($sql)) alert("쿠폰 사용기간이 만료 되었거나 잘못된 쿠폰입니다.","back");

		if($row['sdate'] && $row['edate'] && !$row['days']) {
			if(date("Y-m-d")>$row['edate']) alert("쿠폰 사용기간이 만료 되었거나 잘못된 쿠폰입니다.","back");
		}
		else {
			
			$tmps = date("Y-m-d", strtotime("+{$row['days']} DAY", $row['dates']));
			if(date("Y-m-d")>$tmps) alert("쿠폰 사용기간이 만료 되었거나 잘못된 쿠폰입니다.","back");
		}

		if($row['gid']) {		
			$sql = "SELECT uid,name,cate,price,event FROM mall_goods WHERE uid='{$row['gid']}'";
			if(!$row2 = $mysql->one_row($sql)) continue;
			
			if($row['stype']=='P') {
				$limit = '';
				if($row['use_type']==0) $limit = "limit 1";			
				$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && p_number='{$row['gid']}' {$limit}";
				$mysql->query2($sql);
				
				$cu_total = 0;
				while($row3 = $mysql->fetch_array('2')){
					$gData	= getDisplayOrder($row3);	
					if($row['use_type']==0) $cu_total += $gData['oprice'];			
					else $cu_total += ($gData['oprice'] * $row3['p_qty']);
				}	
				$cu_total = numberLimit(($cu_total * $row['sale'])/100,1);
				$C_PRICE +=  round($cu_total,$ckFloatCnt);
			}
			else {
				if($row['use_type']==0) $C_PRICE += $row['sale'];
				else {
					$sql = "SELECT SUM(p_qty) FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && p_number='{$row['gid']}'";
					$p_qty = $mysql->get_one($sql);				
					$C_PRICE += $row['sale'] * $p_qty;			
				}
			}		

			$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && p_number='{$row['gid']}'";		
			if($mysql->get_one($sql)==0) alert("쿠폰 사용기간이 만료 되었거나 잘못된 쿠폰입니다.","back");		
		}
		else if($row['scate']) {
			$tmps = explode("|",$row['scate']);
			for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
				if(substr($tmps[$i],3,3)=='000') $where[] = "SUBSTRING(p_cate,1,3)='".substr($tmps[$i],0,3)."' ";
				else if(substr($tmps[$i],6,3)=='000') $where[] = "SUBSTRING(p_cate,1,6)='".substr($tmps[$i],0,6)."' ";
				else if(substr($tmps[$i],9,3)=='000') $where[] = "SUBSTRING(p_cate,1,9)='".substr($tmps[$i],0,9)."' ";
				else $where[] = "p_cate='{$tmps[$i]}' ";			
			}	

			$where = join("||",$where);

			$sql = "SELECT p_cate, p_number FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && ({$where})";
			$mysql->query2($sql);

			$cu_total = 0;
			while($row2 = $mysql->fetch_array('2')){
				$gData	= getDisplayOrder($row2);	
				$cu_total += ($gData['oprice'] * $row2['p_qty']);
			}

			if($row['stype']=='P') {						
				$C_PRICE +=  round(($cu_total * $row['sale'])/100,$ckFloatCnt);
			}
			else $C_PRICE += $row['sale'];
		
			if($cu_total<$row['lmt']) alert("쿠폰 사용기간이 만료 되었거나 잘못된 쿠폰입니다.","back");
		}	
		else { 
			$sql = "SELECT p_cate, p_number FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}'";
			$mysql->query2($sql);

			if(($cks_total)<$row['lmt']) alert("쿠폰 사용기간이 만료 되었거나 잘못된 쿠폰입니다.","back");

			if($row['stype']=='P') {						
				$C_PRICE += round((($cks_total) * $row['sale'])/100,$ckFloatCnt);
			}
			else $C_PRICE += $row['sale'];
		}
				
		if($row['lmt'] && $row['lmt']>($cks_total) && $row['type']!=3) alert("쿠폰 사용기간이 만료 되었거나 잘못된 쿠폰입니다.","back");		
	}

	if($use_cupon!=$C_PRICE) alert("쿠폰 사용기간이 만료 되었거나 잘못된 쿠폰입니다.","back");
	unset($row2,$C_PRICE);
} 
else {
	$use_cupon = $cupon = 0;
}

$cks_totals = $cks_total + $cks_carr - $use_reserve - $use_cupon;

$cash_dc = 0;
if($cash_type=='B') {
	$sql	= "SELECT code FROM mall_design WHERE mode='T'";
	$tmps	= $mysql->get_one($sql);
	$tmps = explode("|",$tmps);
	if(!$tmps[4]) $cash_dc = 0;
	else $cash_dc = $tmps[4];
	$cashdc = round(($cks_total * $cash_dc)/100,$ckFloatCnt); 	
	if($ckFloatCnt==0) $cashdc = numberLimit($cashdc,1);
	$cks_totals -= $cashdc;	
	$tsale = $tsale + $cashdc;
}

if($cash_total != $cks_totals) alert("결제 금액이 일치 하지 않습니다. 정상적으로 주문 하시기 바랍니다.","back");
######################### 결제 정보 유효성 체크 ######################

if($cash_type=='C') {
	if($cash_total < $cash[4]) {
		alert("카드결재는 결재금액이 ".number_format($cash[4],$ckFloatCnt)."원 이상 일떄 사용가능합니다.\\다른 결재 방법을 사용하시기 바랍니다.","back");
    } 
}
$card_status = "A";

if($use_reserve && $use_reserve>0) {
    $sql = "SELECT reserve FROM pboard_member WHERE id='{$my_id}'";
	$ck_reserve = $mysql->get_one($sql);
	if($use_reserve > $ck_reserve) alert("사용할 적립금이 적립된 금액을 초과 했습니다.","back");
	if($ck_reserve < $cash[9]) alert("적립금은 ".number_format($cash[9],$ckFloatCnt)."원 이상 일떄 사용가능합니다.","back");	
}
/********************** 결재관련 재 확인 ***************************/

/********************** 주문번호 생성 ***************************/
$signdate = date("Y-m-d H:i:s",time());
if(!$my_id) $my_id = "guest";
$order_num = rand(1000,9999);
$time_header = date("y-md-His",time());
$order_num = $time_header."-".$order_num;
/********************** 주문번호 생성 ***************************/

if($_COOKIE['a_id']) {
	$affiliate = $_COOKIE['a_id'];
	$sql = "SELECT commission FROM mall_affiliate WHERE auth='Y' && id='{$affiliate}'";
	if(!$a_commi = $mysql->get_one($sql)) $a_commi = $affiliate = '';
}
else $a_commi = $affiliate = '';

/********************** 주문정보 저장 ***************************/
$sql = "INSERT INTO mall_order_info (id, order_num, name1, tel1, hphone1, email, name2, tel2, hphone2, zipcode, address, pay_type, pay_date, bank_name, pay_name, message, use_reserve, use_cupon, cupon, carriage, sales, pay_status, cash_info, pay_total, order_status, status_date, affiliate, a_commi, cash_sale, signdate) VALUES ('{$my_id}','{$order_num}','{$name1}','{$tel1}','{$phone1}','{$email}','{$name2}','{$tel2}','{$phone2}','{$zipcode}','{$addr}','{$cash_type}','{$pay_date}','{$bank_name}','{$pay_name}','{$message}','{$use_reserve}','{$use_cupon}','{$cupon}','{$carriage}','{$tsale}','{$card_status}','{$cash_info}','{$cash_total}','A','{$signdate}','{$affiliate}', '{$a_commi}','{$cash_dc}','{$signdate}')";
$mysql->query($sql);
/********************** 주문정보 저장 ***************************/

/********************** 주문 상품정보 저장 ***************************/
$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' {$cwhere}";
$mysql->query($sql);

while($cart_list = $mysql->fetch_array()){
	$sql = "SELECT name,price,reserve,event,coop_price,coop_pay,brand FROM mall_goods WHERE uid = '{$cart_list['p_number']}'";
	$cart_goods = $mysql->one_row($sql);

	if(substr($cart_list['p_cate'],0,3)!='999') {
		$my_type1 = '회원';
		$my_type2 = '회원';
		$my_sale2 = $my_sale;
		$my_point2 = $my_point;

		if($cart_goods['event']>0) {
			if($my_sale < $EVENT_SALE[$cart_goods['event']]) {
				$my_sale2 = $EVENT_SALE[$cart_goods['event']];
				$my_type1 = '이벤트';
			} 
			
			if($my_point < $EVENT_POINT[$cart_goods['event']]) {
				$my_point2 = $EVENT_POINT[$cart_goods['event']];		
				$my_type2 = '이벤트';
			}
		}
		
		if($my_sale2>0) {
			$sale_price = round((($cart_goods['price'] * $my_sale2)/100)*$cart_list['p_qty'],$ckFloatCnt);
			if($cart_list['op_price']>0) $sale_price += round((($cart_list['op_price'] * $my_sale2)/100)*$cart_list['p_qty'],$ckFloatCnt);
		}
		else $sale_price = 0;

		if($my_sale2>0 || $my_point2>0 || $my_carr=='Y') {
			$sale_vls = "{$my_sale2}|{$my_point2}|{$my_carr}|{$my_type1}|{$my_type2}";
		}
		else $sale_vls = '';
	}
	else {
		$sale_price = 0;
		$sale_vls = '';		
		if($cart_goods['coop_pay']=='Y') {
			$sql = "SELECT price FROM mall_goods_cooper WHERE guid='{$cart_list['p_number']}' ORDER BY qty ASC LIMIT 1";
			$cart_goods['price'] = $mysql->get_one($sql);		
		}
		else $cart_goods['price'] = $cart_goods['coop_price'];
	}

	if($cart_list['p_option']) {
		$p_options = array();
		$stmps = explode("|",$cart_list['p_option']);
		for($i=0,$cnt=count($stmps);$i<$cnt;$i++) {
			$sql = "SELECT * FROM mall_goods_option WHERE uid='{$stmps[$i]}'";
			$stmps2 = $mysql->one_row($sql);
			$p_options[] = "{$stmps2['option1']} : {$stmps2['option2']}|{$stmps2['price']}|{$stmps[$i]}";
		}
		$p_options = join("|*|",$p_options);
	}
	else $p_options='';
	
	/************************* 적립금 관련 ***********************/
	$cart_list['p_reserve'] = 0;
	$reserve = explode("|",$cart_goods['reserve']);
	if($reserve[0] =='2') { //쇼핑몰 정책일때
		if($cash[6] =='1') { 
			$cart_list['p_reserve'] = round((($cart_goods['price']+$cart_list['op_price']-$sale_price) * $cash[8])/100,$ckFloatCnt);
		} 
	} 
	else if($reserve[0] =='3') { //별도 책정일때
		$cart_list['p_reserve'] = round((($cart_goods['price']+$cart_list['op_price']-$sale_price) * $reserve[1])/100,$ckFloatCnt);
	}	
	/************************* 적립금 관련 ***********************/

	$sql = "INSERT INTO mall_order_goods (order_num, p_cate, p_number, p_name, p_price, p_qty, p_reserve, p_option, op_price, sale_price, sale_vls, order_status, carriage, status_date, brand, affiliate, signdate) VALUES ('{$order_num}','{$cart_list['p_cate']}','{$cart_list['p_number']}','".addslashes($cart_goods['name'])."','{$cart_goods['price']}','{$cart_list['p_qty']}','{$cart_list['p_reserve']}','{$p_options}','{$cart_list['op_price']}','{$sale_price}','{$sale_vls}','A','{$goods_carriage[$cart_list['uid']]}','{$signdate}','{$cart_goods['brand']}','{$affiliate}','{$signdate}')";

    $mysql->query2($sql);

	if($my_id !='guest') { //적립금 내역 저장
		$subject = "상품구입 적립 (".addslashes($cart_goods['name']).")";
		$goods_num = $cart_list['p_number']."|".$p_options;

		if($my_sale2>0) $cart_list['p_reserve'] = $cart_list['p_reserve'] - round(($cart_list['p_reserve'] * $my_sale2)/100,$ckFloatCnt);
		
		/************************* 적립금 관련 ***********************/
		if($my_point2 > 0) {			
			$cart_list['p_reserve'] += round((($cart_goods['price']+$cart_list['op_price']-$sale_price) * $my_point2)/100,$ckFloatCnt);		
		}	
		/************************* 적립금 관련 ***********************/
		
		if($cart_list['p_reserve']>0) {
			$sql = "INSERT INTO mall_reserve VALUES ('','{$my_id}','{$subject}','".($cart_list['p_reserve']*$cart_list['p_qty'])."','{$order_num}','{$goods_num}','A','{$signdate}')";
			$mysql->query2($sql);
		}
    }	
	
	$sql = "SELECT s_qty FROM mall_goods WHERE uid='{$cart_list['p_number']}'";
	if($mysql->get_one($sql)==4 && substr($cart_list['p_cate'],0,3)!='999') {
		$sql = "UPDATE mall_goods SET qty = qty - '{$cart_list['p_qty']}' WHERE uid='{$cart_list['p_number']}' && s_qty = 4";
		$mysql->query2($sql);

		// 옵션 상품일 경우 수량있을 경우 수량 변경적용
		if($cart_list['p_option']) {			
			$stmps = explode("|",$cart_list['p_option']);
			for($i=0,$cnt=count($stmps);$i<$cnt;$i++) {
				$sql = "UPDATE mall_goods_option SET qty = qty - {$cart_list['p_qty']} WHERE uid='{$stmps[$i]}'";
				$mysql->query2($sql);			
			}
		}
	}

	$sql = "SELECT count(*) FROM mall_goods WHERE uid='{$cart_list['p_number']}' && s_qty = 4 && qty = 0";
	if($mysql->get_one($sql)==1) {
		############ 상품 품절 SMS 보내기 #############
		$code_arr = Array();
		$code_arr['name'] = $name1;
		$code_arr['number'] = $order_num;
		$code_arr['goodsName'] = $cart_goods['name'];
		pmallSmsAutoSend("0000000000","soldout",$code_arr);
		############ 상품 품절 SMS 보내기 #############
	}
	
	$sql = "UPDATE mall_goods SET o_cnt = o_cnt + '{$cart_list['p_qty']}' WHERE uid = '{$cart_list['p_number']}'";
	$mysql->query2($sql);

	if(substr($cart_list['p_cate'],0,3)=='999') {
		if($coop_pay=='Y') {			
			$sql = "SELECT price, s_qty, qty, coop_sdate, coop_edate, coop_cnt FROM mall_goods WHERE uid='{$cart_list['p_number']}'";
			$data2=$mysql->one_row($sql);
			
			$participation = $data2['coop_cnt'] + $cart_list['p_qty'];
			
			$signdate2 = time();
			$sql = "INSERT INTO mall_cooperate (id,guid,qty,p_option,cell,email,status,order_num,signdate) VALUES ('{$my_id}','{$cart_list['p_number']}','{$cart_list['p_qty']}','{$p_options}','{$phone1}','{$email}','B','{$order_num}','{$signdate2}')";
			$mysql->query2($sql);

			$sql = "UPDATE mall_goods SET coop_cnt = coop_cnt+{$cart_list['p_qty']} WHERE uid='{$cart_list['p_number']}'";
			$mysql->query2($sql);

			if($data2['s_qty']==4 && ($data2['qty']==($data2['coop_cnt']+$cart_list['p_qty']))){  // 판매수량 완료 자동 공구마감처리
				$sql = "UPDATE mall_goods SET coop_edate = '".date("Y-m-d H:i")."'  WHERE uid='{$cart_list['p_number']}'";
				$mysql->query2($sql);
			}
		}
		else {
			$sql = "UPDATE mall_cooperate SET status='B', order_num='{$order_num}' WHERE id='{$my_id}' && guid='{$cart_list['p_number']}' && status='A'";
			$mysql->query2($sql);
		}
	}
}
/********************** 주문 상품정보 저장 ***************************/

if($my_id != 'guest' && $use_reserve >0){  //적립금 사용 저장
     $sql = "UPDATE pboard_member SET reserve = reserve - {$use_reserve} WHERE id = '{$my_id}'";
	 $mysql->query($sql);
	 $subject = "상품구입 적립금 사용";
	 $sql = "INSERT INTO mall_reserve VALUES ('','{$my_id}','{$subject}','{$use_reserve}','{$order_num}','','C','{$signdate}')";
	 $mysql->query($sql);
}

if($my_id != 'guest' && $use_cupon >0){  //쿠폰 사용 저장
	$signdate = time();
	$tmp_cupon = explode(",",$cupon);
	for($c=0;$c<count($tmp_cupon);$c++) {
		$sql = "UPDATE mall_cupon SET status = 'B', usedate='{$signdate}' WHERE id = '{$my_id}' && uid='{$tmp_cupon[$c]}'";
		$mysql->query($sql);
	}
} 

$sql = "DELETE FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' {$cwhere}";
$mysql->query($sql);

SetCookie("order_cookie","",-999,"/"); 
SetCookie("order_cookie_vars","",-999,"/"); 

switch($cash_type) {
	case "C" : case "R" : case "V" : case "H" :
		movePage("http://".$_SERVER['HTTP_HOST']."/{$ShopPath}m/index.php?channel=card_pay&amp;order_num={$order_num}"); 
	break;

	default : 
		if($cash_total==0) {
			$sql = "UPDATE mall_order_info SET order_status='B' WHERE order_num = '{$order_num}'";
			$mysql->query($sql);
					
			$signdate = date("Y-m-d H:i:s",time());
			$sql = "UPDATE mall_order_goods SET  order_status = 'B', signdate = '{$signdate}' WHERE order_num='{$ordr_num}'";
			$mysql->query($sql);
		}
		movePage("http://".$_SERVER['HTTP_HOST']."/{$ShopPath}m/{$Main}?channel=order_end&amp;order_num={$order_num}");
	break;
}
?>