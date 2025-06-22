<?
include "../ad_init.php";
include "./conf.php";

###################### 변수 정의 ##########################
$code		= isset($_GET['code'])? $_GET['code']:$_POST['code']; 
$mode		= isset($_GET['mode'])? $_GET['mode']:'';
$field		= isset($_GET['field']) ? $_GET['field'] : '';
$word		= isset($_GET['word']) ? $_GET['word'] : '';
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : '';
$limit		= isset($_GET['limit']) ? $_GET['limit'] : '';
$location	= isset($_GET['location']) ? $_GET['location'] : '';
$point		= isset($_GET['point']) ? $_GET['point'] : '';
$status		= isset($_GET['status']) ? $_GET['status'] : $_POST['status'];

##################### addstring ############################ 
if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($limit) $addstring .= "&limit={$limit}";
if($location) $addstring .= "&location={$location}";
if($status) $addstring .= "&status={$status}";
if($point) $addstring .= "&point={$point}";
if($seccate) $addstring = "&seccate={$seccate}";
if($page) $addstring .="&page={$page}";


if($mode=='all' && $code=='search') {
	$sql = "DELETE FROM mall_{$code}";
	$mysql->query($sql);
	alert("전체 데이타를 삭제처리를했습니다!",$Main.$addstring);
}

$item = isset($_POST['item'])? $_POST['item']:'';
for($i=0,$cnt=count($item);$i<$cnt;$i++) {
	$sql = "SELECT * FROM mall_{$code} WHERE uid = '{$item[$i]}'";
	$row = $mysql->one_row($sql);
	$sql = "DELETE FROM mall_{$code} WHERE uid = '{$item[$i]}'";
	$mysql->query($sql);	
      
	@unlink("{$dir}/{$row[file]}");
    @unlink("{$dir}/{$row[file2]}");
	@unlink("{$dir}/{$row[img1]}");
    @unlink("{$dir}/{$row[img2]}");

	if($code=='banner' || $code=='mobile_banner') {
		@unlink("{$dir}/{$row[banner]}");
		$sql = "UPDATE mall_banner SET rank = rank - 1 WHERE rank > {$row['rank']} && location='{$row['location']}'";
		$mysql->query($sql);
    }
} 	

alert("{$i}건의 삭제처리를했습니다!",$Main.$addstring);

?>