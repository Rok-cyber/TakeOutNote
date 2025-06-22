<?
ob_start();
include "../ad_init.php";

############ 파라미터(값) 검사 ####################
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if($_SERVER['REQUEST_METHOD']=='POST') alert('정상적으로 등록하세요!','back');

$table = isset($_GET['table']) ? $_GET['table'] : '';

if(!$table) alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');

############ 디비설정 ####################
if(!$info = readFiles("{$bo_path}/config.php")) alert("config.php파일이 없습니다.\\nDB설정을 먼저 하십시요","back");  
$info  = explode("||",$info);

//DB 연결설정
$connect = mysql_connect($info[1],$info[3],$info[4]);
mysql_select_db($info[2],$connect);

include "dbDump.php";
$date = date("Y_m_d_h",time());

zbDB_Header($date."_".$table.".sql");
pDB_manager($table);
zbDB_down("pboard_{$table}");
zbDB_down("pboard_{$table}_body");

?>