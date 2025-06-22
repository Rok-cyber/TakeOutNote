<?
if(!$cate) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');

$tpl->define("main","{$skin}/list.html");
$tpl->scan_area("main");

$sql = "SELECT * FROM mall_cate WHERE cate='{$cate}'";
$row = $mysql->one_row($sql);
$SCATENAME = stripslashes($row['cate_name']);
if($row['img1']) $SCATEIMG = "<img src='image/cate/{$row[img1]}' border='0' alt='{$row[cate_name]}' />";

if(substr($cate,0,3)=='999') movePage("{$Main}?channel=cooperate");

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

$cgType = "list";
if($row['list_mode']==2) $cgType = "img";

$main_dsp = explode("|*|",stripslashes($row['code']));

$cate_sub = $row['cate_sub'];
if($cate_sub==1 || $row['cate_dep']==4) {
	$cate_dep1 = 3;
	$cate_dep2 = 4;
	$cate_num = 6;	
}
else {
	$cate_dep1 = 2;
	$cate_dep2 = 3;
	$cate_num = 3;	
}

/**************************** SUB CATE **************************/
if($SKIN_DEFINE['cate_list_type']==2) {
	$sql = "SELECT cate_name,cate,cate_sub FROM mall_cate WHERE cate_dep='".($row['cate_dep'])."' && valid ='1' && cate_parent='{$row['cate_parent']}' ORDER BY number";
}
else {
	$sql = "SELECT cate_name,cate,cate_sub FROM mall_cate WHERE cate_dep='2' && valid ='1' && SUBSTRING(cate,1,3)='".substr($cate,0,3)."' ORDER BY number";
}
$mysql->query($sql);

$cks = 0;
while($row = $mysql->fetch_array()){		
	if(substr($row['cate'],0,3)=='999') continue;
	
	$COLOR1 = "cateColor1";
	if($row['cate']==$cate) $COLOR1 = "cateColor2";
	$CNAME = $row['cate_name'];	
	
	if($row['cate_sub']==1) {
		if($cate_dep1==2) $CLINK = "{$PHP_SELF}?channel=main2&cate={$row['cate']}";		
		else $CLINK = "{$PHP_SELF}?channel=list&cate={$row['cate']}";		
		$sql = "SELECT cate_name, cate FROM mall_cate WHERE cate_dep='{$cate_dep2}' && cate_parent='{$row[cate]}' ORDER BY number";
		$mysql->query2($sql);

		while($row2 = $mysql->fetch_array('2')){
			$COLOR2 = "cateColor3";
			if($row2['cate']==$cate) {
				$COLOR1 = "cateColor2";
				$COLOR2 = "cateColor2";
			}
			$CNAME2 = $row2['cate_name'];
			$CLINK2 = "{$PHP_SELF}?channel=list&cate={$row2['cate']}";		
			$tpl->parse("loop_scate");					
		}
	}
	else $CLINK = "{$PHP_SELF}?channel=list&cate={$row['cate']}";		
	$tpl->parse("loop_cate");	
	$tpl->parse("loop_scate","2");	
	$cks = 1;
}
if($cks == 1) $tpl->parse("is_scate");
/**************************** SUB CATE **************************/	

if($main_dsp[6]=='1')	{ $CATE_UP_CODE	= $main_dsp[7];		$tpl->parse("is_cate_up");	}
if($main_dsp[8]=='1')	{ $HIT_UP_CODE	= $main_dsp[9];		$tpl->parse("is_hit_up");	}


/**************************** CLICK BEST **************************/
$day1 = date("Ymd",strtotime('-1 WEEK', time()));
$cnt_limit	= $SKIN_DEFINE['sub_click_cnt'] ? $SKIN_DEFINE['sub_click_cnt'] : 3;

$sql = "SELECT SUM(view) as sum, cno FROM mall_goods_view  WHERE date >= '{$day1}' {$where2} GROUP BY cno ORDER BY sum DESC LIMIT {$cnt_limit}";
$mysql->query($sql);

$i = 1;
while($data2 = $mysql->fetch_array()){			
	$tmp_cate = substr($data2['cno'],0,12);
	$tmp_number = substr($data2['cno'],12);
	
	$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && type='A' && uid='{$tmp_number}'";
	if(!$data = $mysql->one_row($sql)) continue;

	$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
	$LINK	= $gData['link'];
	$IMAGE	= $gData['image'];
	$NAME	= $gData['name'];
	$COMP	= $gData['comp'];
	$PRICE	= $gData['price'];
	$CPRICE	= $gData['cprice'];
	$CP_PRICE	= $gData['cp_price'];
	$ICON	= $gData['icon'];
	$DRAGD	= $gData['dragd'];
	$QLINK	= $data['uid'];
	$CATE	= $data['cate'];
	
	if($CP_PRICE>0) {
		$tpl->parse("is_coupon");
		$PRICE = $CP_PRICE;
	}
	$PRICE2 = str_replace("원","",$PRICE);

	$tpl->parse("loop_goods4");	
	$tpl->parse("is_coupon","2");
	$i++;
}

