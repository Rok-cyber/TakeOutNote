<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
include "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$type		= isset($_GET['type']) ? $_GET['type'] : $_POST['type'];
$status		= isset($_GET['status']) ? $_GET['status'] : $_POST['status'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
if($_POST['gs']) $page=1;
$gs			= isset($_GET['gs']) ? $_GET['gs'] : $_POST['gs'];

$skin = ".";

if($gs) {
	$addstring2 = "gs={$gs}";
	$where .= "&& a.order_status='{$gs}'";
	$where2 .= "&& order_status='{$gs}'";	
}
else {
	$where .= "&& (a.order_status='X' || a.order_status='Y' || a.order_status='Z')";
	$where2 .= "&& (order_status='X' || order_status='Y' || order_status='Z')";
}

if($field && $word) {
	$addstring .= "&field=$field&word=$word";
	if($field=="multi") {
		$where .= "&& (INSTR(a.order_num,'{$word}') || INSTR(b.id,'{$word}') || INSTR(b.name1,'{$word}') || INSTR(b.name2,'{$word}') || INSTR(b.pay_name,'{$word}') || INSTR(a.p_name,'{$word}'))";
	}
	else if($field=="p_name") $where .= "&& INSTR(a.{$field},'{$word}')";
	else $where .= "&& INSTR(b.{$field},'{$word}')";
}  else $field = "name1";


if($sdate1=='today') $sdate1 = date("Y-m-d");
if($sdate1 && !$sdate2) $sdate2 = $sdate1;

if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&sdate1=$sdate1&sdate2=$sdate2";	
	if($sdate1==$sdate2) $where .= "&& INSTR(a.signdate,'{$sdate1}') ";
	else $where .= "&& (a.signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(a.signdate,'{$sdate2}'))";		
}  

if($type) {
	$addstring .="&type={$type}";	
	if($type=='E') $where .= " && b.escrow='Y' ";
	else $where .= " && b.pay_type='{$type}' ";
}


if($limit) $addstring .="&limit={$limit}";	
else $limit = "10";

$addstring3 = $addstring2.$addstring;
if($page) $addstring .="&page=$page";

$PGConf['page_record_num'] = $limit;
$PGConf['page_link_num']='10';
$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];


$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-6 MONTH', time()));


if(eregi("b.",$where)) {
	$sql = "SELECT COUNT(*) FROM mall_order_goods as a, mall_order_info as b WHERE a.order_num=b.order_num {$where} GROUP BY a.order_num";
	$mysql->query($sql);
	$total_record = $mysql->affected_rows();
	$join_use = 'Y';
}
else {
	$sql = "SELECT COUNT(order_num) FROM mall_order_goods as a WHERE uid!='' {$where} GROUP BY a.order_num";
	$mysql->query($sql);
	$total_record = $mysql->affected_rows();
	$join_use = 'N';	
}

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$record_num);	
$v_num = $total_record - (($page-1) * $record_num);
if($v_num<1 && $page>1) {
	$page = 1;
	$Pstart = $record_num*($page-1);
	$v_num = $total_record - (($page-1) * $record_num);
}

