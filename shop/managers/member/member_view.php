<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

require "{$lib_path}/class.Template.php";

$code = "pboard_member";
$skin = ".";
$field		= $_GET['field'];
$word		= $_GET['word'];
$sdate1		= $_GET['sdate1'];
$sdate2		= $_GET['sdate2'];
$dates		= $_GET['dates'];
$auth		= $_GET['auth'];
$sex		= $_GET['sex'];
$mailling	= $_GET['mailling'];
$sms		= $_GET['sms'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$uid		= $_GET['uid'];
$levels		= $_GET['levels'];

##################### addstring ############################ 
if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($page) $addstring .="&page={$page}";
if($auth) $addstring .= "&auth={$auth}";
if($sex) $addstring .= "&sex={$sex}";
if($mailling) $addstring .= "&mailling={$mailling}";
if($sms) $addstring .= "&sms={$sms}";
if($dates && $sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}&dates={$dates}";	
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($levels) $addstring .="&levels={$levels}";

$MTform  = array('30','name','id','jumin1','jumin2','tel','hphone','zipcode','address','email','homepage','msn','birth','sex','marr','edu','hobby','job','jobname','info','mailling','icon','reserve','level','auth','add1','add2','add3','add4','add5','sms');

$is_arr = Array('','is_jumin','is_tel','is_phone','is_addr','is_email','is_homepage','is_mess','is_bir','is_sex','is_marr','is_edu','is_hobby','is_job','is_jobname','is_info','is_mailling','is_icon','is_add1','is_add2','is_add3','is_add4','is_add5');

$sql	= "SELECT address,info FROM pboard_member WHERE uid=1";
$data	= $mysql->one_row($sql);
$options	= explode("|",$data['address']);
$w_word = explode("|",stripslashes($data['info']));

$sql =  "SELECT * FROM $code WHERE uid = '$uid'";
$row = $mysql->one_row($sql);
   for($i=1;$i<=$MTform[0];$i++){  
         $fd = $MTform[$i];
		 ${"FORM".$i} = stripslashes($row[$fd]);      
   }

$FORM9 = "<a href='./mail_form.html?m_to=$FORM9' onfocus='this.blur();'>$FORM9</a>";

if($FORM10) {
	 if(!eregi("http://",$FORM10)) $FORM10="http://".$FORM10;
	$FORM10 = "<a href='$FORM10' onfocus='this.blur();' target='ablink'>$FORM10</a>";
}
if($FORM12) { 
	$bir = explode("|",$FORM12);
	$FORM12 = "$bir[0]년 $bir[1]월 $bir[2]일 ($bir[3])";
}
if($FORM13) { 
	if($FORM13 == 'M') $FORM13 = "남자";
	else if($FORM13 == 'F') $FORM13 = "여자";
	else $FORM13 = "";
}
if($FORM14) { 
	if($FORM14 == 'N') $FORM14 = "미혼";
	else $FORM14 = "기혼";
}
if($FORM20) { 
	${"CKDM".$FORM20} = "checked";
	if($FORM20 == 'N') $FORM20 = "메일수신 허용안함";
	else $FORM20 = "메일수신 허용함";
}
if($FORM21) $FORM21 = "<img src='../../icon/$FORM21' align=absmiddle>";

if($FORM30) { 
	${"CKDS".$FORM30} = "checked";
	if($FORM30 == 'N') $FORM30 = "SMS수신 허용안함";
	else $FORM30 = "SMS수신 허용함";
}

$FORM31 = $LV[$row['level']];
$level = $row['level'];

$ACTION = "./member_ok.php?mode=modify&uid={$uid}{$addstring}";
$LIST = "member_list.php?{$addstring}";

$tpl = new classTemplate;
$tpl->define("main","member_view.html");
$tpl->scan_area("main");

for($i=1;$i<18;$i++){
	if($options[$i] == '1' || $options[$i] == '2') {		
		$tpl->parse($is_arr[$i]);
	}
}

for($i=21;$i<26;$i++) {
	if($options[$i] == '1' || $options[$i] == '2') {
		${"TADD".($i-20)} = $w_word[$i-16];					
		$tpl->parse($is_arr[$i-3]);		
    }
}

if($options[26] == '1' || $options[26] == '2') {		
	$tpl->parse("is_sms");
}

$sql = "SELECT name, code FROM mall_design WHERE mode='L' && name!='10' ORDER BY name ASC";
$mysql->query($sql);

for($i=2;$i<9;$i++) {
	$row2 = $mysql->fetch_array();

	if($row['level']==$i) $sec = "selected";
	else $sec = '';

	while($row2['name']!=$i) {
		$LEVEL .= "<option value='{$i}' {$sec}>LV{$i}</option>";
		if($i==8) break;
		$i++;
	}
	if($row2['name']==$i) {
		$tmps = explode("|",$row2['code']);
		$LEVEL .= "<option value='{$i}' {$sec}>".stripslashes($tmps[0])."</option>";		
	}
}

$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
?>