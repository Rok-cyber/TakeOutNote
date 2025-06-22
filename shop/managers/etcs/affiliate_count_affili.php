<?

$SELECT = "<select name=year>\n";
for($i=$s_year;$i<=$thisyear;$i++){
	if($i==$year) $SELECT .="<option value=$i selected>{$i}년</option>\n";
	else $SELECT .="<option value=$i>{$i}년</option>\n";
}
$SELECT .= "</select>\n";
    
			
$SELECT .= "<select name=month>\n";
for($i=1;$i<13;$i++){
	if($i==$month) $SELECT .="<option value=$i selected>{$i}월</option>\n";
	else $SELECT .="<option value=$i>{$i}월</option>\n";
}
$SELECT .= "</select>\n";
    	
$SELECT .= "<select name=day>\n";
for($i=1;$i<=31;$i++){
	if($i==$day) $SELECT .="<option value=$i selected>{$i}일</option>\n";
	else $SELECT .="<option value=$i>{$i}일</option>\n";
}
$SELECT .= "</select>\n";

$tpl->define("main","{$skin}/affiliate_count.html");
$tpl->scan_area("main");

$cnts=0;

$sql = "SELECT SUM(total) as total, SUM(total2) as total2 FROM pcount_list_affiliate WHERE year = '{$year}' && month = '{$month}'";
if(!$data = $mysql->one_row($sql)) $TOTAL=$TOTAL2 = 0;
else {
	$TOTAL = $data['total'];
	$TOTAL2 = $data['total2'];
}

$sql = "SELECT total2 FROM pcount_list_affiliate WHERE year = '{$year}' && month = '{$month}' ORDER BY total2 DESC LIMIT 1";
if(!$MAX = $mysql->get_one($sql)) $MAX=0;

$sql = "SELECT id FROM mall_affiliate ORDER BY id ASC";
$mysql->query($sql);

while($row = $mysql->fetch_array()){
	$TTL2 = "<font class=eng>{$row['id']}</font>";
	if($TOTAL !='0') {
		$sql = "SELECT SUM(total) as total, SUM(total2) as total2 FROM pcount_list_affiliate WHERE year = '{$year}'  && month = '{$month}' && day = '{$day}' && affiliate='{$row['id']}'";
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
		
    } else $CNT=$PER=$CNT2=$PER2=$GRP=$GRP2=0; 
	$tpl->parse("loop");	
}

$HOUR2 = date("H");

if($cnts==0) $EVE=$EVE2=0;
else {	
	$EVE = round($TOTAL/$cnts,1);
	$EVE2 = round($TOTAL2/$cnts,1);
}

$TOTAL = number_format($TOTAL);
$TOTAL2 = number_format($TOTAL2);

$TTL1 = "업체별";
$tpl->parse("search_form1");
$tpl->parse("is_{$channel}");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>