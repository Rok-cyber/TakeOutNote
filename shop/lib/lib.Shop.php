<?

/******************************************************************************
 * pMall library 
 *
 * 일자 : 2004. 02. 8
 * 
 *
 * by previl (previl@previl.net)
 *
 ******************************************************************************/

// require나 include시 중복선언 방지를 위한 부분 

if(!isset($__PREVIL_LIB2__))   
{ 
  $__PREVIL_LIB2__ = 1; 

$ckFloatCnt = "0";
$status_arr = Array('A'=>'입금대기중','B'=>'결제완료','C'=>'배송준비중','D'=>'배송중','E'=>'배송완료','Z'=>'취소완료','DEL'=>'주문삭제');
$status_arr2 = Array('XA'=>'반품요청','XB'=>'반품처리중','XC'=>'반품회수완료','XD'=>'반품완료','XZ'=>'반품복원','YA'=>'교환요청','YB'=>'교환처리중','YC'=>'교환회수완료','YD'=>'교환발송완료','YZ'=>'교환복원','ZA'=>'취소요청','ZB'=>'취소승인','ZC'=>'결제환불','ZD'=>'취소완료','ZZ'=>'취소복원','A'=>'입금대기중','B'=>'결제완료','C'=>'배송준비중','D'=>'배송중','E'=>'배송완료','Z'=>'취소완료');

$reason_code_arr = Array('','고객변심','상품품절','배송지연','이중주문','시스템오류','누락배송','택배분실','상품불량','기타');


/*
##############################################
    ::: 경로 표시 :::          
    사용방법 : getLocation('카테고리번호','타입')
	타입값을 주면 마지막 경로에도 링크가 붙습니다.(장바구니에서 사용)
##############################################
*/

function getLocation($cate,$type="",$sepa=">",$css_class=""){
	global $mysql,$Main;
	if(!$cate) return false;
	if(!$type && $sepa) $top=$sepa;
	if(eregi("\.\.",$Main)) $target = "target='_blank'";
	else $target = "";
	if($css_class) $css_class = " class='{$css_class}'";
	
	$tmp  = substr($cate,0,3);
	$sql = "SELECT cate_name, cate_sub FROM mall_cate WHERE cate = '{$tmp}000000000'";
	$row = $mysql->one_row($sql);
	
	if($tmp=='999') {
		$channel_type="cooperate";
		$ck_coop = 'Y';
	} else if($row['cate_sub']==1) $channel_type = "main2";
	else $channel_type = "list";	

	if(!$type)  $SHOP_LOCATION .= " {$sepa} ".$row['cate_name'];
	else $SHOP_LOCATION .= " {$top} <a href='{$Main}?channel={$channel_type}&amp;cate={$tmp}000000000' {$target} {$css_class}>{$row['cate_name']}</a>"; 
	
	$tmp  = substr($cate,3,3);
	if($tmp!="000") {
		$tmp  = substr($cate,0,6);
		$sql = "SELECT cate_name, cate_sub FROM mall_cate WHERE cate = '{$tmp}000000'";
		$row = $mysql->one_row($sql);
		
		if($ck_coop=='Y') $channel_type="cooperate";
		else if($row['cate_sub']==1) $channel_type = "main2";
		else $channel_type = "list";

		if(!$type)  $SHOP_LOCATION .= " {$sepa} ".$row['cate_name'];
		else $SHOP_LOCATION .= " {$sepa} <a href='{$Main}?channel={$channel_type}&amp;cate={$tmp}000000' {$target} {$css_class}>{$row['cate_name']}</a>";
	
		$tmp  = substr($cate,6,3);
		if($tmp!="000") {
			$tmp  = substr($cate,0,9);
			$sql = "SELECT cate_name, cate_sub FROM mall_cate WHERE cate = '{$tmp}000'";
			$row = $mysql->one_row($sql);
			
			if($ck_coop=='Y') $channel_type="cooperate";
			else if($row['cate_sub']==1) $channel_type = "main2";
			else $channel_type = "list";

			if(!$type)  $SHOP_LOCATION .= " {$sepa} ".$row['cate_name'];
			else $SHOP_LOCATION .= " {$sepa} <a href='{$Main}?channel={$channel_type}&amp;cate={$tmp}000' {$target} {$css_class}>{$row['cate_name']}</a>";

			$tmp  = substr($cate,9,3);
			if($tmp!="000") {				
				$sql = "SELECT cate_name FROM mall_cate WHERE cate = '{$cate}'";
				$row_name = $mysql->get_one($sql);

				if(!$type)  $SHOP_LOCATION .= " {$sepa} ".$row_name;
				else $SHOP_LOCATION .= " {$sepa} <a href='{$Main}?channel=list&amp;cate={$cate}' {$target} {$css_class}>{$row_name}</a>";
			}
		}	
	}
	return $SHOP_LOCATION;
}

function getMLocation($cate,$nlink=""){
	global $mysql,$Main;
	if(!$cate) return false;
		
	$tmp  = substr($cate,0,3);
	$sql = "SELECT cate_name FROM mall_cate WHERE cate = '{$tmp}000000000'";
	$row_name = $mysql->get_one($sql);

	if($nlink)  $SHOP_LOCATION .= $row_name;
	else $SHOP_LOCATION .= "<a href='{$Main}?seccate={$tmp}000000000' onfocus='this.blur();' class='mTitle2'>{$row_name}</a>"; 
	
	$tmp  = substr($cate,3,3);
	if($tmp!="000") {
		$tmp  = substr($cate,0,6);
		$sql = "SELECT cate_name FROM mall_cate WHERE cate = '{$tmp}000000'";
		$row_name = $mysql->get_one($sql);

		if($nlink)  $SHOP_LOCATION .= " > ".$row_name;
		else $SHOP_LOCATION .= " > <a href='{$Main}?seccate={$tmp}000000' onfocus='this.blur();' class='mTitle2'>{$row_name}</a>"; 

		$tmp  = substr($cate,6,3);
		if($tmp!="000") {
			$tmp  = substr($cate,0,9);
			$sql = "SELECT cate_name FROM mall_cate WHERE cate = '{$tmp}000'";
			$row_name = $mysql->get_one($sql);

			if($nlink)  $SHOP_LOCATION .= " > ".$row_name;
			else $SHOP_LOCATION .= " > <a href='{$Main}?seccate={$tmp}000' onfocus='this.blur();' class='mTitle2'>{$row_name}</a>"; 

			$tmp  = substr($cate,9,3);
			if($tmp!="000") {				
				$sql = "SELECT cate_name FROM mall_cate WHERE cate = '{$cate}'";
				$row_name = $mysql->get_one($sql);

				if($nlink)  $SHOP_LOCATION .= " > ".$row_name;
				else $SHOP_LOCATION .= " > <a href='{$Main}?seccate={$cate}' onfocus='this.blur();' class='mTitle2'>{$row_name}</a>"; 
			}
		}	
	}
	return $SHOP_LOCATION;
}


function getDisplay($row,$img,$main=''){
	global $Main,$ShopPath,$cash,$my_sale,$my_point,$EVENT_SALE,$EVENT_POINT,$COUPON_GOODS,$COUPON_UID,$ckFloatCnt;
	if(!$row) return;	
	if(!$main) $main=$PHP_SELF;		
	$data = Array();
	$row['name']	= stripslashes($row['name']);
	$data['link']	= "{$Main}?channel=view&amp;uid={$row['uid']}&amp;cate={$row[cate]}";
	$data['name']	= stripslashes($row['name']);    
	
	if($ShopPath!="../") $ShopPath = "";
	if($row[$img]) $data['image']	= "{$ShopPath}image/goods_img{$row[$img]}";
	else {
		switch($img) {
			case "image1" : $data['image'] = "{$ShopPath}image/no_goods/no_goods1.gif"; break;
			case "image2" : $data['image'] = "{$ShopPath}image/no_goods/no_goods2.gif"; break;
			case "image3" : $data['image'] = "{$ShopPath}image/no_goods/no_goods3.gif"; break;
			case "image4" : $data['image'] = "{$ShopPath}image/no_goods/no_goods4.gif"; break;
			case "image5" : $data['image'] = "{$ShopPath}image/no_goods/no_goods5.gif"; break;
		}
	}	

	$data['dragd'] = "onmousedown=\"gToCart.init(event,this,'{$row[cate]}','{$row['uid']}');\"";

	$data['comp']	= stripslashes($row['comp']);
	$data['c_cnt'] = number_format($row['c_cnt']);
	if($row['consumer_price'] && $row['consumer_price'] >0){
		$data['cprice'] = number_format($row['consumer_price'],$ckFloatCnt)."원";
	}

	if(substr($row['cate'],0,3)=='999') $my_sale = $my_point = 0;
	
	$data['my_type1'] = '회원';
	$data['my_type2'] = '회원';
	$data['my_sale'] = $my_sale;
	$data['my_point'] = $my_point;

	if($row['event']>0) {		
		if($my_sale < $EVENT_SALE[$row['event']]) {			
			$data['my_sale'] = $EVENT_SALE[$row['event']];
			$data['my_type1'] = '이벤트';			
		} 	
		if($my_point < $EVENT_POINT[$row['event']]) {
			$data['my_point'] = $EVENT_POINT[$row['event']];		
			$data['my_type2'] = '이벤트';
		}		
	}		
	if($row['price']==0 && $row['price_ment']) $data['price'] = stripslashes($row['price_ment']);
	else {
		$data['oprice'] = $row['price'];
		if($data['my_sale'] > 0) $row['price'] =  round($row['price'] - (($row['price'] * $data['my_sale'])/100),$ckFloatCnt);		
		$data['price'] = number_format($row['price'],$ckFloatCnt)."원";
	}
	/************************* 적립금 관련 ***********************/
	$data['reserve'] = 0;
	$reserve = explode("|",$row['reserve']);
	if($reserve[0] =='2') { //쇼핑몰 정책일때
		if($cash[6] =='1') { 
			$data['reserve'] = round(($row['price'] * $cash[8])/100,$ckFloatCnt);
		} 
	} 
	else if($reserve[0] =='3') { //별도 책정일때
		$data['reserve'] = round(($row['price'] * $reserve[1])/100,$ckFloatCnt);
	}	

	if($data['my_point'] > 0) {
		$data['reserve'] += round(($row['price'] * $data['my_point'])/100,$ckFloatCnt);	
	}	
	$data['reserve'] = number_format($data['reserve'],$ckFloatCnt);
	/************************* 적립금 관련 ***********************/

	/************************* 아이콘 관련 ***********************/
	$data['icon'] = '';
	if($row[icon]){
		$icon = explode("|",$row['icon']);
		for($i=1,$cnt=count($icon);$i<$cnt;$i++) {
		   $data['icon'] .= "<img src='{$ShopPath}image/icon/{$icon[$i]}' border='0' align='absmiddle' alt='icon' />&nbsp;";
		}
	}	
	/************************* 아이콘 관련 ***********************/
	
	/************************* 상품 쿠폰 관련 ***********************/
	$data['cp_price'] = 0;
	for($j=0,$cnt=count($COUPON_GOODS);$j<$cnt;$j++) {
		$ck_coupon = explode("|",$COUPON_GOODS[$j]);		
		if(in_array($row['uid'],$ck_coupon)) {
			$mysql2 = new mysqlClass();
			$sql = "SELECT * FROM mall_cupon_manager WHERE uid='{$COUPON_UID[$j]}'";
			if($cupon = $mysql2->one_row($sql)){
				$tmp_ck = 1;
				if($cupon['sdate'] && $cupon['edate'] && !$cupon['days']) {
					if(date("Y-m-d") < substr($cupon['sdate'],0,10) || date("Y-m-d") > substr($cupon['edate'],0,10)) $tmp_ck = 0;
				}

				if($tmp_ck==1) {		
					if($cupon['stype']=='P') {
						$data['cp_price'] =  round($row['price'] - (($row['price'] * $cupon['sale'])/100),$ckFloatCnt); 
					}
					else $data['cp_price'] =  $row['price'] - $cupon['sale']; 
					$data['cp_price'] = numberLimit($data['cp_price'],1);
					$data['cp_price'] = number_format($data['cp_price'],$ckFloatCnt)."원"; 
					break;
				}	
			}
		}
	}
	/************************* 상품 쿠폰 관련 ***********************/
	
	return $data;
}

function getDisplayOrder($row, $s_my_sale='',$s_my_point=''){
	global $Main,$ShopPath,$cash,$tpl,$MY_SALE,$MY_POINT,$EVENT_SALE,$EVENT_POINT,$ckFloatCnt,$IMG_DEFINE;
	if(!$row) return;	
	if(!$main) $main=$PHP_SELF;	
	if($s_my_sale) $MY_SALE = $s_my_sale;
	if($s_my_point) $MY_POINT = $s_my_point;

	$mysql2 = new mysqlClass();
	$sql = "SELECT uid,name,price,price_ment,image4,carriage,unit,event,reserve,coop_price,coop_pay FROM mall_goods WHERE s_qty !='3' && type='A' && uid='{$row[p_number]}'";
	$tmps = $mysql2->one_row($sql);
	if($row['p_price']) $tmps['price'] = $row['p_price'];
	else {
		if(substr($row['p_cate'],0,3)=='999') {
			if($tmps['coop_pay']=='Y') {
				$sql = "SELECT price FROM mall_goods_cooper WHERE guid='{$row[p_number]}' ORDER BY qty ASC LIMIT 1";
				$tmps['price'] = $mysql2->get_one($sql);
			}
			else $tmps['price'] = $tmps['coop_price'];			
		}
	}
	
	$data = Array();	
		
	$data['link']	= "{$Main}?channel=view&amp;uid={$row[p_number]}&amp;cate={$row[p_cate]}";
	$data['name']	= stripslashes($tmps['name']);    
	
	if($ShopPath!="../") $ShopPath = "";
	if($tmps['image4']) {
		$data['image']	= "<img src='{$ShopPath}image/goods_img{$tmps[image4]}' border='0' width='{$IMG_DEFINE['img4']}' />";	
		$data['simage']	= "{$ShopPath}image/goods_img{$tmps[image4]}";
	}
	else {
		$data['image'] = "<img src='{$ShopPath}image/no_goods/no_goods4.gif' border='0' width='{$IMG_DEFINE['img4']}' />";	
		$data['simage']	= "{$ShopPath}image/no_goods/no_goods4.gif";
	}

	$data['unit']	= stripslashes($tmps['unit']); 
	$data['uid']	= $tmps['uid'];

	if(substr($row['p_cate'],0,3)=='999') $MY_SALE = $MY_POINT = 0;

	$data['my_type1'] = '회원';
	$data['my_type2'] = '회원';
	$data['my_sale'] = $MY_SALE;
	$data['my_point'] = $MY_POINT;
	
	if($tmps['event']>0) {
		if($data['my_sale'] < $EVENT_SALE[$tmps['event']]) {
			$data['my_sale'] = $EVENT_SALE[$tmps['event']];
			$data['my_type1'] = '이벤트';
		} 
		
		if($data['my_point'] < $EVENT_POINT[$tmps['event']]) {
			$data['my_point'] = $EVENT_POINT[$tmps['event']];		
			$data['my_type2'] = '이벤트';
		}		
	}

	if($tmps['price']==0 && $tmps['price_ment']) {
		$data['price'] = 0;
		$data['p_price'] = stripslashes($tmps['price_ment']);
	}
	else {
		if($data['my_sale'] > 0) {
			$data['sale'] = round(($tmps['price'] * $data['my_sale'])/100,$ckFloatCnt);
			$tmps['price'] =  $tmps['price'] - $data['sale'];	
			$row['p_reserve'] = $row['p_reserve'] - round(($row['p_reserve'] * $data['my_sale'])/100,$ckFloatCnt);
		}
		else $data['sale']=0;
		$data['price'] = $tmps['price'];
		$data['p_price'] = number_format($tmps['price'],$ckFloatCnt);
	}
	
	/************************* 아이콘 관련 ***********************/
	$data['icon'] = '';
	if($row[icon]){
		$icon = explode("|",$row['icon']);
		for($i=1,$cnt=count($icon);$i<$cnt;$i++) {
		   $data['icon'] .= "<img src='{$ShopPath}image/icon/{$icon[$i]}' border=0 align='absmiddle' alt='icon' />&nbsp;";
		}
	}		
	/************************* 아이콘 관련 ***********************/
    
	/**************************** GOODS OPTIONS **************************/			
	$p_option = explode("|",$row['p_option']);
	
	$sql = "SELECT option1 FROM mall_goods_option WHERE guid='{$data['uid']}' GROUP BY option1 ORDER BY o_num ASC";
	$mysql2->query($sql);
	
	$option_arr = Array();
	while($row2 = $mysql2->fetch_array()){
		$option_arr[] = $row2['option1'];
	}
		
	for($j=$i=0,$cnt=count($option_arr);$j<$cnt;$j++) {
		$sql = "SELECT * FROM mall_goods_option WHERE guid='{$data['uid']}' && option1='{$option_arr[$j]}' ORDER BY o_num ASC";
		$mysql2->query($sql);
		
		$i = $j+1;		
		$data['op_name'][$i] = $option_arr[$j];
		$data['op_sec'][$i] = 0;
		$OP_LIST = '';

		while($tmp_op=$mysql2->fetch_array()){	
			if($tmp_op['display']=='N') continue;
			
			if(in_array($tmp_op['uid'],$p_option)) { 
				$sec = "selected";
				$data['op_sec'][$i] = $i;
				if($tmp_op['price']>0) {
					$tmp_price = $tmp_op['price'] - round(($tmp_op['price'] * $data['my_sale'])/100,$ckFloatCnt);
					$data['op_sec_vls'][$i] = "{$tmp_op['option2']} (+".number_format($tmp_price,$ckFloatCnt)."원)";
				}
				else $data['op_sec_vls'][$i] = "{$tmp_op['option2']}";
			} 
			else $sec = "";
			
			if($tmp_op['price']>0) {
				$tmp_op['price'] = $tmp_op['price'] - round(($tmp_op['price'] * $data['my_sale'])/100,$ckFloatCnt);
				$tmps2 = " (+".number_format($tmp_op['price'],$ckFloatCnt)."원)";
			}
			else $tmps2 = "";

			if($tmp_op['qty']==0) $OP_LIST .= "<option value='{$tmp_op['uid']}' class='disabled'>{$tmp_op['option2']}{$tmps2} [품절]</option>\n";
			else $OP_LIST .= "<option value='{$tmp_op['uid']}' {$sec}>{$tmp_op['option2']}{$tmps2}</option>\n";		
			
			$data['op_list'][$i] = $OP_LIST;			
		}
	}

	$tmp_sale = 0;	
	if($row['op_price']>0) {
		$tmp_sale = round(($row['op_price'] * $data['my_sale'])/100,$ckFloatCnt);
		$row['op_price'] = $row['op_price'] - $tmp_sale;
		$data['p_op_price'] = "<br />+".number_format($row['op_price'],$ckFloatCnt)."원";
	}
	else $row['op_price'] = 0;
	/**************************** GOODS OPTIONS **************************/

	/************************* 적립금 관련 ***********************/
	$row['p_reserve'] = 0;		
	$reserve = explode("|",$tmps['reserve']);
	if($reserve[0] =='2') { //쇼핑몰 정책일때
		if($cash[6] =='1') { 
			$row['p_reserve'] = round((($tmps['price'] + $row['op_price']) * $cash[8])/100,$ckFloatCnt);
		} 
	} 
	else if($reserve[0] =='3') { //별도 책정일때
		$row['p_reserve'] = round((($tmps['price'] + $row['op_price']) * $reserve[1])/100,$ckFloatCnt);
	}	

	if($data['my_point'] > 0) {		
		$row['p_reserve'] += round((($tmps['price'] + $row['op_price']) * $data['my_point'])/100,$ckFloatCnt);		
	}	
	/************************* 적립금 관련 ***********************/

	/************************* 배송비 관련 ***********************/
	$carriage = explode("|",$tmps['carriage']);
	if($carriage[0]==1) $data['carr'] = "F";
	else if($carriage[0]==3) { //별도 책정일때
		$data['ocarr'] = $carriage[1];
		$data['carr'] = ($row['p_qty']*$carriage[1]);
	}	
	/************************* 배송비 관련 ***********************/
	
	$data['oprice'] = ($data['price']+$row['op_price']);
	$data['sum'] = $row['p_qty'] * ($data['price']+$row['op_price']);
	$data['reserve'] = $row['p_qty'] * $row['p_reserve'];
	$data['sale'] = $row['p_qty'] * ($data['sale'] + $tmp_sale);
	$data['p_reserve'] = number_format($data['reserve'],$ckFloatCnt); 
	$data['p_sum'] = number_format($data['sum'],$ckFloatCnt);	

	return $data;
}

function getDisplayOrder2($row){
	global $Main,$ShopPath,$cash,$tpl,$MY_SALE,$MY_POINT,$EVENT_SALE,$EVENT_POINT,$PHP_SELF,$ckFloatCnt,$IMG_DEFINE;
	if(!$row) return;	
	if(!$Main) $Main=$PHP_SELF;	
	$mysql2 = new mysqlClass();
	$sql = "SELECT carriage,price_ment,uid,unit,image4,brand FROM mall_goods WHERE uid='{$row[p_number]}'";
	$tmps = $mysql2->one_row($sql);
	if($row['p_price']) $tmps['price'] = $row['p_price'];

	$data = Array();	
		
	$data['link']	= "{$Main}?channel=view&amp;uid={$row[p_number]}&amp;cate={$row[p_cate]}";
	$data['name']	= stripslashes($row['p_name']);    
	
	if($ShopPath!="../") $ShopPath = "";
	if($tmps['image4']) {
		$data['image']	= "<img src='{$ShopPath}image/goods_img{$tmps[image4]}' border='0' width='{$IMG_DEFINE['img4']}' />";	
		$data['simage']	= "{$ShopPath}image/goods_img{$tmps[image4]}";
	}
	else {
		$data['image'] = "<img src='{$ShopPath}image/no_goods/no_goods4.gif' border='0' width='{$IMG_DEFINE['img4']}' />";	
		$data['simage']	= "{$ShopPath}image/no_goods/no_goods4.gif";
	}
	
	$data['unit']	= stripslashes($tmps['unit']); 
	$data['uid']	= $tmps['uid'];
	$data['brand']	= $tmps['brand'];
	
	$data['my_sale'] = $MY_SALE;
	$data['my_point'] = $MY_POINT;
	
	if($row['p_price']==0 && $tmps['price_ment']) {
		$data['price'] = 0;
		$data['p_price'] = stripslashes($tmps['price_ment']);
	}
	else {
		if($data['my_sale'] > 0) {
			$data['sale'] = round(($row['p_price'] * $data['my_sale'])/100,$ckFloatCnt);
			$row['p_price'] =  $row['p_price'] - $data['sale'];	
			$row['p_reserve'] = $row['p_reserve'] - round(($row['p_reserve'] * $data['my_sale'])/100,$ckFloatCnt);
		}
		else $data['sale']=0;
		$data['price'] = $row['p_price'];
		$data['p_price'] = number_format($row['p_price'],$ckFloatCnt);
	}
	
	/************************* 적립금 관련 ***********************/
	if($data['my_point'] > 0) {		
		$row['p_reserve'] += round(($row['p_price'] * $data['my_point'])/100,$ckFloatCnt);		
	}	
	/************************* 적립금 관련 ***********************/
	
	/**************************** GOODS OPTIONS **************************/		
	if($row['p_option']) {
		$p_option = explode("|*|",$row['p_option']);
		for($i=0,$cnt=count($p_option);$i<$cnt;$i++) {
			$p_option2 = explode("|",$p_option[$i]);
			if($p_option2[1]>0) {
				$p_option2[1] = $p_option2[1] - round(($p_option2[1] * $my_sale)/100,$ckFloatCnt);
				$data['op_sec_vls'][$i] = "{$p_option2[0]} (+".number_format($p_option2[1],$ckFloatCnt)."원)&nbsp;";
			}
			else $data['op_sec_vls'][$i] = "{$p_option2[0]}";				
		}
	}
	
	$tmp_sale = 0;	
	if($row['op_price']>0) {
		$tmp_sale = round(($row['op_price'] * $data['my_sale'])/100,$ckFloatCnt);
		$row['op_price'] = $row['op_price'] - $tmp_sale;
		$data['p_op_price'] = "<br />+".number_format($row['op_price'],$ckFloatCnt)."원";
	}
	else $row['op_price'] = 0;
	/**************************** GOODS OPTIONS **************************/

	/************************* 배송비 관련 ***********************/
	if($row['carriage']==99999) $data['carr'] = "F";
	else $data['carr'] = $row['p_qty'] * $row['carriage'];
	/************************* 배송비 관련 ***********************/
			
	$data['sum'] = $row['p_qty'] * ($data['price']+$row['op_price']);
	$data['reserve'] = $row['p_qty'] * $row['p_reserve'];
	$data['sale'] = $row['sale_price'];
	$data['p_reserve'] = number_format($data['reserve'],$ckFloatCnt); 
	$data['p_sum'] = number_format($data['sum'],$ckFloatCnt);	

	return $data;
}

function modifyOrder($order_num,$cancel=0) {
	global $mysql;

	$sql = "SELECT code FROM mall_design WHERE mode='B'";
	$tmp_cash = $mysql->get_one($sql);
	$cash = explode("|*|",stripslashes($tmp_cash));
	//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

	$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}'";
	$mysql->query($sql);

	$TCNT = $cks_total = $cks_carr = $tsale = 0;
	$ck_carr_only = '';
	
	while($data = $mysql->fetch_array()){	
		if($cancel==0) {
			if($data['order_status']=='Z' && $data['order_status2']!='A') continue;
			if($data['order_status']=='X' && $data['order_status2']!='A') continue;
		}
		
		if($data['sale_vls']){
			$tmps = explode("|",$data['sale_vls']);
			$MY_SALE = $tmps[0];
			$MY_POINT = $tmps[1];
			$my_carr = $tmps[2];			
		}
		else {
			$MY_SALE = $MY_POINT = 0;
			$my_carr = 'N';
		}
		
		$gData	= getDisplayOrder($data,$MY_SALE,$MY_POINT);		// 디스플레이 정보 가공 후 가져오기

		$cks_total = $cks_total+($gData['sum']);
		$tsale += $gData['sale'];
		if($gData['carr']) {
			if($gData['carr']=='F') { 
				//$cks_carr = 0;
				$my_carr = 'Y';
				$goods_carriage[$data['uid']] = '99999';
			}
			else { 
				$cks_carr += $gData['carr'];
				if(!$ck_carr_only) $ck_carr_only = 'Y';
			}
		}	
		else if($ck_carr_only=='Y') $ck_carr_only = 'N';
		$TCNT++;	
	}	

	if($cash[10] =='1' && $my_carr!='Y' && $ck_carr_only !='Y') { 
		if($cks_total < $cash[11]) $cks_carr += $cash[12];
	} 
	
	$sql = "SELECT address FROM mall_order_info WHERE order_num = '{$order_num}'";
	$addr = stripslashes($mysql->get_one($sql));

	if($cash[13] && trim($cash[14])) {
		$cks_tmps1 = explode("|",$cash[14]);
		$cks_tmps3 = explode("|",$cash[13]);

		for($i=0;$i<count($cks_tmps1);$i++) {	
			$cks_tmps2 = explode(",",$cks_tmps1[$i]);
			for($j=0;$j<count($cks_tmps2);$j++) {		
				if(!$cks_tmps2[$j]) continue;
				if(eregi($cks_tmps2[$j],$addr)) {			
					$cks_carr += $cks_tmps3[$i];
					break;
				}
			}
		}
	}

	$sql = "SELECT id, pay_type, pay_total, carriage, use_reserve, use_cupon, cupon FROM mall_order_info WHERE order_num='{$order_num}'";
	$row = $mysql->one_row($sql);
	
	######################### 쿠폰 유효성 체크 ######################
	if($row['cupon']) {
		$tmp_cupon = explode(",",$row['cupon']);
		$C_PRICE2 = 0;

		for($c=0;$c<count($tmp_cupon);$c++) {
			$sql = "SELECT a.gid, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.pid=b.uid && a.uid='{$tmp_cupon[$c]}'";
			$crow = $mysql->one_row($sql);
			
			$C_PRICE = 0;
			if($crow['gid']) {
				$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$order_num}' && p_number='{$crow['gid']}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D')";
				
				if($mysql->get_one($sql)>0) {
					$sql = "SELECT uid,name,cate,price,event FROM mall_goods WHERE uid='{$crow['gid']}'";
					if(!$row2 = $mysql->one_row($sql)) continue;
					
					if($crow['stype']=='P') {
						$limit = '';
						if($crow['use_type']==0) $limit = "limit 1";			
						$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && p_number='{$crow['gid']}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D'){$limit}";
						$mysql->query2($sql);
						
						$cu_total = 0;
						while($row3 = $mysql->fetch_array('2')){
							$gData	= getDisplayOrder($row3);	
							if($crow['use_type']==0) $cu_total += $gData['oprice'];			
							else $cu_total += ($gData['oprice'] * $row3['p_qty']);
						}	

						$cu_total = numberLimit(($cu_total * $crow['sale'])/100,1);
						$C_PRICE =  round($cu_total,$ckFloatCnt);
					}
					else {
						if($crow['use_type']==0) $C_PRICE = $crow['sale'];
						else {
							$sql = "SELECT SUM(p_qty) FROM mall_order_goods WHERE order_num='{$order_num}' && p_number='{$crow['gid']}'";
							$p_qty = $mysql->get_one($sql);				
							$C_PRICE = $crow['sale'] * $p_qty;			
						}
					}		
				}
			}
			else if($crow['scate']) {
				$tmps = explode("|",$crow['scate']);
				for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
					if(substr($tmps[$i],3,3)=='000') $where[] = "SUBSTRING(p_cate,1,3)='".substr($tmps[$i],0,3)."' ";
					else if(substr($tmps[$i],6,3)=='000') $where[] = "SUBSTRING(p_cate,1,6)='".substr($tmps[$i],0,6)."' ";
					else if(substr($tmps[$i],9,3)=='000') $where[] = "SUBSTRING(p_cate,1,9)='".substr($tmps[$i],0,9)."' ";
					else $where[] = "p_cate='{$tmps[$i]}' ";			
				}	

				$where = join("||",$where);
				$sql = "SELECT p_cate, p_number FROM mall_order_goods WHERE order_num='{$order_num}' && ({$where}) && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D')";
				$mysql->query($sql);

				$cu_total = 0;
				while($crow2 = $mysql->fetch_array()){
					$gData	= getDisplayOrder($crow2);	
					$cu_total += str_replace(",","",$gData['price']);			
				}

				if($cu_total<$crow['lmt']) {
					if($crow['stype']=='P') {						
						$C_PRICE =  round(($cu_total * $crow['sale'])/100,$ckFloatCnt);
					}
					else $C_PRICE = $crow['sale'];
				}
			}	
			else { 
				if(($cks_total+ $cks_carr)>$crow['lmt']) {
					if($crow['stype']=='P') {						
						$C_PRICE =  round((($cks_total+ $cks_carr) * $crow['sale'])/100,$ckFloatCnt);
					}
					else $C_PRICE = $crow['sale'];
				}
			}		
			
			if($crow['lmt'] && $crow['lmt']>$cks_total && $crow['type']!=3) $C_PRICE = 0;
			$C_PRICE2 += $C_PRICE;
		}
		$C_PRICE = $C_PRICE2;
	}

	######################### 쿠폰 유효성 체크 ######################

	$cks_totals = $cks_total + $cks_carr - $row['use_reserve'] - $C_PRICE;
	$cash_dc = 0;
	if($row['pay_type']=='B') {
		$sql	= "SELECT code FROM mall_design WHERE mode='T'";
		$tmps	= $mysql->get_one($sql);
		$tmps = explode("|",$tmps);
		if(!$tmps[4]) $cash_dc = 0;
		else $cash_dc = $tmps[4];
		$cashdc = round(($cks_total * $cash_dc)/100,$ckFloatCnt); 	
		if($ckFloatCnt==0) $cashdc = numberLimit($cashdc,1);
		$cks_totals -= $cashdc;	
		$tsale = $tsale + $cashdc;
	}
	
	if($cks_totals<0) {
		if($row['id'] && $row['id']!='guest' && $row['use_reserve']>0){
			$signdate = date("Y-m-d H:i:s",time());
			$r_reserve = -($cks_totals) + $row['pay_total'];
			if($r_reserve>$row['use_reserve']) $r_reserve = $row['use_reserve'];
			$sql = "UPDATE pboard_member SET reserve = reserve + {$r_reserve} WHERE id = '{$row['id']}'";
			$mysql->query($sql);
			$subject = "취소/반품상품 환불에 따른 사용 적립금 환원";
			
			$sql = "INSERT INTO mall_reserve (id,subject,reserve,order_num,goods_num,status,signdate) VALUES ('{$row['id']}','{$subject}','{$r_reserve}','','{$order_num}','B','{$signdate}')";
			$mysql->query($sql);
			$row['use_reserve'] = $row['use_reserve'] - $r_reserve;
			$add_reserve = ", use_reserve = '{$row['use_reserve']}'";			
		}

		$cancel_carr = $row['carriage'] - $cks_carr;

		$sql = "UPDATE mall_order_info SET sales={$tsale}, carriage = {$cks_carr}, cancel_carriage = cancel_carriage + {$cancel_carr}, use_cupon='{$C_PRICE}' {$add_reserve} WHERE order_num='{$order_num}'";
		$mysql->query($sql);
	}
	else if($row['pay_total'] != $cks_totals) {		
		
		$cancel_total = $row['pay_total'] - $cks_totals;
		$cancel_carr = $row['carriage'] - $cks_carr;

		$sql = "UPDATE mall_order_info SET sales={$tsale}, pay_total = {$cks_totals}, cancel_total = cancel_total + {$cancel_total}, carriage = {$cks_carr}, cancel_carriage = cancel_carriage + {$cancel_carr}, use_cupon='{$C_PRICE}' WHERE order_num='{$order_num}'";
		$mysql->query($sql);
	}	
}

