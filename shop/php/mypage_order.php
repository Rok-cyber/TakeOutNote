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
}
if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&amp;sdate1={$sdate1}&amp;sdate2={$sdate2}";	
	if($sdate1==$sdate2) $where .= "&& INSTR(a.signdate,'{$sdate1}') ";
	else $where .= "&& (a.signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(a.signdate,'{$sdate2}'))";		
}  

$pagestring = $addstring;
$addstring .= "&amp;page={$page}";

$record_num	= 15;
$page_num	= 100;
$type_arr = Array("C"=>"신용카드","B"=>"무통장","R"=>"계좌이체","V"=>"가상계좌","H"=>"핸드폰");

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


$sql = "SELECT COUNT(uid) FROM mall_order_info a WHERE a.id='{$my_id}' {$where}";


$TOTAL = $mysql->get_one($sql);
/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
$TONUM = $TOTAL - (($page-1) * $record_num);
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

$tpl->define("main","{$skin}/mypage_order.html");
$tpl->scan_area("main");

/**************************** GOODS LIST**************************/

if($TOTAL>0) {
	$sql = "SELECT a.uid, a.order_num, a.pay_type, a.order_status, a.signdate, a.use_reserve, a.carriage, a.carr_info, a.pay_total, b.p_name, count(*) as cnt  FROM mall_order_info as a, mall_order_goods as b WHERE a.order_num=b.order_num && a.id='{$my_id}' {$where} group by b.order_num ORDER BY uid DESC LIMIT {$Pstart},{$record_num}";	
	$mysql->query($sql);

	while($row = $mysql->fetch_array()){		
			$NUM	= $row['order_num'];
			if($row['order_status']!='Z') {
				$sql = "SELECT count(*) as cnt FROM mall_order_goods WHERE order_num='{$NUM}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D')";
				$tmps = $mysql->one_row($sql);				
				$row['cnt'] = $tmps['cnt'];
				$sql = "SELECT p_name FROM mall_order_goods WHERE order_num='{$NUM}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D') LIMIT 1";
				$tmps = $mysql->one_row($sql);				
				$row['p_name'] = $tmps['p_name'];
			}
			
			$NAME	= stripslashes($row['p_name']);
			$CNT	= $row['cnt'];
			if($CNT >1) $NAME .= " 외".($CNT-1)."건";
			
			$PRICE = number_format($row['pay_total'],$ckFloatCnt);
			$TYPE = $type_arr[$row['pay_type']];
			$STATUS = $status_arr[$row['order_status']];
			if($row['order_status']=='D') {
				if($row['carr_info']) {
					$tmps = explode("|",$row['carr_info']);
					$sql = "SELECT code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
					$CLINK = $mysql->get_one($sql);
					$CNUM = $tmps[1];
					$tpl->parse("is_carr");
				}
			}
			
			$DATE = substr($row['signdate'],0,16);			
			$tpl->parse("loop");
			$tpl->parse("is_carr","2");
	}

	if($TOTAL > $record_num){
		$pg_string = explode(",",$tpl->getPgstring());
		$pg = new paging($TOTAL,$page);
		$pg->addQueryString($Main."?channel=order{$pagetring}"); 
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