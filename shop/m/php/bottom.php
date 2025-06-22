<?
$tpl->define("main",$skin."/bottom.html");
$tpl->scan_area("main");

$CO_LINK	= $basic[14];
if(!$CO_LINK) $CO_LINK = "http://www.ftc.go.kr/info/bizinfo/communicationList.jsp";
else {
	$CO_LINK = str_replace("&","&amp;",$CO_LINK);
}

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
$START_YEAR = date("Y",$mysql->get_one($sql));

if(!$channel) $tpl->parse("is_main");
else $tpl->parse("is_sub");

if(!$my_id) {
	$CKLOGIN = "N";
}
else {
	$CKLOGIN = "Y";
}

$tpl->parse("main");
$tpl->tprint("main");
?>