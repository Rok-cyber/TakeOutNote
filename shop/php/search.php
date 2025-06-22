<?
$search = isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
$field	= isset($_POST['field']) ? $_POST['field'] : $_GET['field'];

if(!$search) alert('검색어가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');
include "lib/class.Paging.php";


/***********************  BANNER  ********************************/
$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '9' && status='1' ORDER BY rank ASC";
$mysql->query($sql);
while($row_ban = $mysql->fetch_array()){
	if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
	if($row_ban['link']) {
		$BLINK = str_replace("&","&amp;",$row_ban['link']);
		if($row_ban['target']=='2') $BTARGET = "target='_blank'";
		else $BTARGET = "";
	}
	else $BLINK = "#\" onclick=\"return false;";

	$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner3'],stripslashes($row_ban['name']));
	$tpl->parse("loop_banner");	
}
unset($row_ban,$BANNER,$BLINK,$BTARGET);
/***********************  BANNER  ********************************/

// 변수 지정
$usearch = $search;

if($detail && $detail!='') $search = $detail."|*|".$search;

$where = " && type='A' ";

$tmp_search = explode("|*|",$search);
for($i=0;$i<count($tmp_search);$i++){
	$tmp_search[$i] = addslashes($tmp_search[$i]);
	$tmp_search2 = str_replace(" ","",$tmp_search[$i]);

	if($field=='tag') $where .= "  && INSTR({$field},',{$tmp_search[$i]},')";
	else if($field=='brand') {
		$sql = "SELECT uid FROM mall_brand WHERE INSTR(name,'{$tmp_search[$i]}') || INSTR(tag,'{$tmp_search[$i]}')";
		$mysql->query($sql);
		$tmps = array();
		while($row = $mysql->fetch_array()){
			$tmps[] = $row['uid'];
		}
		if($tmps[0]) {
			$brand = join(",",$tmps);
			$where .= "  && brand IN ({$brand})";
		}
	}
	else if($field) {
		if($field=="name") $where .= "  && (INSTR(name,'{$tmp_search[$i]}') || INSTR(search_name,'{$tmp_search2}'))";
		else $where .= "  && INSTR({$field},'{$tmp_search[$i]}')";
	}
	else {
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
	}
	$tmp_search[$i] = stripslashes(stripslashes($tmp_search[$i]));
}

$SEARCH = join(" + ",$tmp_search);
$SEARCH2 = $tmp_search[$i-1];
$SEARCH3 = $tmp_search[0];
$addstring = "?channel=search&search=".urlencode($search);
$ajaxstring = "&search=".urlencode($search);
$search = stripslashes($search);

$limit	= $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$brand	= isset($_GET['brand']) ? $_GET['brand'] : $_POST['brand'];
$order	= isset($_GET['order']) ? $_GET['order'] : "uid";
$cate	= isset($_POST['cate']) ? $_POST['cate'] : $_GET['cate'];
$mo1	= isset($_POST['mo1']) ? $_POST['mo1'] : $_GET['mo1'];
$mo2	= isset($_POST['mo2']) ? $_POST['mo2'] : $_GET['mo2'];

if($field) {
	$addstring .= "&field={$field}";
	$ajaxstring .= "&field={$field}";
}

if($brand) {
	$addstring .= "&brand={$brand}";
	$c2string = "&brand={$brand}";
	$where .= "&& INSTR(brand,'{$brand}')";
	$ajaxstring .= "&brand={$brand}";
}

if(($mo1 || $mo1==0) && $mo2) {
   $where .= " && price BETWEEN '{$mo1}' AND '{$mo2}' ";
   $mstring = "&mo1={$mo1}&mo2={$mo2}";
   $SEC_MON = number_format($mo1)."원 ~ ".number_format($mo2)."원";   
   $ajaxstring .= "&mo1={$mo1}&mo2={$mo2}";
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

$sql = "SELECT COUNT(uid) FROM mall_goods WHERE s_qty !='3' {$where}";
$TOTAL = $mysql->get_one($sql);

if($TOTAL>0) {
	$sql = "SELECT MAX(price) FROM mall_goods WHERE s_qty !='3' {$where}";
	$tmps = $mysql->get_one($sql);
	if(substr($tmps,0,1)==9) $MaxMoney = "10";
	else $MaxMoney = substr($tmps,0,1)+1;

	for($i=1,$cnt=strlen($tmps);$i<$cnt;$i++){
		$MaxMoney .="0";
	}
}
else $MaxMoney = 0;

$record_num = $limit; 
$limit_arr = isset($LIST_DEFINE['limit_arr']) ? $LIST_DEFINE['limit_arr'] : "10,20,30,50,100";
$tmps = explode(',',$limit_arr);
$LIMIT_OPTION = $LIMIT_OPTION2 = "";
for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
	${"SEC".$tmps[$i]} = "";
	if($tmps[$i]==$limit) $LIMIT_OPTION .= "<option value='{$tmps[$i]}' selected>{$tmps[$i]}개씩 보기</option>";
	else $LIMIT_OPTION .= "<option value='{$tmps[$i]}'>{$tmps[$i]}개씩 보기</option>";
	
	$LIMIT_OPTION2 .= "limitBoxs.addItem('{$tmps[$i]}개씩 보기','{$tmps[$i]}');";
	if($limit==$tmps[$i]) $SEC_LIMIT = $i+1;
}
${"SEC".$limit} = "selected";

