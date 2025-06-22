<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

require "{$lib_path}/class.Template.php";

$code = "pboard_member";
$skin = ".";

$tpl = new classTemplate;
$tpl->define("main","member_level.html");
$tpl->scan_area("main");

$sql = "SELECT count(*) FROM pboard_member WHERE level = '1' && uid>1";
$MEMBER1 = number_format($mysql->get_one($sql));

for($i=2;$i<9;$i++){
	if($i%2 ==0) $BGCOLOR = "#efefef";
	else $BGCOLOR = "#ffffff";

	$sql = "SELECT count(*) FROM pboard_member WHERE level = '{$i}' && uid>1";
	$MEMBER = number_format($mysql->get_one($sql));

	$sql = "SELECT code FROM mall_design WHERE name='{$i}' && mode = 'L'";
	$tmps = $mysql->get_one($sql);
	
	if($tmps) {
		$tmps = explode("|",$tmps);
		$NAME = stripslashes($tmps[0]);
		${"SEC".$tmps[1]} = "selected";
		$SALE = stripslashes($tmps[2]);
		$POINT= stripslashes($tmps[3]);
		if($tmps[4]=='Y') $CARR = "checked";
		else $CARR = "";
	}	
	else {
		$SALE = $POINT = 0;
		$CARR = '';
		$NAME = "LV{$i}";
	}
	$tpl->parse("loop");
	$SEC1 = $SEC2 = $SEC3 = $SEC4 = $SEC5 = "";
}


$sql = "SELECT count(*) FROM pboard_member WHERE level = '9' && uid>1";
$MEMBER9 = number_format($mysql->get_one($sql));

$sql = "SELECT code FROM mall_design WHERE name='9' && mode = 'L'";
$tmps = $mysql->get_one($sql);
	
if($tmps) {
	$tmps = explode("|",$tmps);		
	$SALE9 = stripslashes($tmps[2]);
	$POINT9= stripslashes($tmps[3]);
	if($tmps[4]=='Y') $CARR9 = "checked";
}	
else {
	$SALE9 = $POINT9 = 0;
	$CARR9 = '';
}

$sql = "SELECT count(*) FROM pboard_member WHERE level = '10' && uid>1";
$MEMBER10 = number_format($mysql->get_one($sql));

$sql = "SELECT code FROM mall_design WHERE name='10' && mode = 'L'";
$tmps = $mysql->get_one($sql);
	
if($tmps) {
	$tmps = explode("|",$tmps);		
	$SALE10 = stripslashes($tmps[2]);
	$POINT10= stripslashes($tmps[3]);
	if($tmps[4]=='Y') $CARR10 = "checked";
}	
else {
	$SALE10 = $POINT10 = 0;
	$CARR10 = '';
}

for($i=1;$i<4;$i++) {
	$sql = "SELECT code FROM mall_design WHERE name='{$i}' && mode='P'";
	$tmps = $mysql->get_one($sql);
	if($tmps) {
		$tmps = explode("|",$tmps);
		for($j=1;$j<7;$j++) {
			if($tmps[($j-1)]==1) ${"PER".$i.$j} = "checked";
		}
	}
	else {
		if($i==2) $PER21 = "checked";
		else if($i==3) $PER31 = $PER33 = $PER34 = $PER35 = "checked";
	}
}

$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
?>