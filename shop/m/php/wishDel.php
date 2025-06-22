<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

$number = $_GET['number'];
if($number) {
	$sql = "DELETE FROM mall_wish WHERE id='{$my_id}' && p_number='{$number}'";
	$mysql->query($sql);
	$result = "true";
}
else $result = "false";

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 
echo "<item>{$result}</item>\n"; 
echo "<uid>{$number}</uid>\n"; 
echo "</root>";
exit;
?>