<?
$file_name = "itsMallOrderList_".date("Ymd",time());
header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-Disposition: attachment; filename={$file_name}.xls" ); 
header( "Content-Description: Gamza Excel Data" ); 
header( "Content-type: application/vnd.ms-excel;charset=KSC5601" ); 
######################## lib include
include "../ad_init.php";

require "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

$type = $_GET['type'];

if($type!='check') {
	###################### 변수 정의 ##########################
	$order_num	= $_GET['order_num'];
	$gs			= $_GET['gs'];
	$field		= $_GET['field'];
	$word		= $_GET['word'];
	$smoney1	= $_GET['smoney1'];
	$smoney2	= $_GET['smoney2'];
	$sdate1		= $_GET['sdate1'];
	$sdate2		= $_GET['sdate2'];
	$page		= $_GET['page'];
	$limit		= $_GET['limit'];
	$type		= $_GET['type'];
	$status		= $_GET['status'];
	$mobile		= $_GET['mobile'];

	if($gs) {
		$where .= "&& b.order_status='{$gs}'";
	}

	if($field && $word) {
		if($field=="multi") {
			$where .= "&& (INSTR(a.order_num,'{$word}') || INSTR(a.id,'{$word}') || INSTR(a.name1,'{$word}') || INSTR(a.name2,'{$word}') || INSTR(a.pay_name,'{$word}') || INSTR(b.p_name,'{$word}')  || INSTR(a.deli_type,'{$word}'))";
		}
		else if($field=="p_name") $where .= "&& INSTR(b.{$field},'{$word}')";
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
		$where .= " && a.pay_status='{$status}' ";
	}

	if($mobile) {
		$where .= " && a.mobile='{$mobile}'";
	}
}
else {
	$item = $_POST['item'];
	if(!$item) exit;

	$item = "'".join("','",$item)."'";
	$where .= " && a.order_num IN({$item}) ";
}


$skin = ".";
$mysql = new  mysqlClass(); //디비 클래스

$tpl = new classTemplate;
$tpl->define("main","order_excel.html");
$tpl->scan_area("main");

$sql = "SELECT uid,name FROM mall_design WHERE mode='Z'";
$mysql->query($sql);
while($row = $mysql->fetch_array()){
	$DELI_ARR[$row['uid']] = $row['name'];
}
	
/*********************************** QUERY **********************************/
$query = "SELECT a.*, COUNT(b.p_name) as cnt, SUM(b.p_qty) as qty, b.p_name, b.p_qty, b.p_option, a.order_status as order_status1, b.order_status, b.order_status2, b.p_price, b.op_price, b.sale_price FROM mall_order_info as a, mall_order_goods as b WHERE a.order_num=b.order_num {$where} group by b.order_num ORDER BY uid DESC";
$mysql->query($query);		
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
//사용 배열정의 
$MTlist = Array('','order_status','order_num','signdate','id','name1','','pay_total','','carriage','use_reserve','pay_type','','','tel1','hphone1','email','name2','tel2','hphone2','zipcode','address','message','use_cupon','order_status1'); //24

$pay_arr = Array('A'=>'결제미완료','B'=>'결제성공','C'=>'결제실패','D'=>'카드취소');
$pay_arr2 = Array('A'=>'계좌발금완료','B'=>'계좌입금완료','C'=>'입금실패','D'=>'환불');
$pay_arr3 = Array('A'=>'미입금','B'=>'입금완료','C'=>'입금실패','D'=>'환불');