function goodsCuponUse($order_num){
	global $mysql;

	$sql = "SELECT use_cupon, cupon FROM mall_order_info WHERE order_num='{$order_num}'";
	$row = $mysql->one_row($sql);
	
	######################### 쿠폰 유효성 체크 ######################
	if($row['cupon']) {
		$tmp_cupon = explode(",",$row['cupon']);
		$C_PRICE2 = 0;

		for($c=0;$c<count($tmp_cupon);$c++) {
			$sql = "SELECT a.gid, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.pid=b.uid && a.uid='{$tmp_cupon[$c]}'";
			$crow = $mysql->one_row($sql);
			
			$C_PRICE = 0;
			if($crow['gid']) {
				$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$order_num}' && p_number='{$crow['gid']}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D')";
				
				if($mysql->get_one($sql)>0) {
					$sql = "SELECT uid,name,cate,price,event FROM mall_goods WHERE uid='{$crow['gid']}'";
					if(!$row2 = $mysql->one_row($sql)) continue;
					
					if($crow['stype']=='P') {
						$limit = '';
						if($crow['use_type']==0) $limit = "limit 1";			
						$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && p_number='{$crow['gid']}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D'){$limit}";
						$mysql->query2($sql);
						
						$cu_total = 0;
						while($row3 = $mysql->fetch_array('2')){
							$gData	= getDisplayOrder($row3);	
							if($crow['use_type']==0) $cu_total += $gData['oprice'];			
							else $cu_total += ($gData['oprice'] * $row3['p_qty']);
						}	

						$cu_total = numberLimit(($cu_total * $crow['sale'])/100,1);
						$C_PRICE =  round($cu_total,$ckFloatCnt);
					}
					else {
						if($crow['use_type']==0) $C_PRICE = $crow['sale'];
						else {
							$sql = "SELECT SUM(p_qty) FROM mall_order_goods WHERE order_num='{$order_num}' && p_number='{$crow['gid']}'";
							$p_qty = $mysql->get_one($sql);				
							$C_PRICE = $crow['sale'] * $p_qty;			
						}
					}		
				}
			}
			if($crow['lmt'] && $crow['lmt']>$cks_total && $crow['type']!=3) $C_PRICE = 0;
			$C_PRICE2 += $C_PRICE;
		}
		$C_PRICE = $C_PRICE2;
	}

	######################### 쿠폰 유효성 체크 ######################
	return $C_PRICE;

}

