<?
include "sub_init.php";

$name	= addslashes($_POST['name']);
$id		= addslashes($_POST['id']);
$email	= addslashes($_POST['email']);

if(!$name || !$email) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');

$sql = "SELECT count(*) FROM pboard_member WHERE name = '{$name}' && id='{$id}' && email='{$email}'";
if($mysql->get_one($sql)==0) {
	echo "
	<script>
		parent.document.getElementById('viewPw2').style.display='none';
		parent.document.getElementById('viewPw1').style.display='block';
	</script>
	";
	exit;
}
else {	
	
	############ 임시 비번호로 변경 ##################
	$temp_passwd =getCode('4').substr(base64_encode(time()),1,6);  //임시 비밀번호
	$sql = "UPDATE pboard_member SET passwd = '".md5($temp_passwd)."' WHERE id = '{$id}'";
	$mysql->query($sql);

	############ 메일 보내기 ############
	$URL = "http://".$_SERVER["SERVER_NAME"];		
	$sql = "SELECT code FROM mall_design WHERE mode = 'F'";
	$mail_img = explode("|*|",stripslashes($mysql->get_one($sql)));	
	$mail_type = "pwsearch";
	if($mail_img[0]) $MAIL_IMG = "<a href='{$URL}' target='_blank'><img src='{$URL}/image/design/{$mail_img[0]}' width='760' border='0' alt='Mail Image' /></a>";	
	$MAIL_COMMENT = "
			{$id} 님의 회원 아이디와 새롭게 발급된 임시 비밀번호입니다. <br />
			확인후 곧 바로 <a href='{$URL}' onfocus='this.blur();'>{$URL}</a> 에 로그인 하셔서 비밀번호를 변경하여 주시기 바랍니다.<br /><br />
			&nbsp;&nbsp;&nbsp;&nbsp;<b>ID : {$id}</b><br />
			&nbsp;&nbsp;&nbsp;&nbsp;<b>Password : {$temp_passwd}</b><br /><br />
			* 위의 비밀번호를 타이핑하기 힘들때 마우스로 더블클릭한후 Ctrl-C 를 눌러서 복사한후, 비밀번호 입력칸에서 <br>
		    &nbsp;&nbsp;&nbsp;Ctrl-V를 눌러서 복사하세요.
			";	
	include "mail_form.php";   //메일 양식 인클루드
	pmallMailSend($email, "{$basic[1]} 임시비밀번호가 발급 되었습니다.", $mail_form);	
	############ 메일 보내기 ############

	############ SMS 보내기 #############
	$sql = "SELECT hphone FROM pboard_member WHERE id='{$id}'";
	if($hphone=$mysql->get_one($sql)) {
		$code_arr = Array();
		$code_arr['name'] = $name;
		$code_arr['password'] = $temp_passwd;
		pmallSmsAutoSend($hphone,"pwsearch",$code_arr);
	}
	############ SMS 보내기 #############


	echo "
	<script>
		parent.document.getElementById('viewPw1').style.display='none';
		parent.document.getElementById('viewPw2').style.display='block';
	</script>
	";
	exit;

}

include "close.php";
?>

