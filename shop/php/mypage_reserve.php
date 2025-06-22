<?
include "lib/class.Paging.php";

// 변수 지정
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$record_num	= 15;
$page_num	= 100;

$PGConf['page_record_num'] = $record_num;
$PGConf['page_link_num'] = $page_num;


$sql = "SELECT COUNT(*) FROM  mall_reserve WHERE id='{$my_id}'  && status !='D'";
$TOTAL = $mysql->get_one($sql);

$sql = "SELECT SUM(IF(status='A',reserve,0)) as sum1, SUM(IF(status='B',reserve,0)) as sum2, SUM(IF(status='C',reserve,0)) as sum3 FROM  mall_reserve WHERE id='{$my_id}'";
$tmps = $mysql->one_row($sql);
$MONEY1 = $tmps['sum1'];
$MONEY2 = $tmps['sum2'];
$MONEY3 = $tmps['sum3'];

$TOTAL_MONEY = number_format($MONEY1 + $MONEY2 - $MONEY3,$ckFloatCnt);
$TOTAL_USE = number_format($MONEY2 - $MONEY3,$ckFloatCnt);
$MONEY1	= number_format($MONEY1,$ckFloatCnt);

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
$TONUM = $TOTAL - (($page-1) * $record_num);
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

$tpl->define("main","{$skin}/mypage_reserve.html");
$tpl->scan_area("main");

/**************************** GOODS LIST**************************/

if($TOTAL>0) {
	$sql = "SELECT * FROM mall_reserve WHERE id = '{$my_id}'  && status !='D' ORDER BY uid desc LIMIT {$Pstart},{$record_num}";
	$mysql->query($sql);
	$NUM = $TONUM;

	while($row = $mysql->fetch_array()){		
		$NAME = stripslashes($row['subject']);
		$MONEY = number_format($row['reserve'],$ckFloatCnt);
		$TYPE = $row['status'];
		switch ($row['status']){
            case "A" : $STATUS = "적립대기";
			break;
			case "B" : $STATUS = "적립완료";			
			break;
			case "C" : $STATUS = "적립사용";
			break;
			case "E" : $STATUS = "사용취소";
				$NAME .= " (주문취소에 따른 적립금 {$MONEY}원 환원)"; 
				$MONEY = 0;
			break;
        }
			
		$DATE = substr($row['signdate'],0,16);
			
		$tpl->parse("loop");
		$NUM--;
	}

	if($TOTAL > $record_num){
		$pg_string = explode(",",$tpl->getPgstring());
		$pg = new paging($TOTAL,$page);
		$pg->addQueryString($Main."?channel=reserve"); 
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