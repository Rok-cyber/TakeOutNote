<?
@set_time_limit(0);
ob_start();
include "../php/sub_init.php";

$my_point = 0;
$my_sale = 0;

$sql = "SELECT code FROM mall_design WHERE mode='X'";
$row = $mysql->get_one($sql);
$row = explode("|",$row);
if($row[0]!=1) alert("오미엔진페이지 미사용 상태 입니다.","close");
$exword = $row[1];
$fname = "omi";

$times = @filectime("./data/{$fname}.txt");
if($times>time()-600) {
	echo readFiles("./data/{$fname}.txt");
	exit;
}

?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<meta http-equiv="Pragma" content="no-cache"/>

<html>
<head><title>오미엔진페이지</title></head>
<style>
 	body {font-size:9pt; font-family:"굴림"; text-decoration: none; line-height: 13pt; color:	#333333}
</style> 	
</head>
<body topmargin="0" leftmargin="0">

<pre>
<?
$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$cate_arr = Array();
$brand_arr = Array();
$sql = "SELECT cate_name, cate FROM mall_cate ORDER BY cate ASC";
$mysql->query($sql);

while($data = $mysql->fetch_array()){
	$cate_arr[$data['cate']] = $data['cate_name'];
}

$sql = "SELECT * FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);

while($data = $mysql->fetch_array()){
	$brand_arr[$data['uid']] = $data['name'];
}

$sql = "SELECT * FROM mall_goods WHERE type='A' ORDER BY cate ASC, number ASC";
$mysql->query($sql);

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

	if(substr($data['cate'],6,3)!='000') {
		$cate3 = $cate_arr[substr($data['cate'],0,9)."000"];
	} 
	else $cate3 = '';
	if(substr($data['cate'],3,3)!='000') {
		$cate2 = $cate_arr[substr($data['cate'],0,6)."000000"];
	}
	else $cate2 = '';
	$cate1 = $cate_arr[substr($data['cate'],0,3)."000000000"];
	
	$CARR = 0;

	$carriage = explode("|",$data['carriage']);
	if($carriage[0]==3) { //별도 책정일때
		$CARR = $carriage[1];
	}	
	
	if($cash[10] =='1') { 
		if($PRICE < $cash[11]) $CARR += $cash[12];		
	} 

	echo "<p>{$UID}^{$cate1}^{$cate2}^{$cate3}^{$data['comp']}^{$NAME}^{$LINK}^{$PRICE}^{$CARR}^{$IMAGE}^^\r\n";
}

?>
</pre>
</body>
</html>

<?
$tmps = ob_get_contents();
ob_end_flush(); 
ob_end_clean(); 
writeFile("./data/{$fname}.txt",$tmps);
?>