<?
######################## lib include
include "../ad_init.php";

$mode	= $_GET['mode'];
$uid	= $_GET['uid'];
$limit	= $_GET['limit'];
$location= $_GET['location'];
$page	= $_GET['page'];
$field	= $_GET['field'];
$word	= $_GET['word'];
$code	= isset($_GET['code']) ? $_GET['code'] : 'banner';


$addstring = "&location={$location}&page={$page}";
if($limit) $addstring .= "&limit={$limit}";
if($field && $word) $addstring .= "&field={$field}&word={$word}";

if(!$uid || !$location) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');
       
$sql = "SELECT rank, cate FROM mall_{$code} WHERE location='{$location}' && uid='{$uid}'";
$row = $mysql->one_row($sql);
$number = $row['rank'];
if($row['cate']) {
	$where2 = "&& cate='{$row['cate']}'";
}

switch($mode) {
	case "first" :
		if($number==1) movePage("board.php?code={$code}$addstring}");
		$where = "rank < '{$number}'";
		$exp1 = "rank + 1";
		$exp2 = "1";
	break;

	case "up" :
		if($number==1)  movePage("board.php?code={$code}{$addstring}");
		$where = "rank = '".($number-1)."'";
		$exp1 = "rank + 1";
		$exp2 = "rank - 1";
	break;

	case "down" : 
		$sql = "SELECT MAX(rank) FROM mall_{$code} WHERE location='{$location}' {$where2}";
		if($mysql->get_one($sql)==$number)  movePage("board.php?code={$code}{$addstring}");
		$where = "rank = '".($number+1)."'";
		$exp1 = "rank - 1";
		$exp2 = "rank + 1";
	break;			

	case "last" : 
		$sql = "SELECT MAX(rank) FROM mall_{$code} WHERE location='{$location}' {$where2}";
		$lnumber = $mysql->get_one($sql);
		if($lnumber==$number)  movePage("board.php?code={$code}{$addstring}");
		$where = "rank > '{$number}'";
		$exp1 = "rank - 1";
		$exp2 = $lnumber;
	break;
}

$sql = "UPDATE mall_{$code} SET rank = {$exp1} WHERE location='{$location}' && {$where} {$where2}";
$mysql->query($sql);

$sql = "UPDATE mall_{$code} SET rank = {$exp2} WHERE location='{$location}' && uid={$uid} {$where2}";
$mysql->query($sql);

movePage("board.php?code={$code}{$addstring}");

?>
