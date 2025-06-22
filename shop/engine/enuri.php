<?
@set_time_limit(0);
ob_start();
include "../php/sub_init.php";

$my_point = 0;
$my_sale = 0;
$cate = $_GET['cate'];

$sql = "SELECT code FROM mall_design WHERE mode='X'";
$row = $mysql->get_one($sql);
$row = explode("|",$row);
if($row[0]!=1) alert("오미엔진페이지 미사용 상태 입니다.","close");
$exword = $row[1];

if($cate) {
	if(substr($cate,3,3)=='000') $where = "WHERE SUBSTR(cate,1,3) = '".substr($cate,0,3)."'";
	else $where = "&& SUBSTR(cate,1,6) = '".substr($cate,0,6)."'";
}

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$brand_arr = Array();
$sql = "SELECT * FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);

while($data = $mysql->fetch_array()){
	$brand_arr[$data['uid']] = $data['name'];
}

$sql = "SELECT uid, name, sale, point FROM mall_event WHERE s_date <= '".date("Y-m-d")."' && e_date >'".date("Y-m-d")."' ORDER BY s_date ASC";
$mysql->query($sql);
$EVENT_SALE = Array();
$EVENT_POINT = Array();
$EVENT_NAME = Array();

while($evt = $mysql->fetch_array()){	
	$EVENT_SALE[$evt['uid']] = 	$evt['sale'];
	$EVENT_POINT[$evt['uid']] = $evt['point'];	
	$EVENT_NAME[$evt['uid']] = $evt['name'];	
}

$sql = "SELECT uid, sgoods FROM mall_cupon_manager WHERE type='3' ORDER BY uid DESC";
$mysql->query($sql);

$coupon_goods = array();
$coupon_uid		= array();
while($data = $mysql->fetch_array()){
	$coupon_goods[] = $data['sgoods'];
	$coupon_uid[] = $data['uid'];
}

$sql = "SELECT * FROM mall_goods WHERE  type='A' {$where} ORDER BY cate ASC, number ASC";
$mysql->query($sql);

?>
<HTML>
<HEAD>
<TITLE>:::: NuriBot Search Standard Form ::::</TITLE>
</HEAD>
<BODY topmargin='5'>
<table border="0" cellspacing="1" cellpadding="10" bgcolor="white" width="600" align='center'>
<tr><td>▒<b>검색용 페이지  - List 페이지</b></td></tr>
</table>
<center>상품수 : <?=$mysql->affected_rows()?> 개</center>
<table border="0" cellspacing="1" cellpadding="1" bgcolor="black" width="600" align='center'>
    <tr align="center" bgcolor="EDEDED">
        <td width="25" height="24" align="center">번호</td>
        <td width="180" height="24" align="center">제품명</td>
        <td width="40" height="24" align="center">가격</td>
        <td width="35" height="24" align="center">재고<br>유무</td>
        <td width="50" height="24" align="center">배송</td>
        <td width="90" height="24" align="center">웹상품이미지</td>
        <td width="30" height="24" align="center">할인<br>쿠폰 <br></td>
        <td width="30" height="24" align="center">계산서</td>
        <td width="50" height="24" align="center">제조사</td>
        <td width="30" height="24" align="center">상품코드</td>
        <td width="50" height="24" align="center">무이자<br>할부</td>
    </tr>
<?

$i = 1;

while($data = $mysql->fetch_array()){
			
	$gData	= getDisplay($data,'image2');		// 디스플레이 정보 가공 후 가져오기	

	$LINK	= "http://".$_SERVER['SERVER_NAME']."/".$gData['link'];
	$LINK   = str_replace("&amp;","&",$LINK);
	$IMAGE	= "http://".$_SERVER['SERVER_NAME'].$gData['image'];
	$NAME	= $gData['name'];
	$PRICE	= $gData['price'];	
	$PRICE	= str_replace(array(",","원"),"",$PRICE);
	$RESE	= $gData['reserve'];
	$UID	= "SHOP_".$data['uid'];
	$BRAND  = $brand_arr[$data['brand']];
	
	if($exword) {
		$tmps = str_replace("{brand}",$BRAND,$exword);
		$tmps = str_replace("{model}",$data['model'],$tmps);
		$tmps = str_replace("{maker}",$data['comp'],$tmps);
		$NAME = "{$tmps} {$NAME}";
	}
	
	$CARR = "무료";

	$carriage = explode("|",$data['carriage']);
	if($carriage[0]==3) { //별도 책정일때
		$CARR = "유료";
	}	
	
	if($cash[10] =='1') { 
		$CARR = "{$cash[11]}원이상무료";
	}
	
	if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) $QTY = "재고없음";
	else $QTY = "재고있음";

	$COUPON = '';
	for($j=0,$cnt=count($coupon_goods);$j<$cnt;$j++) {
		$ck_coupon = explode("|",$coupon_goods[$j]);
		if(in_array($data['uid'],$ck_coupon)) {
			$sql = "SELECT * FROM mall_cupon_manager WHERE uid='{$coupon_uid[$j]}'";
			if($cupon = $mysql->one_row($sql)){
				$tmp_ck = 1;
				if($cupon['sdate'] && $cupon['edate'] && !$cupon['days']) {
					if(date("Y-m-d") < substr($cupon['sdate'],0,10) || date("Y-m-d") > substr($cupon['edate'],0,10)) $tmp_ck = 0;
				}

				if($tmp_ck==1) {		
					$tmp_price = $gData['oprice'];
					if($cupon['stype']=='P') $COUPON = $cupon['sale']."%";
					else $COUPON =  $cupon['sale']."원"; 
					break;
				}				
			}
		}
	}

	echo "
		<tr align=\"center\" bgcolor=\"#FFFFFF\">\r\n
		<td height=\"24\">{$i}</td>\r\n
			<td height=\"24\" style=\"padding-top:3px;padding-bottom:3px\">\r\n
				<a href='{$LINK}' class=\"link_category1\">{$NAME}</a>\r\n
			</td>\r\n
			<td height=\"24\">{$PRICE}</td>\r\n
			<td height=\"24\">{$QTY}</td>\r\n
			<td height=\"24\">{$CARR}</td>\r\n
			<td height=\"24\">{$IMAGE}</td>\r\n
			<td height=\"24\">{$COUPON}</td>\r\n
			<td height=\"24\">N</td>\r\n
			<td height=\"24\">{$data['comp']}</td>\r\n
			<td height=\"24\">{$UID}</td>\r\n
			<td height=\"24\"></td>\r\n
		</tr>\r\n	
	";
	$i++;
}

?>
</table>

</BODY>
</HTML>

<?
$tmps = ob_get_contents();
ob_end_flush(); 
ob_end_clean(); 
?>