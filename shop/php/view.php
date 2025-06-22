<?
$uid	= $_GET['uid'];
$FOCUS	= isset($_GET['focus']) ? "window.onload = function() { document.getElementById('{$_GET['focus']}').focus(); }" : "";

// 임시장바구니번호 존재확인
if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
	if($my_id) $tempid = $my_id;
	else $tempid = md5(uniqid(rand()));
	SetCookie("tempid",$tempid,0,"/");
} 
else $tempid = $_COOKIE['tempid'];

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

$gData	= getDisplay($data,'image2');		// 디스플레이 정보 가공 후 가져오기
$GOODS_IMG	= $gData['image'];
$GOODS_IMG_WIDTH = $IMG_DEFINE['img2'];
$GOODS_OIMG = $gData['image'];
$GOODS_NAME	= $gData['name'];
$GOODS_PRICE= $gData['price'];
$GOODS_PRICE2= str_replace("원","",$GOODS_PRICE);

if(is_int($gData['price'])) $GOODS_ORIG_PRICE= number_format($data['price'],$ckFloatCnt)."원";
else $GOODS_ORIG_PRICE = $gData['price'];

$GOODS_ICON	= $gData['icon'];
$GOODS_COMP = $gData['comp'];
$GOODS_RESERVE  = $gData['reserve'];
$GOODS_ORESERVE = str_replace(",","",$GOODS_RESERVE);
$orig_cate = $data['cate'];
$BRAND = $data['brand'];

$GOODS_MODEL	= stripslashes($data['model']);
$DEF_QTY		= stripslashes($data['def_qty']);
$GOODS_MADE		= stripslashes($data['made']);
$GOODS_EXPL		= stripslashes($data['explan']);
$GOODS_EXPL = str_replace("../../image","image",$GOODS_EXPL);
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

