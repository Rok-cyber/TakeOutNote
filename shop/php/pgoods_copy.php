<?
include "sub_init.php";
include "$skin/skin_define.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

$uid = $_GET['uid'];

$skin = "../skin/$tmp_skin";
$skin2 = $skin."/";

$sql = "SELECT code FROM mall_design WHERE mode='A'";
$tmp_basic = $mysql->get_one($sql);
$basic = explode("|*|",stripslashes($tmp_basic));
//0:주소,1:쇼핑몰명,2:상호,3:대표자명,4:사번호,5:부가번호,6:주소,7:연락처,8:팩스,9:관리자명,10:이메일,11:타이틀,12:키워드,13:실시간검색어

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$sql = "SELECT * FROM mall_goods WHERE uid='{$uid}'";
if(!$data = $mysql->one_row($sql)) {
	alert('상품이 삭제 되었거나 없는 상품입니다.','close5');
}


$cate = $data['cate'];
$number = $data['number'];

$my_sale = $my_point = 0;

$gData	= getDisplay($data,'image3');		// 디스플레이 정보 가공 후 가져오기
$GOODS_IMG	= "http://".$_SERVER["SERVER_NAME"]."/{$ShopPath}{$gData[image]}";
$GOODS_NAME	= $gData['name'];		
$GOODS_PRICE= $gData['price'];
$GOODS_ICON	= $gData['icon'];
$GOODS_COMP = $gData['comp'];
$GOODS_RESERVE  = $gData['reserve'];

$GOODS_MADE		= stripslashes($data['made']);
$GOODS_EXPL		= stripslashes($data['explan']);
$GOODS_UNIT		= stripslashes($data['unit']);
$P_PRICE		= $data['price'];
$P_CATE			= $data['cate'];

$DATE = date("Y-m-d H:i");

$SHOP_NAME = $basic[1];
$SHOP_URL = "$basic[0]/{$shopPath}{$Main}";

if($_GET['type']=='mail') $tpl->define('main',"{$skin}/pgoods_mail.html");
else $tpl->define('main',"{$skin}/pgoods_copy.html");
$tpl->scan_area('main');

if($COODS_RESERVE>0) $tpl->parse('is_reserve'); 
if($data['consumer_price'] && $data['consumer_price'] >0){
	$GOODS_C_PRICE = number_format($data['consumer_price'],$ckFloatCnt)."원";
    $tpl->parse('is_c_price'); 
}

if($data['brand']>0) {
	$sql = "SELECT name,uid FROM mall_brand WHERE uid='{$data['brand']}'";
	$tmps = $mysql->one_row($sql);
	$GOODS_BRAND = stripslashes($tmps['name']);
	$tpl->parse("is_brand");
}

if($GOODS_COMP) $tpl->parse("is_comp");
if($GOODS_MADE) $tpl->parse("is_made");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>