<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";
require "{$lib_path}/lib.Shop.php";

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

###################### 변수 정의 ##########################
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$mode		= isset($_GET['mode']) ? $_GET['mode'] : 'o1';
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : $_POST['seccate'];
$brands		= isset($_GET['brands']) ? $_GET['brands'] : $_POST['brands'];

$skin = ".";
$img_path	= "../../image/goods_img";
$code = "mall_goods";

$disp_arr1 = Array('','인기상품','추천상품','신상품');
$disp_arr2 = Array('','인기상품','추천상품','신상품');

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
	$where2 .= " && a.brand = '{$brands}'";
	$where3 .= " && a.brand = '{$brands}'";
	$addstring .= "&brands={$brands}";
}	

##################### addstring ############################

$addstring = "mode={$mode}";

if($seccate) {
	if(substr($seccate,3,9)=='000000000') {
		$where .=  " && SUBSTRING(cate,1,3) = '".substr($seccate,0,3)."'";	
		$where2 .=  " && SUBSTRING(a.p_cate,1,3) = '".substr($seccate,0,3)."'";	
		$where3 .=  " && SUBSTRING(a.cno,1,3) = '".substr($seccate,0,3)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = " ==== 2차분류 ==== ";
		$cate3 = " ==== 3차분류 ==== ";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else if(substr($seccate,6,6)=='000000') {
		$where .=  " && SUBSTRING(cate,1,6) = '".substr($seccate,0,6)."'";	
		$where2 .=  " && SUBSTRING(a.p_cate,1,6) = '".substr($seccate,0,6)."'";	
		$where3 .=  " && SUBSTRING(a.cno,1,6) = '".substr($seccate,0,6)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = " ==== 3차분류 ==== ";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else if(substr($seccate,9,3)=='000') {
		$where .=  " && SUBSTRING(cate,1,9) = '".substr($seccate,0,9)."'";	
		$where2 .=  " && SUBSTRING(a.p_cate,1,9) = '".substr($seccate,0,9)."'";	
		$where3 .=  " && SUBSTRING(a.cno,1,9) = '".substr($seccate,0,9)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = substr($seccate,0,9)."000";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else {
		$where .= " && cate = '{$seccate}'";
		$where2 .= " && a.p_cate = '{$seccate}'";
		$where3 .= " &&  SUBSTRING(a.cno,1,12) = '{$seccate}'";
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = substr($seccate,0,9)."000";
		$cate4 = $seccate;
	}
	$addstring .="&seccate={$seccate}";
}

if($limit) $addstring .="&limit={$limit}";	
else $limit = "10";

$PGConf['page_record_num'] = $limit;
$PGConf['page_link_num']='10';
$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

/*********************** 페이지 계산 **************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./goods_rank.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");


switch($mode) {
	case "o1" :
		$sql = "SELECT COUNT(uid) FROM {$code} WHERE uid != '0' && o_cnt>0 {$where}";
		$total_record = $mysql->get_one($sql);
		$TTLS = "판매";
	break;	

	case "o2" :  case "o3" : case "o4" :
		if($mode=='o2') { 
			$DAY = date('Y-m-d', strtotime('-1 MONTH', time()));
			$where2 .= "&& a.signdate >= '{$DAY}'";
		}
		else if($mode=='o3') {
			$DAY = date('Y-m-d', strtotime('-1 WEEK', time()));
			$where2 .= "&& a.signdate >= '{$DAY}'";
		}
		else {
			if($_GET['days']) {
				$DAY = date('Y-m-d', strtotime("-{$_GET['days']} DAY", time()));
				$addstring .= "&days={$_GET['days']}";
			}
			else $DAY = date('Y-m-d');

			for($i=0;$i<7;$i++) {
				if($_GET['days']==$i) $CLR = "orange";
				else $CLR = "";
				$DAY2 = date('d', strtotime("-{$i} DAY", time()))."일";
				$DAY1 = "goods_rank.php?mode=o4&days={$i}";
				$tpl->parse("loop_days");				
			}
			$where2 .= "&& INSTR(a.signdate,'{$DAY}')";
		}		
		$sql = "SELECT COUNT(*) FROM mall_order_goods a WHERE a.uid!='0' {$where2} GROUP BY a.p_number";
		$mysql->query($sql);
		$total_record = $mysql->affected_rows();				
		$TTLS = "판매";
	break;

	case "c1" : case "c2" : case "c3" :
		if($mode=='c1') { 
			$DAY = date('Ymd', strtotime('-1 MONTH', time()));
			$where3 .= "&& a.date >= '{$DAY}'";
		}
		else if($mode=='c2') {
			$DAY = date('Ymd', strtotime('-1 WEEK', time()));
			$where3 .= "&& a.date >= '{$DAY}'";
		}
		else {
			if($_GET['days']) {
				$DAY = date('Ymd', strtotime("-{$_GET['days']} DAY", time()));
				$addstring .= "&days={$_GET['days']}";
			}
			else $DAY = date('Ymd');

			for($i=0;$i<7;$i++) {
				if($_GET['days']==$i) $CLR = "orange";
				else $CLR = "";
				$DAY2 = "<font class='eng {$CLR}'>".date('d', strtotime("-{$i} DAY", time()))."</font>일";
				$DAY1 = "goods_rank.php?mode=c3&days={$i}";
				$tpl->parse("loop_days");				
			}
			$where3 .= "&& INSTR(a.date,'{$DAY}')";
		}
		$sql = "SELECT COUNT(*) FROM mall_goods_view a WHERE a.uid!='0' {$where3} GROUP BY a.cno";
		$mysql->query($sql);		
		$total_record = $mysql->affected_rows();		
		$TTLS = "클릭";
	break;

}

######################## 브랜드 설정 ############################

$pagestring = $addstring;
if($page) $addstring .="&page=$page";

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$limit);	
$v_num =(($page-1) * $limit) + 1;
/*********************************** @LIMIT  CONFIGURATION ***********************************/

switch($mode) {
	case "o1" :
		$query = "SELECT uid,image4,name,price,s_qty,qty,signdate,moddate,reserve,display,cate,o_cnt,brand FROM {$code} WHERE uid != '0' && o_cnt>0 {$where} ORDER BY o_cnt DESC LIMIT {$Pstart},{$limit}";	
	break;	

	case "o2" : case "o3" : case "o4" :
		$query = "SELECT COUNT(a.uid) as o_cnt, a.p_cate, a.p_number FROM mall_order_goods a WHERE a.uid!='0' {$where2} GROUP BY a.p_number ORDER BY o_cnt DESC LIMIT {$Pstart},{$limit}";	
	break;

	case "c1" : case "c2" : case "c3" :
		$query = "SELECT SUM(a.view) as o_cnt, a.cno FROM mall_goods_view a WHERE a.uid!='0' {$where3} GROUP BY a.cno ORDER BY o_cnt DESC LIMIT {$Pstart},{$limit}";
	break;
}

$arr1 = Array('','총판매순위','한달간판매순위','한주간판매순위','일판매순위','한달간클릭순위','한주간클릭순위','일클릭순위');
$arr2 = Array('',"o1","o2","o3","o4","c1","c2","c3");

for($i=1;$i<8;$i++){
	if($mode==$arr2[$i]) $tabs = "tab_on";
	else $tabs = "tab_off";
	if($arr2[$i]) $LNS = "?mode=".$arr2[$i];
	else $LNS = '';
	$TTL = $arr1[$i];
	$tpl->parse("loop_tab");
}

if($total_record > 0) {
	$mysql->query($query);	

	/*********************************** LOOP  ***********************************/
	while ($row2=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";

		if($mode!='o1') {
			if($row2['cno']) {
				$tmp_cate	= substr($row2['cno'],0,12);
				$tmp_number = substr($row2['cno'],12);
			}
			else {
				$tmp_cate	= $row2['p_cate'];
				$tmp_number = $row2['p_number'];
			}
			$sql = "SELECT uid,cate,name,price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty,signdate,moddate,brand FROM mall_goods WHERE uid>0 && uid='{$tmp_number}'";			
			if(!$row = $mysql->one_row($sql)) { 
				$total_record--;
				continue;
			}
			$row['cate'] = $tmp_cate;
		}
		else $row = $row2;
		
		$UID	= $row['uid'];
		$DEL	= "<input type='checkbox' value='{$UID}' name='item[]' onfocus='blur();'>";
		$LINK	= "{$SMain}?channel=view&amp;uid={$row['uid']}&amp;cate={$row[cate]}";
		$MLINK	= "../shopping/goods_write.php?mode=modify&uid={$UID}";
		
		$LIST2 = "<img src='{$img_path}{$row['image4']}' border=0 width=50 height=50>";
		$LIST3 = "<a href='{$MLINK}' onfocus='this.blur();' target='_blank'>".stripslashes($row['name'])."</a>";
		$LIST4 = number_format($row['price']); 
		$LIST6 = $row['qty'];
		switch($row['s_qty']){
			case "1" : $LIST5 = "무제한";
			break;
			case "2" : $LIST5 = "<font style='color:#3366CC'>품절</font>";
			break;
			case "3" : $LIST5 = "<font style='color:#3366CC'>상품숨김</font>";
			break;
			case "4" : 
				if($LIST6==0) $LIST5 = "<font style='color:#3366CC'>품절</font>";
				else $LIST5 = number_format($LIST6);
			break;
		}		

		/************************* 적립금 관련 ***********************/
		$reserve = explode("|",$row['reserve']);
		if($reserve[0] =='2') { //쇼핑몰 정책일때
			if($cash[6] =='1') { 
				$LIST9 = number_format(($row['price'] * $cash[8])/100,$ckFloatCnt);
			} else $LIST9 = 0;
		} 
		else if($reserve[0] =='3') { //별도 책정일때
			$LIST9 = number_format(($row['price'] * $reserve[1])/100,$ckFloatCnt);
		}		
		else $LIST9 = 0;
		/************************* 적립금 관련 ***********************/

		$tmps = explode("|",$row['display']);		
		if($tmps[0] || $tmps[1]) {
			$LIST10 = "<font class='small blue'>전시</font> :";
			if($tmps[0]) $LIST10 .= " [메인 - <a href='../shopping/goods_display.php?disp={$tmps[0]}'><font class='green small'>{$disp_arr1[$tmps[0]]}</font></a>]";
			if($tmps[1]) $LIST10 .= " [분류 - <a href='../shopping/goods_display.php?disp={$tmps[0]}&seccate=".substr($row['cate'],0,3)."000000'><font class='green small'>{$disp_arr2[$tmps[1]]}</font></a>]";
		} 
		else $LIST10 = '';
		
		if($row['brand'] && $row['brand']!=0) {
			if($LIST10) $tmp_empty = "&nbsp;&nbsp;&nbsp;&nbsp;";
			else $tmp_empty = "";
			$LIST16 = "{$tmp_empty}<font class='small blue'>브랜드</font> : <a href='../shopping/goods_brand.php?brands={$row['brand']}'>".$brand_arr[$row['brand']]."</a>";
		}
		else $LIST16 = '';
	   
		$DATE = date("Y-m-d",$row['signdate']);
		if($row['moddate']>0) $MDATE = date("Y-m-d",$row['moddate']);		
		else $MDATE = '';

		$CNT = number_format($row2['o_cnt']);
		
		$LOCATION = getMLocation($row['cate']);
	         
		$tpl->parse("is_man2","1");
		$tpl->parse("loop");
		$v_num++;
	}
	/*********************************** LOOP  ***********************************/

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?".$pagestring); 
	$PAGING = $pg->print_page();  //페이징 
} 

if($total_record==0) $tpl->parse("is_loop");


$TOTAL = $total_record;      //토탈수 

$PAGE = "$page/$total_page";

$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF']."?mode={$mode}";

if($seccate) $tpl->parse("is_seccate");

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");



/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML