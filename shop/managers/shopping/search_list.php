<?
include "../ad_init.php";

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
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : $_POST['seccate'];
$s_qty		= isset($_GET['s_qty']) ? $_GET['s_qty'] : $_POST['s_qty'];
$brand		= isset($_GET['brand']) ? $_GET['brand'] : $_POST['brand'];
$special	= isset($_GET['special']) ? $_GET['special'] : $_POST['special'];
$event		= isset($_GET['event']) ? $_GET['event'] : $_POST['event'];
$disp		= isset($_GET['disp']) ? $_GET['disp'] : $_POST['disp'];
$seccate2	= isset($_GET['seccate2']) ? $_GET['seccate2'] : $_POST['seccate2'];
$brands		= isset($_GET['brands']) ? $_GET['brands'] : $_POST['brands'];
$specials	= isset($_GET['specials']) ? $_GET['specials'] : $_POST['specials'];
$events		= isset($_GET['events']) ? $_GET['events'] : $_POST['events'];
if($seccate2 && !$seccate) $seccate = $seccate2;

$skin = ".";
$img_path	= "../../image/goods_img";
$code = "mall_goods";
$MTlist = Array('uid','image4','name','price','s_qty','qty','signdate','moddate','reserve','display','cate','number','sequence','type','icon','brand');

$disp_arr1 = Array('','인기상품','추천상품','신상품');
$disp_arr2 = Array('','인기상품','추천상품','신상품');

##################### addstring ############################
$where = " && SUBSTRING(cate,1,3)!='999' ";

if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	$where .= "&& INSTR({$field},'{$word}') ";
} else $field = "name";

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

if($s_qty) {
	$addstring .="&s_qty={$s_qty}";
	if($s_qty==2) $where .= " && (s_qty = '2' || (s_qty = '4' && qty=0)) ";
	else $where .= " && s_qty = '{$s_qty}' ";
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

if($brand) {
	$where .= " && brand != '{$brand}'";
	$addstring .= "&brand={$brand}";
	$sec_mode = 'brand';
	$sec = $brand;
	$sa_string = "?brand={$brand}";
	$sa_string2 = "&brand={$brand}";
}

if($special) {
	$where .= " && INSTR(special,',{$special},')=0";
	$addstring .= "&special={$special}";
	$sec_mode = 'special';
	$sec = $special;
	$sa_string = "?special={$special}";
	$sa_string2 = "&special={$special}";
}

if($event) {
	$where .= " && event != '{$event}'";
	$addstring .= "&event={$event}";
	$sec_mode = 'event';
	$sec = $event;
	$sa_string = "?event={$event}";
	$sa_string2 = "&event={$event}";
}


######################## 브랜드 설정 ############################
$sql = "SELECT uid, name FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);

while($row=$mysql->fetch_array()){
	if($brand==$row['uid']) continue;
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

######################## 기획전 설정 ############################
$sql = "SELECT uid, name FROM mall_special ORDER BY uid DESC";
$mysql->query($sql);

while($row=$mysql->fetch_array()){
	if($special==$row['uid']) continue;
	$row['name'] = stripslashes($row['name']);
	$row['name'] = str_replace("\"","&#034;",$row['name']);
	$row['name'] = str_replace("'","&#039;",$row['name']);
	$SPECIAL_LIST .= "<option value='{$row[uid]}'>{$row['name']}</option>\n";
}	

if($specials) {
	$where .= " && INSTR(special,',{$specials},')";
	$addstring .= "&specials={$specials}";
}

######################## 이벤트 설정 ############################
$sql = "SELECT name,uid FROM mall_event WHERE s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."'";
$mysql->query($sql);

while($row=$mysql->fetch_array()){
	if($event==$row['uid']) continue;
	$row['name'] = stripslashes($row['name']);
	$row['name'] = str_replace("\"","&#034;",$row['name']);
	$row['name'] = str_replace("'","&#039;",$row['name']);
	$EVENT_LIST .= "<option value='{$row['uid']}'>{$row['name']}</option>\n";
}	

if($events) {
	$where .= " && event = '{$events}'";
	$addstring .= "&events={$events}";
}

if($disp) {	
	if($seccate2) {
		if(substr($seccate2,3,6)=='000000') {
			$where .=  " && SUBSTRING(cate,1,3) = '".substr($seccate2,0,3)."'";	
			$where .= "&& SUBSTRING(display,3,1) != '{$disp}'";			
		} 
		else if(substr($seccate2,6,3)=='000') {
			$where .=  " && SUBSTRING(cate,1,6) = '".substr($seccate2,0,6)."'";	
			$where .= "&& SUBSTRING(display,3,1) != '{$disp}'";			
		} 
		$addstring .="&seccate2={$seccate2}";
		$sa_string = "?disp={$disp}&seccate2={$seccate2}";
		$sec2 = "&sec2={$seccate2}";
	} 
	else {
		$where .= "&& SUBSTRING(display,1,1) != '{$disp}'";
		$sa_string = "?disp={$disp}";
	}
	$addstring .="&disp={$disp}";
	$sec_mode = 'disp';
	$sec = $disp;
}

$addstring2 = $pagestring = $addstring;
if($page) $addstring .="&page=$page";

$sql = "SELECT COUNT(uid) FROM {$code} WHERE uid != '0' {$where}";
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
$tpl->define("main","./search_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	include "goods_list_process.php";	
} 
else { $tpl->parse("is_loop"); 	}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$C_ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$PAGE = "$page/$total_page";
$ACTION = $_SERVER['PHP_SELF'].$sa_string;   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'].$sa_string;

if($sec_mode) {
	$tpl->parse("is_mode");
}
else $tpl->parse("is_default");

if($seccate) $tpl->parse("is_seccate");

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/
