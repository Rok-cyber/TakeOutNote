<?
ob_start();

$code		= isset($_GET['code']) ? $_GET['code']:'';
$no			= isset($_GET['no']) ? $_GET['no']:'';
$filename	= isset($_GET['filename']) ? $_GET['filename']:'';

$bo_path = ".";
//include "{$bo_path}/lang/korea.php";
require "{$bo_path}/lib/lib.Function.php";
require "{$bo_path}/lib/class.Mysql.php";

############ 환경,디비설정 및 파일 인클루드 ####################
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert("올바른 방법으로 시도하십시오!",'back'); 
if(!$no || !$code || !$filename) alert("정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!",'back');

include "{$bo_path}/dbconn.php";
$mysql = new mysqlClass();

$sql  = "SELECT accesslevel FROM pboard_manager WHERE name = '{$code}'";
$acc_level	= explode("|",$mysql->get_one($sql));

if($acc_level[2]>1) {
	include "$bo_path/lib/checkLogin.php";
	if($acc_level[7] == '!=' && $acc_level[1]!=$my_level) alert("다운로드 받을 수 있는 권한이 없습니다.",'back');
	if($acc_level[7] == '<' && $acc_level[1]>$my_level) alert("다운로드 받을 수 있는 권한이 없습니다.",'back');
}
// 현재글의 Download 수를 올림;;
$sql = "UPDATE pboard_{$code} SET down = down+1 WHERE no={$no}";
$mysql->query($sql);

include "{$bo_path}/close.php";

fileDown("{$bo_path}/data/{$code}/{$filename}",urlencode($filename));
      
?>
