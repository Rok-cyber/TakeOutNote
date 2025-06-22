<?
$type_arr = Array("C"=>"신용카드","B"=>"무통장","R"=>"계좌이체","V"=>"가상계좌","H"=>"핸드폰");

$tpl->define("main","{$skin}/mypage.html");
$tpl->scan_area("main");

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
$sql = "SELECT a.signdate as dates, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$my_id}' && a.pid=b.uid && a.status ='A' ORDER BY a.uid desc";
$mysql->query($sql);

$ABLE_COUPON = 0;
while($row = $mysql->fetch_array()){			
	if($row['sdate'] && $row['edate'] && !$row['days']) {
		if(date("Y-m-d")>$row['edate']) continue;				
	}
	else {
		$tmps = date("Y-m-d", strtotime("+{$row['days']} DAY", $row['dates']));						
		if(date("Y-m-d")>$tmps) continue;			
	}
	$ABLE_COUPON++;
}

################## 상품후기 & 상품문의 정보 #################################
$sql = "SELECT count(*) FROM mall_goods_point WHERE uid>0 && id='{$my_id}'";
$TOTAL_REVIEW = number_format($mysql->get_one($sql));

$sql = "SELECT count(*) FROM mall_goods_qna WHERE uid>0 && id='{$my_id}'";
$TOTAL_QNA = number_format($mysql->get_one($sql));


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>