<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

require "{$lib_path}/class.Template.php";

$code = "pboard_member";
$skin = ".";
$field	= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word	= isset($_GET['word']) ? $_GET['word'] : $_POST['word'];
$page	= isset($_GET['page']) ? $_GET['page']:1;
$uid	= $_GET['uid'];

##################### addstring ############################ 
if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($page) $addstring .="&page={$page}";

$tpl = new classTemplate;
$tpl->define("main","sms_autoform.html");
$tpl->scan_area("main");

$sql = "SELECT * FROM mall_sms_auto WHERE code!='info' ORDER BY uid ASC";
$mysql->query($sql);

$i=0;
while($data = $mysql->fetch_Array()){
	$UID = $data['uid'];
	$TITLE = $data['title'];
	$MSG1 = stripslashes($data['message1']);
	$MSG2 = stripslashes($data['message2']);

	if($data['chk_message1']==1) $CKD1_1 = "checked";
	else $CKD1_1 = "";
	if($data['chk_message2']==1) $CKD2_1 = "checked";
	else $CKD2_1 = "";

	if($data['c_only']==1) { $ONLY1= "2"; $DISA1 = "disabled"; }
	else $DISA1 = $ONLY1 = "";

	if($data['c_only']==2) { $ONLY2 = "2" ; $DISA2 = "disabled"; }
	else $DISA2 = $ONLY2 = "";
	
	if($i%2==0 && $i>0) $tpl->parse("is_tr","1");

	$tpl->parse("loop_sms");
	$tpl->parse("is_tr","2");
	$i++;
}

$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
//insert into mall_sms_auto values('','soldout','상품품절시 발송','','','0','1','1');
?>
