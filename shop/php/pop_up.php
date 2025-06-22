<?
include "sub_init.php";

require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;

$skin2 = $skin."/";
$uid = $_GET['uid'];

if(!$uid) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도 하시기 바랍니다!','close');

$sql = "SELECT subject,days,comment FROM mall_popup WHERE uid='{$uid}'";
$row = $mysql->one_row($sql);

$TITLE = stripslashes($row['subject']);
//$TITLE = iconv("euc-kr","utf-8",$TITLE);
$POP_NAME = "pop_".$uid;
$PCODE = stripslashes($row['comment']);
//$PCODE = iconv("euc-kr","utf-8",$PCODE);

$tpl->define("main","{$skin}/pop_up.html");
$tpl->scan_area("main");

if($_GET['type']==2) {
	$close = "parent.pPopupBoxObj.hide();";
}
else {
	$close = "window.close();";
}
if($row['days'] =='1') $tpl->parse("is_days");
else $tpl->parse("is_close");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>