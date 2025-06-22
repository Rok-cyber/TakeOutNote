<?
######################## lib include
include "../ad_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$mode	= $_GET['mode'];
$uid	= $_GET['uid'];
$link	= $_GET['link'];
$title	= addslashes($_GET['title']);
$posts	= $_GET['posts'];

if($mode=='del') {
	if(!$uid || !$posts) {
		echo "<message>Error</message></root>"; 
		exit;
	}

	$sql = "SELECT count(*) FROM mall_tb_send WHERE uid='{$uid}' && posts='{$posts}'";
	if($mysql->get_one($sql)==0) {
		echo "<message>Error</message></root>";
		exit;
	}
	$sql = "DELETE FROM mall_tb_send WHERE uid='{$uid}' && posts='{$posts}'";
	$mysql->query($sql);

	echo "<message>Succ</message><uid>{$uid}</uid></root>";
	exit;
}

if(!$uid || strlen($uid)==0 || !$link || !$title || !$posts) { 	
	echo "<message>Error</message></root>"; 
	exit;
}

$sql = "SELECT count(*) FROM mall_tb_send WHERE gid='{$uid}' && posts='{$posts}'";
if($mysql->get_one($sql)>0) {
	echo "<message>Duple</message></root>";
	exit;
}

$sql = "SELECT code FROM mall_design WHERE mode='A'";
$tmp_basic = $mysql->get_one($sql);
$basic = explode("|*|",stripslashes($tmp_basic));

$sql = "SELECT cate, number, explan FROM mall_goods WHERE uid='{$uid}'";
$row = $mysql->one_row($sql);

$excerpt = html2txt($row['explan']);
$url = "http://".$_SERVER["SERVER_NAME"]."/index.php?channel=view&cate={$row['cate']}&number={$row['number']}";

$result = sendTb($posts,$url,$title,$basic[1],$excerpt);

if(!$result) {
	echo "<message>Trans</message></root>";
	exit;
}

$signdate = time();
$sql = "INSERT INTO mall_tb_send VALUES('','{$uid}','{$link}','{$title}','{$posts}','{$signdate}')";
$mysql->query($sql);

$sql = "SELECT MAX(uid) FROM mall_tb_send WHERE gid='{$uid}'";
$uid = $mysql->get_one($sql);

$dates = date("m-d H:i");
echo "<message>Succ</message><uid>{$uid}</uid><date>{$dates}</date></root>";
?>