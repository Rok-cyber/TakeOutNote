<?
$sql	= "SELECT code FROM mall_design WHERE mode='W'";
$tmp	= $mysql->get_one($sql);
$ssl	= explode("|*|",$tmp);
$sMain = "./";
if($ssl[0]==1) {
	$tmp    = explode("|",$ssl[2]);	
	if(in_array(5,$tmp)) {
		if($ssl[1]) $sport = ":{$ssl[1]}";
		$sMain = "https://".$_SERVER['HTTP_HOST']."{$sport}/{$ShopPath}";	
		unset($sport);
	}
}

$tpl->define("main","{$skin}/order_02.html");
$tpl->scan_area("main");

/**************************** MEMBER INFOMATION **************************/
if($my_id) {
    $sql = "SELECT * FROM pboard_member WHERE id = '{$my_id}'";
	$mem_info = $mysql->one_row($sql);
	$UNAME		= stripslashes($mem_info['name']);
	$tmp_tel	= explode(" - ",$mem_info['tel']);
	$TEL1		= $tmp_tel[0];
	$TEL2		= $tmp_tel[1];
	$TEL3		= $tmp_tel[2];
	$tmp_phone	= explode(" - ",$mem_info['hphone']);
	$PHONE1		= $tmp_phone[0];
	$PHONE2		= $tmp_phone[1];
	$PHONE3		= $tmp_phone[2];
	$tmp_zip	= explode(" - ",$mem_info['zipcode']);
	$ZIP1		= $tmp_zip[0];
	$ZIP2		= $tmp_zip[1];	
	$EMAIL		= stripslashes($mem_info['email']);
	$ADDR		= stripslashes($mem_info['address']);
	$RESERVE	= number_format($mem_info['reserve'],$ckFloatCnt);
	if(!$mem_info['reserve']) $mem_info['reserve'] = 0;

	$tmps = explode("@",$EMAIL);
	$EMAIL1 = $tmps[0];
	$EMAIL2 = $tmps[1];
	
	$tmps = stripslashes($mem_info['carriage1']);
	$tmps = explode("|",$tmps);
	$NAME2 = $tmps[0];
	$tel = explode(" - ",$tmps[1]);
	$TEL21 = $tel[0];
	$TEL22 = $tel[1];
	$TEL23 = $tel[2];
	$phone = explode(" - ",$tmps[2]);
	$PHONE21 = $phone[0];
	$PHONE22 = $phone[1];
	$PHONE23 = $phone[2];
	$zip = explode(" - ",$tmps[3]);
	$ZIP21 = $zip[0];
	$ZIP22 = $zip[1];
	$ADDR2 = $tmps[4];

	$tmps = stripslashes($mem_info['carriage2']);
	$tmps = explode("|",$tmps);
	$NAME3 = $tmps[0];
	$tel = explode(" - ",$tmps[1]);
	$TEL31 = $tel[0];
	$TEL32 = $tel[1];
	$TEL33 = $tel[2];
	$phone = explode(" - ",$tmps[2]);
	$PHONE31 = $phone[0];
	$PHONE32 = $phone[1];
	$PHONE33 = $phone[2];
	$zip = explode(" - ",$tmps[3]);
	$ZIP31 = $zip[0];
	$ZIP32 = $zip[1];
	$ADDR3 = $tmps[4];

	if($NAME2) $tpl->parse("is_carrinfo1");
	if($NAME3) $tpl->parse("is_carrinfo2");

	if($mem_info['message1']) {	
		$MESSAGE1 = stripslashes($mem_info['message1']);
		$tpl->parse("is_message1");
	}
	if($mem_info['message2']) {	
		$MESSAGE2 = stripslashes($mem_info['message2']);
		$tpl->parse("is_message2");
	}
	if($mem_info['message3']) {	
		$MESSAGE3 = stripslashes($mem_info['message3']);
		$tpl->parse("is_message3");
	}
}
else {
	$TEL1 = "02";
	$PHONE1 = "010";
}
/**************************** MEMBER INFOMATION **************************/

if($_COOKIE['order_cookie']=='Y') {
	$order_cookie_vars = explode("|",$_COOKIE['order_cookie_vars']);
	$UNAME	= $order_cookie_vars[0];
	$TEL1	= $order_cookie_vars[1];
	$TEL2	= $order_cookie_vars[2];
	$TEL3	= $order_cookie_vars[3];
	$PHONE1	= $order_cookie_vars[4];
	$PHONE2	= $order_cookie_vars[5];
	$PHONE3	= $order_cookie_vars[6];
	$EMAIL	= $order_cookie_vars[7];
	$CNAME	= $order_cookie_vars[8];
	$CTEL1	= $order_cookie_vars[9];
	$CTEL2	= $order_cookie_vars[10];
	$CTEL3	= $order_cookie_vars[11];
	$CPHONE1	= $order_cookie_vars[12];
	$CPHONE2	= $order_cookie_vars[13];
	$CPHONE3	= $order_cookie_vars[14];
	$CZIP1	= $order_cookie_vars[15];
	$CZIP2	= $order_cookie_vars[16];
	$CADDR	= $order_cookie_vars[17];
	$CMESSAGE	= $order_cookie_vars[18];	
	$direct	= $order_cookie_vars[19];	
	SetCookie("order_cookie","",-999,"/"); 
	SetCookie("order_cookie_vars","",-999,"/"); 
}
else {
	$CTEL1 = "02";
	$CPHONE1 = "010";
}

