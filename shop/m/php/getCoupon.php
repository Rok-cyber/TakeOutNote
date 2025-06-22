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

$stype_arr = Array('P'=>'%','W'=>'원');
$status_arr = Array("A"=>"쿠폰발급완료","B"=>"쿠폰사용완료","C"=>"쿠폰기간만료","D"=>"쿠폰발급실패");

/**************************** GOODS LIST**************************/
$sql = "SELECT a.status, a.signdate as dates, b.* FROM mall_cupon a, mall_cupon_manager b WHERE a.id = '{$my_id}' && a.pid=b.uid && a.status !='D' ORDER BY a.uid desc LIMIT {$Pstart},{$limit}";
$mysql->query($sql);

while($row = $mysql->fetch_array()){		
	$NAME = stripslashes($row['name']);
	$SALE	= number_format($row['sale']);
	$STYPE	= $stype_arr[$row['stype']];		
	
	if($row['sdate'] && $row['edate'] && !$row['days']) {
		$DATES = substr($row['sdate'],0,10)." ~ ".substr($row['edate'],0,10);
		if(date("Y-m-d")>$row['edate']) $row['status'] = 'C';
	}
	else {
		$DATES = "발급 후 {$row['days']}일";
		$tmps = date("Y-m-d", strtotime("+{$row['days']} DAY", $row['dates']));
		if(date("Y-m-d")>$tmps && $row['status']=='A') $row['status'] = 'C';			
	}
	
	if($row['lmt']>0) $LMT = "<font class='num'>".number_format($row['lmt'])."</font>원 이상구매시";
	else $LMT = "제한없음";
	
	$TYPE = $row['status'];
	$STATUS = $status_arr[$row['status']];

	$DATE = date("Y-m-d",$row['dates']);

	echo "
	  <item>
		<name><![CDATA[{$NAME}]]></name>
		<sale><![CDATA[{$SALE}{$STYPE}]]></sale>
		<lmt><![CDATA[{$LMT}]]></lmt>
		<status><![CDATA[{$STATUS}]]></status>
		<date><![CDATA[{$DATES}]]></date>
	  </item>\n	";	
}

echo "</root>";
?>