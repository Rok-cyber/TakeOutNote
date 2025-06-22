<?
######################## lib include
include "../ad_init.php";
require "{$lib_path}/lib.Shop.php";

$field		= $_GET['field'];
$word		= $_GET['word'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$status		= $_GET['status'];
$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$gid		= isset($_POST['gid']) ? $_POST['gid'] : $_GET['gid'];
$signdate	= time();

if($field && $word) $addstring = "&field=$field&word={$word}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($status) $addstring .="&status={$status}";
if($gid) $addstring .="&gid={$gid}";

switch($mode) {
	case 'del' :
		$item = $_POST['item'];		
		if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {		
			$sql = "DELETE FROM mall_cooperate WHERE uid='{$item[$i]}'";
			$mysql->query($sql);					
		} 
		movePage("coop_participation_list.php?{$addstring}");
	break; 

	case "sms" :
		$message = addslashes($_POST['message']);
		if(!$message)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		
		$sql = "SELECT cell FROM mall_cooperate WHERE guid='{$gid}' && status='A'";
		$mysql->query($sql);
		$send_list = "";
		while($row = $mysql->fetch_array()){
			if($row['cell']) {
				$row['cell'] = str_replace(array("-"," "),"",$row['cell']);
				if($send_list) $send_list .= ",{$row['cell']}";
				else $send_list =$row['cell'];
			}
		}	
		
		$sql = "SELECT message1 FROM mall_sms_auto WHERE code='info'";
		$row = $mysql->get_one($sql);
		$row = explode("|",stripslashes($row));
		$callback = str_replace("-","",$row[2]);
		
		if(!$callback) alert("회신전화번호가 등록되지 않았습니다. sms설정에서 먼저 등록 하시기 바랍니다.","back");
		if($_POST['sms_cnt']==2) {
			pmallSmsSend($send_list,$callback,$message,6,$reservdate,"Y");
		}
		else {
			pmallSmsSend($send_list,$callback,$message,$_POST['send_type'],$reservdate);
		}
		alert("SMS가 발송 되었습니다. SMS발송내역에서 확인 하시기 바랍니다.","close5");
	break;

	case "mail" :
		$subject	= $_POST['subject'];
		$content	= $_POST['message'];
		$content = nl2br($content);
		if(!$subject || !$content)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		############ 메일 보내기전 정의 ############
		$URL = "http://".$_SERVER["SERVER_NAME"];		
		$sql = "SELECT code FROM mall_design WHERE mode = 'F'";
		$mail_img = explode("|*|",stripslashes($mysql->get_one($sql)));	
		$skin_path = "../../";
		$MAIL_IMG = "<a href='{$URL}' target='_blank'><img src='{$URL}/image/design/{$mail_img[0]}' width='760' border='0' alt='Mail Image' /></a>";	
		$MAIL_COMMENT = stripslashes($content);
		include "../../php/mail_form.php";   //메일 양식 인클루드

		############ 상품발송 메일 보내기전 정의 ############

		$sql = "SELECT email FROM mall_cooperate WHERE guid='{$gid}' && status='A'";
		$mysql->query($sql);
		$cnt1 = $cnt2 = 0;		
		while($row = $mysql->fetch_array()){
			if(pmallMailSend($row['email'], $subject, $mail_form)) $cnt1++;
			else $cnt2++;
		}
		
		$e_time = time();
		$error_log = addslashes($error_log);

		$sql = "INSERT INTO pboard_maillog VALUES ('','공구 신청자발송','{$subject}','{$content}','{$cnt2}','{$cnt1}','1','1','','{$error_log}','{$signdate}','{$e_time}','{$signdate}')";
		$mysql->query($sql);
		alert("{$cnt1}건의 메일을 성공적으로 발송했습니다.\\n 메일발송내역을 확인해 보세요","close5");

	break;
}

?>