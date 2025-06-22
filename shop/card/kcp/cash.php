<?
$lib_path = "../../lib";
$inc_path = "../../include";

require "{$lib_path}/lib.Function.php";
include "{$inc_path}/dbconn.php";
require "{$lib_path}/class.Mysql.php";
include "{$lib_path}/checkLogin.php";
$mysql = new mysqlClass();

$uid = $_GET['uid'];
$mode = $_GET['mode'];

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));

$SHOP_ID = trim($cash[3]);
$SHOP_KEY = trim($cash[15]);
$TESTMODE = $cash[16];

if($mode=='cancel') {
	$sql = "SELECT * FROM mall_order_cash WHERE uid='{$uid}'";
	$row = $mysql->one_row($sql);
	
	if($row['status']!='B') {
		echo "False";
		exit;
	}

	$_POST['test_mode'] = $TESTMODE;
	$_POST['req_tx'] = "mod";
	$_POST['mod_type'] = "STSC";
	$_POST['mod_gubn'] = "MG02";
	$_POST['mod_value'] = $row['receipt_no'];
	$_POST['trad_time'] = $row['trad_time'];
}
else {
	
	$sql = "SELECT code FROM mall_design WHERE mode='O'";
	$code = $mysql->get_one($sql);
	if(!$code)  {
		echo "False";
		exit;
	}
	$code = explode("|",stripslashes($code));
	if($code[3]==1) $TAX_TYPE = 1;
	else $TAX_TYPE = 2;

	$sql = "SELECT code FROM mall_design WHERE mode='A'";
	$tmp1 = $mysql->get_one($sql);
	$basic=explode("|*|",stripslashes($tmp1));
	$COMP_NUM = $basic[4];
	$COMP_NAME = $basic[2];
	$COMP_OWNER = $basic[3];
	$COMP_ADDR = $basic[6];
	$COMP_ADDR = str_replace(array("'","\"","|",",",":","&","\n","\\"),"",$COMP_ADDR);
	$COMP_TEL = $basic[7];		

	$sql = "SELECT * FROM mall_order_cash WHERE uid='{$uid}'";
	$row = $mysql->one_row($sql);

	if($row['status']!='A') {
		echo "False";
		exit;
	}

	if($row['order_num']) {
		$sql = "SELECT count(*) FROM mall_order_cash WHERE order_num='{$row['order_num']}' && status='B'";
		if($mysql->get_one($sql)>0) {
			echo "False";
			exit;
		}
		$ORDER_NUM = $row['order_num'];
	}
	else {
		$signdate = date("Y-m-d H:i:s",time());
		$order_num = rand(1000,9999);
		$time_header = date("y-md-His",time());
		$ORDER_NUM = "CASH_".$time_header."-".$order_num;
		$sql = "UPDATE mall_order_cash SET order_num='{$ORDER_NUM}' WHERE uid='{$uid}'";
		$mysql->query($sql);
	}

	$row['goods_name'] = str_replace(array("'","\"","|",",",":","&","\n","\\"),"",$row['goods_name']);
	$row['name'] = str_replace(array("'","\"","|",",",":","&","\n","\\"),"",$row['name']);
	$row['cell'] = str_replace(" - ","-",$row['cell']);

	$TTIME = date("YmdHis",time());
	if($row['cash_type']=='A') $CASH_TYPE = "0";
	else $CASH_TYPE = "1";

	if($TAX_TYPE==1) {
		$PRICE = $row['price'];
		$PRICE1 = round(($row['price']/1.1),0);
		$PRICE2 = $PRICE - $PRICE1;
	}
	else {
		$PRICE = $PRICE1 = $row['price'];
		$PRICE2 = 0;
	}

	$_POST['test_mode'] = $TESTMODE;
	$_POST['req_tx'] = "pay";
	$_POST['ordr_idxx'] = $ORDER_NUM;
	$_POST['good_name'] = $row['goods_name'];
	$_POST['buyr_name'] = $row['name'];
	$_POST['buyr_mail'] = $row['email'];
	$_POST['buyr_tel1'] = $row['cell'];
	$_POST['comment'] = '';
	$_POST['corp_type'] = "0";
	$_POST['corp_tax_type'] = $TAX_TYPE;
	$_POST['corp_tax_no'] = $COMP_NUM;
	$_POST['corp_nm'] = $COMP_NAME;
	$_POST['corp_owner_nm'] = $COMP_OWNER;
	$_POST['corp_addr'] = $COMP_ADDR;
	$_POST['corp_telno'] = $COMP_TEL;
	$_POST['trad_time'] = $TTIME;
	$_POST['tr_code'] = $CASH_TYPE;
	$_POST['id_info'] = $row['auth_number'];
	$_POST['amt_tot'] = $PRICE;
	$_POST['amt_sup'] = $PRICE1;
	$_POST['amt_svc'] = "0";
	$_POST['amt_tax'] = $PRICE2;
}

include "pp_cli_hub.php";
?>