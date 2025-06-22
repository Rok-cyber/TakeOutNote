<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";
require "{$lib_path}/lib.Shop.php";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? $_GET['word'] : $_POST['word'];
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : $_POST['seccate'];
$brands		= isset($_GET['brands']) ? $_GET['brands'] : $_POST['brands'];
$s_qty		= isset($_GET['s_qty']) ? $_GET['s_qty'] : $_POST['s_qty'];
$coop_p		= isset($_GET['coop_p']) ? $_GET['coop_p'] : $_POST['coop_p'];


$skin = ".";
$img_path	= "../../image/goods_img";
$code = "mall_goods";
$MTlist = Array('uid','image4','name','price','reserve','s_qty','qty','signdate','moddate','coop_sdate','coop_edate','coop_close','display','cate','type','icon','coop_cnt','coop_price','o_cnt','coop_pay');

$disp_arr1 = Array('','인기상품','추천상품','신상품');
$disp_arr2 = Array('','인기상품','추천상품','신상품');

##################### addstring ############################
$where = "&& cate='999000000000'";

if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	if($field=="multi") $where .= "&& (INSTR(a.name,'{$word}') || INSTR(a.model,'{$word}') || INSTR(a.tag,'{$word}'))";
	else $where .= "&& INSTR(a.{$field},'{$word}') ";
} 
else $field = "name";

if(!$brands) $brands = "coop_sdate";
if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) { $tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&sdate1=$sdate1&sdate2=$sdate2";	
	if($sdate1==$sdate2) {
		if(eregi('coop',$brands)) $where .= "&& INSTR(a.{$brands},'{$sdate1}') ";
		else $where .= "&& INSTR(from_unixtime(a.{$brands}),'{$sdate1}') ";
	}
	else {
		if(eregi('coop',$brands)) $where .= "&& (a.{$brands} BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(a.{$brands},'{$sdate2}'))";		
		else $where .= "&& ( from_unixtime(a.{$brands}) BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(from_unixtime(a.{$brands}),'{$sdate2}'))";		
	}
}  

if($s_qty) {
	$today = date("Y-m-d H:i");
	switch($s_qty) {
		case '1' : 
			$where .= " && a.coop_sdate > '{$today}' ";
		break;
		case '2' : 
			$where .= " && (a.coop_sdate < '{$today}' && a.coop_edate > '{$today}') ";
		break;
		case '3' : 
			$where .= " && a.coop_edate < '{$today}' ";
		break;		
	}
	$addstring .= "&s_qty={$s_qty}";
}

if($seccate) {
	$addstring .= "&seccate={$seccate}";
	if($seccate==2) $where .= " && b.qty >= a.coop_cnt ";
	else $where .= " && b.qty < a.coop_cnt";
}