/**************************** ORDER BEST **************************/
$cnt_limit	= $SKIN_DEFINE['sub_order_cnt'] ? $SKIN_DEFINE['sub_order_cnt'] : 3;
$sql = "SELECT COUNT(uid) as o_cnt, p_cate, p_number FROM mall_order_goods WHERE uid!='0' && signdate >= '{$day1}' {$where3} GROUP BY p_number ORDER BY o_cnt DESC LIMIT {$cnt_limit}";
$mysql->query($sql);

$i = 1;
while($data2 = $mysql->fetch_array()){		

	$tmp_cate = $data2['p_cate'];
	$tmp_number = $data2['p_number'];
	
	$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && 
	type='A' && uid='{$tmp_number}'";
	if(!$data = $mysql->one_row($sql)) continue;

	$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
	$LINK	= $gData['link'];
	$IMAGE	= $gData['image'];
	$NAME	= $gData['name'];
	$COMP	= $gData['comp'];
	$PRICE	= $gData['price'];
	$CPRICE	= $gData['cprice'];
	$CP_PRICE	= $gData['cp_price'];
	$ICON	= $gData['icon'];
	$DRAGD	= $gData['dragd'];
	$QLINK	= $data['uid'];
	$CATE	= $data['cate'];
	
	if($CP_PRICE>0) {
		$tpl->parse("is_coupon");
		$PRICE = $CP_PRICE;
	}
	$PRICE2 = str_replace("원","",$PRICE);

	$tpl->parse("loop_goods5");		
	$tpl->parse("is_coupon","2");
	$i++;
}

/**************************** REVIEW BEST **************************/
$cnt_limit	= $SKIN_DEFINE['sub_review_cnt'] ? $SKIN_DEFINE['sub_review_cnt'] : 3;
$sql = "SELECT SUM(point)/COUNT(*) as eve,cate, number FROM mall_goods_point WHERE uid>0 {$where} GROUP BY number ORDER BY eve  DESC LIMIT {$cnt_limit}";
$mysql->query($sql);
$i = 1;
while($row = $mysql->fetch_array()) { 

	$SUM_AFTER = round($row['eve']*2,1);
	$SUM_AFTER = ($SUM_AFTER*10);

	$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,comp,image4,icon,event,reserve,qty,s_qty FROM mall_goods WHERE s_qty!='3' && type='A' && uid='{$row['number']}'";
	$data = $mysql->one_row($sql);

	if(!$data) continue;

	$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
	$LINK	= $gData['link'];
	$IMAGE	= $gData['image'];
	$NAME	= $gData['name'];
	$COMP	= $gData['comp'];
	$PRICE	= $gData['price'];
	$CPRICE	= $gData['cprice'];
	$CP_PRICE	= $gData['cp_price'];
	$ICON	= $gData['icon'];
	$DRAGD	= $gData['dragd'];
	$QLINK	= $data['uid'];
	$CATE	= $data['cate'];

	if($CP_PRICE>0) {
		$tpl->parse("is_coupon");
		$PRICE = $CP_PRICE;
	}
	$PRICE2 = str_replace("원","",$PRICE);
	
	$sql = "SELECT content FROM mall_goods_point WHERE cate='{$row['cate']}' && number='{$row['number']}' ORDER BY point DESC, signdate DESC LIMIT 1";
	
	$CONTENT = str_replace("<BR>","<br/>",stripslashes($mysql->get_one($sql)));				
	$CONTENT = html2txt($CONTENT);

	$tpl->parse("loop_goods6");	
	$tpl->parse("is_coupon","2");	
	$i++;
}

include "lib/class.Paging.php";

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$mo1	= isset($_POST['mo1']) ? $_POST['mo1'] : $_GET['mo1'];
$mo2	= isset($_POST['mo2']) ? $_POST['mo2'] : $_GET['mo2'];
$field	= isset($_POST['field']) ? $_POST['field'] : $_GET['field'];
$search	= isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
	
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
$cstring = "&cate={$cate}";

if($search) {
	$fields_arr = Array("tag"=>"태그","name"=>"상품명","brand"=>"브랜드");
	$ajaxstring .= "&search=".urlencode($search)."&field={$field}";
	$sstring .= "&search=".urlencode($search)."&field={$field}";
	
	if($field=='tag') $where .= "  && INSTR({$field},',{$search},')";
	else if($field=='brand') {
		$sql = "SELECT uid FROM mall_brand WHERE INSTR(name,',{$search},') || INSTR(tag,'{$search}')";
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
	else if($field=="name") $where .= "  && (INSTR(name,'{$search}') || INSTR(search_name,'{$search}'))";
	else $where .= "  && INSTR({$field},'{$search}')";

	$fields = $fields_arr[$field];
	$tpl->parse("is_search");
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

if($page) $tpl->parse("is_page");

if($TOTAL==0) $tpl->parse("no_loop");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>