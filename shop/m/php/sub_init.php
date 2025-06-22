<?
@error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
header("Content-Type: text/html; charset=utf-8");

if(!function_exists('userAbortFunc')){
	//메모리제거
	function userAbortFunc() {

		global $mysql,$pg,$tpl;
		if(is_object($mysql)) $mysql->close();
		if(is_object($tpl)) $tpl->close();
		if(is_object($pg)) $pg->close();
		
	}
}

@ignore_user_abort(true); 
@register_shutdown_function('userAbortFunc');

$lib_path = "../../lib";
$inc_path = "../../include";

require "{$lib_path}/lib.Function.php";
include "{$inc_path}/dbconn.php";
require "{$lib_path}/class.Mysql.php";
include "{$lib_path}/checkLogin.php";
require "{$lib_path}/lib.Shop.php";

$_POST = array_map('add_escape_string', $_POST); 
$_GET = array_map('add_escape_string', $_GET); 

$mysql = new mysqlClass();

//스킨 설정
$sql = "SELECT code FROM mall_mobile WHERE mode = 'S'";
$tmp_skin = $mysql->get_one($sql);
if(!$tmp_skin) $tmp_skin = "default";

$skin		= "../skin/$tmp_skin";
$ShopPath	= "";
$Main		= "index.php";

/************* 이벤트/쿠폰 정의 ***************/
$sql = "SELECT uid, sale, point FROM mall_event WHERE s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."' ORDER BY s_date ASC";
$mysql->query($sql);
$EVENT_SALE = Array();
$EVENT_POINT = Array();
while($evt = $mysql->fetch_array()){	
	$EVENT_SALE[$evt['uid']] = 	$evt['sale'];
	$EVENT_POINT[$evt['uid']] = $evt['point'];	
}

$sql = "SELECT uid, sgoods FROM mall_cupon_manager WHERE type='3' ORDER BY uid DESC";
$mysql->query($sql);
$COUPON_GOODS = array();
$COUPON_UID	= array();
while($evt = $mysql->fetch_array()){
	$COUPON_GOODS[] = $evt['sgoods'];
	$COUPON_UID[] = $evt['uid'];
}
unset($evt);
/************* 이벤트/쿠폰 정의 ***************/

?>