<?
include_once("_common.php");

if($userid && $password):

	list($mid,$mname,$status,$email, $token, $limit) = mysql_fetch_array(sql_query("SELECT mid,name,status,email,token,`limit` FROM tons_member WHERE `userid` = '$userid' AND `password` = PASSWORD('$password')"));

	

	if($mid):
		$_SESSION['tons']['mid'] = $mid;
		$_SESSION['tons']['mname'] = $mname;
		$_SESSION['tons']['userid'] = $userid;
		$_SESSION['tons']['status'] = $status;
		$_SESSION['tons']['email'] = $email;
		$_SESSION['tons']['token'] = $token;
		$_SESSION['tons']['limit'] = $limit;

		alert("","list.php");
	else:
		alert($lng['fail']);
	endif;
endif;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title>:: TakeOutNote Cloud ::</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>

	<style type="text/css">
	@import url(http://fonts.googleapis.com/earlyaccess/nanumgothic.css);

	html {width: 100%;height: 100%;}
	body {margin: 0;padding: 0;font-size: 12px;width: 100%;height: 100%;font-family: "Nanum Gothic", arial;}
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
		background-color:#000 !important;
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
<form name="loginForm" method="POST" onSubmit="return chkForm();">
	<div class="wrap">
		<div class="row">
			<label for="userid"><?php echo $lng['user']?> ID</label>
			<input type="text" name="userid" id="userid" class="text basic" value=""/>
		</div>
		<div class="row">
			<label for="password"><?php echo $lng['password']?></label>
			<input type="password" name="password" id="password" class="text basic" value=""/>
		</div>
		<div class="row">
			<input type="submit" value="<?php echo $lng['login']?>" class="button submit"/>
		</div>
		<div class="row" style="color: #fff;line-height: 20px;vertical-align: middle;text-align: right;">
			<b>Language</b> <a href="?lng=ko"> 
			<?php if($lng != "ko") {?><a href="?lng=ko"><img src="../image/south_korea.png" style="height: 20px; vertical-align: middle;"/></a><?php } ?> 
			<?php if($lng != "en") {?><a href="?lng=en"><img src="../image/usa.png" style="height: 20px; vertical-align: middle;" /></a><?php } ?>
		</div>
	</div>
</form>
</div>

<div id="tail"></div>

</body>
</html>

<script type="text/javascript">
function chkForm(){

	var f = document.loginForm;

	if(f.userid.value == ""){
		alert("ID를 입력해 주세요!");
		return false;
	}
	if(f.password.value == ""){
		alert("비밀번호를 입력해 주세요!");
		return false;
	}

	return true;
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
