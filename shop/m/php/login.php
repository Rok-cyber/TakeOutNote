<?
if($my_id && $type=='osearch') movePage("{$Main}?channel=order");

$tpl->define("main","{$skin}/login.html");
$tpl->scan_area("main");

$type	= $_GET['type'];
$num	= $_GET['num'];

$sql	= "SELECT code FROM mall_design WHERE mode='W'";
$tmp	= $mysql->get_one($sql);
$ssl	= explode("|*|",$tmp);
$sMain = "./";
if($ssl[0]==1) {
	$tmp    = explode("|",$ssl[2]);	
	if(in_array(2,$tmp)) {
		if($ssl[1]) $sport = ":{$ssl[1]}";
		$sMain = "https://".$_SERVER['HTTP_HOST']."{$sport}/{$ShopPath}";			
	}
}
unset($ssl,$sport,$tmp);

if($_COOKIE['s_id']) {
	$S_ID =  base64_decode($_COOKIE['s_id']);
	$CKDID = "checked";
}
else $S_ID = "";

if($type=='vorder' || $type=='cart' || $type=='corder') {
	if($type=='corder') $corder = '&amp;direct=Y';
	$tpl->parse("is_guest_order");	
}
else if($type=='osearch') $tpl->parse("is_osearch");
else if($type=='view') $url = "view";

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>