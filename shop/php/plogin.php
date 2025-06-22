<?
include "sub_init.php";
require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;

$type	= $_GET['type'];
$num	= $_GET['num'];

$sMain = "../";

$sql	= "SELECT code FROM mall_design WHERE mode='W'";
$tmp	= $mysql->get_one($sql);
$ssl	= explode("|*|",$tmp);

if($ssl[0]==1) {
	$tmp    = explode("|",$ssl[2]);	
	if(in_array(2,$tmp)) {
		if($ssl[1]) $sport = ":{$ssl[1]}";
		$sMain = "https://".$_SERVER["HTTP_HOST"]."{$sport}/{$ShopPath}";			
	}
}
unset($ssl,$sport,$tmp);

$skin = "../skin/{$tmp_skin}";
$skin2 = $skin."/";

$tpl->define("main","{$skin}/plogin.html");
$tpl->scan_area("main");

if($_COOKIE['s_id']) {
	$S_ID =  base64_decode($_COOKIE['s_id']);
	$CKDID = "checked";
}

if($type) {
	if($type=='direct') $tpl->parse("is_direct");
	else if($type=='direct2') $tpl->parse("is_direct2");
	else if($type!='cart' && $type!='view' && $type!='cooper' && $type!='login') $tpl->parse("is_type");
}
else {
	$type = "order";
	$tpl->parse("no_type");
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>