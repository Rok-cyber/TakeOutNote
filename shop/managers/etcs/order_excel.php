<?
set_time_limit(0);
$file_name = "itsMallOrderList_".date("Ymd",time());
header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-Disposition: attachment; filename={$file_name}.xls" ); 
header( "Content-Description: Gamza Excel Data" ); 
header( "Content-type: application/vnd.ms-excel;charset=utf-8" ); 

######################## lib include
include "../ad_init.php";

require "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$order_num	= $_POST['order_num'];
$gs			= $_POST['gs'];
$field		= $_POST['field'];
$word		= $_POST['word'];
$smoney1	= $_POST['smoney1'];
$smoney2	= $_POST['smoney2'];
$sdate1		= $_POST['sdate1'];
$sdate2		= $_POST['sdate2'];
$type		= $_POST['type'];
$status		= $_POST['status'];
$item		= $_POST['item'];

if($gs) {
	$where .= "&& b.order_status='{$gs}'";
}

if($field && $word) {
	if($field=="p_name") $where .= "&& INSTR(b.{$field},'{$word}')";
	else $where .= "&& INSTR(a.{$field},'{$word}')";
}  else $field = "order_num";


if($smoney1 && $smoney2) {
	if($smoney1 > $smoney2) {$smoney1 = $tmp; $smoney1 = $smoney2; $smoney2 = $tmp;}
	if($smoney1==$smoney2) $where .= "&& INSTR(a.pay_total,'{$smoney1}') ";
	else $where .= "&& a.pay_total BETWEEN '{$smoney1}' AND '{$smoney2}' ";
}

if($sdate1 && $sdate2) {	
	if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	if($sdate1==$sdate2) $where .= "&& INSTR(a.signdate,'{$sdate1}') ";
	else $where .= "&& (a.signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(a.signdate,'{$sdate2}'))";		
}  

if($type) {
	$where .= " && a.pay_type='{$type}' ";
}

if($status) {	
	$where .= " && a.pay_status='{$status}' && pay_type!='B' ";
}


$sql = "SELECT uid,name FROM mall_design WHERE mode='Z'";
$mysql->query($sql);
while($row = $mysql->fetch_array()){
	$DELI_ARR[$row['uid']] = $row['name'];
}

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./order_excel.html");
$tpl->scan_area("main");


$list_arr = Array();
for($i=0,$cnt=count($item);$i<$cnt;$i++) {
	$list_arr = $item[$i];
	$tpl->parse("is_t{$item[$i]}");
}

$sql = "SELECT a.*, COUNT(b.p_name) as cnt, SUM(b.p_qty) as qty, b.p_name, b.p_option, a.order_status as order_status1, b.order_status, b.order_status2 FROM mall_order_info as a, mall_order_goods as b WHERE a.order_num=b.order_num {$where} group by b.order_num ORDER BY uid DESC";
$mysql->query($sql);

while($row = $mysql->fetch_array()){
	$goods_total = $row['pay_total'] - $row['carriage'] + $row['use_reserve'];
	for($i=0,$cnt=count($item);$i<$cnt;$i++) {
		switch($item[$i]) {
			case 'order_status' :
				$row[$item[$i]] = $status_arr[$row['order_status1']];
			break;
			case "goods_name" :
				if($row['cnt']>1) {
					$sql = "SELECT p_name, p_qty, order_status, order_status2,p_option FROM mall_order_goods WHERE order_num = '{$row['order_num']}' ORDER BY uid DESC";
					$mysql->query2($sql);
					while($data = $mysql->fetch_array(2)) {
						if($data['order_status']=='X' || $data['order_status']=='Y' || $data['order_status']=='Z') {
							$tmps = "[".$status_arr2[$data['order_status'].$data['order_status2']]."]";
						}
						else $tmps = "";
						
						$OPTIONS = "";
						$p_option = explode("|*|",$data['p_option']);
						for($ii=0,$cntt=count($p_option);$ii<$cntt;$ii++) {
							$p_option2 = explode("|",$p_option[$ii]);
							if($p_option2[0]) {
								if(!$OPTIONS) $OPTIONS .= "<br />{$p_option2[0]}";
								else $OPTIONS .= ", {$p_option2[0]}";
							}
						}

						$row[$item[$i]] .= stripslashes($data['p_name']).", 수량:{$data['p_qty']}개 {$tmps} {$OPTIONS}<br />";
					}
				}
				else {
					if($row['order_status']=='X' || $row['order_status']=='Y' || $row['order_status']=='Z') {
						$tmps = "[".$status_arr2[$row['order_status'].$row['order_status2']]."]";
					}
					else $tmps = "";

					$OPTIONS = "";
					$p_option = explode("|*|",$row['p_option']);
					for($ii=0,$cntt=count($p_option);$ii<$cntt;$ii++) {
						$p_option2 = explode("|",$p_option[$ii]);
						if($p_option2[0]) {
							if(!$OPTIONS) $OPTIONS .= "<br />{$p_option2[0]}";
							else $OPTIONS .= ", {$p_option2[0]}";
						}
					}

					$row[$item[$i]] = stripslashes($row['p_name']).", 수량:{$row['qty']}개 {$tmps} {$OPTIONS}";	
				}
				
			break;
			case "gooods_total" :
				$row[$item[$i]] = number_format($goods_total);
			break;
			case "pay_total" : case "carriage" : case "use_reserve" : case "use_cupon" :
				$row[$item[$i]] = number_format($row[$item[$i]],$ckFloatCnt);
			break;
			
			case "pay_type" :
				switch($row['pay_type']) {
					case "B" :
						$bank_name = explode(",",$row['bank_name']);
						$row[$item[$i]] = "무통장($bank_name[0])";
					break;
					case "C" :
						$row[$item[$i]] = "신용카드결제";
					break;
					case "R" :
						$row[$item[$i]] = "실시간 계좌이체";
					break;
					case "V" :
						$row[$item[$i]] = "가상 계좌이체";
					break;
					case "H" :
						$row[$item[$i]] = "핸드폰";
					break;
				}
			break;

			case "carriage1" :
				if($row['carr_info']) {
					$tmps = explode("|",$row['carr_info']);			
					$row[$item[$i]] = $DELI_ARR[$tmps[0]];
					$LIST13 = "{$tmps[1]}";
				} 			
			break;
			case "carriage2" :
				if($row['carr_info']) {
					$tmps = explode("|",$row['carr_info']);			
					$row[$item[$i]] = "{$tmps[1]}";
				} 			
			break;			

		}
		${"LIST".$item[$i]} = stripslashes($row[$item[$i]]);
		$tpl->parse("is_{$item[$i]}","1");
	}
	$tpl->parse("loop");
}


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>