<?
switch($plus_info['leftmenu']) {
	case 1 : 
		include "php/customer_menu.php";
		$LEFT_MENU = $CUS_MENU;
	break;
	case 2 : 
		include "php/cate_menu.php";
		$LEFT_MENU = $CATE_MENU;
	break;
	case 3 : 
		include "php/mypage_menu.php";
		$LEFT_MENU = $MY_MENU;
	break;
}

$PAGE_HTML = stripslashes($plus_info['html']);

$tpl->define("main","{$skin}/plus_page.html");
$tpl->scan_area("main");

if($LEFT_MENU) {
	$tpl->parse("board_top1");
	$tpl->parse("board_bottom1");
	
	if($plus_info['board']) {		
		$tpl->tprint("board_top1");
		/************* 게시판 인클루드 *****************/
		$skin2 = $skin;
		$Main2 = $Main;
		$includes = 'Y';
		$code = $plus_info['board'];
		$bo_path="./pboard";
		$main_url = "./{$Main}?channel=plus&plus={$plus}";
		include "./pboard/pboard.php";
		$skin = $skin2;
		$Main = $Main2;
		/************* 게시판 인클루드 *****************/
		$tpl->tprint("board_bottom1");
	}
	else {
		$tpl->parse("is_left");
		$tpl->parse("main");
		$tpl->tprint("main");
	}	
}
else {
	$tpl->parse("board_top2");
	$tpl->parse("board_bottom2");
	
	if($plus_info['board']) {		
		$tpl->tprint("board_top2");
		/************* 게시판 인클루드 *****************/
		$skin2 = $skin;
		$Main2 = $Main;
		$includes = 'Y';
		$code = $plus_info['board'];
		$bo_path="./pboard";
		$main_url = "./{$Main}?channel=plus&plus={$plus}";
		include "./pboard/pboard.php";
		$skin = $skin2;
		$Main = $Main2;
		/************* 게시판 인클루드 *****************/
		$tpl->tprint("board_bottom2");
	}
	else {
		$tpl->parse("is_default");
		$tpl->parse("main");
		$tpl->tprint("main");
	}
}

$tpl->close();
?>