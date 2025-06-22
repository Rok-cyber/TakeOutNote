<?
$uid = $_GET['uid'];
if(!$uid) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');

$sql = "SELECT p_number, p_qty, p_option FROM mall_cart WHERE uid='{$uid}'";
$row = $mysql->one_row($sql);

$sql = "SELECT * FROM mall_goods WHERE uid='{$row['p_number']}'";
if(!$data = $mysql->one_row($sql)) {
	alert('상품이 삭제 되었거나 없는 상품입니다.','back');
}

$tpl->define("main","{$skin}/option_select.html");
$tpl->scan_area('main');

$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
$GOODS_LINK		= $gData['link'];
$GOODS_IMAGE	= "../".$gData['image'];
$GOODS_NAME		= $gData['name'];
$GOODS_PRICE	= $gData['price']; //판매가
$GOODS_PRICE = $GOODS_PRICE2	= str_replace("원","",$gData['price']);	

$GOODS_QTY		= $row['p_qty'];
$DEF_QTY		= stripslashes($data['def_qty']);
$GOODS_UNIT		= stripslashes($data['unit']);
$GOODS_RESERVE  = $gData['reserve'];
$P_PRICE		= $data['price'];
$P_PRICE2		= str_replace(array("원",","),"",$GOODS_PRICE);

if($data['s_qty']==4) $S_QTY = $data['qty'];
else $S_QTY = 0;

if($DEF_QTY==0) $DEF_QTY2=1;
else $DEF_QTY2 = $DEF_QTY;

if($gData['cp_price']) {
	$sql = "SELECT * FROM mall_cupon_manager WHERE type='3' && INSTR(sgoods,'|{$uid}|') ORDER BY uid DESC LIMIT 1";
	if($cupon = $mysql->one_row($sql)){
		$CP_TYPE = $cupon['use_type'];
		$CP_SALE = $cupon['sale'];
		$P_PRICE3 = str_replace(array("원",","),"",$GOODS_CPRICE);		
	}
}

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
if($P_PRICE2>0) $tmp_total = $P_PRICE2;
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

/**************************** 품절 관련 **************************/
if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) {
	$QTY_LIST = "<option value=''>품절</option>";
	
	$tpl->parse('is_limit');
	$tpl->parse('is_limit3');
} 
else {
	$QTY_LIST = "";
	for($i=$DEF_QTY2;$i<101;$i++) {
		$QTY_LIST .= "<option value='{$i}'>{$i}</option>";
	}
	$tpl->parse('no_limit');
	$tpl->parse('is_limit2');
}
/**************************** 품절 관련 **************************/

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>