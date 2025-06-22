<?
$cate	= isset($_GET['cate']) ? $_GET['cate'] : $_POST['cate'];
$uid	= $_GET['uid'];
$tmpid	= getCartId($my_id);

if($my_id) $CKLOGIN = "Y";
else $CKLOGIN = "N";

$sql = "DELETE FROM mall_cart WHERE p_direct='Y'&& tempid='{$tempid}'";
$mysql->query($sql);

$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$tempid}'";
if($mysql->get_one($sql)>0) $ckCart = 'Y';
else $ckCart = 'N';

$sql = "SELECT * FROM mall_goods WHERE uid='{$uid}'";
if(!$data = $mysql->one_row($sql)) {
	if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('해당상품이 삭제되었거나 존재하지 않습니다.',"{$Main}");
	else alert('해당상품이 삭제되었거나 존재하지 않습니다.','back');
}

if($data['s_qty']==3 || $data['type']!='A') {
	if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('해당상품이 삭제되었거나 존재하지 않습니다.',"{$Main}");
	else alert('해당상품이 삭제되었거나 존재하지 않습니다.','back');
}

$tpl->define("main","{$skin}/view.html");
$tpl->scan_area("main");

if(!$cate) $cate = $data['cate'];
$gData	= getDisplay($data,'image1');		// 디스플레이 정보 가공 후 가져오기
$GOODS_IMG	= "../".$gData['image'];
$GOODS_NAME	= $gData['name'];
$GOODS_PRICE= $gData['price'];
$GOODS_PRICE= str_replace("원","",$GOODS_PRICE);

if(is_int($gData['price'])) $GOODS_ORIG_PRICE= number_format($data['price'],$ckFloatCnt);
else $GOODS_ORIG_PRICE = str_replace("원","",$gData['price']);

$GOODS_ICON	= $gData['icon'];
$GOODS_COMP = $gData['comp'];
$GOODS_RESERVE  = $gData['reserve'];
$GOODS_ORESERVE = str_replace(",","",$GOODS_RESERVE);
$orig_cate = $data['cate'];
$GOODS_VENDOR = $data['vendor'];
$BRAND = $data['brand'];

$GOODS_MODEL	= stripslashes($data['model']);
$DEF_QTY		= stripslashes($data['def_qty']);
$GOODS_MADE		= stripslashes($data['made']);
$GOODS_UNIT		= stripslashes($data['unit']);
$P_PRICE		= $data['price'];
$P_PRICE2		= str_replace(array("원",","),"",$GOODS_PRICE);

$SHARE_GOODS = "{$basic[1]} [{$GOODS_NAME}] ";
$SHARE_GOODS = urlencode($SHARE_GOODS);
$SHARE_URL	= "http://".$_SERVER["SERVER_NAME"].str_replace(array("&uid=","&cate="),"/",$_SERVER['REQUEST_URI']);
$SHARE_TAG = substr($data['tag'],1,-1);
$SHARE_TAG = urlencode($SHARE_TAG);

if($data['s_qty']==4) $S_QTY = $data['qty'];
else $S_QTY = 0;

if($DEF_QTY==0) $DEF_QTY2=1;
else $DEF_QTY2 = $DEF_QTY;

if(strlen($data['model'])>0) $tpl->parse("is_model");

if($gData['my_sale']>0) {
	$TTL = $gData['my_type1'];
	$MY_SALE = $gData['my_sale'];
	$tpl->parse("is_my_sale");
}

if($cash[6]==1) {
	if($gData['my_point']>0) {
		$TTL = $gData['my_type2'];
		$MY_POINT = $gData['my_point'];
		$tpl->parse("is_my_point");
	}
	if($GOODS_ORESERVE>0) $tpl->parse("is_reserve");
}

if($data['consumer_price'] && $data['consumer_price'] >0){
	$GOODS_OPRICE = number_format($data['consumer_price'],$ckFloatCnt);
    $tpl->parse('is_oprice'); 
}

if($data['brand']>0) {
	$sql = "SELECT name,uid FROM mall_brand WHERE uid='{$data['brand']}'";
	$tmps = $mysql->one_row($sql);
	$GOODS_BRAND = stripslashes($tmps['name']);
	$PLUS	= $tmps['uid'];
	$tpl->parse("is_brand");
}

if($data['event']>0) {
	$sql = "SELECT name FROM mall_event WHERE uid='{$data['event']}'";
	$GOODS_EVENT = stripslashes($mysql->get_one($sql));
	$PLUS = $data['event'];
	$GOODS_ORIG_PRICE= number_format($data['price'],$ckFloatCnt);
	$tpl->parse("is_event");
}

