<?
$tpl->define("main","{$skin}/goods_{$channel}.html");
$tpl->scan_area("main");

if(!$uid) {
	$tpl->parse("no_event");
}

if($code_use=='Y')	{ 
	$H_CODE	= $code;		
	$tpl->parse("is_h_up");	
}

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;	
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$cate	= isset($_POST['cate']) ? $_POST['cate'] : $_GET['cate'];
$mo1	= isset($_POST['mo1']) ? $_POST['mo1'] : $_GET['mo1'];
$mo2	= isset($_POST['mo2']) ? $_POST['mo2'] : $_GET['mo2'];
$field	= isset($_POST['field']) ? $_POST['field'] : $_GET['field'];
$search	= isset($_POST['search']) ? $_POST['search'] : $_GET['search'];

$addstring = "?channel={$channel}&uid={$uid}";
$ajaxstring = "&{$channel}={$uid}";

if($search) {
	$fields_arr = Array("tag"=>"태그","name"=>"상품명");
	$ajaxstring .= "&search=".urlencode($search)."&field={$field}";
	$sstring .= "&search=".urlencode($search)."&field={$field}";
	
	if($field=='tag') $where .= "  && INSTR({$field},',{$search},')";
	else if($field=="name") $where .= "  && (INSTR(name,'{$search}') || INSTR(search_name,'{$search}'))";
	else $where .= "  && INSTR({$field},'{$search}')";

	$fields = $fields_arr[$field];
	$tpl->parse("is_search");
}

if($channel=='special') $where .= "&& INSTR(special,',{$uid},')";
else $where .= "&& {$channel}='{$uid}'";

$where2 = $where;	

if($cate) {
	$cate_len = strlen($cate);	
	switch($cate_len) {
		case "3" : 
			$where3 = " && SUBSTRING(cate,1,3) = '{$cate}' ";		   	
			$where .= $where3;
			$cate_dep = 1;
		break;
		case "6" : 
			$where3 = " && SUBSTRING(cate,1,6) = '{$cate}' ";		    			
			$where .= $where3;
			$cate_dep = 2;
		break;		
		case "9" : 
			$where3 = " && SUBSTRING(cate,1,9) = '{$cate}' ";		    			
			$where .= $where3;
			$cate_dep = 3;
		break;		
	}	
	$cstring .= "&cate={$cate}";
	$ajaxstring .= "&cate={$cate}";

	$sql = "SELECT cate_name FROM mall_cate WHERE cate_dep='{$cate_dep}' {$where3}";
	$SCATENAME = stripslashes($mysql->get_one($sql));
}

if(($mo1 || $mo1==0) && $mo2) {
	$where .= " && price BETWEEN '{$mo1}' AND '{$mo2}' ";
	$mstring = "&mo1={$mo1}&mo2={$mo2}";
	$SEC_MON = number_format($mo1)."원 ~ ".number_format($mo2)."원";   
	$ajaxstring .= "&mo1={$mo1}&mo2={$mo2}";
	$tpl->parse("is_sec_mon");
	SetCookie("search_yn","Y",0,"/");
}
else SetCookie("search_yn","",-999,"/");

$pstring = "&page={$page}";

$sql = "SELECT COUNT(uid) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";
$TOTAL = $mysql->get_one($sql);

if($TOTAL>0) {
	$sql = "SELECT MAX(price) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";	
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

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
if($TOTAL <= ($page * $record_num)) $TONUM = $TOTAL;
else $TONUM = $record_num; 
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

if($TOTAL>0) {
	/**************************** SEARCH CATE **************************/
	$sql = "SELECT cate_name,cate FROM mall_cate WHERE cate_dep='1' && valid ='1' ORDER BY num";
	$mysql->query($sql);
	
	while($row = $mysql->fetch_array()){
		$cate2 = substr($row['cate'],0,3);
		$sql = "SELECT count(*) FROM mall_goods WHERE SUBSTRING(cate,1,3)='{$cate2}' && s_qty !='3' {$where2}";			    
		$CCNT = $mysql->get_one($sql);	

		if($CCNT>0) {
			$COLOR1 = "cateColor3";
			$CNAME = $row['cate_name'];
			$CLINK = "{$Main}{$addstring}&cate={$cate2}{$mstring}";		
			
			$sql = "SELECT cate_name, cate FROM mall_cate WHERE cate_dep='2' && cate_parent='{$row[cate]}' ORDER BY num";
			$mysql->query2($sql);

			while($row2 = $mysql->fetch_array('2')){
				$COLOR2 = "cateColor3";
				$cate3 = substr($row2['cate'],0,6);
				$sql = "SELECT count(*) FROM mall_goods WHERE SUBSTRING(cate,1,6)='{$cate3}' && s_qty !='3' {$where2}";	
				$CCNT2 = $mysql->get_one($sql);
				if($CCNT2>0) {
					$CNAME2 = $row2['cate_name'];
					$CLINK2 = "{$Main}{$addstring}&cate={$cate3}{$mstring}";		
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
else $tpl->parse("no_loop");

if($page) $tpl->parse("is_page");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>