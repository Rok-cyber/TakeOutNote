<?
$LINK1 = $PHP_SELF;
$CO_TEL	= $basic[7];
$CO_NAME2	= $basic[1];

if(!$my_name) $my_name = $_GET['name'];

$tpl->define("main",$skin."/regist_03.html");
$tpl->scan_area("main");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>