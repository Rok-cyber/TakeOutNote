<?
include "lib/class.Paging.php";

// 변수 지정
$page	= !empty($_GET['page']) ? $_GET['page'] : 1;
$sdate1	= !empty($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2	= !empty($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$status	= !empty($_GET['status']) ? $_GET['status'] : $_POST['status'];

if($status) {
	$addstring .= "&amp;status={$status}";
	$where .= "&& a.order_status='{$status}'";
	$where2 .= "&& order_status='{$status}'";	
}
else {
	$where .= "&& (a.order_status='X' || a.order_status='Y' || a.order_status='Z')";
	$where2 .= "&& (order_status='X' || order_status='Y' || order_status='Z')";
}

if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&amp;sdate1={$sdate1}&amp;sdate2={$sdate2}";	
	if($sdate1==$sdate2) $where .= "&& INSTR(a.signdate,'{$sdate1}') ";
	else $where .= "&& (a.signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(a.signdate,'{$sdate2}'))";		
}  

$pagestring = $addstring;
$addstring .= "&amp;page={$page}";

$record_num	= 10;
$page_num	= 10;
$type_arr = Array("C"=>"신용카드","B"=>"무통장","R"=>"계좌이체","V"=>"가상계좌","H"=>"핸드폰");
$pay_arr = Array('A'=>'결제미완료','B'=>'결제성공','C'=>'결제실패');
$pay_arr2 = Array('A'=>'계좌발급완료','B'=>'계좌입급완료','C'=>'결제실패');

$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-6 MONTH', time()));

$PGConf['page_record_num'] = $record_num;
$PGConf['page_link_num'] = $page_num;


$sql	= "SELECT code FROM mall_design WHERE mode='T'";
$tmp	= $mysql->get_one($sql);
$etc_info = explode("|",$tmp);
$EDATE = isset($etc_info[3]) ? $etc_info[3] : 7;
unset($etc_info);

$sql = "SELECT COUNT(*) FROM mall_order_goods as a, mall_order_info as b WHERE a.order_num=b.order_num && b.id='{$my_id}' {$where} GROUP BY a.order_num";
$mysql->query($sql);
$TOTAL = $mysql->affected_rows();

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
$TONUM = $v_num = $TOTAL - (($page-1) * $record_num);
$PAGE = $page;

if($v_num<1 && $page>1) {
	$page = 1;
	$Pstart = $record_num*($page-1);
	$v_num = $TOTAL - (($page-1) * $record_num);
}

$End = '';
$sql = "SELECT a.order_num FROM mall_order_goods as a, mall_order_info as b WHERE a.order_num=b.order_num && b.id='{$my_id}' {$where} GROUP BY a.order_num ORDER BY a.uid DESC LIMIT {$Pstart},{$record_num}";
$mysql->query($sql);
while($row = $mysql->fetch_array()) {
	if(!$End) $End = $row['order_num'];
	$Start = $row['order_num'];
}
/*********************************** @LIMIT CONFIGURATION ***********************************/

$tpl->define("main","{$skin}/mypage_order_cancel.html");
$tpl->scan_area("main");

/**************************** GOODS LIST**************************/

if($TOTAL>0) {
	$query = "SELECT a.uid, a.p_cate, a.p_number, a.order_num, a.p_name, a.p_qty, a.p_price, a.op_price, a.sale_price, a.order_status as status, a.order_status2 as status2, a.carr_info, a.carriage FROM mall_order_goods as a, mall_order_info as b WHERE a.order_num=b.order_num && b.id='{$my_id}' && a.order_num>='{$Start}' && a.order_num<='{$End}' {$where} ORDER BY a.uid DESC";	
	$mysql->query($query);
	
	$tmp_order_num = $tmp_bg_color = '';
	while($row = $mysql->fetch_array()){				
		if($tmp_order_num!=$row['order_num']) {
			$sql = "SELECT uid, order_num, id, name1, pay_type, pay_status, order_status, signdate FROM mall_order_info WHERE order_num='{$row['order_num']}'";
			$row2 = $mysql->one_row($sql);

			$NUM = $row['order_num'];
			$DATE = substr($row2['signdate'],0,16); 
		
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
					$STATUS2 = "무통장<br /><font class='small orange'>{$pay_status}</font>";
				break;
				case "C" :					
					$STATUS2 = "신용카드 <br /><font class='small'>({$pay_arr[$row2[pay_status]]})</font>";
				break;
				case "R" :
					$STATUS2 = "실시간 계좌이체 <br /><font class='small'>({$pay_arr[$row2[pay_status]]})</font>";
				break;
				case "V" :
					$STATUS2 = "가상 계좌이체 <br /><font class='small'>({$pay_arr2[$row2[pay_status]]})</font>";
				break;
				case "H" :
					$STATUS2 = "핸드폰 <br /><font class='small'>({$pay_arr[$row2[pay_status]]})</font>";
				break;
			}
		}		

		$NAME = stripslashes($row['p_name']);
		$CNT = number_format($row['p_qty']);
		$PRICE = number_format((($row['p_price'] + $row['op_price']) * $row['p_qty']) - $row['sale_price']);
		$STATUS1 = $status_arr2[$row['status'].$row['status2']]; 
		if($row['carriage']==99999) $CARRIAGE = 0;
		else $CARRIAGE = number_format($row['carriage']*$row['p_qty']);
		
		if($row['carr_info']) {
			$tmps = explode("|",$row['carr_info']);			
			$LIST8 = "<a href='{$DELI_ARR[$tmps[0]]}{$tmps[1]}' target='blank' title='배송조회하기'>{$LIST8}</a>";
		} 

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
	}

	if($TOTAL > $record_num){
		$pg_string = explode(",",$tpl->getPgstring());
		$pg = new paging($TOTAL,$page);
		$pg->addQueryString($Main."?channel=order_cancel{$pagestring}"); 
		$PAGING = $pg->print_page($pg_string[0],$pg_string[1],$pg_string[2]);  //페이징 
		$tpl->parse("define_pg");	
	}

	$tpl->parse("is_loop");
}
else {
	$PAGE = 0;
	$TOTAL_PAGE = 0;
	$tpl->parse("no_loop");
}


/**************************** GOODS LIST**************************/

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>