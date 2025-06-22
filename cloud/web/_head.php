<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title>:: TakeOutNote Cloud ::</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/_function.js"></script>

	<link rel="stylesheet" href="css/tons.20140215.css" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css">
</head>
<body>

<? if(stristr($_SERVER['PHP_SELF'], "note.php")){ ?>
<div id="header" data-role="header">
	<div class="logo">TakeOutNote</div>
</div>
<? } else { ?>
<div id="header" data-role="header">
	<div class="logo" onClick="go('list.php');">TakeOutNote</div>
	<div class="beta" onClick="window.open('../api/policy.php?test=Y','','width=600,height=700');">(<span style="text-decoration: underline;"><?php echo $lng['betaservice']?></span>)</div>
	<!--
	<div class="search"><input type="text" name="Query" class="text basic round" style="width: 60px;" value="<?=$Query?>"/><input type="submit" value="검색" class="button basic white" style="margin-left: 3px;"/></div>
	-->
	<!--<div class="member"><?=$USERID?></div>-->
	<div class="member">
		<div class="wrap" onClick="$('#header .member .menu').slideToggle();">
			<img src="imgs/icon/member.png" /> <?=$USERID?> ▼
			<!--<input type="button" class="button basic white" value="logout" onClick="location.href='./index.php';"/>-->
		</div>
		<div class="menu">
			<ul>
				<li><?php echo $lng['usage']?> : <?=ceil($_SESSION['tons']['volume']/1024/1024)?>M / <?=ceil($_SESSION['tons']['limit']/1024/1024)?>M</li>
				<li onClick="go('mypage.php');"><?php echo $lng['mypage']?></li>
				<li style="border: 0;" onClick="location.href='./index.php';"><?php echo $lng['logout']?></li>
			</ul>
		</div>
	</div>
</div>
<? } ?>


<form name="searchForm" method="POST" action="list.php">
<div id="navigation">
	<div class="current"><?=$_NAV['current']?></div>
	<div class="refresh" onClick="location.reload();"><img src="imgs/icon/refresh.png" style="margin-top: 10px;height: 20px;"/></div>
	<div class="search"><input type="text" name="Query" class="text basic round" style="width: 120px;" value="<?=$Query?>"/><input type="submit" value="<?php echo $lng['search']?>" class="button basic white" style="margin-left: 3px;"/></div>
</div>
</form>

<div id="contentsWrap">