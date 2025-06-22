<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$Pstart	= $_GET['Pstart'];
$limit	= $_GET['limit'];

if(!$limit || strlen($Pstart)==0) { 	
	echo "<error>Error</error></root>"; 
	exit;
}

/**************************** GOODS LIST**************************/
$sql = "SELECT * FROM mall_reserve WHERE id = '{$my_id}'  && status !='D' ORDER BY uid desc LIMIT {$Pstart},{$limit}";
$mysql->query($sql);

while($row = $mysql->fetch_array()){		
	$NAME = stripslashes($row['subject']);
	$MONEY = number_format($row['reserve'],$ckFloatCnt);
	$TYPE = $row['status'];
	switch ($row['status']){
		case "A" : $STATUS = "적립대기";
		break;
		case "B" : $STATUS = "적립완료";			
		break;
		case "C" : $STATUS = "적립사용";
		break;
		case "E" : $STATUS = "사용취소";
			$NAME .= " (주문취소에 따른 적립금 {$MONEY}원 환원)"; 
			$MONEY = 0;
		break;
	}
		
	$DATE = substr($row['signdate'],0,16);
	echo "
	  <item>
		<name><![CDATA[{$NAME}]]></name>
		<reserve><![CDATA[{$MONEY}]]></reserve>
		<status><![CDATA[{$STATUS}]]></status>
		<date><![CDATA[{$DATE}]]></date>
	  </item>\n	";	
}

echo "</root>";
?>