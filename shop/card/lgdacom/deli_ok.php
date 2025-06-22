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
	echo "False1";
	exit;
}

$sql = "SELECT carr_info, pay_number FROM mall_order_info WHERE order_num='{$order_num}'";
$row = $mysql->one_row($sql);

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

$deli_arr = array("대한통운"=>"KE","로젠택배"=>"LG","아주택배"=>"AJ","옐로우캡"=>"YC","우체국택배"=>"PO","이젠택배"=>"EZ","트라넷"=>"TN","한진택배"=>"HJ","현대택배"=>"HD","동부익스프레스"=>"FE","Bell Express"=>"BE","CJ"=>"CJ GLS","HTH"=>"SS","KGB택배"=>"KB","KT로지스택배"=>"KT","SC로지스택배"=>"SC","일양로지스"=>"IY","이노지스택배"=>"IN","하나로택배"=>"HN","대신택배"=>"DS","우편등기"=>"RP");

if($TESTMODE==1) {
	$service_url = "http://pgweb.uplus.co.kr:7085/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp";
	$mertkey = "95160cce09854ef44d2edb2bfb05f9f3";  //LG 텔레콤에서 발급한 상점키로 변경해 주시기 바랍니다.
	$mid = "test";
}
else {
	$service_url = "https://pgweb.uplus.co.kr/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp"; 
	$mertkey = $SHOP_KEY;
	$mid = $SHOP_ID;
}

						// 상점ID
$oid = $order_num;						// 주문번호
$dlvtype = "03";				// 등록내용구분
$dlvdate =  date("Ymdhis");			// 발송일자
$dlvcompcode = $deli_arr[$CNAME];	// 배송회사코드
if(!$dlvcompcode) $dlvcompcode = $CNAME;
$dlvcomp =  $CNAME		;		// 배송회사명
$dlvno =  $CNUM;			// 운송장번호
$dlvworker =  "";	// 배송자명
$dlvworkertel =  "";	// 배송자전화번호

$hashdata = md5($mid.$oid.$dlvdate.$dlvcompcode.$dlvno.$mertkey);


$service_url .= "?mid=$mid&oid=$oid&dlvtype=$dlvtype&dlvdate=$dlvdate&dlvcompcode=$dlvcompcode&dlvno=$dlvno&dlvworker=$dlvworker&dlvworkertel=$dlvworkertel&hashdata=$hashdata"; 
$rtnVls = implode("", socketPost($service_url));

if(eregi("FAIL",$rtnVls)) echo "False";

?>