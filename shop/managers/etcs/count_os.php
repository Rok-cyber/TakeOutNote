<?
$tpl->define("main","{$skin}/count_type2.html");
$tpl->scan_area("main");

$cnts=0;
$TTL1 = "OS별";

$sql = "SELECT SUM(cnts) as total FROM pcount_agent WHERE type='O'";
if($TOTAL = $mysql->get_one($sql)) {
	$sql = "SELECT cnts FROM pcount_agent WHERE type='O' ORDER BY cnts DESC LIMIT 1";
	if(!$MAX = $mysql->get_one($sql)) $MAX=0;
	
	$NUM = 0;
	$sql = "SELECT * FROM pcount_agent WHERE type='O' ORDER BY cnts DESC";
	$mysql->query($sql);
	while($data=$mysql->fetch_array()){
		$TTL2 = $data[content];		
		$PER = round($data[cnts]/$TOTAL*100,1);		
		$GRP = round((100*$data[cnts])/$MAX,0);
		$CNT = number_format($data[cnts]);		
		$NUM++;
		$tpl->parse("loop");
	}

} else $TOTAL=$CNT=$PER=$CNT2=$PER2=$GRP=$GRP2=0; 

$TOTAL = number_format($TOTAL);		

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>