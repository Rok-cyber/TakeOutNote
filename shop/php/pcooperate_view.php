<?
include "sub_init.php";
include "$skin/skin_define.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

if(!$my_id) alert("먼저 로그인을 하시기 바랍니다.","close5");

$uid = $_GET['uid'];
$QTY = $_GET['qty'];
$p_option = $_GET['p_option'];

$skin = "../skin/{$tmp_skin}";
$skin2 = $skin."/";
$ShopPath = "../";

$sql = "SELECT count(*) FROM mall_cooperate WHERE id='{$my_id}' && guid='{$gid}'";
if($mysql->get_one($sql)>0) alert("이미 공동구매를 신청 하셨습니다. 신청수량을 변경 하실려면 마이페이지에서 취소 후 다시 신청 하시기 바랍니다.","close5");

$sql = "SELECT hphone, email FROM pboard_member WHERE id='{$my_id}'";
$row = $mysql->one_row($sql);

$EMAIL = stripslashes($row['email']);
$tmps = explode("@",$EMAIL);
$EMAIL1 = $tmps[0];
$EMAIL2 = $tmps[1];

$phone = explode(" - ",$row['hphone']);
$PHONE11 = !empty($phone[0]) ? $phone[0] : '010';
$PHONE12 = $phone[1];
$PHONE13 = $phone[2];


$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$sql = "SELECT * FROM mall_goods WHERE uid='{$uid}'";
if(!$data = $mysql->one_row($sql)) alert('상품이 삭제 되었거나 없는 상품입니다.','close5');

$today	= date("Y-m-d H:i");	
$COOP_TIME = strtotime($data['coop_edate']);
$SER_TIME = time();

if($data['coop_sdate']>$today) { //공구 준비중"
	$TYPE = 3;
	$COOP_TIME = strtotime($data['coop_sdate']);
}
else if($data['coop_edate']<$today) {  //공구 마감
	$TYPE = 2; 
}
else $TYPE = '';

if($data['s_qty']==4) {	
	if($data['qty']<=($data['coop_cnt'])) $TYPE = 2;
	if($data['qty']<=($data['coop_cnt']+$qty)) {
		$ck_qty = $data['qty']-$date['coop_cnt'];
		alert("공동구매 총수량을 넘었습니다. 신청수량을 {$ck_qty}개로 변경하셔서 다신 신청 하시기 바랍니다.","close5");
	}
}

$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
$GOODS_NAME	= $gData['name'];		
$GOODS_PRICE= $gData['price'];
$GOODS_ICON	= $gData['icon'];
$GOODS_RESERVE  = $gData['reserve'];

$DEF_QTY		= stripslashes($data['def_qty']);
$GOODS_UNIT		= stripslashes($data['unit']);

$tpl->define('main',"{$skin}/pcooperate_view.html");
$tpl->scan_area('main');

/************************* 배송비 관련 ***********************/
$carriage = explode("|",$data['carriage']);
if($carriage[0]==3) { //별도 책정일때
	$GOODS_CARR = number_format($carriage[1],$ckFloatCnt)."원";	
	$tpl->parse("is_carr");
}	
/************************* 배송비 관련 ***********************/

/**************************** GOODS OPTIONS **************************/
$tmps = $OP_TITLE = $OP_VALUES = "";

$sql = "SELECT option1 FROM mall_goods_option WHERE guid='{$uid}' GROUP BY option1 ORDER BY o_num ASC";
$mysql->query($sql);
	
$option_arr = Array();
while($row2 = $mysql->fetch_array()){
	$option_arr[] = $row2['option1'];
}


/**************************** GOODS OPTIONS **************************/

$participation = $data['coop_cnt'];

$sql = "SELECT * FROM mall_goods_cooper WHERE guid='{$uid}' ORDER BY qty ASC";
$mysql->query($sql);

$coop_arr = Array();
while($data2=$mysql->fetch_array()) {
	if($data2['qty'] && $data2['price']) {
		$coop_arr[] = Array($data2['qty'],$data2['price']);
	}
}

$COOP_CNT = $participation;
$COOP_PRICE1	= str_replace("원","",$GOODS_PRICE);		
$cnt	=count($coop_arr);

if($coop_arr[0][0]>$participation) {
	$COOP_PRICE = $COOP_PRICE1;
}
else {	
	if($cnt==1) {
		$COOP_PRICE = number_format($coop_arr[0][1],$ckFloatCnt);
		$i=0;
	}
	else {
		if($coop_arr[$cnt-1][0]<$participation) {
			$COOP_PRICE = number_format($coop_arr[$cnt-1][1],$ckFloatCnt);						
		}
		else {
			for($i=0;$i<$cnt;$i++) {								
				if($coop_arr[$i][0]>=$participation) {
					$COOP_PRICE = number_format($coop_arr[$i][1],$ckFloatCnt);						
					break;
				}	
			}
		}
	}
	$i++;
	if($i==$cnt) {
		//공구가 확정
	}	
}

$now_price = str_replace(",","",$COOP_PRICE);
$start_price = str_replace(",","",$COOP_PRICE1);
$COOP_SALE = 100 - round((100*$now_price)/$start_price);
$COOP_PRICE .= "원";

if($GOODS_ORESERVE>0) {		
	/************************* 적립금 관련 ***********************/		
	$reserve = explode("|",$data['reserve']);
	if($reserve[0] =='2') { //쇼핑몰 정책일때
		if($cash[6] =='1') { 
			$tmp_reserve = round(($now_price * $cash[8])/100,$ckFloatCnt);
		} 
	} 
	else if($reserve[0] =='3') { //별도 책정일때
		$tmp_reserve = round(($now_price * $reserve[1])/100,$ckFloatCnt);
	}				
	$GOODS_RESERVE = number_format($tmp_reserve,$ckFloatCnt);
	/************************* 적립금 관련 ***********************/
}

if($TYPE) $tpl->parse("is_limit_{$TYPE}");
else $tpl->parse("is_limit_1");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>

<script LANGUAGE="JavaScript">
<!--
var f = document.goodsForm;
// -->
</script>