if(substr($cate,0,3)=='999') {
	$SER_TIME = time();

	$tpl->define('main',"{$skin}/view_cooperate.html");
	$tpl->scan_area('main');

	$today	= date("Y-m-d H:i");	
	$COOP_TIME = strtotime($data['coop_edate']);
	$tmps = explode(":",countDown($COOP_TIME));
	$TM1 = $tmps[0];
	$TM2 = $tmps[1];
	$TM3 = $tmps[2];
	$TM4 = $tmps[3];

	if($data['coop_sdate']>$today) { //공구 준비중"
		$TYPE = 3;
		$COOP_TIME = strtotime($data['coop_sdate']);
	}
	else if($data['coop_edate']<$today) {  //공구 마감
		$TYPE = 2; 
	}
	else {		
		$TYPE = '';
	}
	
	$data['coop_sdate'] = strtotime($data['coop_sdate']);
	$S_YYYY	= date("Y",$data['coop_sdate']);
	$S_YY	= date("y",$data['coop_sdate']);
	$S_MM	= date("m",$data['coop_sdate']);
	$S_DD	= date("d",$data['coop_sdate']);
	$S_HH	= date("H",$data['coop_sdate']);	 
	$S_II	= date("i",$data['coop_sdate']);	 

	$data['coop_edate'] = strtotime($data['coop_edate']);
	$E_YYYY	= date("Y",$data['coop_edate']);
	$E_YY	= date("y",$data['coop_edate']);
	$E_MM	= date("m",$data['coop_edate']);
	$E_DD	= date("d",$data['coop_edate']);
	$E_HH	= date("H",$data['coop_edate']);	 
	$E_II	= date("i",$data['coop_edate']);	 

	$COOP_CNT = $participation = $data['coop_cnt'];
	
	$sql = "SELECT * FROM mall_goods_cooper WHERE guid='{$uid}' ORDER BY qty ASC";
	$mysql->query($sql);
	
	$coop_arr = Array();
	while($data2=$mysql->fetch_array()) {
		if($data2['qty'] && $data2['price']) {
			$coop_arr[] = Array($data2['qty'],$data2['price']);
			$COOP_LOOP_QTY = number_format($data2['qty']);
			$COOP_LOOP_PRICE = number_format($data2['price'],$ckFloatCnt);
			$tpl->parse("loop_coop");
		}
	}
	
	$COOP_PAY = $data['coop_pay'];
	$COOP_CNT = $participation;
	$COOP_PRICE1	= str_replace("원","",$GOODS_PRICE);		
	$COOP_PER = $COOP_PER2 = 0;
	$COOP_ALIGN = "left";
	$cnt	=count($coop_arr);
	$START_CNT	= $coop_arr[0][0];		
	$END_CNT	= $coop_arr[$cnt-1][0];
	
	if($data['coop_pay']=='Y') {
		$COOP_PRICE = number_format($coop_arr[0][1],$ckFloatCnt);
		$tpl->parse("is_price1","1");
		if($participation>0) {
			$PER = round(100*$participation/$START_CNT);				
			$COOP_PER = $COOP_PER2 =round(40*$PER/100);	
			if($COOP_PER2>=100) $COOP_PER2 = 100;
			if($COOP_PER>=100) {
				$COOP_ALIGN = "right";
				$COOP_PER = 0;
			}
			if($participation>=$START_CNT) $tpl->parse("is_come","1");
		}					
	}
	else if($coop_arr[0][0]>$participation) {
		$COOP_PRICE = $COOP_PRICE1;
		$PER = round(100*$participation/$START_CNT);				
		$COOP_PER = $COOP_PER2 =round(40*$PER/100);			
	}
	else {	
		if($cnt==1) {
			$COOP_PRICE = number_format($coop_arr[0][1],$ckFloatCnt);
			$i=0;
		}
		else {
			if($coop_arr[$cnt-1][0]<$participation) {
				$COOP_PRICE = number_format($coop_arr[$cnt-1][1],$ckFloatCnt);						
			}
			else {
				for($i=0;$i<$cnt;$i++) {												
					if($coop_arr[$i][0]>=$participation) {
						$COOP_PRICE = number_format($coop_arr[$i][1],$ckFloatCnt);						
						break;
					}	
				}
			}
		}
		
		$i++;
		if($i==$cnt) {
			$tpl->parse("is_cooper_ok");
		}		
		if($END_CNT<=$participation) $COOP_PER = 0;
		else {
			$PER = round(100*$participation/$END_CNT);				
			$COOP_PER = 30 - (round(40*$PER/100));			
		}
		$COOP_PER2 = 100 - $COOP_PER;
		$COOP_ALIGN = "right";
		$tpl->parse("is_come","1");
	}
	if($COOP_PER==0) $COOP_PER = 1;

	$now_price = str_replace(",","",$COOP_PRICE);
	$start_price = str_replace(",","",$COOP_PRICE1);
	$COOP_SALE = 100 - round((100*$now_price)/$start_price);
	$COOP_PRICE .= "원";

	if($GOODS_ORESERVE>0) {		
		/************************* 적립금 관련 ***********************/		
		$reserve = explode("|",$data['reserve']);
		if($reserve[0] =='2') { //쇼핑몰 정책일때
			if($cash[6] =='1') { 
				$tmp_reserve = round(($now_price * $cash[8])/100,$ckFloatCnt);
			} 
		} 
		else if($reserve[0] =='3') { //별도 책정일때
			$tmp_reserve = round(($now_price * $reserve[1])/100,$ckFloatCnt);
		}				
		$GOODS_RESERVE = number_format($tmp_reserve,$ckFloatCnt);
		/************************* 적립금 관련 ***********************/
	}
	if($S_QTY>0) $tpl->parse("is_qty");
	if($DEF_QTY>0) $tpl->parse("is_limit_qty");	
	if($TYPE) $tpl->parse("is_limit_{$TYPE}");
	else $tpl->parse("is_limit_1");
}
else {
	$tpl->define('main',"{$skin}/view.html");
	$tpl->scan_area('main');
}

