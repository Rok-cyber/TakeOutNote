<?
include "../ad_init.php";

require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;

$skin = ".";
$keyword = $_POST['keyword'];
$type = isset($_GET['type']) ? $_GET['type'] : $_POST['type'];
$tmps = '';

$tpl->define("main","{$skin}/search_member.html");
$tpl->scan_area("main");

if($type==1) $tpl->parse("is_man1");

if($keyword) {
	$sql = "SELECT name,id,tel,hphone FROM pboard_member WHERE INSTR(name,'{$keyword}') ORDER BY uid DESC";
	$mysql->query($sql);
	$i = 0;
	while($row = $mysql->fetch_array()){
		$MID = stripslashes($row['id']);
		$MNAME = stripslashes($row['name']);
		
		if($type==1) $MID2 = "{$MID}|{$MNAME}";
		else $MID2 = $MID;
		
		$DEL	= "<input type='checkbox' value='{$MID2}' name='item[]' onfocus='blur();'>";		
		if($row['hphone']) $MTEL = stripslashes($row['hphone']);
		else $MTEL = stripslashes($row['tel']);

		if($type==1) $tpl->parse("is_man2","1");
		$tpl->parse("loop");
		$i = 1;
	}
	if($i==0) $tpl->parse("no_loop");

	if($type==1) $tpl->parse("is_man3");
	
	$tpl->parse("is_search");
}
else $tpl->parse("is_default");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>