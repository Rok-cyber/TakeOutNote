<?
$tpl->define("main","{$skin}/goods_best.html");
$tpl->scan_area("main");

$day1 = date("Ymd",strtotime('-1 WEEK', time()));
$day2 = date("Ymd",strtotime('+1 DAY', time()));

$sql = "SELECT SUM(b.view) as sum FROM mall_goods a, mall_goods_view b WHERE a.s_qty!='3' && a.type='A' && SUBSTRING(b.cno,13)=a.uid && b.date BETWEEN {$day1} AND {$day2} GROUP BY cno LIMIT 50";
$mysql->query($sql);
$TOTAL = $mysql->affected_rows();

if($TOTAL==0) $tpl->parse("no_loop");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>