$itsMall = "http://itsmall.kr/update/itsmall.php";
$smsErrorCode = array();
$smsErrorCode['10'] = "잘못된 번호";
$smsErrorCode['11'] = "상위 서비스망 스팸 인식됨";
$smsErrorCode['12'] = "상위 서버 오류";
$smsErrorCode['13'] = "잘못된 필드값";
$smsErrorCode['20'] = "등록된 계정이 아니거나 패스워드가 틀림";
$smsErrorCode['21'] = "존재하지 않는 메시지 ID";
$smsErrorCode['30'] = "가능한 전송 잔량이 없음";
$smsErrorCode['40'] = "전송시간 초과";
$smsErrorCode['41'] = "단말기 busy"; 
$smsErrorCode['42'] = "음영지역";
$smsErrorCode['43'] = "단말기 Power off";
$smsErrorCode['44'] = "단말기 메시지 저장갯수 초과";
$smsErrorCode['45'] = "단말기 일시 서비스 정지";
$smsErrorCode['46'] = "기타 단말기 문제";
$smsErrorCode['47'] = "착신거절";
$smsErrorCode['48'] = "Unkown error";
$smsErrorCode['49'] = "Format Error";
$smsErrorCode['50'] = "SMS서비스 불가 단말기";
$smsErrorCode['51'] = "착신측의 호불가 상태";
$smsErrorCode['52'] = "이통사 서버 운영자 삭제";
$smsErrorCode['53'] = "서버 메시지 Que Full"; 
$smsErrorCode['54'] = "SPAM";
$smsErrorCode['55'] = "SPAM, nospam.or.kr 에 등록된 번호"; 
$smsErrorCode['56'] = "전송실패(무선망단)";
$smsErrorCode['57'] = "전송실패(무선망->단말기단)";
$smsErrorCode['58'] = "전송경로 없음";
$smsErrorCode['60'] = "예약취소";
$smsErrorCode['70'] = "허가되지 않은 IP주소";
$smsErrorCode['99'] = "전송대기";


