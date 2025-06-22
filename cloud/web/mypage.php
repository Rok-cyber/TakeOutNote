<?
include_once("./_common.php");
include_once("./_head.php");

function filter_email_address($email) {  
     $email = filter_var($email, FILTER_SANITIZE_EMAIL);  
  
     if(filter_var($email, FILTER_VALIDATE_EMAIL))   
         return TRUE;  
     else  
        return FALSE;  
}

$myinfo = sql_fetch_array(sql_query("SELECT * FROM tons_member WHERE mid = $MID"));

if($password && $password_re && $password == $password_re){
	$query = "UPDATE tons_member SET password = PASSWORD('$password') WHERE mid = $MID";
	sql_query($query);
}

if($email && $email != $myinfo['email'] && filter_email_address($email)){

	list($duplicate_count) = sql_fetch_one("SELECT COUNT(*) FROM tons_member WHERE email = '$email'");

	if($duplicate_count){
		alert("중복된 E-Mail이 있습니다.", "mypage.php");
	}

	$query = "UPDATE tons_member SET `status` = 'T', email = '$email' WHERE mid = $MID";
	sql_query($query);

	// 토큰 값 $data['token']
	$_MAIL['subject'] = "=?utf-8?B?".base64_encode("TakeOutNote 회원가입을 축하합니다.")."?=\n";
	$_MAIL['message'] = "
		TakeOutNote 회원가입을 축하 합니다.<br/>
		아래 인증URL을 클릭 하시면 TakeOutNote Cloud 서비스를 이용하실 수 있습니다.<br/><br/>

		인증코드 : <a href=\"http://cloud.takeoutnote.com/auth.php?key=".$MTOKEN."\">http://cloud.takeoutnote.com/auth.php?key=".$MTOKEN."</a>";
	$_MAIL['to'] = $email;
	$_MAIL['from'] = "master@takeoutnote.com";

	$_MAIL['headers']  = 'MIME-Version: 1.0' . "\r\n";
	$_MAIL['headers'] .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$_MAIL['headers'] .= 'To: $name <'.$_MAIL['to'].'>' . "\r\n";
	$_MAIL['headers'] .= 'From: TakeOutNote <master@takeoutnote.com>' . "\r\n";

	mail($_MAIL['to'], $_MAIL['subject'], $_MAIL['message'], $_MAIL['headers']);

	alert("E-Mail이 발송 되었습니다.", "index.php"); exit;

} else {
	if($email && $email != $myinfo['email']){
		alert("E-Mail 주소가 올바르지 않습니다!", "mypage.php"); exit;
	}
}

include_once("./include/mypage.php");
include_once("./_tail.php");
?>