<?php
@error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
ob_start();
$lib_path = "./lib";
$inc_path = "./include";

ini_set("session.use_trans_sid", 0);
ini_set("url_rewriter.tags","");

require "{$lib_path}/lib.Function.php";
include "$inc_path/dbconn.php";
require_once "{$lib_path}/class.Mysql.php";
require "{$lib_path}/class.Template.php";
include "{$lib_path}/checkLogin.php";
include "{$lib_path}/lib.Shop.php";

$mysql = new mysqlClass();
$tpl = new classTemplate;

$_POST = array_map('add_escape_string', $_POST); 
$_GET = array_map('add_escape_string', $_GET); 

if($_GET['PC']=='Y') {
	SetCookie("PC",'Y',0,"/");
}

if($_COOKIE['PC']!='Y' && $_GET['PC']!='Y') {
	$mobile_agent = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|HTC)/';
	if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])) {
		$sql = "SELECT code FROM mall_mobile WHERE mode = 'C'";
		$tmps = $mysql->get_one($sql);
		$tmps = explode("|*|",stripslashes($tmps));

		if($tmps[0]==1 && $tmps[1]==1) {  //모바일샵 자동연결
			movePage("./m/");
		}
	}
}

//스킨 설정
$sql = "SELECT code FROM mall_design WHERE mode = 'G'";
$tmp_skin = $mysql->get_one($sql);
if(!$tmp_skin) $tmp_skin = "default";

$skin = "skin/$tmp_skin";
$ShopPath	= "";

if(!file_exists("{$skin}/skin_define.php")) Error("스킨파일이 존재하지 않습니다.");
include "{$skin}/skin_define.php";

$sql = "SELECT code FROM mall_design WHERE mode='A'";
$tmp_basic = $mysql->get_one($sql);
$basic = explode("|*|",stripslashes($tmp_basic));
//0:주소,1:쇼핑몰명,2:상호,3:대표자명,4:사번호,5:부가번호,6:주소,7:연락처,8:팩스,9:관리자명,10:이메일,11:타이틀,12:키워드,13:실시간검색어
if(!$basic[11]) $basic[11] = "항상 즐거운 쇼핑몰!";
$BM_URL = $basic[0];
$BM_NAME = $basic[1];

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비,13:추가배송비,14:추가배송지역,15:상점키,16:테스트모드,17:계좌이체,18:가상계좌,19:핸드폰,20:할부기간,21:무이자,22:무이자기간,23:에스크로사용여부

$Main	 = "index.php";
$channel = isset($_GET['channel']) ? $_GET['channel']:'';
if($_GET['a_id']) {
	SetCookie("a_id",$_GET['a_id'],0,"/");
}

if(eregi("/",$channel)) {
	$tmps = explode("/",$channel);
	$channel = $tmps[0];
	$_GET['uid'] = $tmps[1];
	$_GET['cate'] = $tmps[2];
}

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

if($channel=="view" && $_GET['uid']) {
	$sql = "SELECT name FROM mall_goods WHERE uid='{$_GET['uid']}'";
	$basic[11] = stripslashes(html2txt($mysql->get_one($sql)));
}

if(!$channel) $body_class = "main";
else $body_class = "sub";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> -->

<!-- HEAD START -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Author" content="itsmall, previl" />
<meta name="Keywords" content="<?=$basic[12]?>" />
<meta name="Description" content="" />
<title><?=$basic[11]?></title>

<script type="text/javascript" src="lib/lib.Function.js"></script>
<script type="text/javascript" src="lib/lib.lightBox.js"></script>
<script type="text/javascript" src="lib/lib.popupBox.js"></script>
<script type="text/javascript" src="lib/lib.messageBox.js"></script>
<script type="text/javascript" src="lib/lib.ckForm.js"></script>
<script type="text/javascript" src="lib/lib.ajax.js"></script>
<script type="text/javascript" src="<?=$skin?>/skin.lib.Shop.js"></script>
<link rel="StyleSheet" href="<?=$skin?>/style.css" type="text/css" title="style" />

<script type="text/javascript">
<!--
shop_skin = '<?=$skin?>/';
paths = "";

<?php
if($channel!="view") {
	$listArray = array("list","main2","best","new","reco","event","brand","special","search");
	if(in_array($channel,$listArray)) echo "if(getCookie('mallUrl')!=window.location.href) { \n";
	echo "delCookie('mallPage');\ndelCookie('mallOrder');\ndelCookie('mallLimit');delCookie('mallBest');\ndelCookie('mallType');\n";
	if(in_array($channel,$listArray)) echo "}";
	unset($listArray);
}
?>
// -->
</script>


</head>
<!-- @HEAD END -->

<body class="<?=$body_class?>">

<!-- TOP INCLUDE -->
<?php include "php/top.php"; ?>
<!-- @TOP INCLUDE -->

<!-- CHANNEL CODE INCLUDE -->
<?php 
if(!$my_id) {
	$ck_arr = Array('mypage','order','wish','reserve','modify','passwd','quit','counsel','qna','after','cupon','attendance');

	if(in_array($channel,$ck_arr) || ($channel=='order_detail' && !$_POST['name'])) {
		$channel2 = $channel;
		$channel = "login";		
    }	
}

$cate = isset($_GET['cate']) ? $_GET['cate']:$_POST['cate'];
if($channel=='view' && !$cate) {
	$sql = "SELECT cate FROM mall_goods WHERE uid='{$_GET['uid']}'";
	if(!$cate = $mysql->get_one($sql)) {
		if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('해당상품이 삭제되었거나 존재하지 않습니다.',"{$Main}");
		else alert('해당상품이 삭제되었거나 존재하지 않습니다.','back');
	}
}

