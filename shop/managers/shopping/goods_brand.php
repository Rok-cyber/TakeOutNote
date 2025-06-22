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
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : $_POST['seccate'];
$s_qty		= isset($_GET['s_qty']) ? $_GET['s_qty'] : $_POST['s_qty'];
$brand		= isset($_GET['brand']) ? $_GET['brand'] : $_POST['brand'];


$skin = ".";
$img_path	= "../../image/goods_img";
$code = "mall_goods";
$back = "goods_brand";
$MTlist = Array('uid','image4','name','price','s_qty','qty','signdate','moddate','reserve','display','cate','number','sequence','type','icon','brand');
$disp_arr1 = Array('','인기상품','추천상품','신상품');
$disp_arr2 = Array('','인기상품','추천상품','신상품');

$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];

##################### addstring ############################

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
	$where .= " && s_qty = '{$s_qty}' ";
}

$order = "uid DESC";

if($limit) $addstring .="&limit={$limit}";	
else $limit = "10";

$addstring .= "&order={$order}";

######################## 브랜드 설정 ############################
$sql = "SELECT uid, name FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);

while($row=$mysql->fetch_array()){
	if(!$brand) $brand = $row['uid'];
	if($row['uid'] == $brand) $sec = 'selected';
	else $sec='';
	$row['name'] = stripslashes($row['name']);
	$row['name'] = str_replace("\"","&#034;",$row['name']);
	$row['name'] = str_replace("'","&#039;",$row['name']);	
	$brand_arr[$row['uid']] = $row['name'];
	$BRAND .= "<option value='{$row[uid]}' {$sec}>{$row['name']}</option>\n";
}	

if($brand) $where .= " && brand = '{$brand}'";
else $BRAND = "<option value=''>브랜드샵없음</option>";

$addstring .= "&brand={$brand}";

$pagestring = $addstring;
if($page) $addstring .="&page=$page";

$PGConf['page_record_num'] = $limit;
$PGConf['page_link_num']='10';

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
$tpl->define("main","./goods_brand.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	include "goods_list_process.php";
}
else { $tpl->parse("is_loop"); 	}
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 

$PAGE = "$page/$total_page";
$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

if($seccate) $tpl->parse("is_seccate");

$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

/*#################### SHOPPING  GOODS END #################################*/


 include "../html/bottom_inc.html"; // 하단 HTML