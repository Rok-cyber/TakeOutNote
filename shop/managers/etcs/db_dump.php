<?
include "../ad_init.php";

############ 파라미터(값) 검사 ####################
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if($_SERVER['REQUEST_METHOD']=='POST') alert('정상적으로 등록하세요!','back');

//DB 연결설정

$mysql->close();

$connect = mysql_connect($DBConf['host'],$DBConf['user'],$DBConf['passwd']);
mysql_select_db($DBConf['database'],$connect);

include "../board/dbDump.php";
$date = date("Y_m_d_h",time());

zbDB_Header($date."_".$DBConf['database'].".sql");
zbDB_All_down($DBConf['database']); 
?>