$End = '';
$sql = "SELECT a.order_num FROM mall_order_goods as a, mall_order_info as b WHERE a.order_num=b.order_num {$where} GROUP BY a.order_num ORDER BY a.uid DESC LIMIT {$Pstart},{$record_num}";
$mysql->query($sql);
while($row = $mysql->fetch_array()) {
	if(!$End) $End = $row['order_num'];
	$Start = $row['order_num'];
}
/*********************************** @LIMIT  CONFIGURATION ***********************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./order_glist.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");


$arr1 = Array('전체','취소','반품','교환');
$arr2 = Array("","Z","X","Y");

for($i=0;$i<4;$i++){	
	if($gs==$arr2[$i]) $tabs = "tab_on";
	else $tabs = "tab_off";
	if($arr2[$i]) $LNS = "?gs=".$arr2[$i];
	else $LNS = '';
	$TTL = $arr1[$i];
	$tpl->parse("loop_tab");
}

if($total_record > 0) {

	$sql = "SELECT uid,code FROM mall_design WHERE mode='Z'";
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){
		$DELI_ARR[$row['uid']] = $row['code'];
	}
	
	/*********************************** QUERY **********************************/
    if($join_use=='Y') {
		$query = "SELECT a.uid, a.p_cate, a.p_number, a.order_num, a.p_name, a.p_qty, a.p_price, a.op_price, a.sale_price, a.order_status as status, a.order_status2 as status2, a.carr_info, a.carriage FROM mall_order_goods as a, mall_order_info as b WHERE a.order_num=b.order_num && a.order_num>='{$Start}' && a.order_num<='{$End}' {$where} ORDER BY a.uid DESC";
	}
	else{
		$query = "SELECT a.uid, a.p_cate, a.p_number, a.order_num, a.p_name, a.p_qty, a.p_price, a.op_price, a.sale_price, a.order_status as status, a.order_status2 as status2, a.carr_info, a.carriage FROM mall_order_goods as a WHERE a.order_num>='{$Start}' && a.order_num<='{$End}' {$where} ORDER BY a.uid DESC";
	}
    $mysql->query($query);		
	/*********************************** QUERY  ***********************************/

	/*********************************** LOOP  ***********************************/
    //사용 배열정의 
	$pay_arr = Array('A'=>'결제미완료','B'=>'결제성공','C'=>'결제실패','D'=>'카드취소');
	$pay_arr2 = Array('A'=>'계좌발금완료','B'=>'계좌입금완료','C'=>'입금실패','D'=>'환불');
	$pay_arr3 = Array('A'=>'미입금','B'=>'입금완료','C'=>'입금실패','D'=>'환불');

	$tmp_order_num = '';
	$tmp_bg_color = '';
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;

		if($v_num%2 ==0) $BGCOLOR = "#efefef";
		else $BGCOLOR = "#ffffff";
		
		if($tmp_order_num!=$row['order_num']) {
			$sql = "SELECT uid, order_num, id, name1, pay_type, pay_status, order_status, signdate FROM mall_order_info WHERE order_num='{$row['order_num']}'";
			$row2 = $mysql->one_row($sql);

			$DEL = "<input type='checkbox' value='$row[order_num]' name='item[]'  onfocus='blur();'>";
			$ORDER_NUM = $row['order_num'];
			$LIST2 = substr($row2['signdate'],0,16); 
		
			$LIST3 = stripslashes($row2['name1']);
			$LIST4 = stripslashes($row2['id']);

			$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$row['order_num']}' {$where2}";
			$goods_cnt = $mysql->get_one($sql);
			$ROWSPAN = "rowspan='{$goods_cnt}'";

			switch($row2['pay_type']) {
				case "B" :
					if($row2['order_status']=='A') $pay_status = "미입금";
					else if($row2['order_status']=='Z') { 
						$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$row['order_num']}' && order_status!='Z'";
						if($mysql->get_one($sql)==0) {
							$sql = "SELECT count(*) FROM mall_order_change WHERE order_num='{$row['order_num']}' && refund>0";						
							if($mysql->get_one($sql)>0) $pay_status = "환불";
							else $pay_status = "미입금";
						}
						else {
							$pay_status = "입금완료";
							$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$row['order_num']}' && !(order_status='X' && order_status2='D')";
							if($mysql->get_one($sql)==0) {	
								$sql = "SELECT count(*) FROM mall_order_change WHERE order_num='{$row['order_num']}' && refund>0";						
								if($mysql->get_one($sql)>0) $pay_status = "환불";								
							}
						}
					}
					else $pay_status = "입금완료";
					$LIST9 = "무통장<br /><font class='small orange'>{$pay_status}</font>";
				break;
				case "C" :
					$LIST9 = "신용카드 <br /><font class='small'>({$pay_arr[$row2[pay_status]]})</font>";
				break;
				case "R" :
					$LIST9 = "실시간 계좌이체 <br /><font class='small'>({$pay_arr3[$row2[pay_status]]})</font>";
				break;
				case "V" :
					$LIST9 = "가상 계좌이체 <br /><font class='small'>({$pay_arr2[$row2[pay_status]]})</font>";
				break;
				case "H" :
					$LIST9 = "핸드폰 <br /><font class='small'>({$pay_arr[$row2[pay_status]]})</font>";
				break;
			}
			if($row['escrow']=='Y') $LIST9 .= "&nbsp;<img src='img/icon_escrow.gif' border=0 align='absmiddle' />";
			if($LIST3 && $LIST3!='guest' && $LIST3!='del') $tpl->parse("is_crm","1");      
		}		

		$LIST5 = stripslashes($row['p_name']);
		$LIST5 = "<a href='../shopping/goods_write.php?mode=modify&uid={$row['p_number']}' target='_blank' title='상품정보 수정하기'>{$LIST5}</a>";
		$LIST6 = number_format($row['p_qty']);
		$LIST7 = number_format((($row['p_price'] + $row['op_price']) * $row['p_qty']) - $row['sale_price']);
		$LIST8 = $status_arr2[$row['status'].$row['status2']]; 
		if($row['carriage']==99999) $LIST10 = 0;
		else $LIST10 = number_format($row['carriage']*$row['p_qty']);
		
		if($row['carr_info']) {
			$tmps = explode("|",$row['carr_info']);			
			$LIST8 = "<a href='{$DELI_ARR[$tmps[0]]}{$tmps[1]}' target='blank' title='배송조회하기'>{$LIST8}</a>";
		} 

		$tpl->parse("is_man2","1");

		if($tmp_order_num!=$row['order_num']) {			
			$tpl->parse("type1");
			$tmp_order_num = $row['order_num'];			
			$tmp_bg_color = $BGCOLOR;
			$v_num--;
		}
		else {			
			$BGCOLOR = $tmp_bg_color;
			$tpl->parse("type2");
		}

		$tpl->parse("loop");				
		$tpl->parse("type1","2");		
		$tpl->parse("type2","2");		
		$tpl->parse("is_crm","2");
	}	
} 
else { $tpl->parse("is_loop"); 	}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 
	
$PAGE = "$page/$total_page";
$pg = new paging($total_record,$page);
$pg->addQueryString("?".$addstring3); 
$PAGING = $pg->print_page();  //페이징 
$ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";   //검색 경로
$CANCEL = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

 include "../html/bottom_inc.html"; // 하단 HTML?>