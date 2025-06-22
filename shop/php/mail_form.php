<?
if(!$lib_path) include "sub_init.php";

if(!$tpl) {
	require "{$lib_path}/class.Template.php";
	$tpl = new classTemplate;
}

if(!$basic) {
	$sql = "SELECT code FROM mall_design WHERE mode='A'";
	$tmp_basic = $mysql->get_one($sql);
	$basic = explode("|*|",stripslashes($tmp_basic));
	//0:주소,1:쇼핑몰명,2:상호,3:대표자명,4:사번호,5:부가번호,6:주소,7:연락처,8:팩스,9:관리자명,10:이메일
}

if(!$tmp_skin) {
	//스킨 설정
	$sql = "SELECT code FROM mall_design WHERE mode = 'G'";
	$tmp_skin = $mysql->get_one($sql);
	if(!$tmp_skin) $tmp_skin = "default";
	if($skin_path) $skin = "{$skin_path}/skin/{$tmp_skin}";
	else $skin = "../skin/{$tmp_skin}";
}

$SKIN = "http://".$_SERVER["HTTP_HOST"]."/{$ShopPath}skin/{$tmp_skin}";
$CO_NUM1	= $basic[4];
$CO_NUM2	= $basic[5];
$CO_AD		= $basic[3];
$CO_ADDR	= $basic[6];
$CO_TEL		= $basic[7];
$CO_FAX		= $basic[8];
$CO_SAD		= $basic[9];
$CO_EMAIL	= $basic[10];
$CO_NAME	= $basic[2];
$CO_NAME2	= $basic[1];

$sql = "SELECT signdate FROM pboard_member WHERE uid=1";
$SYEAR = date("Y",$mysql->get_one($sql));

$tpl->define("main","{$skin}/mail_form.html");
$tpl->scan_area("main");

$tpl->parse("is_{$mail_type}");

if($send_type==2 || $send_type==3 || $send_type==4) $tpl->parse("is_mailling");

if($basic[8]) $tpl->parse("is_fax");

$tpl->parse("main");
$mail_form = $tpl->tprint("main","1");
$tpl->close();
?>