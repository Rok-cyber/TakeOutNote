<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$order	= $_GET['order'];
$Pstart	= $_GET['Pstart'];
$limit	= $_GET['limit'];

if(!$limit || strlen($Pstart)==0) { 	
	echo "<error>Error</error></root>"; 
	exit;
}

/**************************** GOODS LIST**************************/
$sql = "SELECT a.uid as uid2, a.memo, a.signdate, b.uid, b.cate, b.number, b.name, b.price, b.price_ment, b.image3, b.icon, b.reserve, b.event, b.c_cnt, b.s_qty, b.qty FROM mall_wish a, mall_goods b WHERE a.id='{$my_id}' && a.p_number=b.uid order by a.uid desc LIMIT {$Pstart},{$limit}";
$mysql->query($sql);

while($data = $mysql->fetch_array()){

	$gData	= getDisplay($data,'image3');		// 디스플레이 정보 가공 후 가져오기

	$LINK	= $gData['link'];
	$IMAGE	= "../".$gData['image'];
	$NAME	= $gData['name'];
	$COMP	= $gData['comp'];
	$PRICE	= $gData['price'];
	$CPRICE	= $gData['cprice'];  //소비자가
	$CP_PRICE= $gData['cp_price'];
	$ICON	= $gData['icon'];
	$RESE	= $gData['reserve'];
	$CCNT	= $gData['c_cnt'];
	$UID	= $data['uid'];
	$CATE	= $data['cate'];
	$EVENT	= $data['event'];
	$OPRICE = $gData['oprice'];
		
	if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) {
		$SOUT = "1";
	} 
	else $SOUT = "";

	echo "
	  <item>
		<link><![CDATA[{$LINK}]]></link>
		<image><![CDATA[{$IMAGE}]]></image>
		<name><![CDATA[{$NAME}]]></name>
		<comp><![CDATA[{$COMP}]]></comp>
		<price><![CDATA[{$PRICE}]]></price>
		<cprice><![CDATA[{$CPRICE}]]></cprice>
		<icon><![CDATA[{$ICON}]]></icon>
		<rese><![CDATA[{$RESE}]]></rese>
		<ccnt><![CDATA[{$CCNT}]]></ccnt>
		<uid><![CDATA[{$UID}]]></uid>		
		<sout><![CDATA[{$SOUT}]]></sout>	
		<cp_price><![CDATA[{$CP_PRICE}]]></cp_price>				
		<cate><![CDATA[{$CATE}]]></cate>	
		<event><![CDATA[{$EVENT}]]></event>	
		<oprice><![CDATA[{$OPRICE}]]></oprice>	
	  </item>\n	";	
}

echo "</root>";
?>