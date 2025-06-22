<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$number	= $_GET['number'];
$Pstart	= $_GET['Pstart'];
$limit	= $_GET['limit'];
$total	= $_GET['total'];
$mypage	= $_GET['mypage'];

if(!$limit || strlen($Pstart)==0) { 	
	echo "<error>Error</error></root>"; 
	exit;
}

/**************************** GOODS LIST**************************/
if($mypage==1) {
	$where = "a.id='{$my_id}'";
}
else {
	$where = "a.number='{$number}'";
}

switch($_GET['order']) {
	case "best" :
		$order = "a.point";
	break;			
	default : $order = "a.uid";		
}
	
$sql = "SELECT a.*, b.image4 FROM mall_goods_point a, mall_goods b WHERE a.uid>0 && a.number=b.uid && {$where} ORDER BY {$order} desc LIMIT {$Pstart},{$limit}";
$mysql->query($sql);

$NUM = $total - ($Pstart - 1);
while($data = $mysql->fetch_array()){
			
	$POINT	= $data['point']*20;	
	$NAME	= htmlspecialchars(stripslashes($data['name']));
	$DATE	= date("Y-m-d H:i",$data['signdate']);
	$TITLE	= htmlspecialchars(stripslashes($data['title']));
	$BUY	= $data['buy'];
	$CONTENT = stripslashes($data['content']);
	$CONTENT = ieHackCheck($CONTENT);
	if(!$data['id'] || $data['id']==$my_id || $my_level>8) $MOD = 1;
	else $MOD = $my_id;
	$UID	= $data['uid'];

	if($mypage) {
		$NAME2	= htmlspecialchars(stripslashes($data['goods_name']));
		$LINK	= "{$Main}?channel=view&uid={$data['number']}&cate={$data['cate']}";
		$IMAGE	= "image/goods_img{$data[image4]}";			
	}
	else $NAME2 = $LINK = $IMAGE = "";


    $NUM--;

	echo "
	  <item>
		<num><![CDATA[{$NUM}]]></num>
		<point><![CDATA[{$POINT}]]></point>
		<name><![CDATA[{$NAME}]]></name>
		<title><![CDATA[{$TITLE}]]></title>
		<buy><![CDATA[{$BUY}]]></buy>
		<content><![CDATA[{$CONTENT}]]></content>
		<date><![CDATA[{$DATE}]]></date>	
		<mod><![CDATA[{$MOD}]]></mod>
		<uid><![CDATA[{$UID}]]></uid>
		<name2><![CDATA[{$NAME2}]]></name2>
		<link><![CDATA[{$LINK}]]></link>
		<image><![CDATA[{$IMAGE}]]></image>
      </item>\n	";	
}

echo "</root>";
?>