<?
######################## lib include
include "../ad_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$uid = $_GET['uid'];

if(!$uid || strlen($uid)==0) { 	
	echo "<error>Error</error></root>"; 
	exit;
}

/**************************** GOODS LIST**************************/

$tmps = explode(",",$uid);
for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
	$sql = "SELECT uid, name, price, image4, s_qty, qty FROM mall_goods WHERE uid = '{$tmps[$i]}'";
	$mysql->query($sql);

	while($data = $mysql->fetch_array()){

		$NAME	= htmlspecialchars(stripslashes($data['name']));
		$PRICE	= number_format($data['price']);
		$UID	= $data['uid'];
		$SOUT	= "";
		if($data['s_qty']==3) $SOUT = "숨김";
		else if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) $SOUT = "품절";

		if(!$data['image4']) $IMAGE = "../../image/no_goods_120.gif";
		else $IMAGE = "../../image/goods_img{$data['image4']}";

		echo "
			<item>
				<name><![CDATA[{$NAME}]]></name>
				<price><![CDATA[{$PRICE}]]></price>
				<image><![CDATA[<img src='{$IMAGE}' border=0 width=80 height=80>]]></image>
				<uid><![CDATA[{$UID}]]></uid>		
				<num><![CDATA[{$NUM}]]></num>
				<sout><![CDATA[{$SOUT}]]></sout>	
			</item>
			";
	}
}
echo "</root>";
?>