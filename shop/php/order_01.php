<?
$tpl->define("main","{$skin}/order_01.html");
$tpl->scan_area('main');

/**************************** CART LIST**************************/
$sql = "UPDATE mall_cart SET p_direct='N' WHERE tempid='{$_COOKIE['tempid']}'";
$mysql->query($sql);

$sql = "DELETE FROM mall_cart WHERE tempid='{$_COOKIE['tempid']}' && SUBSTRING(p_cate,1,3)='999'";
$mysql->query($sql);

$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE[tempid]}'";
$mysql->query($sql);

$TCNT = $total = $carr = $trese = 0;
$ck_carr_only = '';

while($data = $mysql->fetch_array()){	
	$MY_SALE = $my_sale;
	$MY_POINT = $my_point;

	$gData	= getDisplayOrder($data);		// 디스플레이 정보 가공 후 가져오기

	if(!$gData['uid']) {
		$sql = "DELETE FROM mall_cart WHERE uid='{$data['uid']}'";
		$mysql->query($sql);
		alert("삭제된 상품이 있어 장바구니에서 삭제 되었습니다.","{$Main}?channel=cart");
	}

	$LINK	= $gData['link'];
	$IMAGE	= $gData['image'];
	$OIMAGE	= $gData['simage'];
	$NAME	= $gData['name'];	
	$ICON	= $gData['icon'];	
	$OPTION = $gData['option'];
	$PRICE	= $gData['p_price'];
	$OP_PRICE	= $gData['p_op_price'];
	$UID	= $data['uid']; 
	$QTY	= $data['p_qty'];
	$UNIT	= $gData['unit'];
	$RESE	= $gData['p_reserve'];
	$SUM	= $gData['p_sum'];
	$QLINK	= $gData['uid'];
	$LOC = getLocation($data[p_cate],'1');
	$total = $total+($gData['sum']);

	$trese += $gData['reserve'];
	$tsale += $gData['sale'];

	if($gData['my_sale']>0) {
		$TTL = $gData['my_type1'];
		$MY_SALE = $gData['my_sale'];
		$tpl->parse("is_my_sale","1");
	}

	if($gData['my_point']>0) {
		$TTL = $gData['my_type2'];
		$MY_POINT = $gData['my_point'];
		$tpl->parse("is_my_point","1");		
	}
	
	if($gData['carr']) {
		if($gData['carr']=='F') { 
			$carr = 0;
			$my_carr = 'Y';
			$tpl->parse("is_my_free","1");
		}
		else { 
			$carr += $gData['carr'];
			$CARR = number_format($gData['ocarr']);
			$tpl->parse("is_my_carr","1");
			if(!$ck_carr_only) $ck_carr_only = 'Y';
		}
	}	
	else if($ck_carr_only=='Y') $ck_carr_only = 'N';

	for($i=1,$cnt=count($gData['op_name']);$i<=$cnt;$i++){
		if($gData['op_name'][$i] && $gData['op_list'][$i]) {
			$OP_SEC  = $gData['op_sec'][$i];
			$OP_NAME = $gData['op_name'][$i];
			$OP_LIST = $gData['op_list'][$i];
			$OP_SEC_VLS = $gData['op_sec_vls'][$i];
			$tpl->parse("loop_op");
		}
	}
	$p_op_cnt = $cnt;

	$tpl->parse('loop_cart');	   
	$tpl->parse('loop_op','2');
	$tpl->parse("is_my_sale","2");
	$tpl->parse("is_my_point","2");
	$tpl->parse("is_my_carr","2");
	$tpl->parse("is_my_free","2");
	$TCNT++;
}

if($TCNT==0) $tpl->parse('no_cart'); 
else {

	/************************* 배송비 관련 ***********************/ 
	if($cash[10] =='1' && $my_carr!='Y' && $ck_carr_only !='Y') { 
		if($total < $cash[11]) $carr += $cash[12];
	} 
	$TCARR = number_format($carr,$ckFloatCnt);
	/************************* 배송비 관련 ***********************/

	$TOTAL	= number_format($total,$ckFloatCnt);
	$TRESE	= number_format($trese,$ckFloatCnt);
	if($tsale>0) {
		$TSALE	= number_format($tsale,$ckFloatCnt);
		$tpl->parse("is_tsale");
	}
	
	$C_TOTAL = $total+$carr;
	$TOTAL2 = number_format($C_TOTAL,$ckFloatCnt);
	
	for($i=0,$cnt=strlen($TOTAL2);$i<$cnt;$i++){
		if($TOTAL2[$i]==',') $IMG_TOTAL .= "<img src='img/shop/star_jum2.gif' />";
		else $IMG_TOTAL .= "<img src='img/shop/star_num{$TOTAL2[$i]}.gif' />";
	}

	if($my_id) $CKLOG = "Y";
	else $CKLOG = "N";

	if($_GET['pcate']) {
		$pcate = $_GET['pcate'];

		$sql = "SELECT cate_sub FROM mall_cate WHERE cate='{$pcate}'";
		$tmps = $mysql->get_one($sql);

		if($tmps==1) $ILINK = "location.href='{$Main}?channel=main2&cate={$pcate}&page={$_GET['page']}'";
		else $ILINK = "location.href='{$Main}?channel=list&cate={$pcate}&page={$_GET['page']}'";
	}
	else $ILINK = "history.back()";

	$tpl->parse("is_list");	
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>