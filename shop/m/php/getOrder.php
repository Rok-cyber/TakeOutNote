<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$Pstart	= $_GET['Pstart'];
$limit	= $_GET['limit'];

if(!$limit || strlen($Pstart)==0) { 	
	echo "<error>Error</error></root>"; 
	exit;
}

$dates = $_GET['dates'];
$status	= $_GET['status'];

if($status) {
	$where .= "&& a.order_status='{$status}'";
}
if($dates) {	
	$sdate2 = date("Y-m-d");
	switch($dates) {
		case 1 : $sdate1 =  date("Y-m-d"); break;
		case 2 : $sdate1 =  date('Y-m-d', strtotime('-1 WEEK', time())); break;
		case 3 : $sdate1 =  date('Y-m-d', strtotime('-1 MONTH', time())); break;
		case 4 : $sdate1 =  date('Y-m-d', strtotime('-3 MONTH', time())); break;
		case 5 : $sdate1 =  date('Y-m-d', strtotime('-6 MONTH', time())); break;
		case 6 : $sdate1 =  date('Y-m-d', strtotime('-1 YEAR', time())); break;
	}
	
	if($sdate1==$sdate2) $where .= "&& INSTR(a.signdate,'{$sdate1}') ";
	else $where .= "&& (a.signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(a.signdate,'{$sdate2}'))";		
}  

$sql = "SELECT COUNT(uid) FROM mall_order_info a WHERE id='{$my_id}' {$where}";
$TOTAL = $mysql->get_one($sql);
echo "<total><![CDATA[{$TOTAL}]]></total>\n";

$type_arr = Array("C"=>"신용카드","B"=>"무통장","R"=>"계좌이체","V"=>"가상계좌","H"=>"핸드폰");

/**************************** GOODS LIST**************************/
$sql = "SELECT a.uid, a.order_num, a.pay_type, a.order_status, a.signdate, a.use_reserve, a.carriage, a.carr_info, a.pay_total, b.p_name, count(*) as cnt  FROM mall_order_info as a, mall_order_goods as b WHERE a.order_num=b.order_num && id='{$my_id}' {$where} group by b.order_num ORDER BY uid DESC LIMIT {$Pstart},{$limit}";	
$mysql->query($sql);

while($row = $mysql->fetch_array()){		
	$ORDER_NUM	= $row['order_num'];
	if($row['order_status']!='Z') {
		$sql = "SELECT count(*) as cnt FROM mall_order_goods WHERE order_num='{$ORDER_NUM}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D')";
		$tmps = $mysql->one_row($sql);				
		$row['cnt'] = $tmps['cnt'];
		$sql = "SELECT p_name FROM mall_order_goods WHERE order_num='{$ORDER_NUM}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D') LIMIT 1";
		$tmps = $mysql->one_row($sql);				
		$row['p_name'] = $tmps['p_name'];
	}
	
	$NAME	= stripslashes($row['p_name']);
	$CNT	= $row['cnt'];
	if($CNT >1) $NAME .= " 외".($CNT-1)."건";
	
	$PRICE = number_format($row['pay_total'],$ckFloatCnt);
	$TYPE = $type_arr[$row['pay_type']];
	$STATUS = $status_arr[$row['order_status']];
	if($row['order_status']=='D') {
		if($row['carr_info']) {
			$tmps = explode("|",$row['carr_info']);
			$sql = "SELECT code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
			$CLINK = $mysql->get_one($sql);
			$CNUM = $tmps[1];		
		}
	}
	else $CLINK = $CNUM = '';
	$DATE = substr($row['signdate'],0,10);
			
	echo "
	  <item>
		<orderNum><![CDATA[{$ORDER_NUM}]]></orderNum>
		<name><![CDATA[{$NAME}]]></name>
		<price><![CDATA[{$PRICE}]]></price>
		<type><![CDATA[{$TYPE}]]></type>
		<status><![CDATA[{$STATUS}]]></status>
		<date><![CDATA[{$DATE}]]></date>
		<clink><![CDATA[{$CLINK}]]></clink>
		<cnum><![CDATA[{$CNUM}]]></cnum>
	  </item>\n	";	
}

echo "</root>";
?>