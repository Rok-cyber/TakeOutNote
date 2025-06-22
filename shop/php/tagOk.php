<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$uid	= $_GET['uid'];
$tag	= addslashes($_GET['tag']);

if(!$uid || !$tag) { 
	echo "<item>false</item>"; 
	echo "</root>";
	exit;
}

$sql = "SELECT tag FROM mall_goods WHERE uid='{$uid}'";
$tag_arr = $mysql->get_one($sql);

$tag_arr = explode(",",$tag_arr);
if(in_array($tag,$tag_arr)) {
	echo "<item>dupl</item>"; 
	echo "</root>";
	exit;	
}

if(count($tag_arr)==20) {
	echo "<item>over</item>"; 
	echo "</root>";
	exit;	
}

$tag_arr[count($tag_arr)-1] = $tag;
$tag_arr[count($tag_arr)] = '';
$tag_arr = join(",",$tag_arr);

$sql = "UPDATE mall_goods SET tag = '{$tag_arr}' WHERE uid='{$uid}'";
$mysql->query($sql);

echo "<item>succ</item>"; 
echo "<main>{$Main}</main>";
echo "<cname>tag".rand(1,8)."</cname>";
echo "</root>";
exit;	
?>