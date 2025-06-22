<?
$tpl->define("main","{$skin}/mypage_wish.html");
$tpl->scan_area("main");

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$cgType = "list";

if(!$limit) $limit = 12;

$sql = "SELECT count(*) FROM mall_wish a, mall_goods b WHERE a.id='{$my_id}' && a.p_number=b.uid";
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