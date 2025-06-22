<?
$tpl->define("main",$skin."/customer_board.html");
$tpl->scan_area("main");

$code = $_GET['code'];

switch($code){
	case "counsel" : case "sales" : case "cooperation" :
 		$pmode = "write";

	default : $tpl->parse("is_title_{$code}");
}

$tpl->parse("board_top");
$tpl->parse("board_bottom");
$tpl->tprint("board_top");

/************* 게시판 인클루드 *****************/
$skin2 = $skin;
$Main3 = $Main;
$includes = 'Y';
$bo_path="./pboard";
$main_url = "{$Main}?channel=board";
include "./pboard/pboard.php";
$skin = $skin2;
$Main = $Main3;
/************* 게시판 인클루드 *****************/

$tpl->tprint("board_bottom");
$tpl->close();
?>