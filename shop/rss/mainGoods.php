<?
include "../php/sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n"); 

$sql = "SELECT code FROM mall_design WHERE mode='A'";
$tmp_basic = $mysql->get_one($sql);
$basic = explode("|*|",stripslashes($tmp_basic));
//0:주소,1:쇼핑몰명,2:상호,3:대표자명,4:사번호,5:부가번호,6:주소,7:연락처,8:팩스,9:관리자명,10:이메일,11:타이틀,12:키워드,13:실시간검색어

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$type	= $_GET['type'];

if($type=='1') $TITLE = "인기상품";
else if($type=='2') $TITLE = "추천상품";
else {
	$type = '3';
	$TITLE = "신상품";
}
$TLINK = "/{$ShopPath}".$Main;
?>

<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title>메인 <?=$TITLE?> 상품정보</title>
<link><![CDATA[http://<?=$_SERVER["SERVER_NAME"].$TLINK?>]]></link>
<description><![CDATA[<?=$basic[12]?> ]]></description>
<dc:language>ko</dc:language>
<generator>itsMall</generator>
<pubDate><?=date("Y-m-d h:i")?></pubDate>

<?

/**************************** GOODS LIST**************************/
$sql = "SELECT uid,cate,number,name,price,price_ment,comp,image4,icon,event,reserve FROM mall_goods WHERE SUBSTRING(display,1,1)='{$type}' && s_qty!='3' && type='A' ORDER BY signdate DESC LIMIT 50";

$mysql->query($sql);

while($data = $mysql->fetch_array()){
			
	$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기	

	$LINK	= "http://".$_SERVER['SERVER_NAME']."/{$ShopPath}".$gData['link'];
	$LINK   = str_replace("&amp;","&",$LINK);
	$IMAGE	= $gData['image'];
	$NAME	= $gData['name'];
	$PRICE	= $gData['price'];	
	$RESE	= $gData['reserve'];
	$UID	= $data['uid'];
			
	echo "<item>
			<title><![CDATA[ {$NAME} ]]></title>
			<description>
				<![CDATA[
					<div style='width:600px;'>
						<div><img src='http://{$_SERVER['SERVER_NAME']}/{$IMAGE}' border='0' /></div>
						<div><a href='{$LINK}'>{$NAME}</a></div>
						<div>{$PRICE}</div>
					</div>	
				]]>
			</description>
			<link><![CDATA[ {$LINK} ]]></link>					
			<dc:creator><![CDATA[ [{$basic[1]}] {$PRICE} ]]></dc:creator>		   
		  </item>\n
	";
}

echo "
	</channel>
	</rss>
";
?>