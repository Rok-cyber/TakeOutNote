<?
######################## lib include
include "../ad_init.php";
include "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

$skin = ".";

$order_num = $_GET['order_num'];
$uid = $_GET['uid'];

if(!$order_num || !$uid) alert("정보가 넘어오지 못했습니다.","close5");

$sql = "SELECT * FROM mall_order_change WHERE uid='{$uid}' && order_num='{$order_num}'";
$row = $mysql->one_row($sql);
if(!$row) alert("교환 내역이 존재 하지 않습니다","close5");
$sgoods	= $row['sgoods'];
$memo = $row['message'];

unset($row);

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","order_change_send.html");
$tpl->scan_area('main');

$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$sgoods})";
$mysql->query($sql);

$ii = 1;
while($data = $mysql->fetch_array()){

	if($data['sale_vls']){
		$tmps = explode("|",$data['sale_vls']);
		$MY_SALE = $tmps[0];
		$MY_POINT = $tmps[1];
		$MY_CARR = $tmps[2];
		$my_type1 = $tmps[3];
		$my_type2 = $tmps[4];
	}
	else {
		$MY_SALE = $MY_POINT = 0;
		$MY_CARR = 'N';
	}
	
	$gData	= getDisplayOrder2($data);	
	$NAME	= $gData['name'];		
	$QTY	= $data['p_qty'];
	$SUM	= $gData['p_sum'];
	if($gData['carr']=='F') $gData['carr'] = 0;
	$CARR	= number_format($gData['carr']*$data['p_qty']);	
	
	$OP_SEC_VLS = '';
	for($i=0,$cnt=count($gData['op_sec_vls']);$i<$cnt;$i++){
		if($gData['op_sec_vls'][$i]) {	
			$OP_SEC_VLS .= $gData['op_sec_vls'][$i];
		}		
	}
	$STATUS = $status_arr2[$data['order_status'].$data['order_status2']];
	$tpl->parse("loop_change");
	
	$ii++;
}

$sql = "SELECT uid,name,code FROM mall_design WHERE mode='Z'  ORDER BY uid ASC";
$mysql->query($sql);
for($i=0; $row = $mysql->fetch_object(); $i++) {
	if($tmps[0]==$row->uid) $DELIVERY .= "<option value='{$row->uid}' selected>".stripslashes($row->name)."</option>\n";
	else $DELIVERY .= "<option value='{$row->uid}'>".stripslashes($row->name)."</option>\n";
}

$tpl->parse("main");
$tpl->tprint("main");
?>