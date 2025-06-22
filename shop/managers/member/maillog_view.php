<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

$mysql = new  mysqlClass(); //디비 클래스

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? $_GET['word'] : $_POST['word'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];

$code	= "pboard_maillog";
$uid	= $_GET['uid'];
$skin	= ".";
$MTform = array('12','m_to','subject','content','m_true','m_false','s_time','e_time','err_log','signdate','m_total','m_cnt','m_search');


if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($limit) $addstring .= "&limit={$limit}";
if($page) $addstring .="&page={$page}";

$sql =  "SELECT * FROM {$code} WHERE uid = '{$uid}'";
$row = $mysql->one_row($sql);
for($i=1;$i<=$MTform[0];$i++){  
    $fd = $MTform[$i];
	${"FORM".$i} = stripslashes($row[$fd]);      
}

$DATE = date("Y년 m월 d일 h시 i분 s초",$row[signdate]);
$FORM3 = nl2br($FORM3);
$FORM4 = "전체 {$FORM5}건 중 {$FORM4}건 성공, ".($FORM5-$FORM4)."건 실패";
$FORM5 = date("Y년 m월 d일 h시 i분 s초",$FORM6)." ~ ".date("Y년 m월 d일 h시 i분 s초",$FORM7)." : ".round((($FORM7 - $FORM6)), 3)."초";
$FORM6 = nl2br($FORM8);

$tpl = new classTemplate;
$tpl->define("main","./maillog_view.html");
$tpl->scan_area("main");

if($row['m_total']!=$row['m_cnt']) {
	$FORM7 = "전송 중단";
	if($row['m_to'] =='Search Mailling') $type = 'all2';
	else $type = 'all';

	$SEND = "mail_ok.php?uid={$uid}&type={$type}{$row['m_search']}";
	$tpl->parse("is_send");
}
else $FORM7 = "전송완료";

$LIST = "maillog_list.php?{$addstring}";


$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
?>





	