<?
include "sub_init.php";

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if(!$my_id) alert('먼저 로그인을 하시기 바랍니다','back');

$signdate = date("Y-m-d H:i:s",time());
$todays	= date("Y-m-d");
$thisyear  = date('Y');  // 2000
$thismonth = date('n');  // 1, 2, 3, ..., 12
$today     = date('j');  // 1, 2, 3, ..., 31
$uid = $_GET['uid'];

if(!$uid) alert("정보가 넘어오지 못했습니다.","back");

if($_GET['mode']=='comment_del' && $my_level>8) {
	$sql = "DELETE FROM mall_attendance_comment WHERE uid='{$uid}'";
	$mysql->query($sql);
	
	$page = $_GET['page'];
	movePage("../{$Main}?channel=attendance&amp;page='{$page}'");
}

$sql = "SELECT * FROM mall_attendance WHERE uid='{$uid}'";
if(!$row = $mysql->one_row($sql)) alert("출석체크 이벤트가 종료 되었거나 존재하지 않습니다","back");

$msg1 = stripslashes($row['msg1']);
$msg2 = stripslashes($row['msg2']);
$msg3 = stripslashes($row['msg3']);
$msg4 = stripslashes($row['msg4']);

if(substr($row['s_date'],0,10)>$todays || substr($row['e_date'],0,10)<$todays) alert("출석체크 진행기간이 아닙니다","back");

$sql = "SELECT count(*) FROM mall_attendance_check WHERE id='{$my_id}' && year='{$thisyear}' && month='{$thismonth}' && day='{$today}' && puid='{$uid}'";
if($mysql->get_one($sql)>0) alert($msg2,"back");

if($row['method']=='R') {
	$access_ip	= $_SERVER['REMOTE_ADDR'];
	$signdate2	= time();
	$comment	= addslashes($_POST['comment']);
	
	if(!$comment) alert("정보가 넘어오지 못했습니다.","back");

	$sql = "INSERT mall_attendance_comment (puid,id,comment,acc_ip,signdate) VALUES ('{$uid}','{$my_id}','{$comment}','{$access_ip}','{$signdate2}')";
	$mysql->query($sql);
}

$prevyear  = date('Y',time()-86400);  // 2000
$prevmonth = date('n',time()-86400);  // 1, 2, 3, ..., 12
$prevday     = date('j',time()-86400);  // 1, 2, 3, ..., 31

$sql = "SELECT continuity FROM mall_attendance_check WHERE id='{$my_id}' && year='{$prevyear}' && month='{$prevmonth}' && day='{$prevday}' && puid='{$uid}'";
if($conti=$mysql->get_one($sql)) $conti++;
else $conti = 1;

$sql = "SELECT MAX(total) FROM mall_attendance_check WHERE id='{$my_id}' && puid='{$uid}'";
if($total=$mysql->get_one($sql)) $total++;
else $total = 1;

$sql = "INSERT INTO mall_attendance_check VALUES ('','{$uid}','{$my_id}','{$thisyear}','{$thismonth}','{$today}','{$total}','{$conti}',0,0)";
$mysql->query($sql);

$sql = "SELECT MAX(uid) FROM mall_attendance_check WHERE id='{$my_id}' && puid='{$uid}'";
$luid = $mysql->get_one($sql);

$subject = "[출석체크-".stripslashes($row['name'])."] 이벤트 성공 적립금 지급";
switch($row['type']) {
	case "A" : case "T" :
		if($row['type']=='T') {
			$sql = "SELECT count(*) FROM mall_attendance_check WHERE id='{$my_id}' && puid='{$uid}'";
			$conti = $mysql->get_one($sql);
		}
	
		if($conti==$row['condi']) {
			$sql = "SELECT count(*) FROM mall_attendance_check WHERE puid='{$uid}' && id='{$my_id}' && success=1";
			if($mysql->get_one($sql)==0) {
				if($row['stype']=='A') {
					$sql = "INSERT INTO mall_reserve VALUES ('','{$my_id}','{$subject}','{$row['point']}','','','B','{$signdate}')";
					$mysql->query($sql);
					
					$sql = "SELECT MAX(uid) FROM mall_reserve WHERE id='{$my_id}'";
					$ruid = $mysql->get_one($sql);
					
					$sql = "UPDATE pboard_member SET reserve = reserve + '{$row['point']}' WHERE id = '{$my_id}'"; 
					$mysql->query($sql);	
					
					$sql = "UPDATE mall_attendance_check SET success=1, reserve={$ruid} WHERE uid='{$luid}' && id='{$my_id}'";
					$mysql->query($sql);

					alert($msg3,"../{$Main}?channel=attendance");
				}
				else {
					$sql = "UPDATE mall_attendance_check SET success=1 WHERE uid='{$luid}' && id='{$my_id}' && puid='{$uid}'";
					$mysql->query($sql);
					
					alert($msg4,"../{$Main}?channel=attendance");
				}
			}
		}
		
		alert($msg1,"../{$Main}?channel=attendance");

	break;

	case "D" :
		if($row['stype']=='A') {
			$sql = "INSERT INTO mall_reserve VALUES ('','{$my_id}','{$subject}','{$row['point']}','','','B','{$signdate}')";
			$mysql->query($sql);
			
			$sql = "SELECT MAX(uid) FROM mall_reserve WHERE id='{$my_id}'";
			$ruid = $mysql->get_one($sql);

			$sql = "UPDATE pboard_member SET reserve = reserve + '{$row['point']}' WHERE id = '{$my_id}'"; 
			$mysql->query($sql);	
			
			$sql = "UPDATE mall_attendance_check SET success=1, reserve={$ruid} WHERE uid='{$luid}' && id='{$my_id}' && puid='{$uid}'";
			$mysql->query($sql);

			alert($msg3,"../{$Main}?channel=attendance");
		}
		else {
			$sql = "UPDATE mall_attendance_check SET success=1 WHERE uid='{$luid}' && id='{$my_id}' && puid='{$uid}'";
			$mysql->query($sql);
			
			alert($msg4,"../{$Main}?channel=attendance");
		}
	break;	
}


?>