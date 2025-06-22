<?
$lib_path = "../../lib";
$inc_path = "../../include";

require "{$lib_path}/lib.Function.php";
include "{$inc_path}/dbconn.php";
require "{$lib_path}/class.Mysql.php";
include "{$lib_path}/checkLogin.php";
$mysql = new mysqlClass();

if($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) {
	echo "False";
	exit;
}

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));

$SHOP_ID = trim($cash[3]);
$SHOP_KEY = trim($cash[15]);
$TESTMODE = $cash[16];

$order_num = $_GET['order_num'];
if(!$order_num) {
	echo "False";
	exit;
}

$sql = "SELECT carr_info, pay_number FROM mall_order_info WHERE escrow='Y' && order_num='{$order_num}'";
$row = $mysql->one_row($sql);

if(!$row['pay_number']) {
	echo "False";
	exit;
}

if($row['carr_info']) {
	$tmps = explode("|",$row['carr_info']);
	$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
	$tmp = $mysql->one_row($sql);
	$CNAME = $tmp['name'];
	$CNUM = $tmps[1];	
}
else {
	echo "False";
	exit;
}
?>

<form name="mod_escrow_form" action="pp_ax_hub.php" method="post">
<? if($TESTMODE==1) { ?>
	<!-- 테스트 결제시 : T0000 으로 설정, 리얼 결제시 : 부여받은 사이트코드 입력 -->
	<input type='hidden' name='site_cd'         value='T0007'>
	<!-- http://testpay.kcp.co.kr/Pay/Test/site_key.jsp 로 접속하신후 부여받은 사이트코드를 입력하고 나온 값을 입력하시기 바랍니다. -->
	<input type='hidden' name='site_key'        value='4Ho4YsuOZlLXUZUdOxM1Q7X__'>
<? } else { ?>
	<input type='hidden' name='site_cd'         value='<?=$SHOP_ID?>'>
	<input type='hidden' name='site_key'        value='<?=$SHOP_KEY?>'>
<? } ?>

<input type='hidden' name='req_tx'   value='mod_escrow'>
<input type='hidden' name='acnt_yn'  value='N'>
<input type='hidden' name='mod_type'  value='STE1'>
<input type='text' name='tno' value='<?=$row['pay_number']?>'>
<input type='text' name='deli_numb' value='<?=$CNAME?>'>
<input type='text' name='deli_corp' value='<?=$CNUM?>'>
</form>

<script>document.mod_escrow_form.submit();</script>