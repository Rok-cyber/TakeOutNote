<?
include "../ad_init.php";

$skin = ".";
######################## lib include
include "{$skin}/lun2sol.php";   //양음변환 인클루드
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

$tpl = new classTemplate;
$tpl->define("main","{$skin}/schedule_conf.html");
$tpl->scan_area("main");    

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$record_num = 9;
$page_num = 6;

$sql = "SELECT COUNT(*) FROM mall_schedule_date WHERE uid!=''";
$TOTAL = $mysql->get_one($sql);

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
if($TOTAL <= ($page * $record_num)) $TONUM = $TOTAL;
else $TONUM = $record_num; 
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

$sql = "SELECT * FROM mall_schedule_date WHERE uid!='' ORDER BY date ASC LIMIT {$Pstart},{$record_num}";
$mysql->query($sql);

while($data=$mysql->fetch_array()){
	$tmp = explode("-",$data[date]);
	$DATE = "<font class='eng'>{$tmp[0]}</font><font class='small'>년</font><font class='eng'>{$tmp[1]}</font><font class='small'>월</font><font class='eng'>{$tmp[2]}</font><font class='small'>일</font>";  
	if($data[sm]=='M') $SM = "-";
	else $SM = "+";
	$NAME = stripslashes($data[subject]);
	$MOD = "'{$data['uid']}','{$data['date']}','{$NAME}','{$data['sm']}'";
	$DEL = "'{$data['uid']}'";
	$tpl->parse("loop");
}

if($TOTAL>0) {
	$pg = new paging($TOTAL,$page,$record_num,$page_num);
	$pg->addQueryString("?snum={$snum}"); 
	$PAGING = $pg->print_page();  //페이징 
	$tpl->parse("is_paging");
} 
else $tpl->parse("no_loop");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>