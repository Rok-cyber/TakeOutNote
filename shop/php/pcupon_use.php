<?
include "sub_init.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

if(!$my_id) alert('먼저 로그인을 하시기 바랍니다.','close4');

$skin = "../skin/{$tmp_skin}";
$skin2 = $skin."/";

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));

if($_GET['direct']=='Y') {
	$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}'";
	if($mysql->get_one($sql)>0) $where = " && p_direct = 'Y'";
	else {
		$where = "";
		$direct = 'N';
	}
}
else $where = "";

$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' {$where}";
$mysql->query($sql);

$MY_SALE = $my_sale;
$MY_POINT = $my_point;
$TCNT = $cks_total = $cks_carr = 0;

while($data = $mysql->fetch_array()){	
	$gData	= getDisplayOrder($data);		// 디스플레이 정보 가공 후 가져오기

	$cks_total = $cks_total+($gData['sum']);	
	
    if($gData['carr']) {
		$cks_carr += $gData['carr'];
	}		
	$TCNT++;
}

if($TCNT==0) alert("장바구니 상품이 비웠습니다. 다시 주문 하시기 바랍니다",'close4');
if($cash[10] =='1' && $my_carr!='Y') { 
	if($cks_total < $cash[11]) $cks_carr += $cash[12];
} 

$TOTAL = $cks_total; // + $cks_carr;

$tpl->define("main","{$skin}/pcupon_use.html");	
$tpl->scan_area("main");

$sql = "SELECT a.uid as uids, a.status, a.signdate as dates, a.gid, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$my_id}' && a.pid=b.uid && a.status ='A' ORDER BY a.gid DESC, a.uid DESC";
$mysql->query($sql);

$stype_arr = Array('P'=>'%','W'=>'원');

$cnts = 0;
while($row = $mysql->fetch_array()){			
	$DISA = '';
	$C_PRICE = 0;
	
	$UID	= $row['uids'];
	$NAME	= stripslashes($row['name']);
	$SALE	= number_format($row['sale'],$ckFloatCnt);
	$STYPE	= $stype_arr[$row['stype']];

	if($row['sdate'] && $row['edate'] && !$row['days']) {
		$DATES = substr($row['sdate'],0,10)." ~ ".substr($row['edate'],0,10);
		if(date("Y-m-d")>$row['edate']) continue;
	}
	else {
		$tmps = date("Y-m-d", strtotime("+{$row['days']} DAY", $row['dates']));		
		$DATES = "<font class='small'>발급 후 {$row['days']}일</font>({$tmps})";		
		if(date("Y-m-d")>$tmps) continue;			
	}
	
	$LMT = number_format($row['lmt'],$ckFloatCnt);

	if($row['lmt'] && $row['lmt']>$TOTAL && $row['type']!=3) $DISA = "disabled";	

	if($row['gid']) {		
		$sql = "SELECT name FROM mall_goods WHERE uid='{$row['gid']}'";
		if(!$G_NAME = $mysql->get_one($sql)) continue;

		if($row['stype']=='P') {
			$limit = '';
			if($row['use_type']==0) $limit = "limit 1";			
			$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && p_number='{$row['gid']}' {$where} {$limit}";
			$mysql->query2($sql);
			
			$cu_total = 0;
			while($row2 = $mysql->fetch_array('2')){
				$gData	= getDisplayOrder($row2);	
				if($row['use_type']==0) $cu_total += $gData['oprice'];			
				else $cu_total += ($gData['oprice'] * $row2['p_qty']);
			}	
			$cu_total = numberLimit(($cu_total * $row['sale'])/100,1);
			$C_PRICE =  round($cu_total,$ckFloatCnt);
			if($C_PRICE>0) {
				$SPRICE = number_format($C_PRICE);
				$tpl->parse("is_sprice");
			}
		}
		else {
			if($row['use_type']==0) $C_PRICE = $row['sale'];
			else {
				$sql = "SELECT SUM(p_qty) FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && p_number='{$row['gid']}' {$where}";
				$p_qty = $mysql->get_one($sql);				
				$C_PRICE = $row['sale'] * $p_qty;
			}
		}

		$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && p_number='{$row['gid']}' {$where}";
		if($mysql->get_one($sql)==0) $DISA = "disabled";

		$tpl->parse("lmt1","1");
		$tpl->parse("sec2","1");
	}
	else if($row['scate']) {
		$where2 = array();
		$LMT_CATE = array();
		$tmps = explode("|",$row['scate']);
		for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
			if(substr($tmps[$i],3,3)=='000') $where2[] = "SUBSTRING(p_cate,1,3)='".substr($tmps[$i],0,3)."' ";
			else if(substr($tmps[$i],6,3)=='000') $where2[] = "SUBSTRING(p_cate,1,6)='".substr($tmps[$i],0,6)."' ";
			else if(substr($tmps[$i],9,3)=='000') $where2[] = "SUBSTRING(p_cate,1,9)='".substr($tmps[$i],0,9)."' ";
			else $where2[] = "p_cate='{$tmps[$i]}' ";
			$LMT_CATE[] = getMLocation($tmps[$i],1);
		}	

		$where2 = join(" || ",$where2);
		$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && ({$where2}) {$where}";
		$mysql->query2($sql);

		$cu_total = 0;
		while($row2 = $mysql->fetch_array('2')){
			$gData	= getDisplayOrder($row2);	
			$cu_total += ($gData['oprice'] * $row2['p_qty']);			
		}

		if($row['stype']=='P') {									
			$C_PRICE = numberLimit(($cu_total * $row['sale'])/100,1);
			$C_PRICE =  round($C_PRICE,$ckFloatCnt);
		}
		else $C_PRICE = $row['sale'];
	
		if($row['lmt']>0) $tpl->parse("lmt2","1");
		else $tpl->parse("lmt3","1");
		
		$LMT_CATE = join("<br />",$LMT_CATE);
		$tpl->parse("lmt_cate","1");
		
		if($cu_total<$row['lmt']) $DISA = "disabled";	
		$tpl->parse("sec1","1");

	}	
	else { 
		if($row['stype']=='P') {						
			$C_PRICE =  round(($TOTAL * $row['sale'])/100,$ckFloatCnt);
		}
		else $C_PRICE = $row['sale'];
	
		if($row['lmt']>0) $tpl->parse("lmt4","1");
		else $tpl->parse("lmt5","1");
		
		if($TOTAL<$row['lmt']) $DISA = "disabled";	
		$tpl->parse("sec1","1");
	}
			
	$DATE = date("Y-m-d",$row['dates']);

	$tpl->parse("loop");	
	$tpl->parse("sec1","2");
	$tpl->parse("sec2","2");
	$tpl->parse("lmt1","2");
	$tpl->parse("lmt2","2");
	$tpl->parse("lmt3","2");
	$tpl->parse("lmt4","2");
	$tpl->parse("lmt5","2");
	$tpl->parse("lmt_cate","2");
	$tpl->parse("is_sprice","2");
	$cnts = 1;
}

if($cnts==0) $tpl->parse("no_loop");

$TOTAL2  = number_format($TOTAL,$ckFloatCnt);


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();


?>