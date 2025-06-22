<?

$tpl->define("main","{$skin}/count_type1.html");
$tpl->scan_area("main");

$cnts=0;

$sql = "SELECT SUM(total) as total, SUM(total2) as total2 FROM pcount_list";
if(!$data = $mysql->one_row($sql)) $TOTAL=$TOTAL2 = 0;
else {
	$TOTAL = $data[total];
	$TOTAL2 = $data[total2];
}

$sql = "SELECT SUM(total2) as t2 FROM pcount_list GROUP BY year ORDER BY t2 DESC LIMIT 1";
if(!$MAX = $mysql->get_one($sql)) $MAX=0;

for($i=$s_year;$i<=$year;$i++){
	$TTL2 = $i."<font class=small>년</font>";
	if($TOTAL !='0') {
		$sql = "SELECT SUM(total) as total, SUM(total2) as total2 FROM pcount_list WHERE year = '$i'";
		$data = $mysql->one_row($sql);		
		$CNT = number_format($data[total]);
		$CNT2 = number_format($data[total2]);
		
		if($CNT!=0) {
			$PER = round($data[total]/$TOTAL*100,1);
			$GRP = round((100*$data[total])/$MAX,0);
		}
		else $PER = $GRP = 0;
		if($CNT2!=0) {
			$PER2 = round($data[total2]/$TOTAL2*100,1);
			$GRP2 = round((100*$data[total2])/$MAX,0);
		}
		else $PER2 = $GRP2 = 0;

		$cnts++;
		
    } else $CNT=$PER=$CNT2=$PER2=0; 
	$tpl->parse("loop");
}

if($cnts==0) $EVE=$EVE2=0;
else {
	$EVE = round($TOTAL/$cnts,1);
	$EVE2 = round($TOTAL2/$cnts,1);
}

$TOTAL = number_format($TOTAL);
$TOTAL2 = number_format($TOTAL2);

$TTL1 = "년별";
$tpl->parse("is_{$channel}");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>