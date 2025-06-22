<?
include "sub_init.php";

$order_num = $_GET['order_num'];
$sgoods = $_GET['sgoods'];
$status = $_GET['status'];

if(!$order_num || !$sgoods || !$status) alert("정보가 넘어오지 못했습니다.","close5");

$sql = "SELECT pay_type, order_status FROM mall_order_info WHERE order_num = '{$order_num}'";
$row = $mysql->one_row($sql);
$def_status = $row['order_status'];
$pay_type = $row['pay_type'];

$skin = "../skin/{$tmp_skin}";
$skin2 = $skin."/";

// 템플릿
require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;
$tpl->define("main","{$skin}/porder_change.html");
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

	$STATUS = $status_arr[$data['order_status']];
	$tpl->parse("loop_change");
	
	$ii++;
}

$arr = Array("X"=>"반품","Y"=>"교환","Z"=>"취소");
$TTL = $arr[$status];

$REASON = "";
foreach ($reason_code_arr as $key => $value) {
	if($key==0) continue;
	$REASON .= "<option value='{$key}'>{$value}</option>\n";
}

if($status!='Y' && $def_status!='A' && $pay_type=='B') $tpl->parse("is_bank1");
if($status!='Y' && $def_status!='A') $tpl->parse("is_bank2");

$tpl->parse("main");
$tpl->tprint("main");
?>