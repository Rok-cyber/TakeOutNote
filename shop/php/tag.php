<?
$tpl->define("main","{$skin}/tag.html");
$tpl->scan_area("main");

$s_date = date('Y-m-d', strtotime('-1 MONTH', time()));
$s_date2 = strtotime('-1 MONTH', time());
$e_date = date("Y-m-d");
$e_date2 = time();

/**************************** TAG SEARCH BEST **************************/
$sql = "SELECT word, count(*) as cnt FROM mall_search WHERE tag='1' && signdate BETWEEN '{$sdate2}' AND '{$e_date2}' GROUP BY word ORDER BY cnt DESC, word limit 100";
$mysql->query($sql);

$i = 0;
while($row = $mysql->fetch_array()) {
	$TAG = trim(htmlspecialchars($row['word']));
	$TLINK = "{$Main}?channel=search&field=tag&search=".urlencode($TAG);
	$RND = rand(1,8);
	if($i!=0) $tpl->parse("is_line","1");
	$i = 1;
	$tpl->parse("loop_tag");
}
/**************************** TAG SEARCH BEST **************************/

/**************************** SEARCH BEST **************************/
$sql = "SELECT word, count(*) as cnt FROM mall_search GROUP BY word ORDER BY cnt DESC, word limit 10";
$mysql->query($sql);

for($i=1;$i<11;$i++) {
	if($i!=10) $i2 = "0{$i}";
	else $i2 = $i;
	if($row = $mysql->fetch_array()) {
		$WORD = stripslashes($row['word']);
	}
	else $WORD = "";
	
	$tpl->parse("loop_rank_best");	
}
/**************************** SEARCH BEST **************************/

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>