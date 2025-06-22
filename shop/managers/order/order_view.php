<?
$pop = $_GET['pop'];
if($pop) {
	$skin_inc = "Y";
	include "../ad_init.php";
}
else {
	include "../html/top_inc.html"; // 상단 HTML 
}

require "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$order_num	= $_GET['order_num'];
$gs			= $_GET['gs'];
$field		= $_GET['field'];
$word		= $_GET['word'];
$smoney1	= $_GET['smoney1'];
$smoney2	= $_GET['smoney2'];
$sdate1		= $_GET['sdate1'];
$sdate2		= $_GET['sdate2'];
$page		= $_GET['page'];
$limit		= $_GET['limit'];
$type		= $_GET['type'];
$status		= $_GET['status'];
$mobile		= $_GET['mobile'];

$img_path	= '../../image/goods_img';

##################### addstring ############################
if($gs) $addstring2 ="gs={$gs}";
if($field && $word) $addstring .= "&field=$field&word={$word}";
if($seccate) $addstring .= "&seccate={$seccate}";
if($smoney1 && $smoney2) $addstring .= "&smoney1={$smoney1}&smoney2={$smoney2}";
if($sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";
if($page) $addstring .="&page={$page}";
if($limit) $addstring .="&limit={$limit}";
if($type) $addstring .="&type={$type}";
if($status) $addstring .="&status={$status}";
if($pop) $addstring .="&pop={$pop}";
if($mobile) $addstring .="&mobile={$mobile}";

$skin = ".";
$mysql = new  mysqlClass(); //디비 클래스

$tpl = new classTemplate;
$tpl->define("main","order_view.html");
$tpl->scan_area("main");

/**************************** GOODS LIST**************************/
$sql = "SELECT order_status FROM mall_order_info WHERE order_num='{$order_num}'";
$def_status = $mysql->get_one($sql);

$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' ORDER BY p_cate ASC";
$mysql->query($sql);

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$TCNT = $total = $carr = $trese = 0;
$G_NAME = $G_OP = array();
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
		$STEP .= "<br /><span id='nextIcon' class='hand small' onclick='window.open(\"{$tmp['code']}{$tmps[1]}\");'>배송조회</span>";		
	}

    $LTIME = substr($data['status_date'],0,16);
	$LTIME = str_replace(" ","<br />",$LTIME);

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
	
	if(($data['order_status']!='Z' && $data['order_status']!='X' ) || $def_status=='Z' || $data['order_status2']=='A') {	
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

$sql = "SELECT * FROM mall_order_change WHERE order_num = '{$order_num}' ORDER BY signdate ASC";
$mysql->query($sql);

$NUM = 0;
while($data = $mysql->fetch_array()){
	$NUM++;
	$tmps = explode(",",$data['sgoods']);
	$GNAME = array();	
	for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {		
		if($G_NAME[$tmps[$i]]) {
			$GNAME[$i] = "- ".$G_NAME[$tmps[$i]];						
			if($G_OP[$tmps[$i]]) $GNAME[$i] .= " ({$G_OP[$tmps[$i]]})";
		}
	}
	$GNAME = @join("<br />",$GNAME);
	$REASON = $reason_code_arr[$data['reason_code']];
	$NAME = stripslashes($data['name']);
	$MEMO = stripslashes($data['message']);
	$STATE = $status_arr2[$data['status'].$data['status2']];
	$STATE2 = '';
	if($data['status2']=='D' && ($data['refund']>0 || $data['refund_r']>0)) $STATE2 .= "<font class='small green'>(환불완료)</font>";
	if($data['status2']!='Z' && $def_status!='Z') {		
		if(($data['status']!='Y' && $data['status2']=='D' && $def_status!='A') || $data['status2']=='B') {
			$STATE2 = "<span id='nextIcon' class='hand small' onclick='location.href=\"order_change_ok.php?mode=restore&order_num={$order_num}&uid={$data['uid']}{$addstring}\";'>복원</span>";		
		}
		
		switch($data['status']) {
			case "Z" :
				if($data['status2']=='A') {
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change.html?order_num={$order_num}&uid={$data['uid']}&status=Z','iframe','750','550','■ 취소요청 승인처리','20');\">취소승인처리</span>";
				}
				else if($data['status2']=='B') {
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change_refund.php?order_num={$order_num}&uid={$data['uid']}','iframe','750','550','■ 취소상품 환불처리','20');\">환불처리</span>";
				}
			break;
			
			case 'X' :
				if($data['status2']=='A') {
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change.html?order_num={$order_num}&uid={$data['uid']}&status=X','iframe','750','550','■ 반품요청 승인처리','20');\">반품승인처리</span>";
				}
				else if($data['status2']=='B') {
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick='location.href=\"order_change_ok.php?mode=return&order_num={$order_num}&uid={$data['uid']}{$addstring}\";'>회수완료처리</span>";
				}
				else if($data['status2']=='C') {
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change_refund.php?order_num={$order_num}&uid={$data['uid']}','iframe','750','550','■ 반품상품 환불처리','20');\">환불처리</span>";
				}
			break;

			case 'Y' :								
				if($data['status2']=='A') {
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change.html?order_num={$order_num}&uid={$data['uid']}&status=Y','iframe','750','550','■ 교환요청 승인처리','20');\">교환승인처리</span>";
				}
				else if($data['status2']=='B') {
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick='location.href=\"order_change_ok.php?mode=return&order_num={$order_num}&uid={$data['uid']}{$addstring}\";'>회수완료처리</span>";
				}
				else if($data['status2']=='C') {
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change_send.php?order_num={$order_num}&uid={$data['uid']}','iframe','750','450','■ 교환상품 발송처리','20');\">교환발송처리</span>";
				}
			break;
		}

	}
	
	if($data['status2']=='Z') {
		$STATE2 .= "<span id='nextIcon' class='hand small' onclick='location.href=\"order_change_ok.php?mode=del&order_num={$order_num}&uid={$data['uid']}{$addstring}\";'>삭제</span>";
	}
	$DATE = substr($data['status_date'],0,16);
	$tpl->parse("change_loop");
}
if($NUM>0) $tpl->parse("is_change");

/*************************** 환불내역 **************************/
$sql = "SELECT * FROM mall_order_change WHERE order_num = '{$order_num}' && status!='Y' && status2='D' && (refund>0 || refund_r>0) ORDER BY signdate ASC";
$mysql->query($sql);

$NUM2 = 0;
while($data = $mysql->fetch_array()){
	$NUM2++;
	$tmps = explode(",",$data['sgoods']);
	$GNAME = array();	
	for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {		
		if($G_NAME[$tmps[$i]]) {
			$GNAME[$i] = "- ".$G_NAME[$tmps[$i]];						
			if($G_OP[$tmps[$i]]) $GNAME[$i] .= " ({$G_OP[$tmps[$i]]})";
		}
	}
	$GNAME = @join("<br />",$GNAME);
	$REFUND1 = number_format($data['refund_g'] - $data['refund']);
	$REFUND2 = number_format($data['refund']);
	$REFUND3 = number_format($data['refund_r']);
	if($data['refund_type']=='B') $INFOS = $data['bank_info'];
	else $INFOS = "카드결제 취소/부분취소 처리";
	$DATE = substr($data['status_date'],0,16);
	$tpl->parse("refund_loop");
}
if($NUM2>0) $tpl->parse("is_refund");
/*************************** 환불내역 **************************/

/*************************** 적립금내역 **************************/
$reserve_arr = Array("A"=>"<font class='small green'>적립대기</font>","B"=>"<font class='small orange'>적립완료</font>","C"=>"<font class='small blue'>적립사용</font>","D"=>"적립취소","E"=>"사용취소");
$sql = "SELECT * FROM mall_reserve WHERE order_num = '{$order_num}' || goods_num = '{$order_num}' ORDER BY signdate ASC";
$mysql->query($sql);

$NUM3 = 0;
while($data = $mysql->fetch_array()){
	$NUM3++;
	$LIST2 = stripslashes($data['subject']);
	$LIST3 = $data['id'];
	$LIST4 = number_format($data['reserve']);
	$LIST5 = $reserve_arr[$data['status']];
	$LIST6 = substr($data['signdate'],0,16);
	
	$tpl->parse("reserve_loop");	
}
if($NUM3>0) $tpl->parse("is_reserves");
/*************************** 적립금내역 **************************/


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

if(($C_TOTAL2-$info['use_reserve']-$info['use_cupon']) != $info['pay_total'] && $def_status!='Z') {
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
$tel= explode(" - ",$info['tel2']);
$TEL1 = $tel[0];
$TEL2 = $tel[1];
$TEL3 = $tel[2];
$phone = explode(" - ",$info['hphone2']);
$PHONE1 = $phone[0];
$PHONE2 = $phone[1];
$PHONE3 = $phone[2];
$zip = explode(" - ",$info['zipcode']);
$ZIP1 = $zip[0];
$ZIP2 = $zip[1];
$ADDR = stripslashes($info['address']);
$MESSAGE = nl2br(stripslashes($info['message']));
$ADMESS = htmlspecialchars(stripslashes($info['admess']));
$tmps = htmlspecialchars(stripslashes($info['carr_info']));
$tmps = explode("|",$tmps);
$THIS_STEP = $status_arr[$info['order_status']];

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

if($info['order_status']=='Z') $tpl->parse("is_delete");
if($info['order_status']=='A' && $ck_cancel!='Y') $tpl->parse("is_cancel");

if($info['affiliate']) {
	$AFFILIATE = stripslashes($info['affiliate']);
	$AFFILI_COMMI = number_format((($info['pay_total'] + $info['use_reserve'] - $info['carriage'])*$info['a_commi'])/100,$ckFloatCnt)."원 ({$info['a_commi']}%)";
	$tpl->parse("is_affiliate");
}


$ACTION = "order_post.php?{$addstring2}{$addstring}";
$LIST = "order_list.php?{$addstring2}{$addstring}";

if($pop==1) {
	$tpl->parse("is_pop_top");
	$tpl->parse("is_pop_show");
	$tpl->parse("is_pop_bottom");
}
else $tpl->parse("is_pop_hide");

$tpl->parse("main");
$tpl->tprint("main");


if(!$pop) include "../html/bottom_inc.html"; // 하단 HTML
?>