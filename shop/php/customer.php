<?
include "$skin/skin_define.php";

$tpl->define("main","{$skin}/customer.html");
$tpl->scan_area("main");

/************************* faq ******************************/
$sql  = "SELECT category FROM pboard_manager WHERE name = 'faq'";
$cates = $mysql->get_one($sql);
$cates	= explode("|",$cates);

$sql = "SELECT a.subject, a.cate, b.comment FROM pboard_faq  a, pboard_faq_body b WHERE a.no>1 && a.no=b.no ORDER BY a.hit DESC limit {$SKIN_DEFINE['cus_faq']}";
$mysql->query($sql);

for($i=0;$i<$SKIN_DEFINE['cus_faq'];$i++) {	
	if($row = $mysql->fetch_array()){	   		
		$SUBJECT = htmlspecialchars(stripslashes($row['subject'])); 		
		$ANSWER = nl2br(stripslashes($row['comment'])); 	
		$FCATE = $cates[$row['cate']];
	}
	else {		
		$SUBJECT = $ANSWER = $FCATE = '';
	}
	$NO = $i+1;	
	$tpl->parse("loop_faq");
	$tpl->parse("loop_faq2");
}
/************************* faq ******************************/

/************************* Notice & News ******************************/
$sql = "SELECT no,subject FROM pboard_notice WHERE no>1 && idx < 999 && idx > 0 ORDER BY no DESC limit {$SKIN_DEFINE['cus_notice']}";
$mysql->query($sql);

for($i=0;$i<$SKIN_DEFINE['cus_notice'];$i++) {
	if($row = $mysql->fetch_array()){
	    $NO = $row['no'];
		$SUBJECT = htmlspecialchars(stripslashes($row['subject'])); 		
	}
	else $NO = $SUBJECT = '';
	$tpl->parse("loop_notice");
}
/************************* Notice & News ******************************/

/************************* Customer Board ******************************/
$sql = "SELECT no,subject FROM pboard_customer WHERE no>1 && idx < 999 && idx > 0 ORDER BY no DESC limit {$SKIN_DEFINE['cus_customer']}";
$mysql->query($sql);

for($i=0;$i<$SKIN_DEFINE['cus_customer'];$i++) {
	if($row = $mysql->fetch_array()){
	    $NO = $row['no'];
		$SUBJECT = htmlspecialchars(stripslashes($row['subject'])); 		
	}
	else $NO = $SUBJECT = '';
	$tpl->parse("loop_customer");
}
/************************* Customer Board ******************************/

if($my_id) $tpl->parse("is_logins");
else $tpl->parse("is_logouts");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>