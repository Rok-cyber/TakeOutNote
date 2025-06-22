<?
$cate	= isset($_GET['cate']) ? $_GET['cate'] : $_GET['detail'];;
$limit	= isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$search	= isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
$cgType = "list";

if(!$cate) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');
if(!$limit) $limit = 12;

$tpl->define("main","{$skin}/list.html");
$tpl->scan_area("main");

$row = rtnCate($cate);
$SCATE_NAME = $row['cate_name'];
if($row['list_mode']==2) $cgType = "img";

$CATE = $row['cate'];
$CATE_NAME	 = stripslashes($row['cate_name'])." 전체보기"; 				
$CHANNEL = "list";	
$tpl->parse("loop_cate");	

for($i=1;$i<=$row['cate_dep'];$i++) {
	$cate2 = substr($cate,0,$i*3);
	$row2 = rtnCate($cate2,$i*3);
	
	if($row['cate_dep']==$i) $CATE_LOCA = $row2['cate_name'];
	else $CATE_LOCA = $row2['location'];
	$tpl->parse("loop_scate");		
}

if(substr($cate,3,3)=='000') {
	$ajaxstring = "&cate=".substr($cate,0,3);
	$where .= "&& (SUBSTRING(cate,1,3) = '".substr($cate,0,3)."' || INSTR(mcate,',".substr($cate,0,3)."'))";
}
else if(substr($cate,6,3)=='000') {
	$ajaxstring = "&cate=".substr($cate,0,6);
	$where .= "&& (SUBSTRING(cate,1,6) = '".substr($cate,0,6)."' || INSTR(mcate,',".substr($cate,0,6)."'))";
}
else if(substr($cate,9,3)=='000' && $cate_sub==1) {
	$ajaxstring = "&cate=".substr($cate,0,9);
	$where .= "&& (SUBSTRING(cate,1,9) = '".substr($cate,0,9)."' || INSTR(mcate,',".substr($cate,0,9)."'))";
}
else {
	$ajaxstring = "&cate2={$cate}";
	$where .= "&& (cate='{$cate}' || INSTR(mcate,'{$cate}'))";
}

if($search) {
	$ajaxstring .= "&search=".urlencode($search);
	$sstring .= "&search=".urlencode($search);	
	$where .= "  && (INSTR(name,'{$search}') || INSTR(search_name,'{$search}'))";		
}

$cstring = "&cate={$cate}";
$pstring = "&page=1";

$sql = "SELECT COUNT(uid) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";
$TOTAL = $mysql->get_one($sql);

if(!$search) $tpl->parse("is_total");
else $tpl->parse("is_search");

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