if($coop_p) {
	$addstring .= "&coop_p={$coop_p}";
	$where .= " && a.coop_pay = '{$coop_p}'";
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
if($page>1) $addstring .="&page={$page}";

$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-6 MONTH', time()));

$today = time();

$sql = "SELECT MIN(b.qty) as cqty FROM mall_goods a, mall_goods_cooper b WHERE a.uid != '0' && a.uid=b.guid {$where} GROUP BY b.guid";
$mysql->query($sql);
$total_record = $mysql->affected_rows();

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
$tpl->define("main","./coop_goods_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	
/*********************************** QUERY **********************************/
    $query = "SELECT ";
	for($i=0,$cnt=count($MTlist);$i<($cnt-1);$i++){  
         $query .= "a.".$MTlist[$i].", ";	
	}
	$query .= "a.".$MTlist[$i].", MIN(b.qty) as cqty FROM {$code} a, mall_goods_cooper b WHERE a.uid != '0' && a.uid=b.guid {$where} GROUP BY b.guid ORDER BY a.{$order} LIMIT $Pstart,$limit";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$UID	= $row['uid'];
		$MLINK	= "coop_goods_write.php?mode=modify&uid={$UID}{$addstring}";
		$DEL	= "<input type='checkbox' value='{$UID}' name='item[]' onfocus='blur();'>";
		for($i=1;$i<=($cnt-1);$i++){  
			$fd = $MTlist[$i];			
			${"LIST".($i+1)} = stripslashes($row[$fd]);  
		}
	   
		$LIST2 = "<img src='{$img_path}{$LIST2}' border=0 width=50 height=50>";
		$LIST3 = "<a href='{$MLINK}' onfocus='this.blur();'>$LIST3</a>";
		$LIST4 = number_format($LIST4,$ckFloatCnt); 
		
		if($row['coop_pay']=='Y'){
			$sql = "SELECT price FROM mall_goods_cooper WHERE guid='{$row['uid']}' ORDER BY qty ASC LIMIT 1";
			$nowprice = $mysql->get_one($sql);
			$LIST21 = number_format($nowprice,$ckFloatCnt);
			$STATUS3 = "<font class='small orange'>선주문</font>";
		}
		else {
			if($row['coop_price']==0) $LIST21 = $LIST4;
			else $LIST21 = number_format($row['coop_price'],$ckFloatCnt); 
			$STATUS3 = "<font class='small blue'>성립후주문</font>";
		}
		$LIST22 = number_format($row['coop_cnt']); 
		$LIST20 = number_format($row['o_cnt']); 

		switch($LIST6){
			case "1" : $LIST7 = "무제한";
			break;
			case "4" : 
				$LIST7 = "<font class='eng'>{$LIST7}</font>";
			break;
		}
		if($row['type']=='B') $LIST7 = "<font style='color:#3366CC'>분류<br />상품숨김</font>";
		
		/************************* 적립금 관련 ***********************/
		$reserve = explode("|",$LIST5);
		if($reserve[0] =='2') { //쇼핑몰 정책일때
			if($cash[6] =='1') { 
				$LIST5 = number_format(($row['coop_price'] * $cash[8])/100,$ckFloatCnt);
			} else $LIST5 = 0;
		} 
		else if($reserve[0] =='3') { //별도 책정일때
			$LIST5 = number_format(($row['coop_price'] * $reserve[1])/100,$ckFloatCnt);
		}		
		else $LIST5 = 0;
		/************************* 적립금 관련 ***********************/

		/************************* 아이콘 관련 ***********************/
		if($row['icon']){
			$ICON = '';
			$icon = explode("|",$row['icon']);
			for($j=1,$cnt2=count($icon);$j<$cnt2;$j++) {
			   $ICON .= "<img src='../../image/icon/{$icon[$j]}' border='0' align='absmiddle' />";
			}
			$LIST3 .= "&nbsp{$ICON}";
		}	
		/************************* 아이콘 관련 ***********************/
		
		$DATE = date("Y-m-d",$LIST8);
		if($LIST9>0) $MDATE = date("Y-m-d",$LIST9);		
		else $MDATE = '';

		$LIST10 = substr($LIST10,0,16);		
		$LIST11 = substr($LIST11,0,16);
		
		if(strtotime($LIST10) > $today) $STATUS = "<font class='small blue'>공구 준비중</font>";
		else if(strtotime($LIST11) > $today) $STATUS = "<font class='small orange'>공구 진행중</font>";
		else $STATUS = "<font class='small green'>공구 마감</font>";

		if($row['cqty'] <= $row['coop_cnt']) $STATUS2 = "<font class='small orange'>공구 성립</font>";
		else $STATUS2 = "<font class='small'>공구 미성립</font>";
		
		$tmps = explode("|",$LIST13);		
		if($tmps[1]) {
			$LIST13 = "<font class='small blue'>전시</font>:<font class='green small'>{$disp_arr2[$tmps[1]]}</font></a>";
		} 
		else $LIST13 = '';
     
		$tpl->parse("is_man2","1");
		$tpl->parse("loop");
		$v_num--;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?".$addstring2); 
	$PAGING = $pg->print_page();  //페이징 
}
else $tpl->parse("is_loop");
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$C_ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$PAGE = "{$page}/{$total_page}";
$LINK = "./coop_goods_write.php?{$addstring}";    //  상품등록 링크

$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");



/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML