<?
include_once("api/common.php");

if(!$key){
	$message = "잘못된 접근 입니다!";
} else {
	list($status) = mysql_fetch_row(mysql_query("SELECT status FROM tons_member WHERE token = '$key'"));
	if(!$status){
		$message = "잘못된 접근 입니다!";
	} else {
		switch($status){
			case "T" :
				$query = "UPDATE tons_member SET status = 'Y' WHERE token = '$key'";
				$result = mysql_query($query);

				if($result){
					$message = "E-Mail 인증이 완료되었습니다.";
				} else {
					$message = "E-Mail 인증에 실패 했습니다.";
				}
			break;

			case "Y" : $message = "이미 인증이 완료된 ID 입니다."; break;

			case "N" : $message = "접근이 제한된 ID 입니다."; break;

			default : $message = "잘못된 접근 입니다!"; break;
		}
	}
}
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

	#loginWrap div.message {position: relative;margin-top: 55px;font-size: 15px;font-weight: bold;text-align: center;}

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
<form name="loginForm" method="POST">
	<div class="wrap">
		<div class="message"><?=$message?></div>
	</div>
</form>
</div>

<div id="tail"></div>

</body>
</html>