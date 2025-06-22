<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$order	= $_GET['order'];
$type	= $_GET['type'];

if(!$order) { 	
	echo "<error>Error</error></root>"; 
	exit;
}

switch($order){
	case "wo" : 
		$day1 = date('Y-m-d', strtotime('-1 WEEK', time()));

		$sql = "SELECT COUNT(uid) as o_cnt, p_cate, p_number FROM mall_order_goods WHERE uid!='0' && signdate >= '{$day1}' GROUP BY p_number ORDER BY o_cnt DESC LIMIT 60";
	break;
	case "mo" : 
		$day1 = date('Y-m-d', strtotime('-1 MONTH', time()));

		$sql = "SELECT COUNT(uid) as o_cnt, p_cate, p_number FROM mall_order_goods WHERE uid!='0' && signdate >= '{$day1}' GROUP BY p_number ORDER BY o_cnt DESC LIMIT 60";
	break;
	case "wc" :
		$day1 = date("Ymd",strtotime('-1 WEEK', time()));

		$sql = "SELECT SUM(view) as sum, cno FROM mall_goods_view  WHERE date >= '{$day1}' GROUP BY cno ORDER BY sum DESC LIMIT 60";			
	break;
	case "mc" : 
		$day1 = date("Ymd",strtotime('-1 MONTH', time()));

		$sql = "SELECT SUM(view) as sum, cno FROM mall_goods_view  WHERE date >= '{$day1}' GROUP BY cno ORDER BY sum DESC LIMIT 60";			
	break;	
}

$mysql->query($sql);

$i =1;
while($data2 = $mysql->fetch_array()){
	if($i>50) break;
	
	if($data2['cno']) $tmp_number = substr($data2['cno'],12);
	else $tmp_number = $data2['p_number'];
	
	$sql = "SELECT uid,cate,number,name,price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && 
	type='A' && uid='{$tmp_number}'";
	if(!$data = $mysql->one_row($sql)) continue;
	
	if($type=='img') $gData	= getDisplay($data,'image3');		// 디스플레이 정보 가공 후 가져오기
	else $gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기

	$LINK	= $gData['link'];
	$IMAGE	= $gData['image'];
	$NAME	= $gData['name'];
	$COMP	= $gData['comp'];
	$PRICE	= $gData['price'];
	$CP_PRICE= $gData['cp_price'];
	$ICON	= $gData['icon'];
	$RESE	= $gData['reserve'];
	$CCNT	= $gData['c_cnt'];
	$DRAGD	= $gData['dragd'];
	$LOC	= getLocation($data['cate'],"Y");
	$UID	= $data['uid'];
	$CATE	= $data['cate'];
    
	if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) {
		$SOUT = "1";
	} 
	else $SOUT = "";

	if(substr($data['cate'],0,3)=='999') $COOPERATE = 1;
	else $COOPERATE = 0;

	$TAG	= "";
	$tag	= explode(",",stripslashes($data['tag']));

	for($j=1,$cnt=count($tag);$j<$cnt-1;$j++){			
		if($TAG) $TAG .= ", ";
		if(trim($tag[$j])) $TAG .= "<a href='{$Main}?channel=search&amp;field=tag&amp;search=".urlencode($tag[$j])."' class='small'>{$tag[$j]}</a>";
	}
	
	echo "
	  <item>
		<link><![CDATA[{$LINK}]]></link>
		<image><![CDATA[{$IMAGE}]]></image>
		<name><![CDATA[{$NAME}]]></name>
		<comp><![CDATA[{$COMP}]]></comp>
		<price><![CDATA[{$PRICE}]]></price>
		<icon><![CDATA[{$ICON}]]></icon>
		<rese><![CDATA[{$RESE}]]></rese>
		<ccnt><![CDATA[{$CCNT}]]></ccnt>
		<dragd><![CDATA[{$DRAGD}]]></dragd>
		<loc><![CDATA[{$LOC}]]></loc>
		<uid><![CDATA[{$UID}]]></uid>	
		<tag><![CDATA[{$TAG}]]></tag>		
		<sout><![CDATA[{$SOUT}]]></sout>	
		<rank><![CDATA[{$i}]]></rank>
		<cp_price><![CDATA[{$CP_PRICE}]]></cp_price>	
		<cooperate><![CDATA[{$COOPERATE}]]></cooperate>	
		<cate><![CDATA[{$CATE}]]></cate>	
      </item>\n	";	
	$i++;
}

echo "</root>";
?>