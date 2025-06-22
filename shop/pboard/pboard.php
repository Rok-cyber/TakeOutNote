<?
ob_start();
session_cache_limiter('no-cache, must-revalidate');
$BOARD_TIME[] = microtime();
if($_COOKIE['includes'] || $_GET['includes'] || $_POST['includes']) exit;
if($includes!='Y') {	// 게시판을 인클루드시킬때
	$bo_path	= ".";          
    $main_url	= "{$bo_path}/pboard.php";
}
include "{$bo_path}/init.php";	// 초기 설정 파일 인클루드

if($skin2) $Main = "{$main_url}&code={$code}";
else $Main		 = "{$main_url}?code={$code}";

$RSSLINK = "http://".$_SERVER["HOST_HTTP"]."/{$ShopPath}rss/board.php?code={$code}";

if($includes!='Y' && !$main_data['header_url']) {	// 인클루드가 아닐때
	echo "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\" />\n
		<html xmlns=\"http://www.w3.org/1999/xhtml\" />\n
		<head>\n
		<title>{$main_data[title]}</title>\n
		{$LANG_CHR}\n
		</head>\n
		<body topmargin=\"0\" leftmargin=\"0\">\n
		";
} 

echo "<!-- ################# Pboard Area Start ####################### -->";

if($main_data['header_url']) include $main_data['header_url'];
if($main_data['header']) echo $main_data['header'];

echo "
	<link rel=\"StyleSheet\" href=\"{$bo_path}/pb_common.css\" type=\"text/css\" />\n
	<link rel=\"StyleSheet\" href=\"{$skin}/style.css\" type=\"text/css\" />\n\n
	<div id=\"pb_main\" style=\"width:{$bw_size}; text-align:center; background-color:{$main_data[bg_color]};\">\n
	";

######################### 변수정의 및 AddString 정의 ################################
$page		= isset($_GET['page'])		? $_GET['page'] : '';
$spage		= isset($_GET['spage'])		? $_GET['spage'] : '';
$slast_idx	= isset($_GET['slast_idx'])	? $_GET['slast_idx'] : '';
$field		= isset($_POST['field'])	? $_POST['field'] : $_GET['field'];
$word		= isset($_POST['word'])		? urldecode(trim($_POST['word'])) : urldecode(trim($_GET['word']));
if(!$field) $field = "subject";

if(!$seccate) {
	$seccate	= isset($_POST['seccate'])	? $_POST['seccate'] : $_GET['seccate'];
}

if($code=='cus_board') {
	if(!$seccate) $seccate = $pid;
}

if($word) $addstring = "&amp;field={$field}&amp;word=".urlencode($word);
$catestring =$addstring; 
if($seccate) { $addstring .= "&amp;seccate={$seccate}"; $searchstring .= "&amp;seccate={$seccate}"; }
$pagestring = $addstring;
if($page) $addstring .="&amp;page={$page}";
if($spage && $slast_idx) $addstring .= "&amp;slast_idx={$slast_idx}&amp;spage={$spage}";

switch($pmode) {
	case 'write': case 'modify':  case 'reply':  
		include "{$bo_path}/write_form.php";
	break;
	case 'view' : 
		include "{$bo_path}/view.php";
	break;
	case 'del': case 'mdel' : case 'secret' : case 'confirm' : 
		include "{$bo_path}/delete_form.php";
	break;
	case 'login' : 
		include "{$bo_path}/login.php";
	break;
	case 'admin': 
		include "{$ad_path}/login.html";
	break;
	default: 
		if($options[21]=='Y') {
			if(!$no) {
				$sql = "SELECT no FROM pboard_{$code} WHERE no>1 ORDER BY no desc limit 1";
				$data	= $mysql->one_row($sql);
				$_GET['no'] = $data[no];				
			}			
			if($data['no']) include "{$bo_path}/view.php";
			else include "{$bo_path}/list.php";	
		}
		else include "{$bo_path}/list.php";	
}

echo "
	</div>\n
	<iframe name=\"HFrm\" class=\"hidden\"></iframe>\n
	";

$sql_time = $php_time= $total_time = 0;

for($i=0,$cnt=count($SQL_TIME);$i<$cnt;$i+=2) {
	$sql_time += getMicrotime($SQL_TIME[$i], $SQL_TIME[$i+1]);
}

$BOARD_TIME[]	= microtime();
$total_time		= getMicrotime($BOARD_TIME[0], $BOARD_TIME[1]);
$php_time		= $total_time - $sql_time;

echo " <!-- SQL Excuted Time [{$sql_time}Sec] , PHP Excuted Time [{$php_time}Sec], TOTAL Excuted Time [{$total_time}Sec] --> \n";

if($main_data['footer_url']) include $main_data['footer_url'];
if($main_data['footer']) echo $main_data['footer'];
if ($includes !='Y' && !$main_data['footer_url']) echo "</body>\n</html>"; 

echo "<!-- ################# Pboard Area End ####################### -->";

include "{$bo_path}/close.php";
?>