<?
$uid = isset($_GET['uid']) ? $_GET['uid'] : $_GET['detail'];

if(!$uid && $channel!='event') alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');

if($channel=='event') {
	if(!$uid) {
		$sql = "SELECT uid FROM mall_event WHERE s_check='1' && s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."' LIMIT 1";
		$uid = $mysql->get_one($sql);
	}
	
	$sql = "SELECT * FROM mall_event WHERE uid='{$uid}' && s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."'";
	if(!$row = $mysql->one_row($sql)) alert('페이지가 존재하지 않거나 종료된 이벤트 입니다.',"{$Main}?channel=event");	
}
else {
	$sql = "SELECT * FROM mall_{$channel} WHERE uid='{$uid}'";
	if(!$row = $mysql->one_row($sql)) alert('페이지가 존재하지 않습니다.\\n\\n다시 확인해 주시기 바랍니다.','back');
}

$tpl->define("main",$skin."/goods_{$channel}.html");
$tpl->scan_area("main");

$CNAME = stripslashes($row['name']);

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;	
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$search	= isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
if(!$limit) $limit = 12;

$addstring = "?channel={$channel}&uid={$uid}";
$ajaxstring = "&{$channel}={$uid}";

if($search) {
	$ajaxstring .= "&search=".urlencode($search);
	$sstring .= "&search=".urlencode($search);	
	$where .= "  && (INSTR(name,'{$search}') || INSTR(search_name,'{$search}'))";		
}

if($channel=='special') $where .= "&& INSTR(special,',{$uid},')";
else $where .= "&& {$channel}='{$uid}'";

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

?>