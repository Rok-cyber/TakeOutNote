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

$tpl->define("main","{$skin}/count_type1.html");
$tpl->scan_area("main");

$cnts=24;

$sql = "SELECT * FROM pcount_list WHERE year = '$year' && month = '$month' && day='$day'";
if(!$data = $mysql->one_row($sql)) $TOTAL=$TOTAL2 = 0;
else {
	$TOTAL = $data[total];
	$TOTAL2 = $data[total2];
}

$MAX=0;
for($i=0;$i<24;$i++){
    if($i<10) $i3 = "h2_0".$i; 
	else $i3 = "h2_".$i;

	if($data[$i3] > $MAX) $MAX = $data[$i3];
}


for($i=0;$i<24;$i++){
	$TTL2 = $i."<font class=small>시</font> ~ ".($i+1)."<font class=small>시</font>";
			
	if($i<10) { $i2 = "h_0".$i; $i3 = "h2_0".$i; }
	else { $i2 = "h_".$i; $i3 = "h2_".$i; }

	$CNT = number_format($data[$i2]);
	$CNT2 = number_format($data[$i3]);	
	if($TOTAL!=0) { 
		$PER = round($data[$i2]/$data[total]*100,1);					
		$GRP = round((100*$data[$i2])/$MAX,0);
	} else $PER=$GRP=0;
	if($TOTAL2!=0) {
		$PER2 = round($data[$i3]/$data[total2]*100,1);					
		$GRP2 = round((100*$data[$i3])/$MAX,0);
	} else $PER2=$GRP2=0;
        
	
    
	$tpl->parse("loop");
}

$HOUR2 = date("H");
if($thisyear==$year  && $thismonth==$month && $today==$day) $cnts = $HOUR2;

if($cnts==0) $EVE=$EVE2=0;
else {	
	$EVE = round($TOTAL/$cnts,1);
	$EVE2 = round($TOTAL2/$cnts,1);
}

$TOTAL = number_format($TOTAL);
$TOTAL2 = number_format($TOTAL2);

$TTL1 = "시간별";
$tpl->parse("search_form");
$tpl->parse("is_{$channel}");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>