<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Template.php";
$skin = ".";
$mode = isset($_GET['mode']) ? $_GET['mode'] : "affili";
$affiliates = $_GET['affiliates'];

$tpl = new classTemplate;
$tpl->define("main","{$skin}/affiliate_sale_top.html");
$tpl->scan_area("main");
$tmps = "";
$img_arr = Array("","affili","year","month","day","detail");
for($i=1;$i<6;$i++){
	if($mode==$img_arr[$i]) ${"tabs".$i} = "tab{$tmps}_on";
	else ${"tabs".$i} = "tab{$tmps}_off";
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();


// 파라미터 정의 및 검색 영역 정의 
if($_POST['year']) $_GET['year'] = $_POST['year'];
if($_POST['month']) $_GET['month'] = $_POST['month'];
if($_POST['day']) $_GET['day'] = $_POST['day'];

$YEAR = date("Y");     
if(!$year || $year >$YEAR) $year = $YEAR;
$MONTH = date("m");
if(!$month || $month<0 || $month>12) $month = $MONTH;
if(strlen($month) ==1) $month = "0".$month;
$DAY = date("d");
if(!$day) $day = $DAY;
if(strlen($day)==1) $day = "0".$day;
if(!$mode && $mode!='year' && $mode!='month' && $mode!='day' && $mode!='detail') $mode = 'year';  

$ACTION = $PHP_SELF;

######################## 입점사 설정 ############################
$sql = "SELECT * FROM mall_affiliate ORDER BY uid ASC";
$mysql->query($sql);

$affil_name = Array();
while($row=$mysql->fetch_array()){
	if($row['id'] == $affiliates) $sec = 'selected';
	else $sec='';
	$affil_name[$row['id']] = stripslashes($row['name']);
	$AFFILIATE .= "<option value='{$row['id']}' {$sec}>{$row['id']}</option>\n";
}	

if($affiliates) {
	$where = " && affiliate = '{$affiliates}'";
	$where2 = " && a.affiliate = '{$affiliates}'";
	$addstring = "&affiliates={$affiliates}";
	$AFFILIATES = "{$affil_name[$affiliates]}({$affiliates}) - ";
}
else {
	$where = " && affiliate !=''";
	$where2 = " && a.affiliate !=''";
}


$sql = "SELECT SUBSTRING(signdate,1,4) FROM mall_order_info ORDER BY uid ASC LIMIT 1";
$s_year = $mysql->get_one($sql);
if(!$s_year) $s_year = $YEAR;

if($mode!='year') {	
	$SELECT = "<select name=year>\n";
	for($i=$s_year;$i<=$YEAR;$i++){
		if($i==$year) $SELECT .="<option value=$i selected>{$i}년</option>\n";
		else $SELECT .="<option value=$i>{$i}년</option>\n";
	}
	$SELECT .= "</select>\n";
    
	$lastdate=01;
	while (checkdate($month,$lastdate,$year)){ 
	   $lastdate++;  
	}
	$lastday=$lastdate - 1; 
			
	if($mode!='month'){
		$SELECT .= "<select name=month>\n";
		for($i=1;$i<13;$i++){
			if($i==$month) $SELECT .="<option value=$i selected>{$i}월</option>\n";
			else $SELECT .="<option value=$i>{$i}월</option>\n";
		}
		$SELECT .= "</select>\n";
    	
		if($mode =='detail'){			
			if($day > $lastday) $day = $lastday;
			$SELECT .= "<select name=day>\n";
			for($i=1;$i<=$lastday;$i++){
				if($i==$day) $SELECT .="<option value=$i selected>{$i}일</option>\n";
				else $SELECT .="<option value=$i>{$i}일</option>\n";
			}
			$SELECT .= "</select>\n";
        }
    }
}

// 템플릿
$tpl->define("main","affiliate_sale_{$mode}.html");
$tpl->scan_area("main");

$TITLES = "정산금액";

if($mode=='detail') {

	$where = "";
	$TSALE1 = 0;
		 
	if($sdate1 && $sdate2) {
		if($sdate1 > $sdate2) {$sdate1 = $tmp_date; $sdate1 = $sdate2; $sdate2 = $tmp_date;}
		$addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";
		if($sdate1==$sdate2) $where .= "&& INSTR(b.signdate,'{$sdate2}') ";
		else $where .= "&& (b.signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(b.signdate,'{$sdate2}')) ";
	} 
	else {
		$sdate1=$sdate2=date("Y-m-d");
		$where .= " && INSTR(b.signdate,'{$sdate2}') ";
    }
	        
	$sql = "SELECT a.*, b.signdate as signdate2, b.use_cupon, b.pay_total, b.use_reserve, b.carriage, b.a_commi FROM mall_order_goods a, mall_order_info b WHERE a.order_num=b.order_num && a.order_status='E' {$where} {$where2} ORDER BY a.order_num ASC";	
	$mysql->query($sql);
	
	$tmp_order_num = '';	
	while($row=$mysql->fetch_array()){

		if($tmp_order_num!=$row['order_num']) {
			$sql = "SELECT count(*) FROM mall_order_goods a WHERE a.order_num='{$row['order_num']}' {$where2}";
			$goods_cnt = $mysql->get_one($sql);
			$ROWSPAN = "rowspan='{$goods_cnt}'";

			$DATE = substr($row['signdate2'],0,16);
			$ORDER_NUM = $row['order_num'];
			$SALE3 = $row['use_cupon'];
			$SALE4 = $row['pay_total'] + $row['use_reserve'] - $row['carriage'];
			if($row['a_commi']>0) $SALE5 = ($SALE4*$row['a_commi'])/100;
			else $SALE5 = 0;
		}		
		
		$SALE1 = stripslashes($row['p_name']);	
		$SALE2 = (($row['p_price']+$row['op_price'])*$row['p_qty']-$row['sale_price']);
        
		if($tmp_order_num!=$row['order_num']) {			
			$TSALE3 += $SALE3;
			$TSALE4 += $SALE4;
			$TSALE5 += $SALE5;

			$SALE3 = number_format($SALE3,$ckFloatCnt);
			$SALE4 = number_format($SALE4,$ckFloatCnt);
			$SALE5 = number_format($SALE5,$ckFloatCnt);     	

			$tpl->parse("type1");
			$tmp_order_num = $row['order_num'];			
			$tmp_bg_color = $BGCOLOR;
			$v_num--;
		}
		else {			
			$TSALE2 += $SALE2;		
			$SALE2 = number_format($SALE2,$ckFloatCnt);

			$BGCOLOR = $tmp_bg_color;
			$tpl->parse("type2");
		}

        $tpl->parse("loop");
		$tpl->parse("type1","2");		
		$tpl->parse("type2","2");	
		$TSALE1++;
    }
	
	$TSALE2 = number_format($TSALE2,$ckFloatCnt);
	$TSALE3 = number_format($TSALE3,$ckFloatCnt);
	$TSALE4 = number_format($TSALE4,$ckFloatCnt);
	$TSALE5 = $TOTAL = number_format($TSALE5,$ckFloatCnt);
          
} 
else if($mode=='affili') {
	
	$i2 = $year."-".$month;
	
	$sql = "SELECT id FROM mall_affiliate ORDER BY id ASC";
	$mysql->query($sql);

	while($data = $mysql->fetch_array()){

		$sql = "SELECT COUNT(*) FROM mall_order_goods WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status ='E' && affiliate='{$data['id']}'";				
        $SALE1 = $mysql->get_one($sql);

		$sql = "SELECT SUM(pay_total+use_reserve-carriage) as sum, SUM(use_cupon) as sum_cupon, SUM(((pay_total+use_reserve-carriage)*a_commi)/100) as sum_commi FROM mall_order_info WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status ='E' && affiliate='{$data['id']}'";
		$row = $mysql->one_row($sql);

		$SALE2 = $row['sum'] + $row['sum_cupon'];
		$SALE3 = $row['sum_cupon'];
		$SALE4 = $row['sum'];
		$SALE5 = $row['sum_commi'];
					  
		$TSALE1 += $SALE1;
		$TSALE2 += $SALE2;
		$TSALE3 += $SALE3;
		$TSALE4 += $SALE4;
		$TSALE5 += $SALE5;
		
		$SALE2 = number_format($SALE2,$ckFloatCnt);
		$SALE3 = number_format($SALE3,$ckFloatCnt);
		$SALE4 = number_format($SALE4,$ckFloatCnt);
		$SALE5 = number_format($SALE5,$ckFloatCnt);
		
		$AFFILI  = "<a href='affiliate_sale.php?mode=day&affiliates={$data['id']}&year={$year}&month={$month}' title='일별통계보기'>{$data['id']}</a>";
		$SALE1 = "<a href='../order/affiliate_order_list.php?sdate1={$i2}&sdate2={$i2}&affiliates={$data['id']}'><font class=eng>{$SALE1}</font></a>";

		$tpl->parse("loop");
    }
	
	$TSALE2 = number_format($TSALE2,$ckFloatCnt);
	$TSALE3 = number_format($TSALE3,$ckFloatCnt);
	$TSALE4 = number_format($TSALE4,$ckFloatCnt);
	$TSALE5 = $TOTAL = number_format($TSALE5,$ckFloatCnt);	  
	
}
else {

	switch($mode) {
		case "year" :
				$f_s = $s_year;
				$f_e = $YEAR;
				$d_str = "년";
		break;
		case "month" :
				$f_s = 1;
				$f_e = 12;
				$d_str = "월";
				$a_str = "$year-";
		break;
		case "day" :
				$f_s = 1;
				$f_e = $lastday;
				$d_str = "일";
				$a_str = "$year-$month-";
		break;
	}	

	for($i=$f_s;$i<=$f_e;$i++){
		$DAYS = $i.$d_str;
			
		if($i<10) $i2 = $a_str."0".$i;
		else $i2 = $a_str.$i;
			
		$sql = "SELECT COUNT(*) FROM mall_order_goods WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status ='E' {$where}";				
        $SALE1 = $mysql->get_one($sql);

		$sql = "SELECT SUM(pay_total+use_reserve-carriage) as sum, SUM(use_cupon) as sum_cupon, SUM(((pay_total+use_reserve-carriage)*a_commi)/100) as sum_commi FROM mall_order_info WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status ='E' {$where}";
		$row = $mysql->one_row($sql);

		$SALE2 = $row['sum'] + $row['sum_cupon'];
		$SALE3 = $row['sum_cupon'];
		$SALE4 = $row['sum'];
		$SALE5 = $row['sum_commi'];
					  
		$TSALE1 += $SALE1;
		$TSALE2 += $SALE2;
		$TSALE3 += $SALE3;
		$TSALE4 += $SALE4;
		$TSALE5 += $SALE5;
		
		$SALE2 = number_format($SALE2,$ckFloatCnt);
		$SALE3 = number_format($SALE3,$ckFloatCnt);
		$SALE4 = number_format($SALE4,$ckFloatCnt);
		$SALE5 = number_format($SALE5,$ckFloatCnt);
		
		switch($mode) {
			case "year" :
				$DAYS  = "<a href='affiliate_sale.php?mode=month&year={$i}' title='월별통계보기'>{$DAYS}</a>";
			    $SALE1 = "<a href='../order/affiliate_order_list.php?sdate1={$i}&sdate2={$i}'><font class=eng>{$SALE1}</font></a>";
			break;
			case "month" :
				$DAYS  = "<a href='affiliate_sale.php?mode=day&year={$year}&month={$i}' title='일별통계보기'>{$DAYS}</a>";
				$SALE1 = "<a href='../order/affiliate_order_list.php?sdate1={$i2}&sdate2={$i2}'><font class=eng>{$SALE1}</font></a>";
			break;
			case "day" :
				$DAYS  = "<a href='affiliate_sale.php?mode=detail&sdate1={$i2}&sdate2={$i2}' title='상세내역보기'>{$DAYS}</a>";
				$SALE1 = "<a href='../order/affiliate_order_list.php?sdate1={$i2}&sdate2={$i2}{$addstring}'><font class=eng>{$SALE1}</font></a>";				
			break;
		}	
		$tpl->parse("loop");

    }
	
	$TSALE2 = number_format($TSALE2,$ckFloatCnt);
	$TSALE3 = number_format($TSALE3,$ckFloatCnt);
	$TSALE4 = number_format($TSALE4,$ckFloatCnt);
	$TSALE5 = $TOTAL = number_format($TSALE5,$ckFloatCnt);	  
	
}
	

$tpl->parse("main");
$tpl->tprint("main");
?>

			</div>
		</div>
		<div class="bottom"></div>
		</span>		
	</div>
</div>


<?
include "../html/bottom_inc.html"; // 하단 HTML
?>