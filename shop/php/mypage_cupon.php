<?
include "lib/class.Paging.php";

// 변수 지정
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$record_num	= 15;
$page_num	= 100;

$PGConf['page_record_num'] = $record_num;
$PGConf['page_link_num'] = $page_num;

$sql = "SELECT COUNT(*) FROM  mall_cupon WHERE id='{$my_id}'  && status !='D'";
$TOTAL = $mysql->get_one($sql);

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
$TONUM = $TOTAL - (($page-1) * $record_num);
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

$tpl->define("main","{$skin}/mypage_cupon.html");
$tpl->scan_area("main");

/**************************** GOODS LIST**************************/

if($TOTAL>0) {
	$stype_arr = Array('P'=>'%','W'=>'원');
	$status_arr = Array("A"=>"쿠폰발급완료","B"=>"쿠폰사용완료","C"=>"쿠폰기간만료","D"=>"쿠폰발급실패");

	$sql = "SELECT a.status, a.signdate as dates, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$my_id}' && a.pid=b.uid && a.status !='D' ORDER BY a.uid desc LIMIT {$Pstart},{$record_num}";
	$mysql->query($sql);
	$NUM = $TONUM;

	while($row = $mysql->fetch_array()){		
		$NAME = stripslashes($row['name']);
		$SALE	= number_format($row['sale']);
		$STYPE	= $stype_arr[$row['stype']];		
		
		if($row['sdate'] && $row['edate'] && !$row['days']) {
			$DATES = substr($row['sdate'],0,10)." ~ ".substr($row['edate'],0,10);
			if(date("Y-m-d")>$row['edate']) $row['status'] = 'C';
		}
		else {
			$DATES = "발급 후 {$row['days']}일";
			$tmps = date("Y-m-d", strtotime("+{$row['days']} DAY", $row['dates']));
			if(date("Y-m-d")>$tmps && $row['status']=='A') $row['status'] = 'C';			
		}
		
		if($row['lmt']>0) $LMT = "<font class='num'>".number_format($row['lmt'])."</font>원 이상구매시";
		else $LMT = "제한없음";
		
		$TYPE = $row['status'];
		$STATUS = $status_arr[$row['status']];

		$DATE = date("Y-m-d",$row['dates']);
			
		$tpl->parse("loop");
		$NUM--;
	}

	if($TOTAL > $record_num){
		$pg_string = explode(",",$tpl->getPgstring());
		$pg = new paging($TOTAL,$page);
		$pg->addQueryString($Main."?channel=cupon"); 
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