function pmallSmsCnt() {
	global $mysql, $lib_path;
	$sql = "SELECT message1 FROM mall_sms_auto WHERE code='info'";
	$row = $mysql->get_one($sql);

	if($row) {
		$row = explode("|",stripslashes($row));
		$sms_id = $row[0];
		$sms_pw2 = $row[1];
		$sms_pw = previlDecode($row[1]);
		$tel1 = explode("-",$row[2]);

		if(!$sms_id || !$sms_pw) return -1;
		
		require_once("{$lib_path}/coolsms.php");

		// 객체를 생성합니다.
		$sms = new coolsms();

		// 아이디, 비밀번호를 입력합니다.
		$sms->setuser($sms_id, $sms_pw);

		// 서버에 연결합니다.
		if ($sms->connect())
		{
			// 보유 크레딧을 읽어옵니다.
			$result = $sms->remain();
		} else {
			// 오류처리
			echo "서버에 연결할 수 없습니다.";
		}

		// 연결을 끊습니다.
		$sms->disconnect();

		// 결과를 출력합니다.
		if ($result["RESULT-CODE"] == "00")	// RESULT-CODE 가 00이면 성공.
		{
			return $result["CREDITS"];
		} else {
			return 0; 
		}	
	}
	else return -1;
}

function pmallSmsSend($send_list,$callback,$message,$types,$reservdate='',$LMS='N'){
	global $mysql, $lib_path;;
	require_once("{$lib_path}/coolsms.php");
	
	$signdate = time();

	$sql = "SELECT message1 FROM mall_sms_auto WHERE code='info'";
	$row = $mysql->get_one($sql);

	if($row) {
		$row = explode("|",stripslashes($row));
		$userid = $row[0];
		$passwd = previlDecode($row[1]);
	}
	else alert("SMS정보가 설정 되지 않았거나 잘못 되었습니다.","back");
	
	if(!$callback || !$message || !$send_list) alert("발송정보가 부족 합니다.","back");
	
	$sms = new coolsms();
	$sms->setSRK('K0000398308');
	$sms->setuser($userid, $passwd);

	$tmps = explode(",",$send_list);
	$localkey = Array();

	for($i=$i2=0,$cnt=count($tmps);$i<$cnt;$i++) {
		if($tmps[$i]) {
			$localkey[$i2] = $signdate.$i2.trim($tmps[$i]);			
			$message2 = iconv("utf-8","euc-kr",$message);
			if($LMS=='Y') {				
				$subject = hancut($message2,10);
				if (!$sms->addlms(trim($tmps[$i]), $callback, $subject, $message2, "", $reservdate,$localkey[$i2])) {
					alert("오류발생 : ".$sms->lasterror(),"back");
				}
			}
			else {
				if (!$sms->add(trim($tmps[$i]), $callback, $message2, "", $reservdate,$localkey[$i])) {
					alert("오류발생 : ".$sms->lasterror(),"back");
				}
			}
			$i2++;
		}		
	}
	
	switch($types) {
		case "1" :
			if($cnt>1) {
				$send_sms_list = $tmps[0]."외 ".($cnt-1). "건";
			}
			else $send_sms_list = $tmps[0];
		break;
		case "2" : 
			$send_sms_list = "회원등급별발송";
		break;
		case "3" :
			$send_sms_list = "회원전체발송";
		break;
		case "4" : 
			$send_sms_list = "주소록그룹별발송";
		break;
		case "5" :
			$send_sms_list = "주소록전체발송";
		break;
	}
	
	$nsent = 0;
	if ($sms->connect()){
		$nsent = $sms->send();
	} 
	else {
		alert("오류발생 : 서버에 접속할 수 없습니다.");
	}

	if ($sms->errordetected()) {
		$tpye = 2;
		$err_msg = iconv("euc-kr","utf-8",$sms->lasterror());
	}
	else if($reservdate) { 
		$err_msg = $reservdate;
		$type = 3;
	}
	else $type = 1;

	$sms->disconnect();
	
	$localkey = join(",",$localkey);

	$sql = "INSERT INTO mall_sms_list VALUES('','{$type}','{$send_sms_list}','{$message}','0','{$nsent}','{$err_msg}','{$LMS}','{$localkey}','1','{$signdate}')";
	$mysql->query2($sql);	
}

