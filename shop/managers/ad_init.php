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

$bo_path	= "../../pboard";      
$lib_path	= "../../lib";
$inc_path	= "../../include";
$ShopPath	= "";
$ckFloatCnt = "0";

require "$lib_path/lib.Function.php";
include "$inc_path/dbconn.php";
require "$lib_path/class.Mysql.php";

$mysql = new  mysqlClass(); //디비 클래스

$_POST = array_map('add_escape_string', $_POST); 
$_GET = array_map('add_escape_string', $_GET); 

if(!$_COOKIE['my_id']) movePage("../html/login.html");      //로그인

require "$lib_path/checkLogin.php";      //로그인 체크

if($my_level < 9) {
	$sql = "SELECT code FROM mall_design WHERE name='{$my_level}' && mode='L'";
	$tmps = $mysql->get_one($sql);
	if(!$tmps) Error("접속권한이 없는 페이지 입니다.");
	
	$tmps = explode("|",$tmps);
	if($tmps[1]==0) Error("접속권한이 없는 페이지 입니다.");
	
	$sql = "SELECT code FROM mall_design WHERE name='{$tmps[1]}' && mode='P'";
	$tmps = $mysql->get_one($sql);
	if(!$tmps) Error("접속권한이 없는 페이지 입니다.");

	$tmps = explode("|",$tmps);
}
else if($my_level==9) {
	$sql = "SELECT code FROM mall_design WHERE name='3' && mode='P'";
	$tmps = $mysql->get_one($sql);	
	if(!$tmps) $tmps = "1||1|1|1|";

	$tmps = explode("|",$tmps);

}
else {
	$tmps = "1|1|1|1|1|1";
	$tmps = explode("|",$tmps);
}

if($tmps[0]==1) $ACCESS_MANAGER['goods'] = 'Y';
else $ACCESS_MANAGER['goods'] = 'N';

if($tmps[1]==1) $ACCESS_MANAGER['order'] = 'Y';
else $ACCESS_MANAGER['order'] = 'N';

if($tmps[2]==1) $ACCESS_MANAGER['design'] = 'Y';
else $ACCESS_MANAGER['design'] = 'N';

if($tmps[3]==1) $ACCESS_MANAGER['member'] = 'Y';
else $ACCESS_MANAGER['member'] = 'N';

if($tmps[4]==1) $ACCESS_MANAGER['etc'] = 'Y';
else $ACCESS_MANAGER['etc'] = 'N';

if($tmps[5]==1) $ACCESS_MANAGER['conf'] = 'Y';
else $ACCESS_MANAGER['conf'] = 'N';

if($ShopPath) $_SERVER['REQUEST_URI'] = str_replace($ShopPath,"",$_SERVER['REQUEST_URI']);
$menu_name = (explode("/",$_SERVER['REQUEST_URI']));

if($my_level!=10) {
	switch($menu_name[2]) {
		case "shopping" :
			if($ACCESS_MANAGER['goods'] != 'Y') Error("접속권한이 없는 페이지 입니다.");
		break;

		case "order" :
			if($ACCESS_MANAGER['order'] != 'Y') Error("접속권한이 없는 페이지 입니다.");
		break;

		case "design" :
			if($ACCESS_MANAGER['design'] != 'Y') Error("접속권한이 없는 페이지 입니다.");
		break;

		case "member" :
			if($ACCESS_MANAGER['member'] != 'Y') Error("접속권한이 없는 페이지 입니다.");
		break;

		case "conf" :
			if($ACCESS_MANAGER['conf'] != 'Y') Error("접속권한이 없는 페이지 입니다.");
		break;			

		case "html" :			
			if($my_level<10) {
				$access_arr = Array('goods','order','design','member','etc','conf');
				$movepage_arr = Array('shopping/goods_list.php','order/order_main.html','design/main.html','member/member_list.php','board/board_list.php','conf/conf.html');
				for($i=0;$i<6;$i++) {
					if($ACCESS_MANAGER[$access_arr[$i]] == 'Y') {
						movePage("../".$movepage_arr[$i]);
					}
				}
				exit;
			}
		break;			

		case "etcs" :
			if(eregi("sale.php|affiliate|data|excel",$menu_name[3])) {
				if($ACCESS_MANAGER['order'] != 'Y') Error("접속권한이 없는 페이지 입니다.");
			} 
			else {
				if($ACCESS_MANAGER['etc'] != 'Y') Error("접속권한이 없는 페이지 입니다.");
			}
		break;



		default : 
			$code = $_GET['code'];
			switch($code) {
				case "add_page" : 
					if($ACCESS_MANAGER['design'] != 'Y') Error("접속권한이 없는 페이지 입니다."); 
				break;
				case "affiliate" :  case "affiliate_banner" :  case "affiliate_acount" : 
					if($ACCESS_MANAGER['design'] != 'Y') Error("접속권한이 없는 페이지 입니다."); 
				break;
				case "reserve" : case "member_quit" : case "sms_addr" : case "sms_list" : case "sms_sample" : 
					if($ACCESS_MANAGER['member'] != 'Y') Error("접속권한이 없는 페이지 입니다."); 
				break;
				case "goods_point" : case "goods_qna" : case "brand" :  case "event" : case "special" : 
					if($ACCESS_MANAGER['goods'] != 'Y') Error("접속권한이 없는 페이지 입니다."); 
				break;
				default : 
					if($ACCESS_MANAGER['etc'] != 'Y') Error("접속권한이 없는 페이지 입니다."); 
				break;
			}
		break;
	}
}


if($skin_inc=='Y') {
	/******************** 스킨 설정 *************************/
	$sql = "SELECT code FROM mall_design WHERE mode = 'G'";
	$tmp_skin = $mysql->get_one($sql);
	if(!$tmp_skin) $tmp_skin = "default";

	include "../../skin/{$tmp_skin}/skin_define.php";
}
$SMain = "../../index.php";


/************* 이벤트 정의 ***************/
$sql = "SELECT uid, sale, point FROM mall_event WHERE s_date <= '".date("Y-m-d")."' && e_date >'".date("Y-m-d")."' ORDER BY s_date ASC";
$mysql->query($sql);
$EVENT_SALE = Array();
$EVENT_POINT = Array();
while($evt = $mysql->fetch_array()){	
	$EVENT_SALE[$evt['uid']] = 	$evt['sale'];
	$EVENT_POINT[$evt['uid']] = $evt['point'];	
}
unset($evt);
/************* 이벤트 정의 ***************/
?>