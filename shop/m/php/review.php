<?
$tpl->define("main","{$skin}/review.html");
$tpl->scan_area("main");

$number	= isset($_POST['number']) ? $_POST['number'] : $_GET['number'];
$limit	= isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";

if(!$number) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');
if(!$limit) $limit = 12;

$ajaxstr = "&number={$number}";

$sql =  "SELECT uid,cate,number,name,price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE uid='{$number}'";
if(!$row = $mysql->one_row($sql)) alert('해당상품이 삭제되었거나 존재하지 않습니다.','back');

$gData	= getDisplay($row,'image4');		// 디스플레이 정보 가공 후 가져오기
$GOODS_LINK		= $gData['link'];
$GOODS_IMAGE	= "../".$gData['image'];
$GOODS_NAME		= $gData['name'];
$GOODS_PRICE	= $gData['price']; //판매가
$GOODS_PRICE2	= str_replace("원","",$gData['price']);	

$sql = "SELECT count(*) as cnt, SUM(point) as sum FROM mall_goods_point WHERE uid>0 && number='{$number}'";
$tmps = $mysql->one_row($sql);

$TOTAL = $tmps['cnt'];
if($TOTAL==0) $SUM_AFTER = 0;
else $SUM_AFTER = round(($tmps['sum']*2)/$tmps['cnt'],1);
$SUM_AFTER = sprintf("%01.1f", $SUM_AFTER);
$PER_AFTER = ($SUM_AFTER*10);

if($TOTAL>0) {
	/*********************************** LIMIT CONFIGURATION ***********************************/
	$record_num = $limit; 
	$Pstart = $record_num*($page-1);
	$TOTAL_PAGE = ceil($TOTAL/$record_num);	
	$PAGE = $page;
	/*********************************** @LIMIT CONFIGURATION ***********************************/
}
else $tpl->parse("no_loop");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>