$sql = "SELECT cate_name,cate FROM mall_cate WHERE cate_dep='1' && valid ='1' ORDER BY number";
$mysql->query($sql);
while($row = $mysql->fetch_array()){
	$SELECT .= "<option value='".substr($row['cate'],0,3)."'>$row[cate_name]</option>\n";
}

######################## 브랜드 설정 ############################
$sql = "SELECT * FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);

$BRAND = "";
while($row=$mysql->fetch_array()){
	$row['name'] = stripslashes($row['name']);
	$BRAND .= "<option value='{$row[uid]}'>{$row['name']}</option>\n";
}	

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
if($TOTAL <= ($page * $record_num)) $TONUM = $TOTAL;
else $TONUM = $record_num; 
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

/**************************** SEARCH SAVE **************************/
if($TOTAL) {
	$access_ip	= $_SERVER['REMOTE_ADDR'];
	$tmps = time()-86400;
	$tags = 0;
	if($field=='tag') $tags = '1';
	else if($field=='brand') $tags = '2';
	$sql = "SELECT count(*) FROM mall_search WHERE word='{$usearch}' && ip='{$access_ip}' && signdate > {$tmps}";	
	if($mysql->get_one($sql)==0) {		
		$sql = "INSERT INTO mall_search VALUES('','{$usearch}','{$tags}','{$access_ip}',".time().")";	
		$mysql->query($sql);
	}
	else if($tags=='1') {
		$sql = "UPDATE mall_search SET tag = '1' WHERE word='{$usearch}' && ip='{$access_ip}' && signdate > {$tmps}";
		$mysql->query($sql);
	}
}
/**************************** SEARCH SAVE **************************/

$tpl->define("main",$skin."/search.html");
$tpl->scan_area("main");

/**************************** SEARCH BEST **************************/
$sql = "SELECT word, count(*) as cnt FROM mall_search GROUP BY word ORDER BY cnt DESC, word limit 10";
$mysql->query($sql);

for($i=1;$i<11;$i++) {
	if($row = $mysql->fetch_array()) {
		$WORD = stripslashes($row['word']);
	}
	else $WORD = "";
	
	$tpl->parse("loop_rank_best");	
}
/**************************** SEARCH BEST **************************/

if($mstring) $tpl->parse("is_sec_mon");

if($TOTAL>0) {
	/**************************** SEARCH CATE **************************/
	$sql = "SELECT cate_name,cate FROM mall_cate WHERE cate_dep='1' && valid ='1' ORDER BY number";
	$mysql->query($sql);

	while($row = $mysql->fetch_array()){
		$cate2 = substr($row['cate'],0,3);
		$sql = "SELECT count(*) FROM mall_goods WHERE SUBSTRING(cate,1,3)='{$cate2}' && s_qty !='3' {$where2}";			    
		$CCNT = $mysql->get_one($sql);	

		if($CCNT>0) {
			$COLOR1 = "cateColor3";
			$CNAME = $row['cate_name'];
			$CLINK = "{$Main}{$addstring}{$c2string}&cate={$cate2}";		
			
			$sql = "SELECT cate_name, cate FROM mall_cate WHERE cate_dep='2' && cate_parent='{$row[cate]}' && valid ='1' ORDER BY number";
			$mysql->query2($sql);

			while($row2 = $mysql->fetch_array('2')){
				$COLOR2 = "cateColor3";
				$cate3 = substr($row2['cate'],0,6);
				$sql = "SELECT count(*) FROM mall_goods WHERE SUBSTRING(cate,1,6)='{$cate3}' && s_qty !='3' {$where2}";	
				$CCNT2 = $mysql->get_one($sql);
				if($CCNT2>0) {
					$CNAME2 = $row2['cate_name'];
					$CLINK2 = "{$Main}{$addstring}{$c2string}&cate={$cate3}";		
					if($cate==$cate3) $COLOR2 = "cateColor2";
					$tpl->parse("loop_scate");					
				}
			}

			if(substr($cate,0,3)==$cate2) $COLOR1 = "cateColor2";
			
			$tpl->parse("loop_cate");	
			$tpl->parse("loop_scate","2");

		}				
	}
	$tpl->parse("is_scate");
	/**************************** SEARCH CATE **************************/	
}
else {
	$tpl->parse("no_loop");
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>