function pmallSmsAutoSend($send_num,$send_type,$code_arr=""){
	global $mysql, $lib_path;;
	require_once("{$lib_path}/coolsms.php");
	
	$signdate = time();

	$sql = "SELECT message1 FROM mall_sms_auto WHERE code='info'";
	$row = $mysql->get_one($sql);
	
	if($row) {
		$row = explode("|",stripslashes($row));
		$userid = $row[0];
		$passwd = previlDecode($row[1]);
		$callback = str_replace("-","",$row[2]);
		$admin_num = str_replace("-","",$row[3]);
	}
	else return false;

	$cnts = pmallSmsCnt();
	if($cnts<1) return false;
	
	if(!$send_num || !$send_type || !$userid || !$passwd) return false;

	$send_num = str_replace("-","",trim($send_num));
	$send_num = str_replace(" ","",trim($send_num));
	
	$sql = "SELECT * FROM mall_sms_auto WHERE code='{$send_type}'";
	$row = $mysql->one_row($sql);
	
	$sql = "SELECT code FROM mall_design WHERE mode='A'";
	$tmp_basic = $mysql->get_one($sql);
	$basic = explode("|*|",stripslashes($tmp_basic));

	if($row['chk_message1']==1) {
		$sms = new coolsms();
		$sms->setSRK('K0000398308');
		$sms->setuser($userid, $passwd);

		$localkey = $signdate."1".getCode(1).$send_num;
		$message = $row['message1'];
		$message = str_replace("{shopName}",$basic[1],$message);
		$message = str_replace("{name}",$code_arr['name'],$message);
		$message = str_replace("{number}",$code_arr['number'],$message);
		$message = str_replace("{price}",$code_arr['price'],$message);
		$message = str_replace("{goodsName}",$code_arr['goodsName'],$message);
		$message = str_replace("{account}",$code_arr['account'],$message);
		$message = str_replace("{password}",$code_arr['password'],$message);
		$message = str_replace("{deli_comp}",$code_arr['deli_comp'],$message);
		$message = str_replace("{deli_no}",$code_arr['deli_no'],$message);

		$message2 = iconv("utf-8","euc-kr",$message);
	
		if(strlen($message2)>80) {
			$subject = hancut($message2,10);
			if (!$sms->addlms($send_num, $callback, $subject, $message2, "", "",$localkey)) {
				return false;
			}
		}
		else {
			if (!$sms->add($send_num, $callback, $message2, "", "", $localkey)) {
				return false;
			}
		}		

		if ($sms->connect()) $sms->send();
		else return false;

		if ($sms->errordetected()) {
			$type = 2;
			$err_msg = iconv("euc-kr","utf-8",$sms->lasterror());
		}
		else $type = 1;

		$sql = "INSERT INTO mall_sms_list VALUES('','{$type}','{$send_num}','{$message}','0','1','{$err_msg}','N','{$localkey}','1','{$signdate}')";
		$mysql->query2($sql);	

		$sms->disconnect();
	}

	if($row['chk_message2']==1 && $admin_num) {
		$sms = new coolsms();
		$sms->setSRK('K0000398308');
		$sms->setuser($userid, $passwd);

		$localkey = $signdate."2".getCode(1).$admin_num;
		$message = $row['message2'];
		$message = str_replace("{shopName}",$basic[1],$message);
		$message = str_replace("{name}",$code_arr['name'],$message);
		$message = str_replace("{number}",$code_arr['number'],$message);
		$message = str_replace("{price}",$code_arr['price'],$message);
		$message = str_replace("{goodsName}",$code_arr['goodsName'],$message);
		$message = str_replace("{deli_comp}",$code_arr['deli_comp'],$message);
		$message = str_replace("{deli_no}",$code_arr['deli_no'],$message);

		$message2 = iconv("utf-8","euc-kr",$message);
	
		if(strlen($message2)>80) {
			$subject = hancut($message2,10);
			if (!$sms->addlms($admin_num, $callback, $subject, $message2, "", "",$localkey)) {
				return false;
			}
		}
		else {
			if (!$sms->add($admin_num, $callback, $message2, "", "", $localkey)) {
				return false;
			}
		}	
		
		if ($sms->connect()) $sms->send();
		else return false;

		if ($sms->errordetected()) {
			$type = 2;
			$err_msg = iconv("euc-kr","utf-8",$sms->lasterror());
		}
		else $type = 1;

		$sql = "INSERT INTO mall_sms_list VALUES('','{$type}','{$admin_num}','{$message}','0','1','{$err_msg}','N','{$localkey}','1','{$signdate}')";
		$mysql->query2($sql);	

		$sms->disconnect();
	}

	################ SMS 발송결과 확인 ################
	$sql = "SELECT uid,status,err_msg FROM mall_sms_list WHERE result='1' && status!='2' ORDER BY uid DESC LIMIT 1";
	$mysql->query2($sql);
	while($row = $mysql->fetch_array(2)){
		if(($row['status']==3 && $row['err_msg']>date("Y-m-d H:i:s")) || $row['status']==1) {
			pmallSmsResult($row['uid']);
		}
	}

	return true;
}


