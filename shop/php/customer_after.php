<?
$limit	= isset($_GET['limit']) ? $_GET['limit'] : 10;
$field	= !empty($_GET['field']) ? $_GET['field'] : 'title';
$word	= urldecode($_GET['word']);

$afterstr = "";

if($word) { 
	$afterstr = "&field={$field}&word=".urlencode($word);
	$where = "&& INSTR(a.{$field},'{$word}')";
}

$sql = "SELECT count(*) FROM mall_goods_point a, mall_goods b WHERE a.uid>0 && a.number=b.uid {$where}";
$CNT_AFTER = $mysql->get_one($sql);

$tpl->define("main","{$skin}/customer_after.html");
$tpl->scan_area("main");

if($CNT_AFTER==0) {
	$tpl->parse('no_after');
	$PAGE = 0;
	$PAGE_TOTAL = 0;
}
else {
	$PAGE = 1;
	$PAGE_TOTAL = ceil($CNT_AFTER/$limit);		
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>