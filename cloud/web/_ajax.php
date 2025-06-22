<?
include_once("_common.php");
require '../lib/PHPMailerAutoload.php';

switch($act):
	case "authResend":
		if($MEMAIL && $MTOKEN){

			if($newemail){
				$MEMAIL = $newemail;

				$query = "UPDATE tons_member SET email = '$MEMAIL' WHERE userid = '$USERID'";
				$result = sql_query($query, false);

				if($result){
					$_SESSION['tons']['email'] = $MEMAIL;
				}
			}

	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
	$mail->Charset = "utf-8";
	$mail->Encoding = "base64";
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 587;
	$mail->SMTPSecure = 'tls';
	$mail->SMTPAuth = true;
	$mail->Username = "service@takeoutnote.com";
	$mail->Password = "weg2jud3e";
	$mail->setFrom('service@takeoutnote.com', 'TakeOutNote');
	$mail->addReplyTo('service@takeoutnote.com', 'TakeOutNote');
	$mail->addAddress($MEMAIL, '');
	$mail->Subject = "=?utf-8?B?".base64_encode("TakeOutNote 회원가입을 축하합니다.")."?=\n";
	$_MAIL['message'] = "
		TakeOutNote 회원가입을 축하 합니다.<br/>
		아래 인증URL을 클릭 하시면 TakeOutNote Cloud 서비스를 이용하실 수 있습니다.<br/><br/>

		인증코드 : <a href=\"http://cloud.takeoutnote.com/auth.php?key=".$MTOKEN."\">http://cloud.takeoutnote.com/auth.php?key=".$MTOKEN."</a>";
	$mail->msgHTML($_MAIL['message']);
	$mail->AltBody = 'http://cloud.takeoutnote.com/auth.php?key='.$MTOKEN;

	if (!$mail->send()) {
		//echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		//echo "Message sent!";
	}


			// 토큰 값 $data['token']
			/*
			$_MAIL['subject'] = "=?utf-8?B?".base64_encode("TakeOutNote 회원가입을 축하합니다.")."?=\n";
			$_MAIL['message'] = "
				TakeOutNote 회원가입을 축하 합니다.<br/>
				아래 인증URL을 클릭 하시면 TakeOutNote Cloud 서비스를 이용하실 수 있습니다.<br/><br/>

				인증코드 : <a href=\"http://cloud.takeoutnote.com/auth.php?key=".$MTOKEN."\">http://cloud.takeoutnote.com/auth.php?key=".$MTOKEN."</a>";
			$_MAIL['to'] = $MEMAIL;
			$_MAIL['from'] = "master@takeoutnote.com";

			$_MAIL['headers']  = 'MIME-Version: 1.0' . "\r\n";
			$_MAIL['headers'] .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$_MAIL['headers'] .= 'To: $name <'.$_MAIL['to'].'>' . "\r\n";
			$_MAIL['headers'] .= 'From: TakeOutNote <master@takeoutnote.com>' . "\r\n";

			mail($_MAIL['to'], $_MAIL['subject'], $_MAIL['message'], $_MAIL['headers']);
			*/
		}
	break;

	case "notePublic":
		$_TMP['status'] = sql_fetch_one("SELECT public FROM tons_note WHERE nid = $nid");

		if($_TMP['status']=="Y"){
			$result = sql_query("UPDATE tons_note SET public = 'N' WHERE nid = $nid");
			if($result) echo "N";
		} elseif($_TMP['status']=="N"){
			$result = sql_query("UPDATE tons_note SET public = 'Y' WHERE nid = $nid");
			if($result) echo "Y";
		} else {
			echo "FALSE";
		}
	break;

	case "extraPublic":
		$_TMP['status'] = sql_fetch_one("SELECT public FROM tons_note_extra WHERE eid = $eid");

		if($_TMP['status']=="Y"){
			$result = sql_query("UPDATE tons_note_extra SET public = 'N' WHERE eid = $eid");
			if($result) echo "N";
		} elseif($_TMP['status']=="N"){
			$result = sql_query("UPDATE tons_note_extra SET public = 'Y' WHERE eid = $eid");
			if($result) echo "Y";
		} else {
			echo "FALSE";
		}
	break;

	case "replyWrite":
		$reply = urldecode($reply);
		$rid = sql_fetch_one("SELECT MAX(rid) FROM tons_reply"); $rid++;
		$query = "INSERT INTO tons_reply SET
			`rid` = $rid, `rpid` = $rid, `nid` = $nid,
			`mid` = $mid, `mname` = '$mname', 
			`reply` = '$reply', 
			regdate = NOW()";
		$result = sql_query($query);
		if($result){
			$return['result'] = "OK";
			$return['data'] = "
				<li class=\"basic\" rid=\"{$rid}\">
					<div class=\"profile\"><div class=\"icon\"></div></div>
					<div class=\"reply\">
						<div class=\"title\">
							<strong>{$mname}</strong>(".date("Y-m-d H:i:s").") 
							<!--<img src=\"imgs/icon/modify.png\" width=\"15\"/>-->
							<img src=\"imgs/icon/delete.png\" class=\"delete\" width=\"13\"/>
						</div>
						<div>{$reply}</div>
					</div>
				</li>
			";
		}
		echo json_encode($return);
	break;

	case "extraUpdate":

	break;

	case "extraDelete":
		$query = "DELETE FROM tons_reply WHERE `rid` = $rid AND `mid` = $MID";
		$result = sql_query($query);
		if($result) $return['result'] = "OK";
		echo json_encode($return);
	break;
endswitch;
?>