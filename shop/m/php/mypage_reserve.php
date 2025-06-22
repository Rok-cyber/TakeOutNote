<?
$tpl->define("main","{$skin}/mypage_reserve.html");
$tpl->scan_area("main");

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;

if(!$limit) $limit = 12;

$sql = "SELECT COUNT(*) FROM  mall_reserve WHERE id='{$my_id}'  && status !='D'";
$TOTAL = $mysql->get_one($sql);

$sql = "SELECT SUM(IF(status='A',reserve,0)) as sum1, SUM(IF(status='B',reserve,0)) as sum2, SUM(IF(status='C',reserve,0)) as sum3 FROM  mall_reserve WHERE id='{$my_id}'";
$tmps = $mysql->one_row($sql);
$MONEY1 = $tmps['sum1'];
$MONEY2 = $tmps['sum2'];
$MONEY3 = $tmps['sum3'];

$TOTAL_MONEY = number_format($MONEY1 + $MONEY2 - $MONEY3,$ckFloatCnt);
$TOTAL_USE = number_format($MONEY2 - $MONEY3,$ckFloatCnt);
$MONEY1	= number_format($MONEY1,$ckFloatCnt);

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