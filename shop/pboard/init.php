<?
@error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
header("Content-Type: text/html; charset=utf-8");

if($_GET['code']) $code		= $_GET['code'];
if($_GET['pmode']) $pmode	= $_GET['pmode'];

$ad_path = "{$bo_path}/admin"; 
$ho_path = $_SERVER['DOCUMENT_ROOT'];
$ShopPath	= "";

################ 언어 설정 #######################
switch($code) {
	default : include "{$bo_path}/lang/korea.php";
}

include "{$bo_path}/lib/lib.Function.php";

############ 환경,디비설정 및 파일 인클루드 ####################
if(!$code) Error($LANG_ERR_MSG[4]); 

include "{$bo_path}/dbconn.php";
include "{$bo_path}/lib/checkLogin.php";
include "{$bo_path}/lib/class.Mysql.php";
require "{$bo_path}/lib/class.Template.php";
require "{$bo_path}/lib/class.Paging.php";

$mysql = new mysqlClass(); 

$_POST = array_map('add_escape_string', $_POST); 
$_GET = array_map('add_escape_string', $_GET); 

if($_GET['admin_board'] || $_POST['admin_board']) $admin_board = '';
if($_GET['admin_board2'] || $_POST['admin_board2']) $admin_board2 = '';
if($code=='affil_counsel' && $my_level>8 && $memo_type=='Y') $admin_board2 = 'Y';
if($code=='affil_counsel' && $admin_board2!='Y') {
	if($_COOKIE['a_my_id']) {
		if(!$lib_path) $lib_path = "../lib";
		include "{$lib_path}/checkALogin.php";
		$my_level = 2;
		$my_name = $a_my_name;
		$my_id = $a_my_id;
		$my_email = $a_my_email;
		$my_homepage="";
	}
	else {
		$my_id = "";
		$my_level = "1";
		$my_name = "";
		$my_email = "";
		$my_homepage="";			
	}	
}

$sql  = "SELECT * FROM pboard_manager WHERE name = '{$code}'";
if(!$main_data = $mysql->one_row($sql)) Error($LANG_ERR_MSG[5]);
if($skin2 && is_file("{$skin2}/board/{$main_data['s_name']}/write.html")) $skin = "{$skin2}/board/".$main_data['s_name'];
else $skin = "{$bo_path}/skin/".$main_data['s_name'];
$bw_size = $main_data['b_w_size'];
$PGConf['page_record_num']	= $main_data['inpage'];
$PGConf['page_link_num']	= $main_data['pagelink'];
if($bw_size <101) $bw_size .= "%";
else $bw_size .= "px";
$options = explode("||",$main_data['options']);

/*   options[]        
	0:전체목록 출력| 1:카테고리 | 2:HTML | 3:공지사항 | 4:자료실 | 5:간단한 답글 | 6:비밀글 | 7:동영상 링크 | 8:관련사이트 링크 | 9:로그인 아이콘 | 10:업로드 파일수 | 11:링크 가능수 | 12 : 게시판 관리 아이디 | 13 : 답변메일 | 14 : 갤러리 이미지수 | 15 : 갤러리 이미지 사이즈 | 16:방명록형태 | 17:방명록형태시 내용글제한수 | 18: 새글표시 시간 | 19 : 웹에디터 사용 | 20 : 갤러리 사이즈고정 | 21 : 블로그형태 */ 

$acc_level	= explode("|",$main_data['accesslevel']);
$category	= explode("|",$main_data['category']);
$img_cnt	= $options[14];
$img_sz		= $options[15];
if($options[12] && $my_id == base64_decode(stripslashes($options[12]))) $my_level=9;    //게시판관리 회원 임시 레벨 9... 

############### ACCESS LEVEL CHECK ###############
if($memo_type=='Y') {
	if($acc_level[9] == '!=' && $acc_level[3]!=$my_level) alert($LANG_ACC_MSG[0],'back');	
	if($acc_level[9] == '<' && $acc_level[3]>$my_level) alert($LANG_ACC_MSG[0],'back');
} else {
	switch($pmode) {
		case "write" : case "del" : 
			if($acc_level[8] == '!=' && $acc_level[2]!=$my_level) alert($LANG_ACC_MSG[0],'back');
			if($acc_level[8] == '<' && $acc_level[2]>$my_level) alert($LANG_ACC_MSG[0],'back');
		break;
		case "reply" : 
			if($acc_level[13] == '!=' && $acc_level[12]!=$my_level) alert($LANG_ACC_MSG[4],'back');
			if($acc_level[13] == '<' && $acc_level[12]>$my_level) alert($LANG_ACC_MSG[4],'back');
		break;
		case "view" : 
			if($acc_level[7] == '!=' && $acc_level[1]!=$my_level) alert($LANG_ACC_MSG[1],'back');
			if($acc_level[7] == '<' && $acc_level[1]>$my_level) alert($LANG_ACC_MSG[1],'back');
		break;
		default : 
			if($acc_level[6] == '!=' && $acc_level[0]!=$my_level) alert($LANG_ACC_MSG[2],'back');
			if($acc_level[6] == '<' && $acc_level[0]>$my_level) alert($LANG_ACC_MSG[2],'back');
	}
}
?>