if($channel) {
	include "php/location.php";	//LOCATION
	include "php/cate_menu.php"; // $CATE_MENU 정의
}

if($ck_login!='2'){
	switch ($channel) {
		case "main2" : case "list" : case "view" :			
			if($channel=="main2") {
				$sql ="SELECT cate_sub FROM mall_cate WHERE cate='{$cate}'";
				if($mysql->get_one($sql)!='1') $channel = "list";
			}		
			include "php/{$channel}.php";
        break;

		case "customer" : case "board" : case "idpwsearch" : case "docu" : case "osearch" : case "after2" : case "qna2" :
			include "php/customer_menu.php"; // $CUS_MENU 정의
			if($channel=='customer') include "php/customer.php"; 
			else if($channel=='osearch') include "php/mypage_order_detail.php";
			else if($channel=='after2') include "php/customer_after.php"; 
			else if($channel=='qna2') include "php/customer_qna.php"; 
			else include "php/customer_{$channel}.php"; 
		break;	

		case "mypage" : case "wish" : case "reserve" : case "order" : case "order_cancel" : case "order_detail" : case "modify" : 
		case "passwd" : case "quit" : case "counsel" : case "after" : case "qna" : case "cupon" : case "cooperate_list" :
			include "php/mypage_menu.php"; // $MY_MENU 정의
			if($channel=='mypage') include "php/mypage.php"; 
			else if($channel=='modify') include "php/regist.php"; 
			else if($channel=='cooperate_list') include "php/mypage_cooperate.php"; 
			else include "php/mypage_{$channel}.php"; 
		break;	
		
		case "login" : case "login2" : case "regist" : case "regist2" : 
		case "card_pay" : case "search" : case "tag" : case "rss" : case "cooperate" : case "attendance" :
			include "php/{$channel}.php";
		break;

		case "brand" : case "special" : case "event" : 
			include "php/sbe_page.php";
		break;
		
		case "cart" : 
			include "php/order_01.php"; 
		break;
		case "order_form" : 
			include "php/order_02.php"; 
		break;		
		case "order_end" : 
			include "php/order_03.php"; 
		break;
				
		case "plus" : include "php/plus_page.php"; 
		break;
		case "today" : case "new" : case "reco" : case "best" :
			include "php/goods_{$channel}.php"; 
		break;	
		
		default : include "php/main.php"; 
		break;
	}
}
?>
<!-- @CHANNEL CODE INCLUDE -->
<!-- BOTTOM INCLUDE -->
<?php include "php/bottom.php"; ?>
<!-- @BOTTOM INCLUDE -->

<iframe name="HFrm" id="HFrm" style="display:none"></iframe>

<script type="text/javascript">
<!--
setTimeout(reLoads,10000000);

function reLoads(){
	window.location.reload();
}

window.onscroll = function () {
	if(document.getElementById('pLightLayer')) {		
					
		function moveLayer2(){		
			tObj2 = document.getElementById('pLightLayer');
			if(tObj2==null) return;
			tTo2  = (document.body.scrollTop || document.documentElement.scrollTop) + pLightBox.Top;
			tFrom2= parseInt (tObj2.style.top);

			if (tFrom2 != tTo2) {
				yOffset2 = Math.ceil(Math.abs(tTo2 - tFrom2) / 15);  //속도
				
				if (tTo2 < tFrom2) yOffset2 = -yOffset2;          
				
				tObj2.style.top = (tFrom2 + yOffset2) + 'px';		
				tId2=setTimeout(moveLayer2,50); 
			} 
			else {
				if(typeof(tId2) != "undefined") clearTimeout(tId2);			
			}	
		}
		moveLayer2();
	}

	if(typeof(skinOnScroll)=='function') {
		skinOnScroll();
	}
}
// -->
</script>

</body>
</html>

<?php 

/****************** 팝업창 *******************************/
if(!$channel) {
	$PDATE = date("Y-m-d");
	$sql = "SELECT uid,subject, info,days,type FROM mall_popup WHERE status ='1' && ((SUBSTRING(end_date,1,10) > '{$PDATE}' || SUBSTRING(end_date,1,10) = '0000-00-00'))";	
	$mysql->query($sql);
	while($tmp_pinfo = $mysql->fetch_array()){
		$pop_name = "pop_".$tmp_pinfo['uid'];		
		
		if(!$_COOKIE[$pop_name] || $tmp_pinfo['days']=='2'){
			$pinfo = explode("|",$tmp_pinfo['info']);
			if($tmp_pinfo['type']==1) {		
				echo "<script>\n
						  window.open('php/pop_up.php?uid={$tmp_pinfo['uid']}','{$pop_name}','top=$pinfo[0], left=$pinfo[1], width=$pinfo[2], height=".($pinfo[3]+20).", scrollbars=no');\n
					  </script>\n
					";
			}
			else {				
				echo "<script>\n
						openPopup({$tmp_pinfo['uid']}, {$pinfo[0]}, {$pinfo[1]}, {$pinfo[2]}, {$pinfo[3]});\n
					  </script>\n
						";
			}
		}
	}
	include "php/counter.php";   // 카운터
}

if(!$channel || $channel=='main2' || $channel=='list' || $channel=='view') {
	if($_GET['a_id']) {
		$affiliate = $_GET['a_id'];
		include "php/counter_affiliate.php";   // 카운터
	}
}
/****************** 팝업창 *******************************/

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


?>