<?
$tpl->define("main","{$skin}/customer_idpwsearch.html");
$tpl->scan_area("main");

$sql	= "SELECT code FROM mall_design WHERE mode='W'";
$tmp	= $mysql->get_one($sql);
$ssl	= explode("|*|",$tmp);
$sMain = "./";
if($ssl[0]==1) {
	$tmp    = explode("|",$ssl[2]);	
	if(in_array(3,$tmp)) {
		if($ssl[1]) $sport = ":{$ssl[1]}";
		$sMain = "https://".$_SERVER['HTTP_HOST']."{$sport}/{$ShopPath}";	
		unset($sport);
	}
	if(!$_GET['ssl']=='Y') movePage("{$sMain}index.php?channel=idpwsearch&ssl=Y");
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
var f = document.joinForm;
// -->
</SCRIPT>