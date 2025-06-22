<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$sid = $_GET['sid'];
if(!$sid) { 
	echo "<item>Error</item></root>"; 
	exit;
}

############ 사용금지 아이디 검사 ####################
$sql = "SELECT address,info FROM pboard_member WHERE uid=1";
$data = $mysql->one_row($sql);

$options = explode("|",$data['address']);
$w_word = explode("|",$data['info']);

$x_id = explode(",",$w_word[4]);
$x_id[] = "del";
$x_id[] = "guest";
for($i=0;$i<count($x_id);$i++){
	if($x_id[$i]==$sid)  { $x_ck=1; break; }
}


############ 아이디 중복 검사 ####################
$sql = "SELECT count(*) FROM pboard_member WHERE uid >1 && id = '{$sid}'";
$cnt = $mysql->get_one($sql);

if($cnt || $x_ck) {
	echo "<item>false</item></root>"; 
	exit;
}
else {
	echo "<item>true</item></root>"; 
	exit;
}

?>