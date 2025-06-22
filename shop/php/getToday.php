<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$mo1	= $_GET['mo1'];
$mo2	= $_GET['mo2'];
$type	= $_GET['type'];

/**************************** GOODS LIST**************************/
@session_start();
$tmp = explode(',',$_SESSION['today_view']);
$cnt = count($tmp);

for($i=0,$Tcnt=0;$i<=$cnt;$i++){
	$tmp2 = explode(":",$tmp[$i]);
	$sql = "SELECT uid,cate,number,name,price,price_ment,image3,image4,icon,comp,reserve,c_cnt, tag, s_qty, qty FROM mall_goods WHERE uid='{$tmp2[1]}'";
	if($data=$mysql->one_row($sql)){		
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

		if($mo1 && $mo2) {
			if($data['price']<$mo1 || $data['price']>$mo2) continue;
		}	
		
		$TAG	= "";
		$tag	= explode(",",stripslashes($data['tag']));

		for($j=1,$cnt2=count($tag);$j<$cnt2-1;$j++){			
			if($TAG) $TAG .= ", ";
			if(trim($tag[$j])) $TAG .= "<a href='{$Main}?channel=search&field=tag&search=".urlencode($tag[$j])."' class='small'>{$tag[$j]}</a>";
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
			<cp_price><![CDATA[{$CP_PRICE}]]></cp_price>	
			<cooperate><![CDATA[{$COOPERATE}]]></cooperate>	
			<cate><![CDATA[{$CATE}]]></cate>	
		  </item>\n	";	
	}
}

echo "</root>";
?>