$SYEAR = date("Y");
$SMON = date("m");
$SDAY = date("d");

for($i=$SYEAR;$i<$SYEAR+2;$i++){
	if($i==$SYEAR) $PYEAR .= "<option value='{$i}년' selected>{$i}년</option>\n";
	else $PYEAR .= "<option value='{$i}년'>{$i}년</option>\n"; 
}
for($i=1;$i<13;$i++){
	if($i==$SMON) $PMONTH .= "<option value='{$i}월' selected>{$i}월</option>\n";
	else $PMONTH .= "<option value='{$i}월'>{$i}월</option>\n";
}
for($i=1;$i<32;$i++){
	if($i==$SDAY) $PDAY .= "<option value='{$i}일' selected>{$i}일</option>\n";
	else $PDAY .= "<option value='{$i}일'>{$i}일</option>\n";
}

/**************************** CART LIST**************************/
if($_GET['direct']=='Y') {
	$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}'";
	if($mysql->get_one($sql)>0) {
		$where = " && p_direct = 'Y'";
		$direct = 'Y';
	}
	else {
		$where = "";
		$direct = 'N';
	}
}
else $where = "";

$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' {$where}";
$mysql->query($sql);

$TCNT = $total = $carr = $trese = 0;
$ck_carr_only = $ck_coop = '';
while($data = $mysql->fetch_array()){	
	$MY_SALE = $my_sale;
	$MY_POINT = $my_point;

	$gData	= getDisplayOrder($data);		// 디스플레이 정보 가공 후 가져오기

	if(!$gData['uid']) {
		$sql = "DELETE FROM mall_cart WHERE uid='{$data['uid']}'";
		$mysql->query($sql);
		alert("삭제된 상품이 있어 장바구니에서 삭제 되었습니다.","{$Main}?channel=order_form&direct={$direct}");
	}

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

    if($gData['my_sale']>0) {
		$TTL = $gData['my_type1'];
		$MY_SALE = $gData['my_sale'];
		$tpl->parse("is_my_sale","1");
	}

	if($gData['my_point']>0) {
		$TTL = $gData['my_type2'];
		$MY_POINT = $gData['my_point'];
		$tpl->parse("is_my_point","1");
	}

	if($gData['carr']) {
		if($gData['carr']=='F') { 
			//$carr = 0;
			$my_carr = 'Y';
			$tpl->parse("is_my_free","1");
		}
		else { 
			$carr += $gData['carr'];
			$CARR = number_format($gData['ocarr']);
			$tpl->parse("is_my_carr","1");
			if(!$ck_carr_only) $ck_carr_only = 'Y';
		}
	}
	else if($ck_carr_only=='Y') $ck_carr_only = 'N';
	
	for($i=1,$cnt=count($gData['op_name']);$i<=$cnt;$i++){
		if($gData['op_sec_vls'][$i]) {			
			$OP_NAME = $gData['op_name'][$i];
			$OP_SEC_VLS = $gData['op_sec_vls'][$i];
			$tpl->parse("loop_op");
		}
	}

	if(substr($data['p_cate'],0,3)=='999') {
		$ck_coop = 'Y';
	}	

	$tpl->parse('loop_cart');	   
	$tpl->parse('loop_op','2');
	$tpl->parse("is_my_sale","2");
	$tpl->parse("is_my_point","2");
	$tpl->parse("is_my_carr","2");
	$tpl->parse("is_my_free","2");
	$TCNT++;
}