/***********************  BANNER  ********************************/
$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '9' && status='1' ORDER BY rank ASC";
$mysql->query($sql);
while($row_ban = $mysql->fetch_array()){
	if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
	if($row_ban['link']) {
		$BLINK = str_replace("&","&amp;",$row_ban['link']);
		if($row_ban['target']=='2') $BTARGET = "target='_blank'";
		else $BTARGET = "";
	}
	else $BLINK = "#\" onclick=\"return false;";

	$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner3'],stripslashes($row_ban['name']));
	$tpl->parse("loop_banner");	
}
unset($row_ban,$BANNER,$BLINK,$BTARGET);
/***********************  BANNER  ********************************/

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
	$GOODS_OPRICE = number_format($data['consumer_price'],$ckFloatCnt)."원";
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
	$GOODS_ORIG_PRICE= number_format($data['price'],$ckFloatCnt)."원";
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
	$GOODS_CARR = number_format($carriage[1],$ckFloatCnt)."원";	
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
					$IMAGE	= $gData['image'];
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
					
					$tpl->parse('loop_relate');
					$tpl->parse("is_coupon","2");
					$CNT_RELATE++;
				} 
			}
		}	
	}
	else $tpl->parse('no_relate');
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
		$IMAGE	= $gData['image'];
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
		
		$tpl->parse('loop_relate');
		$tpl->parse("is_coupon","2");
		$CNT_RELATE++;
		$i++;
	} 
	if($i==1) $tpl->parse('no_relate');
}
/**************************** GOODS LIKE GOODS **************************/

/**************************** OTHER IMAGE **************************/
$tmp_dir = str_replace("../../","",$data['other_image']);
$OCNT = 0;
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

			if(is_file("{$tmp_dir}/{$only_name}_Pthum2.{$ext}")) $ot_img1[] = "{$tmp_dir}/".urlencode($only_name)."_Pthum2.{$ext}";
			else $ot_img1[] = "{$tmp_dir}/".urlencode($file);

			$ot_img2[] = "{$tmp_dir}/".urlencode($file);
		}		
	}
	@closedir($handle);		

	sort($ot_img1);
	sort($ot_img2);

	for($i=0,$cnt=count($ot_img1);$i<$cnt;$i++) {
		$OT_IMG1 = $ot_img1[$i];
		$OT_IMG2 = $ot_img2[$i];
		$tpl->parse("loop_ot_img");		
		$OCNT++;
	}
	
	if($OCNT>$SKIN_DEFINE['other_vcnt']) $OWIDTH = ($IMG_DEFINE['other_s'] + 10) * $OCNT;
	else $OWIDTH = ($IMG_DEFINE['other_s'] + 10) * $SKIN_DEFINE['other_vcnt'];

	for($i=$OCNT;$i<$SKIN_DEFINE['other_vcnt'];$i++) $tpl->parse("no_ot_img");	
    if($OCNT>0) $tpl->parse("is_ot_img");
	unset($ot_img1, $ot_img2);
}	
/**************************** OTHER IMAGE **************************/

/**************************** 배송정보 관련 **************************/
$sql = "SELECT code FROM mall_design WHERE mode='D'";
$tmps = $mysql->get_one($sql);
$INFO = stripslashes($tmps);
$INFO = str_replace("../../image","image",$INFO);
/**************************** 배송정보 관련 **************************/

/**************************** 품절 관련 **************************/
if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) {
	$tpl->parse('is_limit');
	$tpl->parse('is_limit3');
} 
else {
	$tpl->parse('no_limit');
	$tpl->parse('is_limit2');
}
/**************************** 품절 관련 **************************/

