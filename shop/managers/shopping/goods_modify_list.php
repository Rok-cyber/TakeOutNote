<?
$skin_inc = "Y";
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";
require "{$lib_path}/lib.Shop.php";

######################## 분류 생성 ##############################
$tmps1	= "CATEname = [[' ==== 1차분류 ==== ',[' ==== 2차분류 ==== ',[' ==== 3차분류 ==== ',' ==== 4차분류 ==== ']]]";
$tmps2	= "CATEnum	= [['',['',['','']]]";
$cnts=0;
$sql = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = 1 && cate!='999000000000' ORDER BY number ASC";
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

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? $_GET['word'] : $_POST['word'];
$smoney1	= isset($_GET['smoney1']) ? $_GET['smoney1'] : $_POST['smoney1'];
$smoney2	= isset($_GET['smoney2']) ? $_GET['smoney2'] : $_POST['smoney2'];
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : $_POST['seccate'];
$brands		= isset($_GET['brands']) ? $_GET['brands'] : $_POST['brands'];
$s_qty		= isset($_GET['s_qty']) ? $_GET['s_qty'] : $_POST['s_qty'];

$skin = ".";
$img_path	= "../../image/goods_img";
$code = "mall_goods";
$MTlist = Array(11,'uid','image3','name','brand','price','consumer_price','s_qty','qty','reserve','cate','number');

##################### addstring ############################
$where = " && SUBSTRING(cate,1,3)!='999' ";

if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	if($field=="multi") $where .= "&& (INSTR(name,'{$word}') || INSTR(model,'{$word}') || INSTR(tag,'{$word}'))";
	else $where .= "&& INSTR({$field},'{$word}') ";
} 
else $field = "name";

