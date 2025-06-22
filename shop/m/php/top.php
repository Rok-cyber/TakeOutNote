<?
$img_path = "../image/mobile/";
$sql = "SELECT code FROM mall_mobile WHERE mode='C'";
$common = explode("|*|",$mysql->get_one($sql));

#################### 로고 ########################
if($common[2]) {
	$LOGO = "<img src='{$img_path}{$common[2]}' alt='logo' />";
}
else $LOGO = $BM_NAME;

$tpl->define("main","{$skin}/top.html");
$tpl->scan_area("main");

if($my_id) $tpl->parse("is_logout");
else $tpl->parse("is_login");

if(!$channel) $tpl->parse("is_main");
else $tpl->parse("is_sub");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>