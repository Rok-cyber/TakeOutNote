<?
include "sub_init.php";
include "$skin/skin_define.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

$uid = $_GET['uid'];

$skin = "../skin/{$tmp_skin}";
$skin2 = $skin."/";
$ShopPath = "../";

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비


$sql = "SELECT * FROM mall_goods WHERE uid='{$uid}'";
if(!$data = $mysql->one_row($sql)) alert('상품이 삭제 되었거나 없는 상품입니다.','close5');

if($data['s_qty']==3 || $data['type']!='A') {
	alert('해당상품이 삭제되었거나 존재하지 않습니다.','close5');
}

// 임시장바구니번호 존재확인
if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
	if($my_id) $tempid = $my_id;
	else $tempid = md5(uniqid(rand()));
	SetCookie("tempid",$tempid,0,"/");
} 
else $tempid = $_COOKIE['tempid'];

$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$tempid}'";
if($mysql->get_one($sql)>0) $ckCart = 'Y';
else $ckCart = 'N';

$cate = $data['cate'];
$number = $data['number'];

$gData	= getDisplay($data,'image2');		// 디스플레이 정보 가공 후 가져오기
$GOODS_IMG	= $gData['image'];
$GOODS_IMG_WIDTH = $IMG_DEFINE['img2'];
$GOODS_OIMG = $gData['image'];
$GOODS_NAME	= $gData['name'];		
$GOODS_PRICE= $gData['price'];
$GOODS_ORIG_PRICE= number_format($data['price'],$ckFloatCnt)."원";
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

$tpl->define('main',"{$skin}/pquick_view.html");
$tpl->scan_area('main');

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
	$GOODS_C_PRICE = number_format($data['consumer_price'],$ckFloatCnt)."원";
    $tpl->parse('is_c_price'); 
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
		else $tpl->parse("is_op_text");
	}

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

/**************************** OTHER IMAGE **************************/
$tmp_dir = str_replace("../../","../",$data['other_image']);
$tmp_dir2 = str_replace("../","/",$tmp_dir);
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
}	
/**************************** OTHER IMAGE **************************/

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

/**************************** 상품평 **************************/
$sql = "SELECT count(*) as cnt, SUM(point) as sum FROM mall_goods_point WHERE uid>0 && cate='{$cate}' && number='{$uid}'";
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
/**************************** 상품평 **************************/


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

	array_push($tmp, "{$cate}:{$uid}");
	$goods_view = implode(',',$tmp);	  
	session_register("goods_view");
    $_SESSION['goods_view'] = $goods_view;		
}

$tmp=explode(',',$_SESSION['today_view']);
if(!in_array("{$cate}:{$uid}",$tmp)){      
	array_push($tmp, "{$cate}:{$uid}");
	$today_view = implode(',',$tmp);	  
	session_register("today_view");
    $_SESSION['today_view'] = $today_view;		
}
/**************************** 조회수 증가 & 오늘본 상품 추가 **************************/

if($my_id) {  //쇼핑찜 링크
   $LWISH = "php/wish_ok.php?cate={$cate}&number={$number}";
   $CKLOGIN = "Y";
} 
else {
   $LWISH = "javascript:ckLogin();";
   $CKLOGIN = "N";
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>

<script LANGUAGE="JavaScript">
<!--
var f = document.goodsForm;
// -->
</script>