if($gData['cp_price']) {
	$sql = "SELECT * FROM mall_cupon_manager WHERE type='3' && INSTR(sgoods,'|{$uid}|') ORDER BY uid DESC LIMIT 1";
	if($cupon = $mysql->one_row($sql)){
		$CUID = $cupon['uid'];
		$CP_TYPE = $cupon['use_type'];
		$CP_SALE = $cupon['sale'];
		$CP_SALE_TYPE = $cupon['stype'];
		$GOODS_CPRICE = $gData['cp_price'];
		$P_PRICE3 = str_replace(array("원",","),"",$GOODS_CPRICE);
		$tpl->parse("is_cupon");
	}
}

if($GOODS_COMP) $tpl->parse("is_comp");
if($GOODS_MADE) $tpl->parse("is_made");

$sql = "SELECT uid FROM mall_goods WHERE cate='{$cate}' && number > '{$data['number']}' ORDER BY number ASC LIMIT 1";
if($tmp_uid = $mysql->get_one($sql)){
	$NEXT_GOODS = "{$Main}?channel=view&uid={$tmp_uid}&cate={$cate}\"";	
}
else $NEXT_GOODS = "#\" onclick=\"return false;";

$sql = "SELECT uid FROM mall_goods WHERE cate='{$cate}' && number < '{$data['number']}' ORDER BY number DESC LIMIT 1";
if($tmp_uid = $mysql->get_one($sql)){
	$PREV_GOODS = "{$Main}?channel=view&uid={$tmp_uid}&cate={$cate}\"";	
}
else $PREV_GOODS = "#\" onclick=\"return false;";

/************************* 배송비 관련 ***********************/
$P_CARR = 0;
$carriage = explode("|",$data['carriage']);
if($carriage[0]==1) {
	$tpl->parse("is_carr_free");
}
else if($carriage[0]==3) { //별도 책정일때
	$GOODS_CARR = number_format($carriage[1],$ckFloatCnt);	
	$P_CARR = $carriage[1];
	$tpl->parse("is_carr");
}	
/************************* 배송비 관련 ***********************/

/**************************** GOODS OPTIONS **************************/
$tmps = $OP_TITLE = $OP_VALUES = "";
$op_img_arr = Array('','색상','사이즈','용량','옵션','선택사항','추가구매');

$sql = "SELECT option1 FROM mall_goods_option WHERE guid='{$uid}' GROUP BY option1 ORDER BY o_num ASC";
$mysql->query($sql);
	
$option_arr = Array();
while($row2 = $mysql->fetch_array()){
	$option_arr[] = $row2['option1'];
}

for($j=$i=0,$cnt=count($option_arr);$j<$cnt;$j++) {

	$sql = "SELECT * FROM mall_goods_option WHERE guid='{$uid}' && option1='{$option_arr[$j]}' ORDER BY o_num ASC";
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

		if($tmp_op['qty']==0) $OP_VALUES .= "<option value='{$tmp_op['uid']}' class='disabled'>{$tmp_op['option2']}{$tmps} [품절]</option>\n";
		else $OP_VALUES .= "<option value='{$tmp_op['uid']}'>{$tmp_op['option2']}{$tmps2}</option>\n";		
	}
	$tpl->parse("is_option");
	$tpl->parse("is_op_img","2");
	$tpl->parse("is_op_text","2");	
}
$op_cnt = $i;
/**************************** GOODS OPTIONS **************************/