if($TCNT==0) $tpl->parse('no_cart'); 
else {

	if($ck_coop!='Y') $tpl->parse("is_cartpage");

	/************************* 배송비 관련 ***********************/ //수정요망	
	if($cash[10] =='1' && $my_carr!='Y' && $ck_carr_only !='Y') { 
		if($total < $cash[11]) $carr += $cash[12];
	} 
	$TCARR = number_format($carr,$ckFloatCnt);
	/************************* 배송비 관련 ***********************/

	$TOTAL	= number_format($total,$ckFloatCnt);
	$TRESE	= number_format($trese,$ckFloatCnt);

	$sql	= "SELECT code FROM mall_design WHERE mode='T'";
	$tmps	= $mysql->get_one($sql);
	$tmps = explode("|",$tmps);
	
	if($tmps[4] && $tmps[4]>0) {
		$cashdc = round(($total * $tmps[4])/100,$ckFloatCnt);
		if($ckFloatCnt==0) $cashdc = numberLimit($cashdc,1);
		$CASHDC = number_format($cashdc,$ckFloatCnt);
		$tpl->parse("is_cash_dc");
	}
	else $cashdc = 0;

	if($tsale>0) {
		$TSALE	= number_format($tsale,$ckFloatCnt);
		$tpl->parse("is_tsale");
	}
	$C_TOTAL = $total+$carr;
	$TOTAL2 = number_format($C_TOTAL,$ckFloatCnt);

	$C_TOTAL2 = $total+$carr-$cashdc;
	$TOTAL3 = number_format($C_TOTAL2,$ckFloatCnt);
	
	for($i=0,$cnt=strlen($TOTAL2);$i<$cnt;$i++){
		if($TOTAL2[$i]==',') $IMG_TOTAL .= "<img src='img/shop/star_jum2.gif' alt='.' />";
		else $IMG_TOTAL .= "<img src='img/shop/star_num{$TOTAL2[$i]}.gif' alt='{$TOTAL2[$i]}' />";
	}

	
	if($my_id) $CKLOG = "Y";
	else $CKLOG = "N";

	
	/**************************** 결재 관련 *****************************/
	if($cash[0] =='1') {
		$BANK_SELECT = "<option value=''>입금할 은행을 선택하시기 바랍니다.</option>";
		$tmp_bank = explode("|",$cash[5]);
		for($b=0;$b<(count($tmp_bank)-1);$b++){
			 $bank_info = explode(",",$tmp_bank[$b]);
			 $BANK_SELECT .="<option value='{$tmp_bank[$b]}'>{$bank_info[0]} : {$bank_info[1]} , 예금주 : {$bank_info[2]}</option>";
		}
		
		$sql = "SELECT code FROM mall_design WHERE mode='O'";
		$code = $mysql->get_one($sql);

		if($code) {
			$code = explode("|",stripslashes($code));			
			if($code[0]==1 && $code[1]==1) {
				$tpl->parse("is_bank_cash");
			}
		}

		$tpl->parse("is_bank1");
		$tpl->parse("is_bank2");
	} 
	
	if($cash[1]=='1') $tpl->parse("is_card1");
	if($cash[17]=='1') $tpl->parse("is_card2");
	if($cash[18]=='1') $tpl->parse("is_card3");
	if($cash[19]=='1') $tpl->parse("is_card4");
	if($cash[16]==1 && ($cash[1]=='1' || $cash[17]=='1' || $cash[18]=='1' || $cash[19]=='1')) {
		$tpl->parse("is_card");
		$tpl->parse("is_test");
	}

	if($mem_info[reserve] >0 && $my_id) {
		$RESERVE_USE = number_format($cash[9],$ckFloatCnt);
		if($mem_info[reserve] < $cash[9]) {			
			$tpl->parse("is_reserve1");
		} else {
			$tpl->parse("is_reserve1");
			$tpl->parse("is_reserve2");
		}
	}

	/**************************** 결재 관련 *****************************/

	if($MESSAGE1 || $MESSAGE2 || $MESSAGE3) $tpl->parse("is_message");
	else $tpl->parse("is_no_message");

	if(!$my_id) {
		$sql	= "SELECT code FROM mall_design WHERE mode='T'";
		$tmps	= $mysql->get_one($sql);
		$tmps = explode("|",$tmps);

		if($tmps[2]=='1') {
			$sql = "SELECT * FROM mall_document WHERE mode='C'";
			$row = $mysql->one_row($sql);

			$PRIV = stripslashes($row['code']);
			$PRIV = str_replace("{shopName}",$basic[1],$PRIV);
			$PRIV = str_replace("{name}",$basic[9],$PRIV);
			$PRIV = str_replace("{email}",$basic[10],$PRIV);
			$PRIV = str_replace("{tel}",$basic[7],$PRIV);
			$PRIV = str_replace("700px",$SKIN_DEFINE['regist_docu']."px",$PRIV);
			$PRIV = str_replace("678px",$SKIN_DEFINE['regist_docu']."px",$PRIV);
			$tpl->parse("is_guest");
		}
	}

	$CARR_MONEY = $cash[13];
	$CARR_AREA = trim($cash[14]);
	
	if($my_id) {
		$sql = "SELECT a.signdate as dates, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$my_id}' && a.pid=b.uid && a.status ='A' ORDER BY a.uid desc";
		$mysql->query($sql);

		$CNUM = 0;
		while($row = $mysql->fetch_array()){			
			if($row['sdate'] && $row['edate'] && !$row['days']) {
				if(date("Y-m-d")>$row['edate']) continue;				
			}
			else {
				$tmps = date("Y-m-d", strtotime("+{$row['days']} DAY", $row['dates']));						
				if(date("Y-m-d")>$tmps) continue;			
			}
			$CNUM++;
		}

		if($CNUM>0) {
			$tpl->parse("is_cupon");
		}
	}

	$tpl->parse("is_list");	
}

if(!$cash[4]) $cash[4]=0;
$CARDUSE1 = $cash[4];
$CARDUSE2 = number_format($cash[4],$ckFloatCnt);
$RESEUSE = $mem_info['reserve'];
if(!$RESEUSE) $RESEUSE = 0;

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>