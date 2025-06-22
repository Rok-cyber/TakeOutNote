<?

$sql	= "SELECT code FROM mall_design WHERE mode='W'";
$tmp	= $mysql->get_one($sql);
$ssl	= explode("|*|",$tmp);
$sMain = "./";
if($ssl[0]==1) {
	$tmp    = explode("|",$ssl[2]);	
	if(in_array(4,$tmp)) {
		if($ssl[1]) $sport = ":{$ssl[1]}";
		$sMain = "https://".$_SERVER['HTTP_HOST']."{$sport}/{$ShopPath}";	
		unset($sport);
	}
}
$tpl->define("main","{$skin}/mypage_passwd.html");
$tpl->scan_area("main");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
var f = document.joinForm;
// -->
</SCRIPT>