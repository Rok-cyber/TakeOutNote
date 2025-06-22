<?
include "sub_init.php";
include "$skin/skin_define.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

$uid = $_GET['uid'];

$skin = "../skin/{$tmp_skin}";
$skin2 = $skin."/";
$ShopPath = "../";

$sql = "SELECT p_number, p_qty, p_option FROM mall_cart WHERE uid='{$uid}'";
$row = $mysql->one_row($sql);

$sql = "SELECT * FROM mall_goods WHERE uid='{$row['p_number']}'";
if(!$data = $mysql->one_row($sql)) {
	alert('상품이 삭제 되었거나 없는 상품입니다.','close5');
}

$gData	= getDisplay($data,'image3');		// 디스플레이 정보 가공 후 가져오기
$GOODS_IMG	= $gData['image'];
$GOODS_IMG_WIDTH = $IMG_DEFINE['img3'];
$GOODS_NAME	= $gData['name'];		
$GOODS_PRICE= $gData['price'];
$GOODS_ICON	= $gData['icon'];
$GOODS_QTY  = $row['p_qty'];
$DEF_QTY		= stripslashes($data['def_qty']);
$GOODS_UNIT		= stripslashes($data['unit']);
$GOODS_RESERVE  = $gData['reserve'];
$P_PRICE		= $data['price'];
$P_PRICE2		= str_replace(array("원",","),"",$GOODS_PRICE);

if($data['s_qty']==4) $S_QTY = $data['qty'];
else $S_QTY = 0;

$tpl->define('main',"{$skin}/poption_select.html");
$tpl->scan_area('main');

if($data['consumer_price'] && $data['consumer_price'] >0){
	$GOODS_C_PRICE = number_format($data['consumer_price'],$ckFloatCnt)."원";
    $tpl->parse('is_c_price'); 
}

if($GOODS_RESERVE>0) $tpl->parse('is_reserve'); 

if($gData['cp_price']) {
	$sql = "SELECT * FROM mall_cupon_manager WHERE type='3' && INSTR(sgoods,'|{$uid}|') ORDER BY uid DESC LIMIT 1";
	if($cupon = $mysql->one_row($sql)){
		$GOODS_CPRICE = $gData['cp_price'];
		$P_PRICE3 = str_replace(array("원",","),"",$GOODS_CPRICE);
	}
}

/************************* 배송비 관련 ***********************/
$P_CARR = 0;
$carriage = explode("|",$data['carriage']);
if($carriage[0]==1) {
	$tpl->parse("is_carr_free");
}
else if($carriage[0]==3) { //별도 책정일때
	$GOODS_CARR = number_format($carriage[1],$ckFloatCnt)."원";	
	$P_CARR = $carriage[1];
	$tpl->parse("is_carr");
}	
/************************* 배송비 관련 ***********************/

/**************************** GOODS OPTIONS **************************/
$tmps = $OP_TITLE = $OP_VALUES = "";
$op_img_arr = Array('','색상','사이즈','용량','옵션','선택사항','추가구매');

$sql = "SELECT option1 FROM mall_goods_option WHERE guid='{$data['uid']}' GROUP BY option1 ORDER BY o_num ASC";
$mysql->query($sql);
	
$option_arr = Array();
while($row2 = $mysql->fetch_array()){
	$option_arr[] = $row2['option1'];
}

for($j=$i=0,$cnt=count($option_arr);$j<$cnt;$j++) {

	$sql = "SELECT * FROM mall_goods_option WHERE guid='{$data['uid']}' && option1='{$option_arr[$j]}' ORDER BY o_num ASC";
	$mysql->query($sql);
	
	$OP_TITLE = $option_arr[$j];
	if(in_array($OP_TITLE,$op_img_arr)) {
		for($o=1;$o<7;$o++) {
			if($OP_TITLE==$op_img_arr[$o]) break;
		}
		if(is_file("{$skin}/img/shop/ttl_goods_option{$o}.gif")) $tpl->parse("is_op_img");
	}
	else $tpl->parse("is_op_text");

	$OP_VALUES = "\n<option value=''>선택</option>\n";		
	$i = $j+1;
	while($tmp_op=$mysql->fetch_array()){	
		
		if($tmp_op['display']=='N') continue;
				
		if($tmp_op['price']>0) {
			$tmp_op['price'] = $tmp_op['price'] - round(($tmp_op['price'] * $MY_SALE)/100,$ckFloatCnt);
			$tmps2 = " (+".number_format($tmp_op['price'],$ckFloatCnt)."원)";
		}
		else $tmps2 = "";

		$OP_VLS = $tmp_op['uid'];
		$OP_TEXT = "{$tmp_op['option2']}{$tmps}";
		if($tmp_op['qty']==0) {
			$OP_TEXT .= " [품절]";
			$OP_VALUES .= "<option value='{$OP_VLS}' class='disabled'>{$OP_TEXT}</option>\n";
		}
		else {
			$OP_TEXT .= $tmps2;
			$OP_VALUES .= "<option value='{$OP_VLS}'>{$OP_TEXT}</option>\n";
		}		
		
		$tpl->parse("is_option_vlaue");		
	}
	unset($OP_VLS,$OP_TEXT);
	$tpl->parse("is_option");
	$tpl->parse("is_op_img","2");
	$tpl->parse("is_op_text","2");	
	$tpl->parse("is_option_vlaue","2");
}
$op_cnt = $i;
/**************************** GOODS OPTIONS **************************/

/*********************** 옵션선택 및 수량변경시 가격적용 이용 **************************/
if($P_PRICE3>0) $tmp_total = $P_PRICE3;
else if($P_PRICE2>0) $tmp_total = $P_PRICE2;
else $tmp_total = $P_PRICE;

$P_TOTAL = $tmp_total + $P_CARR;
$GOODS_TOTAL = number_format($P_TOTAL);

if($P_PRICE2>0) $tmp_total = $P_PRICE2;
else $tmp_total = $P_PRICE;
$P_TOTAL2 = $tmp_total + $P_CARR;

$P_RESERVE = 0;
if($GOODS_ORESERVE>0) {		
	$reserve = explode("|",$data['reserve']);
	if($reserve[0] =='2') { //쇼핑몰 정책일때
		if($cash[6] =='1') $P_RESERVE = $cash[8];
	} 
	else if($reserve[0] =='3') { //별도 책정일때
		$P_RESERVE = $reserve[1];
	}
}
$P_RESERVE += $MY_SALE;
/*********************** 옵션선택 및 수량변경시 가격적용 이용 **************************/



$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>