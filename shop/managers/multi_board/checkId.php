<?
include "../ad_init.php";

header("Content-type: text/xml; charset=EUC-KR"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="EUC-KR"?'.">\n<root>\n"); 

$sid = $_GET['sid'];
$code = $_GET['code'];
if(!$sid || !$code) { 
	echo "<item>Error</item></root>"; 
	exit;
}

############ 아이디 중복 검사 ####################
$sql = "SELECT count(*) FROM mall_{$code} WHERE id = '{$sid}'";
$cnt = $mysql->get_one($sql);

if($cnt) {
	echo "<item>false</item></root>"; 
	exit;
}
else {
	echo "<item>true</item></root>"; 
	exit;
}

?>