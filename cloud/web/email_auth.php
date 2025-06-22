<?
include_once("_common.php");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title>:: TakeOutNote Cloud ::</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/_function.js"></script>

	<style type="text/css">
	html {width: 100%;height: 100%;}
	body {margin: 0;padding: 0;font-size: 12px;width: 100%;height: 100%;font-family: arial;}
	#tail {position: fixed;bottom: 0;width: 100%;height: 50%;background: #333;z-index: 1;}
	#loginWrap {
		position: fixed;top: 50%;left: 50%;margin: -150px 0 0 -150px;width: 300px;height: 250px;
		z-index: 2;
	}
	#loginWrap div.title {width: 100%;height: 35px;font-size: 20px;padding-top: 15px;font-weight: bold;text-align: center;}
	#loginWrap div.wrap {
		position: relative;
		width: 100%;height: 175px;
		padding-top: 25px;
		-webkit-border-top-left-radius:15px;
		-moz-border-radius-topleft:15px;
		border-top-left-radius:15px;
		-webkit-border-top-right-radius:15px;
		-moz-border-radius-topright:15px;
		border-top-right-radius:15px;
		-webkit-border-bottom-right-radius:15px;
		-moz-border-radius-bottomright:15px;
		border-bottom-right-radius:15px;
		-webkit-border-bottom-left-radius:15px;
		-moz-border-radius-bottomleft:15px;
		border-bottom-left-radius:15px;
		border: solid 2px #f1f1f1;
		background-color: #fff;
	}

	#loginWrap div.row {position: relative;margin-bottom: 20px;height: 40px;}
	#loginWrap div.row .message {}
	#loginWrap div.row .message p {margin: 3px 0;padding: 0;text-align: center;}
	#loginWrap .row label {position: absolute; left: 25px; top: 13px;color: #000;font-weight: bold;z-index: 3;}
	#loginWrap .row input {position: absolute; left: 15px; top: 0;background-color: transparent;}
	#loginWrap .row input.text.basic {
		width: 260px; height: 40px;
		font-size: 20px;
		line-height: 40px;
		padding-left: 10px;
		font-weight: bold;
		-webkit-border-top-left-radius:5px;
		-moz-border-radius-topleft:5px;
		border-top-left-radius:5px;
		-webkit-border-top-right-radius:5px;
		-moz-border-radius-topright:5px;
		border-top-right-radius:5px;
		-webkit-border-bottom-right-radius:5px;
		-moz-border-radius-bottomright:5px;
		border-bottom-right-radius:5px;
		-webkit-border-bottom-left-radius:5px;
		-moz-border-radius-bottomleft:5px;
		border-bottom-left-radius:5px;
		border: 0;
		background: #f1f1f1;
	}
	#loginWrap .submit {
		border: 0;
		width: 270px; height: 40px;
		background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #55adcd), color-stop(1, #3d87b6) );
		background:-moz-linear-gradient( center top, #55adcd 5%, #3d87b6 100% );
		filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#55adcd', endColorstr='#3d87b6');
		background-color:#55adcd;
		text-shadow:1px 1px 0px #4a7dab;
		color: #fff;
	}


	.round, .button {
		-webkit-border-top-left-radius:3px;
		-moz-border-radius-topleft:3px;
		border-top-left-radius:3px;
		-webkit-border-top-right-radius:3px;
		-moz-border-radius-topright:3px;
		border-top-right-radius:3px;
		-webkit-border-bottom-right-radius:3px;
		-moz-border-radius-bottomright:3px;
		border-bottom-right-radius:3px;
		-webkit-border-bottom-left-radius:3px;
		-moz-border-radius-bottomleft:3px;
		border-bottom-left-radius:3px;
	}

	.button { cursor: pointer; }

	</style>
</head>
<body>

<div id="loginWrap">
<div class="title">TakeOutNote Cloud Beta</div>
<form name="loginForm" id="loginForm" method="POST" onSubmit="return false;">
	<div class="wrap">
		<div class="row">
			<div class="message" id="auth_message">
				<p><strong>[<?=$USERID?>]</strong> ID는 인증이 필요합니다.</p>
				<p><strong><?=$MEMAIL?></strong></p>
				<p id="ing" style="display: none;color: #DC143C;font-weight: bold;">전송중입니다. 잠시만 기다려 주세요.</p>
			</div>
		</div>
		<div class="row" id="re_email">
			<input type="button" value="인증 E-Mail 재발송" class="button submit" onClick="$('#ing').css('display','block');auth_re();"/>
		</div>
		<div class="row" id="new_email" style="display: none;">
			<label for="new_email_input">새로운 E-Mail주소를 입력해 주세요</label>
			<input type="text" name="new_email" id="new_email_input" class="text basic" value=""/>
		</div>
		<div class="row">
			<input type="button" value="인증 E-Mail 주소 변경" class="button submit" onClick="auth_change();"/>
		</div>
	</div>
</form>
</div>

<div id="tail"></div>

</body>
</html>

<script type="text/javascript">

function auth_re(){
	$("#ing").css('display', 'block');
	alert("E-Mail 전송 중입니다. 잠시만 기다려 주세요!");
	getData("_ajax.php?act=authResend");
	alert("E-Mail이 발송되었습니다. 메일함을 확인해 주세요!");
	$("#ing").css('display', 'none');
}

function IsEmail(email) {
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(!regex.test(email)) {
	   return false;
	}else{
	   return true;
	}
}

function auth_change(){
	if($("#new_email").css("display") == "none"){
		$("#re_email").css("display", "none");
		$("#new_email").css("display", "block");
		$("#new_email_input").focus();
		$("#auth_message").html("<p><strong>E-Mail주소 입력 후 변경 버튼을 눌러주세요</strong></p>");
	} else {
		var email = $("#new_email_input").val();

		if(email == "" || IsEmail(email) == false){
			alert("E-mail주소를 확인 해주세요!");
			return false;
		}

		var data = getData("_ajax.php?act=authResend&newemail=" + email);
		if(data){
			alert("중복된 E-Mail 입니다! 다른 주소를 입력해 주세요!");
		} else {
			alert("E-Mail이 발송되었습니다. 메일함을 확인해 주세요!");
		}
		location.reload();
	}
}

$(document).ready(function(){
	$('#loginWrap input').on('click focus', function(){
		$(this).siblings('label').hide();
	});
	$('#loginWrap input').on('blur', function(){
		if($.trim($(this).val()).length === 0) $(this).siblings('label').show();
	});
	$(document).on('focus', ':input', function(){
		$(this).attr('autocomplete', 'off');
	});
});
</script>