<?
$tpl->define("main","{$skin}/customer_menu.html");
$tpl->scan_area("main");

$sql  = "SELECT category FROM pboard_manager WHERE name = 'faq'";
$category = $mysql->get_one($sql);
$category = explode("|",$category);
for($i=1;$i<=$category[0];$i++) {
	$CNAME = stripslashes($category[$i]);	
	$tpl->parse("loop_faq_cate");
}

$sql  = "SELECT category FROM pboard_manager WHERE name = 'counsel'";
$category = $mysql->get_one($sql);
$category = explode("|",$category);
for($i=1;$i<=$category[0];$i++) {
	$CNAME = stripslashes($category[$i]);	
	$tpl->parse("loop_counsel_cate");
}

unset($CNAME,$category,$i);


/***********************  BANNER  ********************************/
$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '3' && status='1' ORDER BY rank ASC";
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

if($my_id) $tpl->parse("is_login");
else $tpl->parse("is_logout");

$tpl->parse("main");
$CUS_MENU = $tpl->tprint("main","1");
$tpl->close();
?>