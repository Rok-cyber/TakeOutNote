<?
$tpl->define("main","{$skin}/mypage_menu.html");
$tpl->scan_area("main");

$sql = "SELECT valid FROM mall_cate WHERE cate = '999000000000'";
$valid = $mysql->get_one($sql);
if($valid==1) $tpl->parse("is_cooperate");

/***********************  BANNER  ********************************/
$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '7' && status='1' ORDER BY rank ASC";
$mysql->query($sql);
while($row_ban = $mysql->fetch_array()){
	if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
	if($row_ban['link']) {
		$BLINK = str_replace("&","&amp;",$row_ban['link']);
		if($row_ban['target']=='2') $BTARGET = "target='_blank'";
		else $BTARGET = "";
	}
	else $BLINK = "#\" onclick=\"return false;";

	$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner3'],stripslashes($row_ban['name']));
	$tpl->parse("loop_banner");	
}
unset($target);
/***********************  BANNER  ********************************/

$tpl->parse("main");
$MY_MENU = $tpl->tprint("main","1");
$tpl->close();
?>