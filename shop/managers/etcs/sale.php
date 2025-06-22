<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
include "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";
$skin = ".";
$mode = $_GET['mode'];

$tpl = new classTemplate;
$tpl->define("main","{$skin}/sale_top.html");
$tpl->scan_area("main");
$tmps = "";
$img_arr = Array("","year","month","day","detail","goods");
for($i=1;$i<6;$i++){
	if($mode==$img_arr[$i]) ${"tabs".$i} = "tab{$tmps}_on";
	else ${"tabs".$i} = "tab{$tmps}_off";
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();


// 파라미터 정의 및 검색 영역 정의 
$year	= isset($_POST['year'])? $_POST['year'] : $_GET['year'];
$month	= isset($_POST['month'])? $_POST['month'] : $_GET['month'];
$day	= isset($_POST['day'])? $_POST['day'] : $_GET['day'];

$YEAR = date("Y");     
if(!$year || $year >$YEAR) $year = $YEAR;
$MONTH = date("m");
if(!$month || $month<0 || $month>12) $month = $MONTH;
if(strlen($month) ==1) $month = "0".$month;
$DAY = date("d");
if(!$day) $day = $DAY;
if(strlen($day)==1) $day = "0".$day;
if(!$mode && $mode!='year' && $mode!='month' && $mode!='day' && $mode!='detail') $mode = 'year';  

$ACTION = $_SERVER['PHP_SELF'];

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
$tpl->define("main","sale_{$mode}.html");
$tpl->scan_area("main");

if($mode=='goods') {
    $skin = ".";
	$addstring="mode=goods";

	######################## 분류 생성 ##############################
	$tmps1	= "CATEname = [[' ==== 1차분류 ==== ',[' ==== 2차분류 ==== ',[' ==== 3차분류 ==== ',' ==== 4차분류 ==== ']]]";
	$tmps2	= "CATEnum	= [['',['',['','']]]";
	$cnts=0;
	$sql = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = 1 ORDER BY number ASC";
	$mysql->query($sql);
	while($row=$mysql->fetch_array()){    
		$row['cate_name'] = addslashes($row['cate_name']);
		if($row['cate_sub']==1) {
			$tmps1.= ",['{$row[cate_name]}'";		
			$tmps2.= ",['{$row[cate]}'";		
			$sql2 = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = '2' AND cate_parent = '{$row[cate]}' ORDER BY number ASC";
			$mysql->query2($sql2);
			while($row2=$mysql->fetch_array(2)){
				$row2['cate_name'] = addslashes($row2['cate_name']);
				if($row2['cate_sub']==1) {
					$tmps1.= ",['{$row2[cate_name]}'";	
					$tmps2.= ",['{$row2[cate]}'";	
					$sql3 = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = '3' AND cate_parent = '{$row2[cate]}' ORDER BY number ASC";
					$mysql->query3($sql3);
					while($row3=$mysql->fetch_array(3)){
						$row3['cate_name'] = addslashes($row3['cate_name']);
						if($row3['cate_sub']==1) {
							$tmps1.= ",['{$row3[cate_name]}'";	
							$tmps2.= ",['{$row3[cate]}'";	
							$sql4 = "SELECT cate,cate_name FROM mall_cate WHERE cate_dep = '4' AND cate_parent = '{$row3[cate]}' ORDER BY number ASC";
							$mysql->query4($sql4);						
							while($row4=$mysql->fetch_array(4)){							
								$row4['cate_name'] = addslashes($row4['cate_name']);
								$tmps1.= ",'{$row4[cate_name]}'";
								$tmps2.= ",'{$row4[cate]}'";
							}
							$tmps1.= "]";
							$tmps2.= "]";
						} 
						else {
							$tmps1.= ",['{$row3[cate_name]}']";		
							$tmps2.= ",['{$row3[cate]}']";		
						}	
					}
					$tmps1.= "]";
					$tmps2.= "]";
				} 
				else {
					$tmps1.= ",['{$row2[cate_name]}']";		
					$tmps2.= ",['{$row2[cate]}']";		
				}
			}
		} 
		else {
			$tmps1.= ",['{$row[cate_name]}'";		
			$tmps2.= ",['{$row[cate]}'";		
		}
		$tmps1.= "]";
		$tmps2.= "]";	
		$cnts=1;	
	}
	$tmps1.= "]";
	$tmps2.= "]";
	######################## 분류 생성 ##############################

	$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
	$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
	$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
	$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
	$page		= isset($_GET['page']) ? $_GET['page'] : 1;
	$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : $_POST['seccate'];
	$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
	
	if($field && $word) {
		$addstring .= "&field={$field}&word=".urlencode($word);
		$where .= "&& INSTR({$field},'{$word}') ";
	} 

	if($sdate1 && $sdate2) {
		if($sdate1 > $sdate2) {$sdate1 = $tmp_date; $sdate1 = $sdate2; $sdate2 = $tmp_date;}
		$addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";
		if($sdate1==$sdate2) $where .= "&& INSTR(signdate,'{$sdate2}') ";
		else $where .= "&& (signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(signdate,'{$sdate2}')) ";
	} 
	else {	
		$addstring2 = $addstring;
	}

	if($seccate) {
		if(substr($seccate,3,9)=='000000000') {
			$where .=  " && SUBSTRING(p_cate,1,3) = '".substr($seccate,0,3)."'";	
			$cate1 = substr($seccate,0,3)."000000000";
			$cate2 = " ==== 2차분류 ==== ";
			$cate3 = " ==== 3차분류 ==== ";
			$cate4 = " ==== 4차분류 ==== ";
		} 
		else if(substr($seccate,6,6)=='000000') {
			$where .=  " && SUBSTRING(p_cate,1,6) = '".substr($seccate,0,6)."'";	
			$cate1 = substr($seccate,0,3)."000000000";
			$cate2 = substr($seccate,0,6)."000000";
			$cate3 = " ==== 3차분류 ==== ";
			$cate4 = " ==== 4차분류 ==== ";
		} 
		else if(substr($seccate,9,3)=='000') {
			$where .=  " && SUBSTRING(p_cate,1,9) = '".substr($seccate,0,9)."'";	
			$cate1 = substr($seccate,0,3)."000000000";
			$cate2 = substr($seccate,0,6)."000000";
			$cate3 = substr($seccate,0,9)."000";
			$cate4 = " ==== 4차분류 ==== ";
		} 
		else {
			$where .= " && p_cate = '{$seccate}'";
			$cate1 = substr($seccate,0,3)."000000000";
			$cate2 = substr($seccate,0,6)."000000";
			$cate3 = substr($seccate,0,9)."000";
			$cate4 = $seccate;
		}
		$addstring .="&seccate={$seccate}";
	}
	
	if(!$limit) {	
		$limit = 10;
		$PGConf['page_record_num'] = 10;
	}
	else {
		$addstring .="&limit={$limit}";	
		$PGConf['page_record_num'] = $limit;
	}

	if($order) $addstring .="&order={$order}";
	else $order = "qty";

	$PGConf['page_link_num'] = 10;

	$record_num = $PGConf['page_record_num'];
	$page_num = $PGConf['page_link_num'];

	$pagestring = $addstring;
	$addstring .="&page=$page";

	$sql = "SELECT count(*) FROM mall_order_goods WHERE order_status!='Z' && order_status!='X' {$where} GROUP BY p_number";
    $mysql->query($sql);
	
    $total_record = $mysql->affected_rows();

	/*********************************** LIMIT CONFIGURATION ***********************************/
	$Pstart = $record_num*($page-1);
	$total_page = ceil($total_record/$record_num);	
	$v_num = $total_record - (($page-1) * $record_num);
	/*********************************** @LIMIT CONFIGURATION ***********************************/

	if($total_record > 0) {
		
		/*********************************** QUERY ***********************************/
		$query = "SELECT p_number, p_name as name, SUM(p_qty) as qty, p_price as price, SUM(op_price) as op_price, SUM(sale_price) as sale_price, SUM((p_qty*p_price)+op_price-sale_price) as sum_price FROM mall_order_goods WHERE order_status!='Z' {$where} GROUP BY p_number ORDER BY {$order} DESC LIMIT $Pstart,$record_num";
		$mysql->query($query);
		/*********************************** QUERY ***********************************/

		/*********************************** LOOP ***********************************/
		$NUM=($page-1)*$record_num+1;
		while ($row=$mysql->fetch_array()){
		  
		  if($NUM%2 ==0) $BGCOLOR = "#efefef";
		  else $BGCOLOR = "#ffffff";

		  $NAME = $row['name'];
		  $NAME = "<a href='../order/order_list.php?sdate1={$sdate1}&sdate2={$sdate2}&field=p_number&word={$row['p_number']}' title='주문내역'>{$NAME}</a>";

		  $QTY = $row['qty'];
		  $PRICE = number_format($row['price'],$ckFloatCnt);
		  $SUM = number_format($row['sum_price'],$ckFloatCnt);
		  
		  $tpl->parse("loop");
		  $NUM++;
		}
		/*********************************** LOOP ***********************************/

		require "{$lib_path}/class.Paging.php";
		$pg = new paging($total_record,$page);
		$pg->addQueryString("?{$pagestring}");
		$PAGING = $pg->print_page();  //페이징 
	} 
	else $tpl->parse("noloop");
	
	if($seccate) $tpl->parse("is_seccate");

	$TOTAL = $total_record;      //토탈수 
	$PAGE = "{$page}/{$total_page}";	

} else if($mode!='detail') {

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

	$TSALE1 = $TSALE2 = $TSALE3 = $TSALE4 = $TSALE5 = $TSALE6 = $TSALE7 = $TSALE8 = 0;

	for($i=$f_s;$i<=$f_e;$i++){
		$DAYS = $i.$d_str;
			
		if($i<10) $i2 = $a_str."0".$i;
		else $i2 = $a_str.$i;
			
		$sql = "SELECT COUNT(*) FROM mall_order_info WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status !='Z'";				
        $SALE1 = $mysql->get_one($sql);

		$sql = "SELECT SUM(carriage) as carriage , SUM(use_reserve) as reserve, SUM(use_cupon) as cupon FROM mall_order_info WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status !='Z'";		
		$row = $mysql->one_row($sql);						
		if($row['carriage']) $SALE3 = $row['carriage'];
		else $SALE3 = 0;
			
		$sql = "SELECT COUNT(*) FROM mall_order_info WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status!='A' && order_status!='Z'";					
        $SALE4 = $mysql->get_one($sql);
            
		$sql = "SELECT SUM(pay_total) FROM mall_order_info WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status!='A' && order_status!='Z'";
		$SALE5 = $mysql->get_one($sql);			
		if(!$SALE5) $SALE5 = 0;
			
		if($row['reserve']) $SALE6 = $row['reserve'];
		else $SALE6 = 0;

		if($row['cupon']) $SALE8 = $row['cupon'];
		else $SALE8 = 0;

		$sql = "SELECT SUM(pay_total) FROM mall_order_info WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status='A'" ;
		$SALE7 = $mysql->get_one($sql);

		$sql = "SELECT SUM(pay_total) FROM mall_order_info WHERE uid>0 && INSTR(signdate,'{$i2}') && order_status!='Z'";
        $SALE2 = ($mysql->get_one($sql)-$SALE3+$SALE6+$SALE8);
			      
		$TSALE1 += $SALE1;
		$TSALE2 += $SALE2;
		$TSALE3 += $SALE3;
		$TSALE4 += $SALE4;
		$TSALE5 += $SALE5;
		$TSALE6 += $SALE6;
		$TSALE7 += $SALE7;
		$TSALE8 += $SALE8;

		$SALE2 = number_format($SALE2,$ckFloatCnt);
		$SALE3 = number_format($SALE3,$ckFloatCnt);
		$SALE5 = number_format($SALE5,$ckFloatCnt);
		$SALE6 = number_format($SALE6,$ckFloatCnt);
		$SALE7 = number_format($SALE7,$ckFloatCnt);            
		$SALE8 = number_format($SALE8,$ckFloatCnt);            

		switch($mode) {
			case "year" :
				$DAYS  = "<a href='sale.php?mode=month&year={$i}' title='월별통계보기'>{$DAYS}</a>";
			    $SALE1 = "<a href='../order/order_list.php?sdate1={$i}&sdate2={$i}'><font class=eng>{$SALE1}</font></a>";
       			$SALE4 = "<a href='../order/order_list.php?sdate1={$i}&sdate2={$i}&cash=Y'>{$SALE4}</font></a>";
			break;
			case "month" :
				$DAYS  = "<a href='sale.php?mode=day&year={$year}&month={$i}' title='일별통계보기'>{$DAYS}</a>";
				$SALE1 = "<a href='../order/order_list.php?sdate1={$i2}&sdate2={$i2}'><font class=eng>{$SALE1}</font></a>";
				$SALE4 = "<a href='../order/order_list.php?sdate1={$i2}&sdate2={$i2}&cash=Y'><font class=eng>{$SALE4}</font></a>";
			break;
			case "day" :
				$DAYS  = "<a href='sale.php?mode=detail&sdate1={$i2}&sdate2={$i2}' title='상세내역보기'>{$DAYS}</a>";
				$SALE1 = "<a href='../order/order_list.php?sdate1={$i2}&sdate2={$i2}'><font class=eng>{$SALE1}</font></a>";
				$SALE4 = "<a href='../order/order_list.php?sdate1={$i2}&sdate2={$i2}&cash=Y'><font class=eng>{$SALE4}</font></a>";
			break;
		}	
		$tpl->parse("loop");

    }
	
	$TOTAL = number_format($TSALE2 + $TSALE3 - $TSALE6 - $TSALE8,$ckFloatCnt);
	$TSALE2 = number_format($TSALE2,$ckFloatCnt);
	$TSALE3 = number_format($TSALE3,$ckFloatCnt);
	$TSALE5 = number_format($TSALE5,$ckFloatCnt);
	$TSALE6 = number_format($TSALE6,$ckFloatCnt);
	$TSALE7 = number_format($TSALE7,$ckFloatCnt);
	$TSALE8 = number_format($TSALE8,$ckFloatCnt);
	            
} 
else {
	
	$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
	$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];

	$TSALE1 = $TSALE2 = $TSALE3 = $TSALE4 = $TSALE5 = $TSALE6 = $TSALE7 = $TSALE8 = 0;
		 
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
	        
   	$sql = "SELECT a.*, b.signdate as signdate2 FROM mall_order_goods a, mall_order_info b WHERE a.order_num=b.order_num {$where} && a.order_status!='Z' && a.order_status!='X'  ORDER BY a.order_num ASC";	
	$mysql->query($sql);		
	
	while($row=$mysql->fetch_array()){
		$DATE = substr($row['signdate2'],0,16);
		$SALE1 = stripslashes($row['p_name']);	
		$SALE1 = "<a href='../order/order_view.php?order_num={$row['order_num']}' onfocus='this.blur();'>{$SALE1}</a>";
		$SALE2 = (($row['p_price']+$row['op_price'])*$row['p_qty'])-$row['sale_price'];
        if($row['order_num'] != $tmp_order_num){
			$sql = "SELECT carriage,use_reserve,use_cupon FROM mall_order_info WHERE order_num='{$row['order_num']}'";
			$row2=$mysql->one_row($sql);
			$SALE3 = $row2['carriage'];
			$SALE5 = $row2['use_reserve']; 
			$SALE7 = $row2['use_cupon']; 
			$tmp_order_num = $row['order_num'];
        } 
		else {
			$SALE3 = $SALE5 = $SALE7 = 0;			
        }

		if($row['order_status'] !='A') $SALE4 = $SALE2 + $SALE3 - $SALE5 - $SALE7;
		else $SALE4 = 0;

        if($row['order_status']=='Y') {
			$SALE6 = $status_arr2[$row['order_status'].$row['order_status2']]; 	
		}
		else $SALE6 = $status_arr[$row['order_status']]; 
						      
		$TSALE2 += $SALE2;
		$TSALE3 += $SALE3;
		$TSALE4 += $SALE4;
		$TSALE5 += $SALE5;
		$TSALE7 += $SALE7;

		$SALE2 = number_format($SALE2,$ckFloatCnt);
		$SALE3 = number_format($SALE3,$ckFloatCnt);
		$SALE4 = number_format($SALE4,$ckFloatCnt);
		$SALE5 = number_format($SALE5,$ckFloatCnt);            
		$SALE7 = number_format($SALE7,$ckFloatCnt);            

        $tpl->parse("loop");
		$TSALE1++;
    }
	
	$TOTAL = number_format($TSALE2 + $TSALE3 - $TSALE5 - $TSALE7,$ckFloatCnt);
	$TSALE2 = number_format($TSALE2,$ckFloatCnt);
	$TSALE3 = number_format($TSALE3,$ckFloatCnt);
	$TSALE4 = number_format($TSALE4,$ckFloatCnt);
	$TSALE5 = number_format($TSALE5,$ckFloatCnt);
	$TSALE7 = number_format($TSALE7,$ckFloatCnt);
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