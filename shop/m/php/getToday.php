<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 


/**************************** GOODS LIST**************************/
@session_start();
$tmp = explode(',',$_SESSION['today_view']);
$cnt = count($tmp);

for($i=0,$Tcnt=0;$i<=$cnt;$i++){
	$tmp2 = explode(":",$tmp[$i]);
	$sql = "SELECT uid,cate,number,name,price,price_ment,image3,image4,icon,comp,reserve,c_cnt, tag, s_qty, qty,event FROM mall_goods WHERE uid='{$tmp2[1]}'";
	if($data=$mysql->one_row($sql)){		
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
}

echo "</root>";
?>