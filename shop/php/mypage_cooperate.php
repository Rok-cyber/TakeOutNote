<?
include "lib/class.Paging.php";

// 변수 지정
$page	= !empty($_GET['page']) ? $_GET['page'] : 1;
$sdate1	= !empty($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2	= !empty($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];

$addstring = "&amp;page={$page}";
if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&amp;sdate1={$sdate1}&amp;sdate2={$sdate2}";	
	if($sdate1==$sdate2) $where .= "&& INSTR(from_unixtime(a.signdate),'{$sdate1}') ";
	else $where .= "&& ( from_unixtime(a.signdate) BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(from_unixtime(a.signdate),'{$sdate2}'))";	
}  

$record_num	= 15;
$page_num	= 100;

$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-3 MONTH', time()));
$DAY6 = date('Y-m-d', strtotime('-6 MONTH', time()));

$PGConf['page_record_num'] = $record_num;
$PGConf['page_link_num'] = $page_num;

$sql = "SELECT COUNT(uid) FROM mall_cooperate a WHERE id='{$my_id}' {$where}";


$TOTAL = $mysql->get_one($sql);
/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
$TONUM = $TOTAL - (($page-1) * $record_num);
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

$tpl->define("main","{$skin}/mypage_cooperate.html");
$tpl->scan_area("main");

/**************************** GOODS LIST**************************/
$today	= date("Y-m-d H:i");

if($TOTAL>0) {
	$sql = "SELECT a.*, b.name, b.price, b.coop_price, b.coop_close, b.coop_edate, b.coop_pay FROM mall_cooperate as a, mall_goods as b WHERE a.guid=b.uid && a.id='{$my_id}' {$where} ORDER BY a.uid DESC LIMIT {$Pstart},{$record_num}";	
	$mysql->query($sql);

	while($row = $mysql->fetch_array()){		
			$NUM	= date("Y-m-d H:i:s",$row['signdate']);			
			$NAME	= stripslashes($row['name']);
			$CNT	= $row['qty'];			

			if($row['coop_pay']=='Y'){
				$sql = "SELECT price FROM mall_goods_cooper WHERE guid='{$row['guid']}' ORDER BY qty ASC LIMIT 1";
				$PRICE = number_format($mysql->get_one($sql),$ckFloatCnt);
			}
			else $PRICE = number_format($row['coop_price'],$ckFloatCnt);

			$UID = $row['uid'];
			$GID = $row['guid'];

			if($row['coop_edate']<$today) {  //공구 마감
				$TYPE = $TYPE2 = 2; 
				$STATUS = "<font class='small orange'>공구마감</font>";
				$CLOSEDATE =  date("Y-m-d", strtotime("+{$row['coop_close']} DAY", strtotime($row['coop_edate'])));
				if(date("Y-m-d")>$CLOSEDATE) $TYPE = $TYPE2 = "5";
				//else if($row['coop_sale']==0) $TYPE2 = "6";
			}
			else {
				$TYPE = '';
				$STATUS = "<font class='small blue'>공구진행중</font>";
				$CLOSEDATE = $row['coop_close'];

				$sql = "SELECT price FROM mall_goods_cooper WHERE guid='{$row['guid']}' ORDER BY qty DESC LIMIT 1";
				if($mysql->get_one($sql)==$row['coop_price']) {
					$STATUS .= "<br /><font class='small orange'>공구가 확정</font>";
					$TYPE2 = 2;					
				}
			}
			
			if($row['status']=='B') $TYPE2 = "3";
			else if($row['status']=='C') $TYPE2 = "8";
			else if($row['status']=='D') $TYPE2 = "4";
			
			if($row['coop_pay']=='Y') $tpl->parse("is_type7","1");
			else $tpl->parse("is_type{$TYPE}","1");
			$tpl->parse("is_process{$TYPE2}","1");
			$tpl->parse("loop");			
			$tpl->parse("is_type{$TYPE}","2");	
			$tpl->parse("is_type7","2");	
			$tpl->parse("is_process{$TYPE2}","2");
	}

	if($TOTAL > $record_num){
		$pg_string = explode(",",$tpl->getPgstring());
		$pg = new paging($TOTAL,$page);
		$pg->addQueryString($Main."?channel=cooperate_list{$addstring}"); 
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