<?
$vendor = isset($_GET['vendor']) ? $_GET['vendor'] : $_GET['detail'];
if(!$vendor) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');

$sql = "SELECT * FROM mall_vendor WHERE id='{$vendor}'";
if(!$row=$mysql->one_row($sql)) alert("삭제된 미니샵이거나 존재하지 않는 미니샵 입니다","back");

$tpl->define("main",$skin."/minishop.html");
$tpl->scan_area("main");

if($row['m_name']) $CNAME = stripslashes($row['m_name']);
else $CNAME = stripslashes($row['comp']);

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;	
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$search	= isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
if(!$limit) $limit = 12;

$addstring = "?channel={$channel}&vendor={$vendor}";
$ajaxstring = "&vendor={$vendor}";

if($search) {
	$ajaxstring .= "&search=".urlencode($search);
	$sstring .= "&search=".urlencode($search);	
	$where .= "  && (INSTR(name,'{$search}') || INSTR(search_name,'{$search}'))";		
}

$where .= "&& vendor='{$vendor}'";
$pstring = "&page={$page}";
$where2 = $where;

$sql = "SELECT COUNT(uid) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";
$TOTAL = $mysql->get_one($sql);

if(!$search) $tpl->parse("is_total");
else $tpl->parse("is_search");

if($TOTAL) {
	$record_num = $limit; 

	/*********************************** LIMIT CONFIGURATION ***********************************/
	$Pstart = $record_num*($page-1);
	$TOTAL_PAGE = ceil($TOTAL/$record_num);	
	if($TOTAL <= ($page * $record_num)) $TONUM = $TOTAL;
	else $TONUM = $record_num; 
	$PAGE = $page;
	/*********************************** @LIMIT CONFIGURATION ***********************************/	
}
else $tpl->parse("no_content");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

//include "php/counter_vendor.php";

?>