<?
$tpl->define("main","{$skin}/mypage_order.html");
$tpl->scan_area("main");

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;

if(!$limit) $limit = 12;

$sql = "SELECT COUNT(uid) FROM mall_order_info WHERE id='{$my_id}'";
$TOTAL = number_format($mysql->get_one($sql));

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