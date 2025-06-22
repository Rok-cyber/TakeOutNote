<?
$order_num = $_GET['order_num'];
if(!$order_num)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

$sql = "SELECT * FROM mall_order_info WHERE order_num = '{$order_num}'";
$row = $mysql->one_row($sql);

if(!$row) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

if($row['pay_status']=='B') {
	alert("이미 결제가 성공되었습니다.","{$MAIN}?channel=order_end&order_num={$order_num}");
}

$sql = "SELECT p_name, count(*) as cnt FROM mall_order_goods WHERE order_num='{$order_num}' GROUP BY order_num";
$tmps = $mysql->one_row($sql);
if($tmps['cnt']>1) $title = "{$tmps['p_name']} 외".($tmps['cnt']-1)."건 구입";
else $title= $tmps['p_name']." 구입";

$order_id	= stripslashes($row['id']);
$email		= stripslashes($row['email']);
$order_name = stripslashes($row['name1']);
$order_tel	= stripslashes($row['hphone1']);
$rece_name	= stripslashes($row['name2']);
$rece_tel	= stripslashes($row['tel2']);
$rece_cell	= stripslashes($row['hphone2']);
$rece_zip	= stripslashes($row['zipcode']);
$rece_addr	= stripslashes($row['address']);
$cash_total = $row['pay_total'];
$cash_type	= $row['pay_type'];

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));

$SHOP_ID = trim($cash[3]);
$SHOP_KEY = trim($cash[15]);
$TESTMODE = $cash[16];

$MAIN = "http://".$_SERVER["HTTP_HOST"]."/{$ShopPath}m/";
$LINK = "{$MAIN}index.php?channel=card_pay&order_num={$order_num}";

if($cash[23]=="1") {
	$EUSE = "Y";
	$EUSE2 = "O";
}
else {
	$EUSE = "N";
	$EUSE2 = "N";
}

$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$order_num}'";
$GCNT = $mysql->get_one($sql);

switch($cash[2]){
	case "KCP" : 
		switch($cash_type) {
			case "C" : 
				$PAYMODE = "100000000000"; 
				if($cash[20]) $halbu = trim($cash[20]);
				else $halbu = '12';
				if($cash[21]=='1') $noint = "Y";
				else $noint = "N";
				$noint_str = trim($cash[22]);

				$CTYPE = "CARD";
				$CTYPE2 = "card";
				$CNAME = "카드결제";
				$EUSE = "N";

			break;
			case "R" : 
				$PAYMODE = "010000000000"; 
				$CTYPE = "BANK";
				$CTYPE2 = "acnt";
				$CNAME = "실시간계좌이체";
				$EUSE = "N";
			break;
			case "V" : 
				$PAYMODE = "001000000000"; 
				$CTYPE = "VCNT";
				$CTYPE2 = "vcnt";
				$CNAME = "가상계좌이체";
			break;
			case "H" : 
				$PAYMODE = "000010000000"; 
				$CTYPE = "MOBX";
				$CTYPE2 = "mobx";
				$CNAME = "핸드폰결제";
				$EUSE = "N";
			break;
		}
		$onload = "chk_pay();";
		include "card/kcp/kcp.php";
	break;
}

$tpl->define("main",$skin."/card_pay.html");
$tpl->scan_area("main");
$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

if($_GET['ck']!=2) {
	echo "<script>window.onload = function(){ {$onload} }</script>";
}
?>