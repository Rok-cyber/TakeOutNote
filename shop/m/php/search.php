<?
$search = isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
$detail = isset($_POST['detail']) ? $_POST['detail'] : $_GET['detail'];
if(!$search) alert('검색어가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');

$tpl->define("main",$skin."/search.html");
$tpl->scan_area("main");

// 변수 지정
$usearch = $search;

if($detail && $detail!='') $search = $detail."|*|".$search;

$where = " && type='A' ";

$tmp_search = explode("|*|",$search);
for($i=0;$i<count($tmp_search);$i++){
	$tmp_search[$i] = addslashes($tmp_search[$i]);
	$tmp_search2 = str_replace(" ","",$tmp_search[$i]);

	$sql = "SELECT uid FROM mall_brand WHERE INSTR(name,'{$tmp_search[$i]}') || INSTR(tag,'{$tmp_search[$i]}')";
	$mysql->query($sql);
	$tmps = array();
	while($row = $mysql->fetch_array()){
		$tmps[] = $row['uid'];
	}
	if($tmps[0]) {
		$brand = join(",",$tmps);
		$brand_where = " || brand IN ({$brand})";
	}
	else $brand_where = '';

	$where .= "  && (INSTR(name,'{$tmp_search[$i]}') || INSTR(search_name,'{$tmp_search2}') || INSTR(tag,',{$tmp_search[$i]},') {$brand_where})";

	$tmp_search[$i] = stripslashes(stripslashes($tmp_search[$i]));
}

$SEARCH = join(" + ",$tmp_search);
$SEARCH2 = $tmp_search[$i-1];
$SEARCH3 = $tmp_search[0];
$addstring = "?channel=search&search=".urlencode($search);
$ajaxstring = "&search=".urlencode($search);
$search = stripslashes($search);

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_GET['order']) ? $_GET['order'] : "uid";
$cate	= isset($_POST['cate']) ? $_POST['cate'] : $_GET['cate'];
if(!$limit) $limit = 12;

if($field) {
	$addstring .= "&field={$field}";
	$ajaxstring .= "&field={$field}";
}

$pstring = "&page={$page}";

$sql = "SELECT access_level, cate FROM mall_cate";
$mysql->query($sql);
while($tmps=$mysql->fetch_array()) {
	if($tmps['access_level'] && $my_level<9) {
		$access_level = explode("|",$tmps['access_level']);
		if(($access_level[1]=='!=' && $access_level[0]!=$my_level) || ($access_level[1]=='<' && $access_level[0]>$my_level)) {
			$where .= " && cate != '{$tmps['cate']}' ";
			$where2 .= " && cate != '{$tmps['cate']}' ";
		}
	}
}
unset($access_level);

$where2 = $where;
if($cate) {
	$cate_len = strlen($cate);	
	switch($cate_len) {
        case "3" : 
			$where .= " && SUBSTRING(cate,1,3) = '{$cate}' ";		   		    
		break;
		case "6" : 
			$where .= " && SUBSTRING(cate,1,6) = '{$cate}' ";		    			
		break;		
    }	
	$cstring .= "&cate={$cate}";
	$ajaxstring .= "&cate={$cate}";
}

$sql = "SELECT COUNT(uid) FROM mall_goods WHERE s_qty !='3' && cate!='999000000000' {$where}";
$TOTAL = $mysql->get_one($sql);

if($TOTAL) {
	$record_num = $limit; 

	/*********************************** LIMIT CONFIGURATION ***********************************/
	$Pstart = $record_num*($page-1);
	$TOTAL_PAGE = ceil($TOTAL/$record_num);	
	if($TOTAL <= ($page * $record_num)) $TONUM = $TOTAL;
	else $TONUM = $record_num; 
	$PAGE = $page;
	/*********************************** @LIMIT CONFIGURATION ***********************************/
	
	/**************************** SEARCH SAVE **************************/
	$access_ip	= $_SERVER['REMOTE_ADDR'];
	$tmps = time()-86400;
	$tags = 0;
	$sql = "SELECT count(*) FROM mall_search WHERE word='{$usearch}' && ip='{$access_ip}' && signdate > {$tmps}";	
	if($mysql->get_one($sql)==0) {		
		$sql = "INSERT INTO mall_search VALUES('','{$usearch}','{$tags}','{$access_ip}',".time().")";	
		$mysql->query($sql);
	}	
	/**************************** SEARCH SAVE **************************/
}
else $tpl->parse("no_content");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>