/**************************** GOODS LIKE GOODS **************************/
$CNT_RELATE = 0;
if($data['op_goods_type']=='A') {	
	if($data['op_goods']) {
		$op_goods = explode(',',$data['op_goods']);	
		$op_goods = array_reverse($op_goods);
		for($i=0,$cnt=count($op_goods);$i<=$cnt;$i++){
			if($op_goods[$i]) {
				$sql = "SELECT uid,cate,number,name,price,price_ment,comp,image3,icon,event,c_cnt FROM mall_goods WHERE uid='{$op_goods[$i]}' && s_qty !='3' && type='A'";
				if($tmps = $mysql->one_row($sql)){
					$gData	= getDisplay($tmps,'image3');		// 디스플레이 정보 가공 후 가져오기
					$LINK	= $gData['link'];
					$IMAGE	= "../".$gData['image'];
					$NAME	= $gData['name'];
					$COMP	= $gData['comp'];
					$PRICE	= $gData['price'];
					$ICON	= $gData['icon'];
					$DRAGD	= $gData['dragd'];	
					$CCNT	= $gData['c_cnt'];
					$QLINK  = $tmps['uid'];
					$CATE	= $tmps['cate'];

					if($CP_PRICE>0) {
						$tpl->parse("is_coupon");
						$PRICE = $CP_PRICE;
					}

					$PRICE2 = str_replace("원","",$PRICE);
					
					$tpl->parse("loop_sicon");
					
					$tpl->parse('loop_relate');
					$tpl->parse("is_coupon","2");
					$CNT_RELATE++;
				} 
			}
		}	
		$tpl->parse('is_relate');
	}
}
else {
	$op_goods = explode("|",$data['op_goods_type']);
	
	if($op_goods[1]==1) $op_where = " && cate='{$data['cate']}'";
	if($op_goods[2]==1) $op_order = " ORDER BY o_cnt DESC ";
	else if($op_goods[2]==2) $op_order = " ORDER BY c_cnt DESC ";
	else if($op_goods[2]==3) $op_order = " ORDER BY rand() ";
	if(!$op_goods[3]) $op_goods[3] = 4;

	$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,image3,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && type='A' && uid!='{$uid}' {$op_where} {$op_order} LIMIT {$op_goods[3]}";
	$mysql->query($sql);
	
	$i = 1;
	while($tmps = $mysql->fetch_array()){
		$gData	= getDisplay($tmps,'image3');		// 디스플레이 정보 가공 후 가져오기
		$LINK	= $gData['link'];
		$IMAGE	= "../".$gData['image'];
		$NAME	= $gData['name'];
		$COMP	= $gData['comp'];
		$PRICE	= $gData['price'];
		$CPRICE	= $gData['cprice'];
		$CP_PRICE	= $gData['cp_price'];
		$ICON	= $gData['icon'];
		$DRAGD	= $gData['dragd'];	
		$CCNT	= $gData['c_cnt'];
		$QLINK  = $tmps['uid'];
		$CATE	= $tmps['cate'];
		
		if($CP_PRICE>0) {
			$tpl->parse("is_coupon");
			$PRICE = $CP_PRICE;
		}

		$PRICE2 = str_replace("원","",$PRICE);

		$tpl->parse("loop_sicon");	
		
		$tpl->parse('loop_relate');
		$tpl->parse("is_coupon","2");
		$CNT_RELATE++;
		$i++;
	} 
	if($i>1) $tpl->parse('is_relate');
}
/**************************** GOODS LIKE GOODS **************************/

/**************************** OTHER IMAGE **************************/
$tmp_dir = str_replace("../../","../",$data['other_image']);
$OCNT = 1;
if(is_dir($tmp_dir)) { 	
	$handle	= @opendir($tmp_dir);
	$ot_img1 = array();
	$ot_img2 = array();
	while ($file = @readdir($handle)) {
		if($file != '.' && $file != '..' && is_file("{$tmp_dir}/{$file}") && !eregi("_Pthum",$file)) {			
			$lenStr= strlen($file);                         // 파일 길이 
			$dotPos = strrpos($file, ".");              // 맨 마지막 도트의 위치 
			$only_name = substr($file, 0, $dotPos);
			$ext = getExtension($file);
			$ot_img2[] = "{$tmp_dir}/".urlencode($file);
		}		
	}
	@closedir($handle);		

	sort($ot_img2);

	for($i=0,$cnt=count($ot_img2);$i<$cnt;$i++) {
		$OT_IMG2 = $ot_img2[$i];
		$tpl->parse("loop_ot_img");		
		$OCNT++;
	}	
    if($OCNT>1) $tpl->parse("is_ot_img");
	unset($ot_img2);
}	
/**************************** OTHER IMAGE **************************/

/**************************** 배송정보 관련 **************************/
$sql = "SELECT code FROM mall_design WHERE mode='D'";
$tmps = $mysql->get_one($sql);
$INFO = stripslashes($tmps);
$INFO = str_replace("../../image","../image",$INFO);
/**************************** 배송정보 관련 **************************/

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

/**************************** 필수정보   **************************/
$ck_info = 0;
$sql = "SELECT * FROM mall_goods_info WHERE guid='{$uid}' ORDER BY o_num ASC";
$mysql->query($sql);
while($row2 = $mysql->fetch_array()){
	$opName = $row2['name1'];
	$opContent = $row2['content1'];
		
	$tpl->parse("loop_info");
	
	if($row2['name2']!='x') {
		$opName = $row2['name2'];
		$opContent = $row2['content2'];
		$tpl->parse("loop_info");
	}			
	$ck_info = 1;
}
if($ck_info==1) $tpl->parse("is_goods_info");	
/**************************** 필수정보  **************************/

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