function pmallSmsResult($uid){
	global $mysql, $lib_path, $smsErrorCode;
	require_once("{$lib_path}/coolsms.php");
	
	$sql = "SELECT message1 FROM mall_sms_auto WHERE code='info'";
	$row = $mysql->get_one($sql);

	if($row) {
		$row = explode("|",stripslashes($row));
		$userid = $row[0];
		$passwd = previlDecode($row[1]);
	}
	else return;

	$sql = "SELECT * FROM mall_sms_list WHERE uid='{$uid}' && result ='1'";
	if(!$row = $mysql->one_row($sql)) return;
	if($row['status']==2) return;
	$err_msg = $row['err_msg'];

	if($row['localkey']) {		
		$sms = new coolsms();
		$sms->setuser($userid, $passwd);
		$sms->connect();

		$localkey = explode(",",$row['localkey']);
		
		$succ_cnt = 0;
		$ck_ok = 1;
		for($i=0,$cnt=count($localkey);$i<$cnt;$i++) {
			$tmps2 = '';
			$result = $sms->rcheck($localkey[$i]);			
			
			if($result["STATUS"]==0 || ($result["STATUS"]==1 && $result["RESULT-CODE"]=='00') || $result["STATUS"]==9) return;
			if($result["STATUS"]==2 || $result["STATUS"]==5) {
				if($result["RESULT-CODE"]=='00') {
					$succ_cnt++;
				}
				else {
					$tmps = str_replace($row['signdate'].$i,"",$localkey[$i]);
					$tmps2 = ($smsErrorCode[$result["RESULT-CODE"]]) ? $smsErrorCode[$result["RESULT-CODE"]] :  iconv("euc-kr","utf-8",$result["RESULT-MESSAGE"]);
					$err_msg .= "<br /> {$tmps} : {$tmps2}";
				}
				$ck_ok = 2;
			}
			else {				
				$tmps = str_replace($row['signdate'].$i,"",$localkey[$i]);
				$tmps2 = ($smsErrorCode[$result["RESULT-CODE"]]) ? $smsErrorCode[$result["RESULT-CODE"]] :  iconv("euc-kr","utf-8",$result["RESULT-MESSAGE"]);
				$err_msg .= "<br /> {$tmps} : {$tmps2}";
			}			
		}
		$sms->disconnect();

		$sql = "UPDATE mall_sms_list SET succ_cnt ='{$succ_cnt}', err_msg = '{$err_msg}', result ='{$ck_ok}' WHERE uid='{$uid}' && result = '1'";
		$mysql->query3($sql);
	}
}