/**************************** 필수정보   **************************/
$ck_info = 0;
$sql = "SELECT * FROM mall_goods_info WHERE guid='{$uid}' ORDER BY o_num ASC";
$mysql->query($sql);
while($row2 = $mysql->fetch_array()){
	$opName1 = $row2['name1'];
	$opContent1 = $row2['content1'];
	$opName2 = $row2['name2'];
	$opContent2 = $row2['content2'];
	$opUid2 = $row2['uid'];
	if($opName2=='x') $tpl->parse("is_info1","1");
	else $tpl->parse("is_info2","1");			
	$ck_info = 1;

	$tpl->parse("loop_info");			
	$tpl->parse("is_info1","2");
	$tpl->parse("is_info2","2");			
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

/**************************** 태그 **************************/
if($data['tag']) {
	$tag = stripslashes($data['tag']);
	$tag = substr($tag,1,-1);
	$tag = explode(",",$tag);
	
	for($i=0,$cnt=count($tag);$i<$cnt;$i++){
		if($tag[$i]) {
			$TAG = trim(htmlspecialchars($tag[$i]));
			$TLINK = "{$Main}?channel=search&field=tag&&search=".urlencode($TAG);
			$RND = rand(1,8);
			$tpl->parse("loop_tag");
		}
	}
}

$sql = "SELECT code FROM mall_design WHERE mode='T'";
$tmps = $mysql->get_one($sql);
$tmps = explode("|",$tmps);
$tmps = $tmps[0];
if($tmps) {
	if($tmps==2 && !$my_id) $oclick = "onclick=\"alert('로그인을 하시기 바랍니다!');\"";
	else $oclick = "onclick=\"ckTagForm();\"";
	$tpl->parse("is_tag");
}
/**************************** 태그 **************************/

/**************************** 상품평 **************************/
$sql = "SELECT count(*) as cnt, SUM(point) as sum FROM mall_goods_point WHERE uid>0 && number='{$uid}'";
$tmps = $mysql->one_row($sql);

$CNT_AFTER = $tmps['cnt'];
if($CNT_AFTER==0) $SUM_AFTER = 0;
else $SUM_AFTER = round(($tmps['sum']*2)/$tmps['cnt'],1);

$SUM_AFTER = sprintf("%01.1f", $SUM_AFTER);

if(strlen($SUM_AFTER)==3) $tmps = " ".$SUM_AFTER;
else $tmps = $SUM_AFTER;
$SUM1 = substr($tmps,0,1);
if($SUM1==" ") $SUM1 = "";
$SUM2 = substr($tmps,1,1);
$SUM3 = substr($tmps,3,1);
$SUM4 = ($SUM_AFTER*10);

$sql = "SELECT * FROM mall_goods_point WHERE uid>0 && number='{$uid}' ORDER BY uid DESC LIMIT 3";
$mysql->query($sql);
for($i=0;$i<3;$i++) {
	if($tmps=$mysql->fetch_array()){
		$ATITLE = htmlspecialchars(stripslashes($tmps['title']));
		$ADATE	= date("y.m.d",$tmps['signdate']);
	}
	else $ATITLE = $ADATE = "";
	$tpl->parse('loop_Tafter');	
}

if($CNT_AFTER==0) $tpl->parse('no_after');

$afterstr = "&cate={$cate}&number={$uid}";

if(!$my_id) $PLINK= "javascript:alert('로그인 후 이용하실 수 있습니다!');";
else $PLINK="#\" onclick=\"return ckpForm();";
/**************************** 상품평 **************************/

/**************************** 상품Q&A **************************/
$sql = "SELECT count(*) FROM mall_goods_qna WHERE uid>0 && number='{$uid}'";
$CNT_QNA = $mysql->get_one($sql);
if($CNT_QNA==0) $tpl->parse('no_qna');
$qnastr = "&cate={$cate}&number={$uid}";

if($my_level>8) $tpl->parse("is_admin");
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
		$sql = "INSERT INTO mall_goods_view (uid,cno,date,view,brand) VALUES('','{$orig_cate}{$uid}','{$dates}',1,'{$BRAND}')";
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

if($my_id) $CKLOGIN = "Y";
else $CKLOGIN = "N";

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>