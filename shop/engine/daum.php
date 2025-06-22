<?
@set_time_limit(0);
ob_start();
include "../php/sub_init.php";

$my_point = 0;
$my_sale = 0;

$mode = $_GET['mode'];
if($mode && ($mode!='summ' && $mode!='new')) alert("잘못된 주소 입니다.","close");
$sql = "SELECT code FROM mall_design WHERE mode='X'";
$row = $mysql->get_one($sql);
$row = explode("|",$row);
if($row[12]!=1) alert("다음쇼핑하우엔진페이지 미사용 상태 입니다.","close");
$exword = $row[13];
$fname = "daum{$mode}";

$times = @filectime("./data/{$fname}.txt");
if($times>time()-600) {
	echo readFiles("./data/{$fname}.txt");
	exit;
}

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$cate_arr = Array();
$brand_arr = Array();
$sql = "SELECT cate_name, cate FROM mall_cate ORDER BY cate ASC";
$mysql->query($sql);

while($data = $mysql->fetch_array()){
	$cate_arr[$data['cate']] = $data['cate_name'];
}

$sql = "SELECT * FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);

while($data = $mysql->fetch_array()){
	$brand_arr[$data['uid']] = $data['name'];
}

$sql = "SELECT uid, name, sale, point FROM mall_event WHERE s_date <= '".date("Y-m-d")."' && e_date >'".date("Y-m-d")."' ORDER BY s_date ASC";
$mysql->query($sql);
$EVENT_SALE = Array();
$EVENT_POINT = Array();
$EVENT_NAME = Array();

while($evt = $mysql->fetch_array()){	
	$EVENT_SALE[$evt['uid']] = 	$evt['sale'];
	$EVENT_POINT[$evt['uid']] = $evt['point'];	
	$EVENT_NAME[$evt['uid']] = $evt['name'];	
}

$sql = "SELECT uid, sgoods FROM mall_cupon_manager WHERE type='3' ORDER BY uid DESC";
$mysql->query($sql);

$coupon_goods = array();
$coupon_uid		= array();
while($data = $mysql->fetch_array()){
	$coupon_goods[] = $data['sgoods'];
	$coupon_uid[] = $data['uid'];
}

if($mode=="new") {
	$where = " && signdate > '".(time()-86400)."'";
}

$sql = "SELECT * FROM mall_goods WHERE type='A' {$where} ORDER BY cate ASC, number ASC";
$mysql->query($sql);

while($data = $mysql->fetch_array()){
			
	$gData	= getDisplay($data,'image2');		// 디스플레이 정보 가공 후 가져오기	

	$LINK	= "http://".$_SERVER['SERVER_NAME']."/".$gData['link'];
	$LINK   = str_replace("&amp;","&",$LINK);
	$IMAGE	= "http://".$_SERVER['SERVER_NAME']."/".$gData['image'];
	$NAME	= $gData['name'];
	$PRICE	= $gData['price'];	
	$PRICE	= str_replace(array(",","원"),"",$PRICE);
	$RESE	= $gData['reserve'];
	$UID	= "SHOP_".$data['uid'];
	$BRAND  = $brand_arr[$data['brand']];
	
	if($exword) {
		$tmps = str_replace("{brand}",$BRAND,$exword);
		$tmps = str_replace("{model}",$data['model'],$tmps);
		$tmps = str_replace("{maker}",$data['comp'],$tmps);
		$NAME = "{$tmps} {$NAME}";
	}
	
	if(substr($data['cate'],9,3)!='000') {
		$cate4 = $cate_arr[$data['cate']];
	} 
	else $cate4 = '';
	if(substr($data['cate'],6,3)!='000') {
		$cate3 = $cate_arr[substr($data['cate'],0,9)."000"];
	} 
	else $cate3 = '';
	if(substr($data['cate'],3,3)!='000') {
		$cate2 = $cate_arr[substr($data['cate'],0,6)."000000"];
	}
	else $cate2 = '';
	$cate1 = $cate_arr[substr($data['cate'],0,3)."000000000"];
	
	$CARR = 0;

	$carriage = explode("|",$data['carriage']);
	if($carriage[0]==1) { //없음
		$CARR = 0;
		$CARR2 = '';
	}
	else if($carriage[0]==3) { //별도 책정일때
		$CARR = 1;
		$CARR2 = $carriage[1];
	}
	else {
		if($cash[10] =='1') { 
			$CARR = 2;
			$CARR2 = "{$cash[11]}원미만 {$cash[12]}원";
		} 
	}
	
	$COUPON = '';
	for($j=0,$cnt=count($coupon_goods);$j<$cnt;$j++) {
		$ck_coupon = explode("|",$coupon_goods[$j]);
		if(in_array($data['uid'],$ck_coupon)) {
			$sql = "SELECT * FROM mall_cupon_manager WHERE uid='{$coupon_uid[$j]}'";
			if($cupon = $mysql->one_row($sql)){
				$tmp_ck = 1;
				if($cupon['sdate'] && $cupon['edate'] && !$cupon['days']) {
					if(date("Y-m-d") < substr($cupon['sdate'],0,10) || date("Y-m-d") > substr($cupon['edate'],0,10)) $tmp_ck = 0;
				}

				if($tmp_ck==1) {		
					$tmp_price = $gData['oprice'];
					if($cupon['stype']=='P') $COUPON = $cupon['sale']."%";
					else $COUPON =  $cupon['sale']."원"; 
					break;
				}				
			}
		}
	}
	
	$times = @filectime("../{$gData['image']}");
	$times = date("Ymdhis",$times);

	switch($mode) {
		case "summ" :
			echo "<<<begin>>>\r\n<<<pid>>>{$UID}\r\n<<<price>>>{$PRICE}\r\n<<<pname>>>{$NAME}\r\n<<<end>>>\r\n";
		break;
		
		default :
			echo "<<<begin>>>\r\n<<<pid>>>{$UID}\r\n<<<price>>>{$PRICE}\r\n<<<pname>>>{$NAME}\r\n<<<pgurl>>>{$LINK}\r\n<<<igurl>>>{$IMAGE}\r\n<<<updateimg>>>{$times}\r\n<<<cate1>>>".substr($data['cate'],0,3)."\r\n<<<cate2>>>".substr($data['cate'],0,6)."\r\n<<<cate3>>>".substr($data['cate'],0,9)."\r\n<<<cate4>>>{$cate}\r\n<<<catename1>>>{$cate1}\r\n<<<catename2>>>{$cate2}\r\n<<<catename3>>>{$cate3}\r\n<<<catename4>>>{$cate4}\r\n<<<model>>>{$data['model']}\r\n<<<brand>>>{$BRAND}\r\n<<<maker>>>{$data['comp']}\r\n<<<pdate>>>\r\n<<<coupon>>>{$COUPON}\r\n<<<pcard>>>\r\n<<<point>>>{$RESE}\r\n<<<deliv>>>{$CARR}\r\n<<<deliv2>>>{$CARR2}\r\n<<<end>>>\r\n";
		break;
	}
}

$tmps = ob_get_contents();
ob_end_flush(); 
ob_end_clean(); 
writeFile("./data/{$fname}.txt",$tmps);
?>