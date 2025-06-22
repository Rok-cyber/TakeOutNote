<?
include "../ad_init.php";

######################## lib include
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";
require "{$lib_path}/lib.Shop.php";


###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$groups		= isset($_GET['groups']) ? $_GET['groups'] : $_POST['groups'];

$skin = ".";
$img_path	= "../../image/goods_img";
$code = "mall_sms_sample";
$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];

##################### addstring ############################
if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	$where .= "&& INSTR({$field},'{$word}') ";
} else $field = "title";

if($groups) {
	$addstring .= "&groups={$groups}";
	$where .= "&& groups = '{$groups}' ";
}

if($order) $addstring .="&order={$order}";	
else $order = "uid DESC";

if(!$limit) {	
	$limit = 8;
	$PGConf['page_record_num'] = 8;
}
else {
	$addstring .="&limit={$limit}";	
	$PGConf['page_record_num'] = $limit;
}
$PGConf['page_link_num'] = 10;

$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];

$addstring2= $addstring;
if($page) $addstring .="&page=$page";

$sql = "SELECT groups FROM {$code} GROUP BY groups ORDER BY groups DESC";
$mysql->query($sql);
$SECGROUP = "";
while($row = $mysql->fetch_array()){
	if($row['groups']==$groups) $sec = "selected";
	else $sec ="";
	$SECGROUP .= "<option value='{$row['groups']}' {$sec}>{$row['groups']}</option>";
}

$sql = "SELECT COUNT(uid) FROM {$code} WHERE uid != '0' {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$limit);	
$v_num = $total_record - (($page-1) * $limit);
/*********************************** @LIMIT  CONFIGURATION ***********************************/

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

/*********************** 페이지 계산 **************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./search_sms_sample.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	
/*********************************** QUERY **********************************/
    $query = "SELECT uid, groups, title, message FROM {$code} WHERE uid != '0' {$where} ORDER BY {$order} LIMIT $Pstart,$limit";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	$i=0;
	while ($row=$mysql->fetch_array()){
		if($i==4) {
			$i=0;
			$tpl->parse("is_tr","1");
		}
		
		$UID = $row['uid'];
		$LIST2 = stripslashes($row['groups']);
		$LIST3 = stripslashes($row['title']);
		$LIST4 = stripslashes($row['message']);		
		     
		$tpl->parse("loop");
		$tpl->parse("is_tr","2");

		$i++;
	}
	for($i2=$i;$i2<4;$i2++) $tpl->parse("loop_empty");

} else { $tpl->parse("is_loop"); 	}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$C_ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$PAGE = "$page/$total_page";

$pg = new paging($total_record,$page);
$pg->addQueryString("?".$addstring2); 
$PAGING = $pg->print_page();  //페이징 
$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/
