<?
######################## lib include
include "../ad_init.php";

$limit	= $_GET['limit'];
$page	= $_GET['page'];
$field	= $_GET['field'];
$word	= $_GET['word'];
$value1	= $_GET['value1'];
$value2	= $_GET['value2'];
$signdate = time();

$addstring = "&page={$page}";
if($limit) $addstring .= "&limit={$limit}";
if($field && $word) $addstring .= "&field={$field}&word={$word}";

if(!$value1 || !$value2) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');

$sql = "SELECT count(*) FROM mall_auto_search WHERE word='{$value1}'";
if($mysql->get_one($sql)>0) alert("[{$value1}] 검색어는 이미 등록 되어 있습니다.","back");

$sql = "INSERT INTO mall_auto_search VALUES('','{$value1}','{$value2}','0','{$signdate}')";
$mysql->query($sql);

movePage("board.php?code=search{$addstring}");
?>
