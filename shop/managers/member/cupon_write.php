<? 
$skin_inc = "Y";
include "../html/top_inc.html"; // 상단 HTML 

$skin = ".";
######################## lib include
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$mode		= isset($_GET['mode']) ? $_GET['mode'] : 'write';
$field		= $_GET['field'];
$word		= $_GET['word'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$sectype	= $_GET['sectype'];
$uid		= $_GET['uid'];

##################### addstring ############################
if($field && $word) $addstring = "&field=$field&word={$word}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($sectype) $addstring .="&sectype={$sectype}";


// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./cupon_write.html");
$tpl->scan_area("main");


if($mode=='write') {	//
	$TMODE = "등록";
	$stype = 'W';
	$type = $range = $dtype = $sqty = 0;
	$LMT = 0;
	$ODDS = 100;
	$CNTS = 1;
	$CKD11 = "checked";
	$CKD21 = "checked";
} 
else {	//상품수정	

	if(!$uid)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
	$sql = "SELECT * FROM mall_cupon_manager WHERE uid='{$uid}'";
	if(!$row=$mysql->one_row($sql)) alert("등록되지않은 쿠폰이거나 삭제된 쿠폰 입니다.","back");

	$NAME	= stripslashes($row['name']);
	$SALE	= $row['sale'];
	$LMT	= $row['lmt'];
	$stype  = $row['stype'];
	
	if($row['sdate'] && $row['edate'] && !$row['days']) {
		$SDATE = substr($row['sdate'],0,10);
		$EDATE = substr($row['edate'],0,10);
		$DAYS  = '';
		$dtype = 0;
	}
	else {
		$DAYS  = $row['days'];
		$SDATE = $EDATE = '';
		$dtype = 1;
	}

	if($row['sqty']==1) {
		$QTY = $row['qty'];		
		$sqty = 1;
	}
	else {
		$QTY = '';
		$sqty = 0;
	}
	
	$type = $row['type'] - 1;

	if($row['scate']) {
		$tmps = explode("|",$row['scate']);
		for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
			$CATE = $tmps[$i];
			if(substr($CATE,3,9)=='000000000') {					
				$CATE1 = $CATE;
				$CATE2 = " ==== 2차분류 ==== ";
				$CATE3 = " ==== 3차분류 ==== ";
				$CATE4 = " ==== 4차분류 ==== ";					
			}
			else if(substr($CATE,6,6)=='000000') {
				$CATE1 = substr($CATE,0,3)."000000000";
				$CATE2 = $CATE;
				$CATE3 = " ==== 3차분류 ==== ";	
				$CATE4 = " ==== 4차분류 ==== ";	
			}
			else if(substr($CATE,9,3)=='000') {
				$CATE1 = substr($CATE,0,3)."000000000";
				$CATE2 = substr($CATE,0,6)."000000";
				$CATE3 = $CATE;
				$CATE4 = " ==== 4차분류 ==== ";	
			}
			else {
				$CATE1 = substr($CATE,0,3)."000000000";
				$CATE2 = substr($CATE,0,6)."000000";
				$CATE3 = substr($CATE,0,9)."000";
				$CATE4 = $CATE;
			}						
			$tpl->parse("loop_scate");
		}
	}

	if($row['sgoods']) {
		if($row['type']==3) $row['sgoods'] = substr($row['sgoods'],1,-1);
		$SGOODS = str_replace("|",",",$row['sgoods']);
		$tpl->parse("is_sgoods");
	}

	if($row['scate'] || $row['sgoods']) $range = 1;
	else $range = 0;

	$ODDS = $row['odds'];
	$CNTS = $row['cnts'];

	${"CKD1".$row['down_type']} = "checked";
	${"CKD2".$row['use_type']} = "checked";

	$TMODE = "수정";
	
}	//end of mode

######################### 분류 생성 ##############################
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

$LIST = "cupon_list.php?{$addstring}";
$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html"; // 하단 HTML
?>
