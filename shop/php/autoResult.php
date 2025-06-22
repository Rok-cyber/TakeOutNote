<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$search = $_GET['search'];
$ints	= $_GET['ints'];

if(!$search) { 
	echo "</root>"; 
	exit;
}

if(is_numeric($search) && $ints!='1' && strlen($search)==1) {
	$where = "{$search},%";
}
else $where = "{$search}%";

$sql = "SELECT word, split_word FROM mall_auto_search WHERE split_word like '{$where}' ORDER BY ord DESC";
$mysql->query($sql);

while($data=$mysql->fetch_array()){
	if($ints==1 && is_numeric($search) && eregi(",",$data['split_word'])) continue; 
	if($ints!=1 && is_numeric($search) && !eregi(",",$data['split_word'])) continue; 
	
	$data['word'] = stripslashes($data['word']);

	echo "
	  <item>
		<word><![CDATA[{$data[word]}]]></word>
		<sword><![CDATA[{$data[split_word]}]]></sword>
      </item>\n	";
}

echo "</root>";
?>