function pmallMailSend($email, $subject, $mail_form){
	global $mysql, $lib_path, $socket_mail, $basic;

	if(!$email || !$mail_form) return false;
	
	$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
	$sender = '=?UTF-8?B?'.base64_encode($basic[1]).'?=';

	if($socket_mail=='Y') {				
		$from = "{$basic[1]}<{$basic[10]}>\nContent-Type:text/html"; 
		require_once "{$lib_path}/class.Smtp.php";
		$mail = new classSmtp("self"); 
		$mail->send($email, $from, $subject, $mail_form); 
		//$mail->pt_error();		
	}
	else {
		/* HTML 메일을 보내려면, Content-type 헤더를 설정해야 합니다. */
		$headers = "From: {$sender} <{$basic[10]}>\n";
		$headers .= "MIME-Version: 1.0\n";
		$message = chunk_split(base64_encode($mail_form));
		$boundary = "b".md5(uniqid(time()));
		//$headers .= "Content-Type: multipart/mixed; boundary = {$boundary}\n\nThis is a MIME encoded message.\n\n--{$boundary}";
		//$headers .= "\n";
		$headers .= "Content-type: text/html; charset=utf-8\n";
		$headers .= "Content-Transfer-Encoding: base64\n\n{$message}\n\n";
		//$headers .= "--{$boundary}";
		$headers .= "--\n";
		
		mail($email, $subject, "", $headers);
	}		
	return true;
}

} // End of if(!isset($__PREVIL_LIB2__)) 
?>