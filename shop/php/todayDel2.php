<?
$lib_path = "../lib";
require "{$lib_path}/lib.Function.php";

$ouid = $_GET['uid'];
$uid = explode(",",$ouid);

@session_start();
$tmp=explode(',',$_SESSION['today_view']);
for($i=0,$cnt=count($uid);$i<$cnt;$i++) {
	$cate = substr($uid[$i],0,12);
	$number = substr($uid[$i],12);
	
	for($j=0,$cnt2=count($tmp);$j<=$cnt2;$j++){			
		if($tmp[$j]=="{$cate}:{$number}") {
			array_splice($tmp,$j,1);							
			break;		
		}	
	}
}

$today_view = implode(',',$tmp);	  
session_register("today_view");
$_SESSION['today_view'] = $today_view;	

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 
echo "<item>true</item>\n"; 
echo "<type>Today</type>\n"; 
echo "<uid>{$ouid}</uid>\n"; 
echo "</root>";
exit;
?>