if($seccate) {
	if(substr($seccate,3,9)=='000000000') {
		$where .=  " && SUBSTRING(cate,1,3) = '".substr($seccate,0,3)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = " ==== 2차분류 ==== ";
		$cate3 = " ==== 3차분류 ==== ";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else if(substr($seccate,6,6)=='000000') {
		$where .=  " && SUBSTRING(cate,1,6) = '".substr($seccate,0,6)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = " ==== 3차분류 ==== ";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else if(substr($seccate,9,3)=='000') {
		$where .=  " && SUBSTRING(cate,1,9) = '".substr($seccate,0,9)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = substr($seccate,0,9)."000";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else {
		$where .= " && cate = '{$seccate}'";
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = substr($seccate,0,9)."000";
		$cate4 = $seccate;
	}
	$addstring .="&seccate={$seccate}";
}

if(strlen($smoney1) && $smoney2) {
    if($smoney1 > $smoney2) {$smoney1 = $tmp; $smoney1 = $smoney2; $smoney2 = $tmp;}
	$addstring .= "&smoney1=$smoney1&smoney2=$smoney2";
	if($smoney1==$smoney2) $where .= "&& INSTR(price,'{$smoney1}') ";
	else $where .= "&& price BETWEEN '{$smoney1}' AND '{$smoney2}' ";
}

if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) { $tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&sdate1=$sdate1&sdate2=$sdate2";	
	if($sdate1==$sdate2) $where .= "&& INSTR(from_unixtime(signdate),'{$sdate1}') ";
	else $where .= "&& ( from_unixtime(signdate) BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(from_unixtime(signdate),'{$sdate2}'))";		
}  

if($s_qty) {
	$addstring .="&s_qty={$s_qty}";
	if($s_qty==2) $where .= " && (s_qty = '2' || (s_qty = '4' && qty=0)) ";
	else if($s_qty==3) $where .= " && (s_qty = '3' || type='B') ";
	else $where .= " && s_qty = '{$s_qty}' ";
}

if($order) $addstring .="&order={$order}";	
else $order = "uid DESC";

######################## 브랜드 설정 ############################
$sql = "SELECT uid, name FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);

while($row=$mysql->fetch_array()){
	$row['name'] = stripslashes($row['name']);
	$row['name'] = str_replace("\"","&#034;",$row['name']);
	$row['name'] = str_replace("'","&#039;",$row['name']);
	$BRAND_LIST .= "<option value='{$row[uid]}'>{$row['name']}</option>\n";
	$brand_arr[$row['uid']] = $row['name'];
}	

if($brands) {
	$where .= " && brand = '{$brands}'";
	$addstring .= "&brands={$brands}";
}

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
if($page) $addstring .="&page=$page";

$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-6 MONTH', time()));

$sql = "SELECT COUNT(uid) FROM {$code} WHERE uid != '0' && type!='D' {$where}";
$total_record = $mysql->get_one($sql);

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
$tpl->define("main","./goods_modify_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	
/*********************************** QUERY **********************************/
    $query = "SELECT ";
	for($i=1;$i<$MTlist[0];$i++){  
         $query .= $MTlist[$i].", ";	
	}
	$query .= $MTlist[$i]." FROM {$code} WHERE uid != '0' && type!='D' {$where} ORDER BY {$order} LIMIT $Pstart,$limit";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$UID	= $row[uid];
		for($i=2;$i<$MTlist[0];$i++){  
			$fd = $MTlist[$i];			
			${"LIST".$i} = stripslashes($row[$fd]);  
		}
		$LIST2 = "<img src='{$img_path}{$LIST2}' border=0 width=50 height=50>";
		$LIST4 = $brand_arr[$LIST4];

		$DSP11 = $DSP12 = $DSP13 = $DSP14 = $DISA1 = "";
		${"DSP1".$row['s_qty']} = "checked";
		if($row['s_qty']!='4') $DISA1 = "disabled";
		
		$DSP21 = $DSP22 = $DSP23 = $DSP24 = $DISA2 = "";
		$reserve = explode("|",$row['reserve']);
		${"DSP2".$reserve[0]} = "checked";
		if($reserve[0] !='3') $DISA2 = "disabled";
		$reserve = $reserve[1];
		$CATE = $row['cate'];
		$NUMBER = $row['number'];
		
		$qty = $row['qty'];

		############################ 상품 옵션 #################################
		$sql = "SELECT option1 FROM mall_goods_option WHERE guid='{$UID}' GROUP BY option1 ORDER BY o_num ASC";
		$mysql->query2($sql);

		$option_arr = Array();
		while($row2 = $mysql->fetch_array(2)){
			$option_arr[] = $row2['option1'];
		}
			
		for($j=0,$cnt=count($option_arr);$j<$cnt;$j++) {
			$sql = "SELECT * FROM mall_goods_option WHERE guid='{$UID}' && option1='{$option_arr[$j]}' ORDER BY o_num ASC";
			$mysql->query2($sql);
			$opNum = 0;
			while($row2 = $mysql->fetch_array(2)){				
				$opType1 = $row2['option1'];
				$opType2 = $row2['option2'];
				$opPrice = $row2['price'];
				$opQty = $row2['qty'];
				$opUid = $row2['uid'];
				$opNum++;
				$tpl->parse("loop_op");
			}
		}
		$opType1 = $opType2 = $opPrice = $opQty = '';
		unset($option_arr);
		if($j>0) $tpl->parse("is_op");
		############################ 상품 옵션 #################################
				
		$tpl->parse("loop");
		$tpl->parse("is_op","2");
		$tpl->parse("loop_op","2");
		$v_num--;
	}

} else { $tpl->parse("is_loop"); 	}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$C_ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$PAGE = "$page/$total_page";
$LINK = "./goods_write.php?{$addstring}";    //  상품등록 링크

$pg = new paging($total_record,$page);
$pg->addQueryString("?".$addstring2); 
$PAGING = $pg->print_page();  //페이징 
$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

if($seccate) $tpl->parse("is_seccate");


$ISIZE1 = $IMG_DEFINE['img1'];
$ISIZE2 = $IMG_DEFINE['img2'];
$ISIZE3 = $IMG_DEFINE['img3'];
$ISIZE4 = $IMG_DEFINE['img4'];
$ISIZE5 = $IMG_DEFINE['img5'];

$tpl->parse("main");
$tpl->tprint("main");



/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML
 ?>