<?
$tpl->define("main","{$skin}/mypage_counsel.html");
$tpl->scan_area("main");

$tpl->parse("board_top");
$tpl->parse("board_bottom");
$tpl->tprint("board_top");

/************* 게시판 인클루드 *****************/
$skin2 = $skin;
$Main2 = $Main;
$includes = 'Y';
$code = "counsel";
$bo_path="./pboard";
$main_url = "{$Main}?channel=counsel";
include "./pboard/pboard.php";
$skin = $skin2;
$Main = $Main2;
/************* 게시판 인클루드 *****************/

$tpl->tprint("board_bottom");
$tpl->close();
?>