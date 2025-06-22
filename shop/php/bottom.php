<?
$tpl->define("main",$skin."/bottom.html");
$tpl->scan_area("main");

/************************* 오늘본 상품 ***********************/
@session_start();
$tmp = explode(',',$_SESSION['today_view']);
$tmp = array_reverse($tmp);
$cnt = count($tmp);

for($i=0,$Tcnt=0;$i<=$cnt;$i++){
	$tmp2 = explode(":",$tmp[$i]);
	if(!$tmp2[0]) continue;
	$sql = "SELECT uid,number,name,price,price_ment,image4,event,reserve,qty,s_qty FROM mall_goods WHERE uid='{$tmp2[1]}'";
	if($data=$mysql->one_row($sql)){		
		$data['cate'] = $tmp2[0];
		$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
		$LINK	= $gData['link'];		
		$NAME	= $gData['name'];		
		$PRICE	= $gData['price'];
		$PRICE2 = str_replace("원","",$PRICE);
		$IMAGE  = $gData['image'];
		$CATE	= $tmp2[0];
		$NUMBER = $tmp2[1];	
		$tpl->parse("loop_today_goods");
		$tpl->parse("loop_qb_today_goods");
		$Tcnt++;
		if($Tcnt==20) break;
	}
}

if($Tcnt==0) {
	$tpl->parse("empty_today_goods");
	$dispqToday = "block";
}
else {
	$dispqToday = "none";
	$qbTodayWidth = ($IMG_DEFINE['img4'] + 20) * $Tcnt;
}
/************************* 오늘본 상품 ***********************/


/************************* 관심 상품 ***********************/
$sql = "SELECT a.uid as uid2, a.signdate, b.uid, b.cate, b.number, b.name, b.price, b.price_ment, b.image4, b.icon, b.reserve, b.event, b.qty, b.s_qty FROM mall_wish a, mall_goods b WHERE a.id='{$my_id}' && a.p_number=b.uid order by a.uid desc";
$mysql->query($sql);

$Wcnt = 0;
while($data = $mysql->fetch_array()){	
	$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기

	$LINK	= $gData['link'];	
	$NAME	= $gData['name'];	
	$PRICE	= $gData['price'];
	$PRICE2 = str_replace("원","",$PRICE);
	$IMAGE  = $gData['image'];
	$UID	= $data['uid2'];
	$CATE	= $data['cate'];
	$NUMBER	= $data['uid'];

    $Wcnt++;    
	
	$tpl->parse("loop_qb_wish_goods");  
}
if($Wcnt==0) {
	$tpl->parse("qb_empty_wish_goods");
	$dispqWish = "block";
	$qbWishWidth = 0;
}
else {
	$dispqWish = "none";
	$qbWishWidth = ($IMG_DEFINE['img4'] + 20) * $Wcnt;
}
/************************* 관심 상품 ***********************/


/************************* 장바구니 상품 ***********************/
$sql = "SELECT * FROM mall_cart WHERE tempid='{$_COOKIE[tempid]}' ORDER BY date ASC";
$mysql->query($sql);

$Ccnt = 0;
$CART_SUM = 0;
while($data = $mysql->fetch_array()){	
	$gData	= getDisplayOrder($data);		// 디스플레이 정보 가공 후 가져오기

	$LINK	= $gData['link'];	
	$NAME	= $gData['name'];	
	$PRICE = number_format($gData['oprice'],$ckFloatCnt)."원";
	$PRICE2 = str_replace("원","",$PRICE);
	$OPRICE = $gData['oprice'];	
	$IMAGE  = $gData['simage'];
	$QTY	= $data['p_qty'];
	$UID	= $data['uid'];
	$CATE	= $data['p_cate'];
	$NUMBER	= $data['p_number'];
    $Ccnt++;    
	
	$OPTION = "Y";
	$OPTION_DSP1 = $OPTION_DSP2 = "none";
	$CART_SUM += $gData['sum'];	
	if(count($gData['op_list'])>0) {		
		if($gData['op_sec'][1]) $OPTION_DSP1 = "block";
		else {
			$OPTION_DSP2 = "block";
			$OPTION = "N";
		}
	}
	$tpl->parse("loop_cart_goods");  
	$tpl->parse("loop_qb_cart_goods");  
}
if($Ccnt==0) {
	$tpl->parse("empty_cart_goods");
	$dispqCart = "block";
	$qbCartWidth = 0;
}
else {
	$CART_SUM = number_format($CART_SUM);
	$dispqCart = "none";
	$qbCartWidth = ($IMG_DEFINE['img4'] + 20) * $Ccnt;
}
/************************* 장바구니 상품 ***********************/

$CO_LINK	= $basic[14];
if(!$CO_LINK) $CO_LINK = "http://www.ftc.go.kr/info/bizinfo/communicationList.jsp";
else {
	$CO_LINK = str_replace("&","&amp;",$CO_LINK);
}

if($common[2]==1) $tpl->parse("is_cimg1");
else  {
	$CO_NUM1	= $basic[4];
	$CO_NUM2	= $basic[5];
	$CO_AD		= $basic[3];
	$CO_ADDR	= $basic[6];
	$CO_TEL		= $basic[7];
	$CO_FAX		= $basic[8];
	$CO_SAD		= $basic[9];
	$CO_EMAIL	= $basic[10];
	$CO_NAME	= $basic[2];
	$CO_NAME2	= $basic[1];	

	$sql = "SELECT signdate FROM pboard_member WHERE uid=1";
	$START_YEAR = date("Y",$mysql->get_one($sql));

	$tpl->parse("is_cimg2");
}

/***********************  BANNER  ********************************/
$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '6' && status='1' ORDER BY rank ASC";
$mysql->query($sql);
while($row_ban = $mysql->fetch_array()){
	if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
	if($row_ban['link']) {
		$BLINK = str_replace("&","&amp;",$row_ban['link']);
		if($row_ban['target']=='2') $BTARGET = "target='_blank'";
		else $BTARGET = "";
	}
	else $BLINK = "#\" onclick=\"return false;";

	$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner6'],stripslashes($row_ban['name']));
	$tpl->parse("loop_banner");		
}
unset($BLINK, $BTARGET, $BLINK);
/***********************  BANNER  ********************************/

if(!$channel) $tpl->parse("is_main");
else $tpl->parse("is_sub");

if(ckBrowser()=="MS-Explorer 6.0") $tpl->parse("is_fixed_ie6");
else $tpl->parse("is_fixed_default");

if(!$my_id) {
	$tpl->parse("qb_login");
	$CKLOGIN = "N";
}
else {
	$tpl->parse("qb_logout");
	$CKLOGIN = "Y";
}

$tpl->parse("main");
$tpl->tprint("main");
?>