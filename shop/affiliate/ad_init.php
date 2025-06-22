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
$ckAdminPage = 'Y';

require "$lib_path/lib.Function.php";
include "$inc_path/dbconn.php";
require "$lib_path/class.Mysql.php";

$mysql = new  mysqlClass(); //디비 클래스

$_POST = array_map('add_escape_string', $_POST); 
$_GET = array_map('add_escape_string', $_GET); 

$_POST['logInPage'] = '';
$_GET['logInPage'] = '';

if($logInPage!='Y') {
	if(!$_COOKIE['a_my_id']) movePage("../html/login.html");      //로그인
	require "{$lib_path}/checkALogin.php";      //로그인 체크
}

if($ShopPath) $_SERVER['REQUEST_URI'] = str_replace($ShopPath,"",$_SERVER['REQUEST_URI']);
$menu_name = (explode("/",$_SERVER['REQUEST_URI']));

if($skin_inc=='Y') {
	/******************** 스킨 설정 *************************/
	$sql = "SELECT code FROM mall_design WHERE mode = 'G'";
	$tmp_skin = $mysql->get_one($sql);
	if(!$tmp_skin) $tmp_skin = "default";

	include "../../skin/{$tmp_skin}/skin_define.php";
}
$SMain = "../../index.php";
?>