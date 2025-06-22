<?
$tpl->define("main","{$skin}/cscenter.html");
$tpl->scan_area("main");

/************************* Notice & News ******************************/
$sql = "SELECT no,subject,signdate FROM pboard_notice WHERE no>1 && idx < 999 && idx > 0 ORDER BY no DESC limit 3";
$mysql->query($sql);

while($row = $mysql->fetch_array()){
    $NO = $row['no'];
	$SUBJECT = htmlspecialchars(stripslashes($row['subject'])); 
	$D_YYYY	= date("Y",$row['signdate']);
	$D_YY	= date("y",$row['signdate']);
	$D_MM	= date("m",$row['signdate']);
	$D_DD	= date("d",$row['signdate']);
	$tpl->parse("loop_notice");
}
/************************* Notice & News ******************************/

$sql = "SELECT code FROM mall_mobile WHERE mode='T'";
$tmps = $mysql->get_one($sql);
$center = explode("|*|",stripslashes($tmps));

$TEL = !empty($center[0]) ? $center[0] : $basic[7];
$FAX = !empty($center[1]) ? $center[1] : $basic[8];
$EMAIL = !empty($center[2]) ? $center[2] : $basic[10];
$TIME1 = !empty($center[3]) ? $center[3] : "09:00 ~ 18:00";
$TIME2 = !empty($center[4]) ? $center[4] : "09:00 ~ 13:00";
$TIME3 = !empty($center[5]) ? $center[5] : "휴무";
$TIME4 = !empty($center[6]) ? $center[6] : "12:00 ~ 13:00";

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>