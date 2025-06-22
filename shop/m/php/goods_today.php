<?
$tpl->define("main","{$skin}/goods_today.html");
$tpl->scan_area("main");

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$cgType = "list";

if(!$limit) $limit = 12;

@session_start();
if($_SESSION['today_view']) {
	$tmp = explode(',',$_SESSION['today_view']);
	$TOTAL = count($tmp)-1;
}
else $TOTAL = 0;

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