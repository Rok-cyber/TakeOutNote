<?
$type_arr = Array("C"=>"신용카드","B"=>"무통장","R"=>"계좌이체","V"=>"가상계좌","H"=>"핸드폰");

$tpl->define("main","{$skin}/mypage.html");
$tpl->scan_area("main");

$sql = "SELECT code FROM mall_design WHERE name='{$my_level}' && mode = 'L'";
$tmps = $mysql->get_one($sql);

if($tmps) {
	$tmps = explode("|",$tmps);
	$MY_GRAD = stripslashes($tmps[0]);
	$MY_SALE = stripslashes($tmps[2]);
	$MY_POINT= stripslashes($tmps[3]);
	if($tmps[4]=='Y') $MY_CARR = ", <font title='배송료별도상품 및 추가배송료지역(도서산간)제외'>기본배송료무료</font>";
	$tpl->parse("is_member_info");
}

$sql = "SELECT COUNT(uid) FROM mall_order_info WHERE id='{$my_id}'";
$TOTAL_ORDER = number_format($mysql->get_one($sql));

$sql = "SELECT COUNT(a.uid) FROM mall_wish a, mall_goods b WHERE a.id='{$my_id}' && a.p_number=b.uid";
$TOTAL_WISH = number_format($mysql->get_one($sql));


################## 적립금 정보 #################################
$sql = "SELECT COUNT(*) FROM  mall_reserve WHERE id='{$my_id}'  && status !='D'";
$TOTAL = $mysql->get_one($sql);

$sql = "SELECT SUM(IF(status='A',reserve,0)) as sum1, SUM(IF(status='B',reserve,0)) as sum2, SUM(IF(status='C',reserve,0)) as sum3 FROM  mall_reserve WHERE id='{$my_id}'";
$tmps = $mysql->one_row($sql);
$MONEY1 = $tmps['sum1'];
$MONEY2 = $tmps['sum2'];
$MONEY3 = $tmps['sum3'];

$TOTAL_MONEY = number_format($MONEY1 + $MONEY2 - $MONEY3,$ckFloatCnt);
$TOTAL_USE = number_format($MONEY2 - $MONEY3,$ckFloatCnt);
$MONEY1	= number_format($MONEY1,$ckFloatCnt);

################## 쿠폰 정보 #################################
$sql = "SELECT SUM(IF(status='A',1,0)) as cnt1, SUM(IF(status='B',1,0)) as cnt2 FROM mall_cupon WHERE id='{$my_id}'";
$tmps = $mysql->one_row($sql);
$ABLE_CUPON = number_format($tmps['cnt1']);
$USE_CUPON =  number_format($tmps['cnt2']);
$TOTAL_CUPON = number_format($tmps['cnt1']+$tmps['cnt2']);;

################## 최근 주문내역 #################################
$sql = "SELECT a.uid, a.order_num, a.pay_type, a.order_status, a.signdate, a.use_reserve, a.carriage, a.carr_info, a.pay_total, b.p_name, count(*) as cnt  FROM mall_order_info as a, mall_order_goods as b WHERE a.order_num=b.order_num && a.id='{$my_id}' group by b.order_num ORDER BY uid DESC LIMIT 5";	
$mysql->query($sql);

$ck = 0;
while($row = $mysql->fetch_array()){		
	$NUM = $row['order_num'];		
	
	if($row['order_status']!='Z') {
		$sql = "SELECT count(*) as cnt FROM mall_order_goods WHERE order_num='{$NUM}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D')";
		$tmps = $mysql->one_row($sql);				
		$row['cnt'] = $tmps['cnt'];
		$sql = "SELECT p_name FROM mall_order_goods WHERE order_num='{$NUM}' && !(order_status='X' && order_status2='D') && !(order_status='Z' && order_status2='D') LIMIT 1";
		$tmps = $mysql->one_row($sql);				
		$row['p_name'] = $tmps['p_name'];
	}
	
	$NAME	= stripslashes($row['p_name']);
	$CNT	= $row['cnt'];
	if($CNT >1) $NAME .= " 외".($CNT-1)."건";
	
	$PRICE = number_format($row['pay_total'],$ckFloatCnt);
	$TYPE = $type_arr[$row['pay_type']];
	$STATUS = $status_arr[$row['order_status']];
			
	$DATE = substr($row['signdate'],0,16);			
	$tpl->parse("loop_order");	
	$ck = 1;
}

if($ck==0) $tpl->parse("no_order");

#################### 최근 관심상품 #############################
$sql = "SELECT a.uid as uid2, a.memo, a.signdate, b.uid, b.cate, b.number, b.name, b.price, b.price_ment, b.image4, b.icon, b.reserve FROM mall_wish a, mall_goods b WHERE a.id='{$my_id}' && a.p_number=b.uid order by a.uid desc LIMIT 5";
$mysql->query($sql);
$ck =0;
$i = 1;
while($data = $mysql->fetch_array()){
	$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기

	$LINK	= $gData['link'];
	$IMAGE	= $gData['image'];
	$NAME	= $gData['name'];	
	$ICON	= $gData['icon'];			
	$PRICE	= $gData['price'];
	$PRICE2 = str_replace("원","",$PRICE);
	$UID	= $data['uid2']; 
	$CATE	= $data['cate'];
	$QLINK	= $data['uid'];
	$RESE	= $gData['reserve'];	
	$DRAGD	= $gData['dragd'];
	$MEMO   = stripslashes($data['memo']);
	$DATE	= date("Y-m-d",$data['signdate']);
	$LOC = getLocation($data['cate'],'1');
		
	$tpl->parse('loop_wish');	   				
	$ck = 1;
	$i++;
}
if($ck==0) $tpl->parse("no_wish");


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>