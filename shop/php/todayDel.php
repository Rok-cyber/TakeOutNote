<?
$lib_path = "../lib";
require "{$lib_path}/lib.Function.php";

$cate	= $_GET['cate'];
$number = $_GET['number'];

@session_start();
$tmp=explode(',',$_SESSION['today_view']);
for($i=0,$cnt=count($tmp);$i<=$cnt;$i++){		
	if($tmp[$i]=="{$cate}:{$number}") {
		array_splice($tmp,$i,1);							
		break;		
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
echo "<cate>{$cate}</cate>\n"; 
echo "<number>{$number}</number>\n"; 
echo "</root>";
exit;
?>