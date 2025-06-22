<?
######################## lib include
include "../ad_init.php";

$field		= $_GET['field'];
$word		= $_GET['word'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$status		= $_GET['status'];
$type		= $_GET['type'];
$stype		= $_GET['stype'];
$method		= $_GET['method'];
$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$uid		= isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];

if($field && $word) $addstring = "&field=$field&word={$word}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($status) $addstring .="&status={$status}";
if($type) $addstring .="&type={$type}";
if($stype) $addstring .="&stype={$stype}";
if($method) $addstring .="&method={$method}";

$signdate = time();
$dir = "../../image/attendance";

if($mode=="reserve") {
	$field2		= $_GET['field2'];
	$word2		= $_GET['word2'];
	$status1		= $_GET['status1'];
	$status2		= $_GET['status2'];

	if($field2 && $word2) $addstring .= "&field2=$field2&word2={$word2}";
	if($status1) $addstring .="&status1={$status1}";
	if($status2) $addstring .="&status2={$status2}";
	
	$signdate = date("Y-m-d H:i:s",time());
	$title = addslashes($_POST['title']);
	$reserve = addslashes($_POST['reserve']);
	
	if(!$uid || !$title || !$reserve) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		
	$item = isset($_POST['item'])? $_POST['item']:'';
	for($i=0,$cnt=count($item);$i<$cnt;$i++) {
		$sql = "INSERT INTO mall_reserve VALUES ('','{$item[$i]}','{$title}','{$reserve}','','','B','{$signdate}')";
		$mysql->query($sql);
		
		$sql = "SELECT MAX(uid) FROM mall_reserve WHERE id='{$item[$i]}'";
		$ruid = $mysql->get_one($sql);

		$sql = "UPDATE pboard_member SET reserve = reserve + '{$reserve}' WHERE id = '{$item[$i]}'"; 
		$mysql->query($sql);	

		$sql = "SELECT MAX(uid) FROM mall_attendance_check WHERE id='{$item[$i]}' && puid='{$uid}'";
		$luid = $mysql->get_one($sql);
		
		$sql = "UPDATE mall_attendance_check SET success=1, reserve={$ruid} WHERE uid='{$luid}' && id='{$item[$i]}' && puid='{$uid}'";
		$mysql->query($sql);		
	} 	

	alert("적립금이 지급 되었습니다.","attendance_check_list.php?{$addstring}");
}

if($mode=='write' || $mode=='modify') {
	$title = addslashes($_POST['title']);
	$s_date = addslashes($_POST['s_date']);
	$e_date = addslashes($_POST['e_date']);

	if($s_date>$e_date) {
		$tmps = $s_date;
		$s_date = $e_date;
		$e_date = $tmps;
	}
    
	if($mode=='modify') $where = " && uid!='{$uid}'";
	else $where = ''; 

	$sql = "SELECT count(*) FROM mall_attendance WHERE s_date <= '{$s_date}' && e_date >='{$s_date}' {$where}";
	if($mysql->get_one($sql)>0) alert("다른 이벤트와 기간이 중복되었습니다. 다시 확인 하시기 바랍니다","back");

	$sql = "SELECT count(*) FROM mall_attendance WHERE s_date <= '{$e_date}' && e_date >='{$e_date}' {$where}";
	if($mysql->get_one($sql)>0) alert("다른 이벤트와 기간이 중복되었습니다. 다시 확인 하시기 바랍니다","back");

	$type = addslashes($_POST['type']);
	if($type=='A') $condi = addslashes($_POST['condi1']);
	else if($type=='T') $condi = addslashes($_POST['condi2']);
	else $condi = '';
	$stype = addslashes($_POST['stype']);
	$point = addslashes($_POST['point']);
	$method = addslashes($_POST['method']);
	$msg1 = addslashes($_POST['msg1']);
	$msg2 = addslashes($_POST['msg2']);
	$msg3 = addslashes($_POST['msg3']);
	$msg4 = addslashes($_POST['msg4']);
	$code = addslashes($_POST['code']);

	if($_POST['code_use']=='Y') $code_use = 'Y';
	else $code_use = 'N';	
}

switch($mode) {	
	case "del" :
		$item = $_POST['item'];		
		if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			$sql = "SELECT img FROM mall_attendance WHERE uid='{$item[$i]}'";
			$tmp_img = $mysql->get_one($sql);
			if($tmp_img) @unlink("{$dir}/".$tmp_img); 

			$sql = "DELETE FROM mall_attendance WHERE uid='{$item[$i]}'";
			$mysql->query($sql);	
			
			$sql = "DELETE FROM mall_attendance_check WHERE puid='{$item[$i]}'";
			$mysql->query($sql);	

			$sql = "DELETE FROM mall_attendance_comment WHERE puid='{$item[$i]}'";
			$mysql->query($sql);	
		} 
		movePage("attendance_list.php?{$addstring}");
	break; 

	case "write" :	
		if(!$title || !$s_date || !$e_date || !$type || !$method || !$stype) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		
		if(!eregi("none",$_FILES['img']['tmp_name']) && $_FILES['img']['tmp_name']) {									
			$img = upFile($_FILES['img']['tmp_name'],$_FILES['img']['name'],$dir,'','true');
		}
		else $img = '';

		$sql = "INSERT INTO mall_attendance VALUES('','{$title}','{$s_date}','{$e_date}','{$type}','{$condi}','{$point}','{$stype}','{$method}',0,'{$msg1}','{$msg2}','{$msg3}','{$msg4}','{$img}','{$code_use}','{$code}','{$signdate}')";
		$msg = "출석체크가 등록되었습니다.";		
	break;

	case "modify" :
		if(!$uid || !$s_date || !$e_date || !$type || !$method || !$stype) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		if(!eregi("none",$_FILES['img']['tmp_name']) && $_FILES['img']['tmp_name']) {									
			$img = upFile($_FILES['img']['tmp_name'],$_FILES['img']['name'],$dir,'','true');
			$img = " , img='{$img}'";
			$sql = "SELECT img FROM mall_attendance WHERE uid='{$uid}'";
			$tmp_img = $mysql->get_one($sql);
			if($tmp_img) @unlink("{$dir}/".$tmp_img); 
		}
		else $img = '';

		$sql = "UPDATE mall_attendance SET title='{$title}', s_date='{$s_date}', e_date='{$e_date}', type='{$type}', condi='{$condi}', point='{$point}', stype='{$stype}', method='{$method}', msg1='{$msg1}', msg2='{$msg2}', msg3='{$msg3}', msg4='{$msg4}', code_use='{$code_use}', code='{$code}' {$img} WHERE uid='{$uid}'";
		$msg = "출석체크가 수정되었습니다.";

	break;

	default : alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

}
$mysql->query($sql);
alert($msg,"attendance_list.php?{$addstring}");

?>