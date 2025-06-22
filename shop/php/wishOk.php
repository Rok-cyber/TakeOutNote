<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$uid = $_GET['uid'];

if(!$my_id) {
	echo "<item>false</item>"; 
	echo "</root>";
	exit;
}

if($_GET['mode']=='del') {
	$uid2 = explode(",",$uid);
	for($i=0,$cnt=count($uid2);$i<$cnt;$i++) {
		if($uid2[$i]) {
			$sql = "DELETE FROM mall_wish WHERE uid = '{$uid2[$i]}' && id='{$my_id}'";
			$mysql->query($sql);
		}
	}
	
	echo "<item>true</item>\n"; 
	echo "<type>Wish</type>\n"; 
	echo "<uid>{$uid}</uid>\n"; 
	echo "</root>";
	exit;
}
else if($_GET['mode']=='info') {
	$sql = "SELECT p_cate, p_number FROM mall_wish WHERE uid='{$uid}' && id='{$my_id}'";
	$row = $mysql->one_row($sql);
	echo "
	<item>true</item>\n
		<uid><![CDATA[{$uid}]]></uid>\n
		<cate><![CDATA[{$row['p_cate']}]]></cate>\n
		<number><![CDATA[{$row['p_number']}]]></number>\n
	</root>";
	exit;
}

$cate = substr($uid,0,12);
$number = substr($uid,12);

if(!$cate || !$number) { 
	echo "<item>false</item>"; 
	echo "</root>";
	exit;
}
if(substr($cate,0,3)=='999') {
	echo "<item>공동구매 상품은 관심상품에 담을 수 없습니다.</item>\n</root>"; 
	exit;			
}

$signdate	= time();

$sql = "SELECT count(*) FROM mall_wish WHERE p_cate = '{$cate}' && p_number = '{$number}' && id='{$my_id}'";
if($mysql->get_one($sql)==0) {
	$sql = "INSERT INTO mall_wish(uid,id,p_number,p_cate,memo,signdate) VALUES ('','{$my_id}','{$number}','{$cate}','','{$signdate}')";					
	$mysql->query($sql);
}   
else {
	echo "<item>이미 관심상품 목록에 등록 되어 있습니다.</item>"; 
	echo "</root>";
	exit;
}

$sql = "SELECT MAX(uid) FROM mall_wish WHERE id='{$my_id}'";
$uid = $mysql->get_one($sql);

$MY_SALE = $my_sale;
$MY_POINT = $my_point;
$data = Array();
$data['p_cate'] = $cate;
$data['p_number'] = $number;
$gData	= getDisplayOrder($data);
$price = $gData['p_price'];
$price2 = str_replace("원","",$price);

echo "
	<item>true</item>\n
		<uid><![CDATA[{$uid}]]></uid>\n
		<link><![CDATA[?channel=view&amp;uid={$number}&amp;cate={$cate}]]></link>\n
		<name><![CDATA[{$gData[name]}]]></name>\n
		<image><![CDATA[{$gData[simage]}]]></image>\n
		<price><![CDATA[{$price}]]></price>\n
		<price2><![CDATA[{$price2}]]></price2>\n
	</root>";
?>