<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

require "{$lib_path}/class.Template.php";
require "{$lib_path}/class.Paging.php";

$skin = ".";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$dates		= isset($_GET['dates']) ? $_GET['dates'] : $_POST['dates'];
$auth		= isset($_GET['auth']) ? $_GET['auth'] : $_POST['auth'];
$sex		= isset($_GET['sex']) ? $_GET['sex'] : $_POST['sex'];
$levels		= isset($_GET['levels']) ? $_GET['levels'] : $_POST['levels'];
$mailling	= isset($_GET['mailling']) ? $_GET['mailling'] : $_POST['mailling'];
$sms		= isset($_GET['sms']) ? $_GET['sms'] : $_POST['sms'];

if(!$field) $field = "name";
if(!$dates) $dates = "signdate";

if($word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	if($field=="multi") $where .= "&& (INSTR(name,'{$word}') || INSTR(id,'{$word}') || INSTR(tel,'{$word}') || INSTR(hphone,'{$word}') || INSTR(email,'{$word}') || INSTR(address,'{$word}'))";
	else $where .= "&& INSTR({$field},'{$word}')";
}

if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) { $tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&sdate1=$sdate1&sdate2=$sdate2";	
	if($sdate1==$sdate2) $where .= "&& INSTR(from_unixtime({$dates}),'{$sdate1}') ";
	else $where .= "&& ( from_unixtime({$dates}) BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(from_unixtime({$dates}),'{$sdate2}'))";		
}     

if($auth) {
	$addstring .= "&auth={$auth}";
	$where .= " && auth='{$auth}'";
}

if($levels) {
	$addstring .= "&levels={$levels}";
	$where .= " && level='{$levels}'";
}

if($sex) {
	$addstring .= "&sex={$sex}";
	$where .= " && sex='{$sex}'";
}

if($mailling) {
	$addstring .= "&mailling={$mailling}";
	$where .= " && mailling='{$mailling}'";
}

if($sms) {
	$addstring .= "&sms={$sms}";
	$where .= " && sms='{$sms}'";
}

$mailstring	= $addstring;

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

if($order) {
	$addstring .= "&order={$order}";
}
else $order = "signdate DESC";

$pagestring	= $addstring;
$addstring .= "&page={$page}";

$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-3 MONTH', time()));
$DAY6 = date('Y-m-d', strtotime('-6 MONTH', time()));

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

$sql = "SELECT COUNT(uid) FROM pboard_member WHERE uid >1 {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$record_num);	
$v_num = $total_record - (($page-1) * $record_num);
/*********************************** @LIMIT CONFIGURATION ***********************************/

/*********************** 페이지 계산 **************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","member_list.html");
$tpl->scan_area('main');

if($total_record > 0) {
	
	/*********************************** QUERY ***********************************/
    $query = "SELECT uid,name,id,mailling,sms,logtime,reserve,cnts,auth,signdate,level FROM pboard_member WHERE uid >1 {$where} ORDER BY {$order} LIMIT {$Pstart},{$record_num}";
    $mysql->query($query);
	/*********************************** QUERY ***********************************/

	/*********************************** LOOP ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#efefef";
		else $BGCOLOR = "#ffffff";
	    
		if($row['level']==10) $disp = "disabled";
		else $disp = "";

		$DEL = "<input type='checkbox' value='{$row[uid]}' name='item[]' {$disp}>";
		$NAME = stripslashes($row['name']);
		$LIST1 = "<a href='./member_view.php?uid={$row[uid]}{$addstring}' onfocus='this.blur();' title='회원정보수정'>{$NAME}</a>";
		$LIST2 = $row['id'];
		$LIST3 = "";
		$LIST11 = $LV[$row['level']];
		if($row['mailling']=='N') $LIST4 = "허용안함";
		else $LIST4 = "허용";

		if($row['sms']=='N') $LIST12 = "허용안함";
		else $LIST12 = "허용";

		if($row['logtime']) $LIST5 = date("y-m-d:H",$row['logtime']);
		else $LIST5 = '';
		$LIST6 = date("y-m-d",$row['signdate']);
      
		$sql = "SELECT count(*) FROM mall_order_info WHERE id = '{$row[id]}'";
		$LIST7 = $mysql->get_one($sql);
		$LIST7 = "<a href='../order/order_list.php?field=id&word={$row[id]}' onfocus=this.blur();><font class=eng title='주문목록보기'>{$LIST7}</font></a>";
		$LIST8 = "<a href='../multi_board/board.php?code=reserve&field=id&word={$row[id]}' onfocus=this.blur(); title='적립내역보기'><font class=eng>".number_format($row['reserve'],$ckFloatCnt)."</font></a>";
		
		$LIST9 = number_format($row['cnts']);

		if($row['auth']=='Y') $LIST10 = '승인';
		else $LIST10 = '미승인';

		$tpl->parse("loop");
		$v_num--;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?{$pagestring}");
	$PAGING = $pg->print_page();  //페이징 

} 
else $tpl->parse("noloop");
/*********************************** LOOP ***********************************/

$TOTAL = $total_record;      //토탈수 
$PAGE = "$page/$total_page";
$LINK1 = "";    // 목록보기 링크 

$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("main");
$tpl->tprint("main");


include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
?>





	