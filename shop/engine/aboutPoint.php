<?
@set_time_limit(0);
ob_start();
include "../php/sub_init.php";

$sql = "SELECT code FROM mall_design WHERE mode='X'";
$row = $mysql->get_one($sql);
$row = explode("|",$row);
if($row[10]!=1) alert("어바웃엔진페이지 미사용 상태 입니다.","close");

$fname = "aboutpoint";

$times = @filectime("./data/{$fname}.xml");
if($times>time()-600) {
	echo @readFiles("./data/{$fname}.xml");
	exit;
}

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n"); 

?>

<feedbacklist xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

<?

$sql = "SELECT * FROM mall_goods_point ORDER BY uid DESC LIMIT 5000";
$mysql->query($sql);

while($data = $mysql->fetch_array()){
	
	if($data['id']) {
		$wid = substr($data['id'],0,3)."***";
	}
	else $wid = "guest";

	$date = date("Y-m-d H:i:s",$data['signdate']);
	$score = $data['point']*2;
			
	echo "
		<feedback>
		<id>{$data['number']}</id>
		<con_id>{$data['uid']}</con_id>
		<type>0</type>
		<subject><![CDATA[{$data['title']}]]></subject>
		<contents><![CDATA[{$data['content']}]]></contents>
		<writer>{$wid}</writer>
		<wdate>{$date}</wdate>
		<score>{$score}</score>
		</feedback>
	";
}

$tmps = ob_get_contents();
ob_end_flush(); 
ob_end_clean(); 
writeFile("./data/{$fname}.xml",$tmps);
?>