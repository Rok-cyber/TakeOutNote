<?
######################## lib include
include "../ad_init.php";

$mode	= $_GET['mode'];
$uid	= $_GET['uid'];
$limit	= $_GET['limit'];
$disps	= $_GET['disps'];
$order	= $_GET['order'];
$page	= $_GET['page'];
$seccate= $_GET['seccate'];
$num	= $_GET['num'];
$odisp	= substr($order,5,1);

$addstring = "disps={$disps}&page={$page}&order={$order}";
if($seccate) { 	
	$addstring .="&seccate={$seccate}";
	if($odisp==2) $cwhere = "&& SUBSTRING(cate,1,3)='".substr($seccate,0,3)."'";
	$cwhere = "&& SUBSTRING(cate,1,6)='".substr($seccate,0,6)."'";
}
if($limit) $addstring .= "&limit={$limit}";

if(!$uid || !$disps) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');

if($mode=='del') {
	$sql = "SELECT display, o_num1, o_num2, o_num3, cate FROM mall_goods WHERE uid='{$uid}'";
	$row = $mysql->one_row($sql);

	$display = $row['display'];
	if(!$display) alert('해당 상품이 삭제되었거나 디스플레이 선택이 되지 않은 상품입니다.','back');

	$tmps = explode("|",$display);
	if($odisp==1) {
		$sql = "UPDATE mall_goods SET o_num1 = o_num1 - 1 WHERE o_num1!=0 && o_num1>{$row['o_num1']} && SUBSTRING(display,1,1)='{$tmps[0]}'";
		$mysql->query($sql);

		$tmps[0] = '0';
		$where = ", o_num1 = ''";
	}
	else {
		$cate1 = substr($row['cate'],0,3);
		$cate2 = substr($row['cate'],0,6);

		$sql = "UPDATE mall_goods SET o_num2 = o_num2 - 1 WHERE o_num2!=0 && o_num2>{$row['o_num2']} && SUBSTRING(display,3,1)='{$tmps[1]}' && SUBSTRING(cate,1,3)='{$cate1}'";
		$mysql->query($sql);		
		$sql = "UPDATE mall_goods SET o_num3 = o_num3 - 1 WHERE o_num3!=0 && o_num3>{$row['o_num3']} && SUBSTRING(display,3,1)='{$tmps[1]}' && SUBSTRING(cate,1,6)='{$cate2}'";
		$mysql->query($sql);	

		$tmps[1] = '0';
		$where = ", o_num2 = '', o_num3 = ''";
	}

	$display = join("|",$tmps);
	
	$sql = "UPDATE mall_goods SET display='{$display}' {$where} WHERE uid='{$uid}'";
	$mysql->query($sql);

	movePage("goods_display.php?{$addstring}");
}
		
$sql = "SELECT o_num{$odisp} FROM mall_goods WHERE uid='{$uid}'";		
$number = $mysql->get_one($sql);

if($number<1) alert('해당 상품이 삭제되었거나 디스플레이 선택이 되지 않은 상품입니다.','back');
       
switch($mode) {
	case "first" :
		if($number==1) movePage("goods_display.php?{$addstring}");
		$where = "o_num{$odisp} < '{$number}'";
		$exp1 = "o_num{$odisp} + 1";
		$exp2 = "1";
	break;

	case "up" :
		if($number==1)  movePage("goods_display.php?{$addstring}");
		$where = "o_num{$odisp} = '".($number-1)."'";
		$exp1 = "o_num{$odisp} + 1";
		$exp2 = "o_num{$odisp} - 1";
	break;

	case "down" : 
		$sql = "SELECT MAX(o_num{$odisp}) FROM mall_goods";
		if($mysql->get_one($sql)==$number)  movePage("goods_display.php?{$addstring}");
		$where = "o_num{$odisp} = '".($number+1)."'";
		$exp1 = "o_num{$odisp} - 1";
		$exp2 = "o_num{$odisp} + 1";
	break;			

	case "last" : 
		$sql = "SELECT MAX(o_num{$odisp}) FROM mall_goods";
		$lnumber = $mysql->get_one($sql);
		if($lnumber==$number)  movePage("goods_display.php?{$addstring}");
		$where = "o_num{$odisp} > '$number'";
		$exp1 = "o_num{$odisp} - 1";
		$exp2 = $lnumber;
	break;
	
	case "move" :
		if($number==$num) movePage("goods_display.php?{$addstring}");
		if($number<$num) {
			$sql = "SELECT MAX(o_num{$odisp}) FROM mall_goods";
			$lnumber = $mysql->get_one($sql);
			if($lnumber<=$num)  $num = $lnumber;
					
			$where = "o_num{$odisp} > '{$number}' && o_num{$odisp} <= '{$num}'";
			$exp1 = "o_num{$odisp} - 1";
			$exp2 = $num;
		}	
		else {
			if($num<=1) $num=1;
			$where = "o_num{$odisp} >= {$num} && o_num{$odisp} < '{$number}'";
			$exp1 = "o_num{$odisp} + 1";
			$exp2 = $num;
		}		
	break;
}

if($odisp>1) $odisp2 = 3;
else $odisp2 = 1;
		
$sql = "UPDATE mall_goods SET o_num{$odisp} = {$exp1} WHERE o_num{$odisp}!=0 && SUBSTRING(display,{$odisp2},1)='{$disps}' && {$where} {$cwhere}";
$mysql->query($sql);

$sql = "UPDATE mall_goods SET o_num{$odisp} = {$exp2} WHERE uid={$uid}";
$mysql->query($sql);

$sql = "SELECT uid FROM mall_goods WHERE o_num{$odisp}!=0 && SUBSTRING(display,{$odisp2},1)='{$disp}' ORDER BY o_num{$odisp} ASC";
$mysql->query($sql);

$i=1;
while($row = $mysql->fetch_array()){
	$sql = "UPDATE mall_goods SET o_num{$odisp} = {$i} WHERE uid='{$row['uid']}'";
	$mysql->query2($sql);
	$i++;
}

movePage("goods_display.php?{$addstring}");

?>
