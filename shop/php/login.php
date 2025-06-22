<?
if($my_id && $type=='osearch') movePage("{$Main}?channel=order");

include "$skin/skin_define.php";
$type = $_GET['type'];

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

$tpl->define("main","{$skin}/login.html");
$tpl->scan_area("main");

/***********************  BANNER  ********************************/
$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '4' && status='1' ORDER BY rank ASC";
$mysql->query($sql);
while($row_ban = $mysql->fetch_array()){
	if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
	if($row_ban['link']) {
		$BLINK = str_replace("&","&amp;",$row_ban['link']);
		if($row_ban['target']=='2') $BTARGET = "target='_blank'";
		else $BTARGET = "";
	}
	else $BLINK = "#\" onclick=\"return false;";

	$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner4'],stripslashes($row_ban['name']));
	$tpl->parse("loop_banner");		
}
unset($BLINK, $BTARGET, $BLINK);
/***********************  BANNER  ********************************/

if($_COOKIE['s_id']) {
	$S_ID =  base64_decode($_COOKIE['s_id']);
	$CKDID = "checked";
}

if($type=='osearch') $tpl->parse("is_osearch");
else $tpl->parse("is_logins");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>