<?
ob_start();
$bo_path = ".";

include "{$bo_path}/lib/lib.Function.php";

$code	= $_GET['code'];
$no		= $_GET['no'];

if(!$code || !$no) exit;

include "$bo_path/dbconn.php";
include "$bo_path/lib/class.Mysql.php";

$mysql = new mysqlClass(); 

################## 조회수 증가 ##################
@session_start();
$tmp=explode(',',$_SESSION['pboard_view']);
if(!in_array("{$code}:{$no}",$tmp)){      
	  $sql = "UPDATE pboard_{$code} SET hit = hit+1 WHERE no='{$no}'";	  
	  $mysql->query($sql);	  
	  array_push($tmp, $code.':'.$no);
	  $pboard_view = implode(',',$tmp);	  
      $_SESSION['pboard_view'] = $pboard_view;	  
	  unset($tmp);
}
include "{$bo_path}/close.php";
?>