$NUM = 1;
while ($row=$mysql->fetch_array()){
	$OPTIONS = '';
	
	for($i=1;$i<25;$i++){  
		$fd = $MTlist[$i];
		${"LIST".$i} = stripslashes($row[$fd]);   
		if(eregi("tel|hphone|zipcode",$fd)) ${"LIST".$i} = str_replace(" - ","-",${"LIST".$i});
	}
	
	$LIST61 = $LIST62 = $LIST63 = '';
	$LIST8 = 0;
	if($row['cnt']>1) {
		$rowspan = $row['cnt'];
		$sql = "SELECT p_name, p_qty, order_status, order_status2,p_option, p_price, op_price, sale_price FROM mall_order_goods WHERE order_num = '{$row['order_num']}' ORDER BY uid DESC";
		$mysql->query2($sql);
		$ckk =1;
		while($data = $mysql->fetch_array(2)) {
			if($data['order_status']=='X' || $data['order_status']=='Y' || $data['order_status']=='Z') {
				$tmps = "[".$status_arr2[$data['order_status'].$data['order_status2']]."]";
			}
			else $tmps = "";
			
			$OPTIONS = "";
			$p_option = explode("|*|",$data['p_option']);
			for($i=0,$cnt=count($p_option);$i<$cnt;$i++) {
				$p_option2 = explode("|",$p_option[$i]);
				if($p_option2[0]) {
					if(!$OPTIONS) $OPTIONS .= "<br />{$p_option2[0]}";
					else $OPTIONS .= ", {$p_option2[0]}";
				}
			}
			
			$LIST61 = stripslashes($data['p_name']);
			$LIST62 = number_Format($data['p_qty']);
			$LIST64 = $tmps;
			$LIST63 = $OPTIONS;
			$LIST65 = (($data['p_price'] + $data['op_price']) * $data['p_qty']) - $data['sale_price'];
			$LIST8 += $LIST65;
			$LIST65 = number_format($LIST65,$ckFloatCnt);

			if($ckk!=$row['cnt']) $tpl->parse("gloop");
			$ckk++;
		}
	}
	else {
		$rowspan = 1;
		if($row['order_status']=='X' || $row['order_status']=='Y' || $row['order_status']=='Z') {
			$tmps = "[".$status_arr2[$row['order_status'].$row['order_status2']]."]";
		}
		else $tmps = "";

		$OPTIONS = "";
		$p_option = explode("|*|",$row['p_option']);
		for($i=0,$cnt=count($p_option);$i<$cnt;$i++) {
			$p_option2 = explode("|",$p_option[$i]);
			if($p_option2[0]) {
				if(!$OPTIONS) $OPTIONS .= "<br />{$p_option2[0]}";
				else $OPTIONS .= ", {$p_option2[0]}";
			}
		}

		$LIST61 = stripslashes($row['p_name']);
		$LIST62 = number_Format($row['p_qty']);
		$LIST64 = $tmps;
		$LIST63 = $OPTIONS;
		$LIST65 = (($row['p_price'] + $row['op_price']) * $row['p_qty']) - $row['sale_price'];
		$LIST8 += $LIST65;
		$LIST65 = number_format($LIST65,$ckFloatCnt);
	}
	
	/**************************** GOODS OPTIONS **************************/			
	
	$LIST1 = $status_arr[$LIST24]; 	
	$LIST8 = number_format($LIST8,$ckFloatCnt);
	$LIST7 = number_format($LIST7,$ckFloatCnt);	  
	$LIST9 = number_format($LIST9,$ckFloatCnt);	  
	$LIST10 = number_format($LIST10,$ckFloatCnt);
	$LIST23 = number_format($LIST23,$ckFloatCnt);
	$LIST20 = str_replace(" - ","-",$LIST20);
	
	switch($LIST11) {
		case "B" :
			$bank_name = explode(",",$row['bank_name']);
			$LIST11 = "무통장<br /><font class='small'>($bank_name[0])</font>";
		break;
		case "C" :
			$LIST11 = "신용카드 <br /><font class='small'>({$pay_arr[$row[pay_status]]})</font>";
		break;
		case "R" :
			$LIST11 = "실시간 계좌이체 <br /><font class='small'>({$pay_arr[$row[pay_status]]})</font>";
		break;
		case "V" :
			$LIST11 = "가상 계좌이체 <br /><font class='small'>({$pay_arr2[$row[pay_status]]})</font>";
		break;
		case "H" :
			$LIST11 = "핸드폰 <br /><font class='small'>({$pay_arr[$row[pay_status]]})</font>";
		break;
	}

	if($row['carr_info']) {
		$tmps = explode("|",$row['carr_info']);			
		$LIST12 = $DELI_ARR[$tmps[0]];
		$LIST13 = "{$tmps[1]}";
	} 
	else $LIST12 = $LIST13 = "";
		
	$tpl->parse("loop");	
	$tpl->parse("gloop","2");
	$NUM++;
}

$tpl->parse("main");
$tpl->tprint("main");
?>