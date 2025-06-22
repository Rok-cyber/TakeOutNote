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
$level		= isset($_GET['level']) ? $_GET['level'] : $_POST['level'];
$sms		= isset($_GET['sms']) ? $_GET['sms'] : $_POST['sms'];

$skin = ".";
$img_path	= "../../image/goods_img";
$code = "pboard_member";
$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];

##################### addstring ############################
if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	$where .= "&& INSTR({$field},'{$word}') ";
} else $field = "name";

if($level) {
	$addstring .= "&level={$level}";
	$where .= " && level='{$level}'";
}

if($sms) {
	$addstring .= "&sms={$sms}";
	$where .= " && sms='{$sms}'";
}

if($order) $addstring .="&order={$order}";	
else $order = "uid DESC";

if(!$limit) {	
	$limit = 10;
	$PGConf['page_record_num'] = 10;
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

$sql = "SELECT name, code FROM mall_design WHERE mode='L' && name!='10' ORDER BY name ASC";
$mysql->query($sql);

for($i=2;$i<9;$i++) {
	$row = $mysql->fetch_array();
	while($row['name']!=$i) {
		$LEVEL .= "<option value='{$i}'>LV{$i}</option>";
		$LV[$i] = "LV{$i}";
		if($i==8) break;
		$i++;
	}
	if($row['name']==$i) {
		$tmps = explode("|",$row['code']);
		$LEVEL .= "<option value='{$i}'>".stripslashes($tmps[0])."</option>";
		$LV[$i] = stripslashes($tmps[0]);
	}
}

$LV[1] = "일시정지";
$LV[9] = "부관리자";
$LV[10] = "관리자";

$sql = "SELECT COUNT(uid) FROM {$code} WHERE uid>1 {$where}";
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
$tpl->define("main","./search_sms_member.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	
/*********************************** QUERY **********************************/
    $query = "SELECT level, id, name, hphone, sms FROM {$code} WHERE uid > 1 {$where} ORDER BY {$order} LIMIT $Pstart,$limit";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$LIST2 = $LV[$row['level']];
		$LIST3 = stripslashes($row['id']);
		$LIST4 = stripslashes($row['name']);
		$LIST5 = stripslashes($row['hphone']);		
		$DEL	= "<input type='checkbox' value='{$LIST5}' name='item[]' onfocus='blur();'>";

		if($row['sms']=='N') $LIST6 = "허용안함";
		else $LIST6 = "허용";
	         
		$tpl->parse("is_man2","1");
		$tpl->parse("loop");
		$v_num--;
	}

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

if($seccate) $tpl->parse("is_seccate");

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/