/**************************** 상품평 **************************/
$sql = "SELECT count(*) as cnt, SUM(point) as sum FROM mall_goods_point WHERE uid>0 && number='{$uid}'";
$tmps = $mysql->one_row($sql);

$CNT_AFTER = $tmps['cnt'];
if($CNT_AFTER==0) $SUM_AFTER = 0;
else $SUM_AFTER = round(($tmps['sum']*2)/$tmps['cnt'],1);
$SUM_AFTER = sprintf("%01.1f", $SUM_AFTER);
$PER_AFTER = ($SUM_AFTER*10);
/**************************** 상품평 **************************/

/**************************** 상품Q&A **************************/
$sql = "SELECT count(*) FROM mall_goods_qna WHERE uid>0 && number='{$uid}'";
$CNT_QNA = $mysql->get_one($sql);
/**************************** 상품Q&A **************************/

/**************************** 조회수 증가 & 오늘본 상품 추가 **************************/
@session_start();
$tmp=explode(',',$_SESSION['goods_view']);
if(!in_array("{$orig_cate}:{$uid}",$tmp)){      
	$sql = "UPDATE mall_goods SET v_cnt = v_cnt + 1 WHERE uid='{$uid}'";	
	$mysql->query($sql);
     
	$dates = date("Ymd");
	$sql = "SELECT count(*) FROM mall_goods_view WHERE cno='{$orig_cate}{$uid}' && date='{$dates}'";
	$cnt = $mysql->get_one($sql);
	if($cnt<1) {
		$sql = "INSERT INTO mall_goods_view VALUES('','{$orig_cate}{$uid}','{$dates}',1,'{$BRAND}')";
	}
	else {
		$sql = "UPDATE mall_goods_view SET view = view +1 WHERE cno='{$orig_cate}{$uid}' && date='{$dates}'";
	}
	$mysql->query($sql);	

	array_push($tmp, "{$orig_cate}:{$uid}");
	$goods_view = implode(',',$tmp);	  
	session_register("goods_view");
    $_SESSION['goods_view'] = $goods_view;		
}

$tmp=explode(',',$_SESSION['today_view']);
if(!in_array("{$orig_cate}:{$uid}",$tmp)){      
	array_push($tmp, "{$orig_cate}:{$uid}");
	$today_view = implode(',',$tmp);	  
	session_register("today_view");
    $_SESSION['today_view'] = $today_view;		
}
/**************************** 조회수 증가 & 오늘본 상품 추가 **************************/

/**************************** 입점사정보 **************************/
if($GOODS_VENDOR) {
	$sql = "SELECT * FROM mall_vendor WHERE id='{$GOODS_VENDOR}'";
	$row = $mysql->one_row($sql);

	if($row['m_name']) $M_NAME = stripslashes($row['m_name']);
	else $M_NAME = stripslashes($row['comp']);
	
	$M_COMP = stripslashes($row['comp']);
	$M_INFO = stripslashes(nl2br($row['m_info']));
	$M_TEL = stripslashes($row['m_tel']);
	$M_FAX = stripslashes($row['m_fax']);
	$M_EMAIL = stripslashes($row['m_email']);
	$M_ADDR = stripslashes($row['comp_addr']);
	$L_SIZE = $VENDOR_IMG_DEFINE['main_logo'];

	if($row['carr_info']) $INFO = stripslashes($row['carr_info']);

	if($row['m_image']) {
		$M_LOGO = "image/vendor/{$row['m_image']}";	
	}
	else $M_LOGO = "image/no_goods/no_logo.gif";
	
	$tpl->parse("is_vendor");
}
/**************************** 입점사정보 **************************/

/********* 배송안내 기본 이미지 문자로 치환 ********************/
$INFO = str_replace(array("<IMG src=\"../image/up_img/etc/ttt_info1.gif\" border=0>","<img border=\"0\" src=\"../image/up_img/etc/ttt_info1.gif\">"),"<b style='color:#222'>01 배송안내</b>",$INFO);
$INFO = str_replace(array("<IMG src=\"../image/up_img/etc/ttt_info2.gif\" border=0>","<img border=\"0\" src=\"../image/up_img/etc/ttt_info2.gif\">"),"<b style='color:#222'>02 교환/반품안내</b>",$INFO);
$INFO = str_replace(array("<IMG src=\"../image/up_img/etc/ttt_info3.gif\" border=0>","<img border=\"0\" src=\"../image/up_img/etc/ttt_info3.gif\">"),"<b style='color:#222'>03 환불안내</b>",$INFO);
/********* 배송안내 기본 이미지 문자로 치환 ********************/


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>