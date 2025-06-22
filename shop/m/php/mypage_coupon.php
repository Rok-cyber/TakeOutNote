<?
$tpl->define("main","{$skin}/mypage_coupon.html");
$tpl->scan_area("main");

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;

if(!$limit) $limit = 12;

$sql = "SELECT a.signdate as dates, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$my_id}' && a.pid=b.uid && a.status ='A' ORDER BY a.uid desc";
$mysql->query($sql);

$ABLE_COUPON = 0;
while($row = $mysql->fetch_array()){			
	if($row['sdate'] && $row['edate'] && !$row['days']) {
		if(date("Y-m-d")>$row['edate']) continue;				
	}
	else {
		$tmps = date("Y-m-d", strtotime("+{$row['days']} DAY", $row['dates']));						
		if(date("Y-m-d")>$tmps) continue;			
	}
	$ABLE_COUPON++;
}

$sql = "SELECT COUNT(*) FROM  mall_cupon WHERE id='{$my_id}'  && status !='D'";
$TOTAL = $mysql->get_one($sql);

if($TOTAL>0) {
	/*********************************** LIMIT CONFIGURATION ***********************************/
	$record_num = $limit; 
	$Pstart = $record_num*($page-1);
	$TOTAL_PAGE = ceil($TOTAL/$record_num);	
	$PAGE = $page;
	/*********************************** @LIMIT CONFIGURATION ***********************************/	
}
else $tpl->parse("no_content");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>