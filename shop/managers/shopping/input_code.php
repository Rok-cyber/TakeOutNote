<?
include "../ad_init.php";

######################## lib include
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";


###################### 변수 정의 ##########################
$disp		= isset($_GET['disp']) ? $_GET['disp'] : $_POST['disp'];
$bundle		= isset($_GET['bundle']) ? $_GET['bundle'] : $_POST['bundle'];
$goods_num	= isset($_GET['goods_num']) ? $_GET['goods_num'] : $_POST['goods_num'];
$brands		= isset($_GET['brands']) ? $_GET['brands'] : $_POST['brands'];
$events		= isset($_GET['events']) ? $_GET['events'] : $_POST['events'];
$patrons	= isset($_GET['patrons']) ? $_GET['patrons'] : $_POST['patrons'];

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./input_code.html");
$tpl->scan_area("main");

if($disp) {	
	$sec_mode = 'disp';
	$sec = $disp;
}

if($goods_num) {
	$sec_mode = 'goods_num';
}

if($sec_mode) {
	if($sec_mode=='goods_num') $tpl->parse("is_num");
	else $tpl->parse("is_mode");
}
else if($bundle) $tpl->parse("is_bundle");
else $tpl->parse("is_default");

$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/
