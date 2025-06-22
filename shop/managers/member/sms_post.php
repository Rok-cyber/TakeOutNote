<?
######################## lib include
include "../ad_init.php";
require "{$lib_path}/lib.Shop.php";

$field		= $_GET['field'];
$word		= $_GET['word'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$sectype	= $_GET['s_qty'];
$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$uid		= isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];

if($field && $word) $addstring = "&field=$field&word={$word}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($sectype) $addstring .="&sectype={$sectype}";

switch($mode) {
	case "auto" :
		$sql = "SELECT uid FROM mall_sms_auto WHERE code!='info' ORDER BY uid DESC";
		$mysql->query($sql);

		while($data = $mysql->fetch_Array()){
			$sql = "UPDATE mall_sms_auto SET message1 = '{$_POST['message1_'.$data['uid']]}', message2 = '{$_POST['message2_'.$data['uid']]}', chk_message1 = '{$_POST['chk_message1_'.$data['uid']]}', chk_message2 = '{$_POST['chk_message2_'.$data['uid']]}' WHERE uid='{$data['uid']}'";
			$mysql->query2($sql);
		}
		
		movePage("sms_autoform.php");
	break;

	case "conf" :
		$id = $_POST['sms_id'];
		if($_POST['sms_pw']) $pw = previlEncode($_POST['sms_pw']);
		else if($_POST['sms_pw2']) $pw = $_POST['sms_pw2'];
		$tel1 = $_POST['sms_tel11']."-".$_POST['sms_tel12']."-".$_POST['sms_tel13'];
		$tel2 = $_POST['sms_tel21']."-".$_POST['sms_tel22']."-".$_POST['sms_tel23'];
		
		if(!$id || !$pw) alert("정보가 제대로 넘어오지 못했습니다!","back");
		
		$message = "{$id}|{$pw}|{$tel1}|{$tel2}";
		$sql = "SELECT count(*) FROM mall_sms_auto WHERE code='info'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_sms_auto SET code='info', message1='{$message}'";
		}
		else {
			$sql = "UPDATE mall_sms_auto SET code='info', message1='{$message}' WHERE code='info'";
		}
		$mysql->query($sql);

		alert("SMS 기본정보를 설정 했습니다","sms_conf.html");
	break;
	
	case "send" :
		if($_POST['cell11'] && $_POST['cell12']) {
			$recv_num = $_POST['cell11'].$_POST['cell12'].$_POST['cell13'];
		}

		$message = addslashes($_POST['message']);
		
		switch($_POST['send_type']) {
			case "1" :
				$send_list = $_POST['sms_lists'];						
			break;
			case "2" : case "3" :
				if($_POST['send_type']==2) {
					$level = $_POST['level'];
					$where = " && level = '{$level}'";
				}
				$sql = "SELECT hphone FROM pboard_member WHERE hphone!='' && sms='Y' {$where}";
				$mysql->query($sql);
				$send_list = "";
				while($row = $mysql->fetch_array()){
					if($row['hphone']) {
						$row['hphone'] = str_replace(" - ","",$row['hphone']);
						if($send_list) $send_list .= ",{$row['hphone']}";
						else $send_list =$row['hphone'];
					}
				}
			break;
			case "4" : case "5" :
				if($_POST['send_type']==4) {
					$level = $_POST['groups'];
					$where = " && groups = '{$level}'";
				}
				$sql = "SELECT cell FROM mall_sms_addr WHERE uid!='' {$where}";
				$mysql->query($sql);
				$send_list = "";
				while($row = $mysql->fetch_array()){
					if($row['cell']) {
						$row['cell'] = str_replace("-","",$row['cell']);
						if($send_list) $send_list .= ",{$row['cell']}";
						else $send_list =$row['cell'];
					}
				}
			break;
			
		}

		if($_POST['reserv']==1) {
			$reservdate = $_POST['year'].sprintf("%02s", $_POST['month']).sprintf("%02s", $_POST['day']).sprintf("%02s", $_POST['hour']).sprintf("%02s", $_POST['min'])."01"; 
		}
		else $reservdate ='';
		
		if($_POST['sms_cnt']==2) {
			pmallSmsSend($send_list,$recv_num,$message,$_POST['send_type'],$reservdate,"Y");
		}
		else {
			pmallSmsSend($send_list,$recv_num,$message,$_POST['send_type'],$reservdate);
		}

		alert("SMS가 발송 되었습니다. SMS발송내역에서 확인 하시기 바랍니다.","sms_send.html");

	break;
}
?>