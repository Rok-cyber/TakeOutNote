<?
include "../php/sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n"); 

$code = $_GET['code'];
$sql  = "SELECT * FROM pboard_manager WHERE name = '{$code}'";
$data = $mysql->one_row($sql);

if(!$data['name']) {
	echo "<response>이미 삭제된 게시판이거나 존재하지 않은 게시판입니다. 게시판 이름을 확인 후 다시 시도해 주세요.</response>" ; 
	exit;
}

if(eregi('counsel|cooperation|sales',$data['name'])) {
	echo "<response>RSS로 비공개된 게시판 입니다.</response>" ; 
	exit;	
}

$acc_level	= explode("|",$data['accesslevel']);
if(($acc_level[6] == '!=' && $acc_level[0]!=$my_level) || ($acc_level[6] == '<' && $acc_level[0]>$my_level) || ($acc_level[7] == '!=' && $acc_level[1]!=$my_level) || ($acc_level[7] == '<' && $acc_level[1]>$my_level)) {
	echo "<response>RSS로 비공개된 게시판 입니다.</response>" ; 
	exit;	
}

$TITLE = stripslashes($data['title']);
$URL = "http://{$_SERVER['HTTP_HOST']}/{$ShopPath}{$Main}?channel=board&code={$code}";
$DESC = "{$basic[1]} - {$TITLE}";

$sql = "SELECT code FROM mall_design WHERE mode='A'";
$tmp_basic = $mysql->get_one($sql);
$basic = explode("|*|",stripslashes($tmp_basic));
//0:주소,1:쇼핑몰명,2:상호,3:대표자명,4:사번호,5:부가번호,6:주소,7:연락처,8:팩스,9:관리자명,10:이메일,11:타이틀,12:키워드,13:실시간검색어
?>

<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title><?=$TITLE?></title>
<link><![CDATA[ <?=$URL?> ]]></link>
<description><![CDATA[ <?=$DESC?> ]]></description>
<dc:language>ko</dc:language>
<generator>itsMall</generator>
<pubDate><?=date("Y-m-d h:i")?></pubDate>

<?
$sql = "SELECT m.subject, m.signdate, m.no, m.secret, m.file, b.comment FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no=b.no && b.memo!='1' && m.no>1 LIMIT 20";
$mysql->query($sql);

while($row=$mysql->fetch_array()){
	$SUBJECT = stripslashes($row['subject']);
	$comment = html2txt(stripslashes($row['comment']));
	if($row['secret']==1) $COMMENT = "비밀글입니다";
	else $COMMENT = hanCut($comment,255);

	if(eregi("\.gif|\.jpg|\.pnp|\.bmp",$row['file'])){
		$IMG = imgSizeCh("../pboard/data/{$code}/",$row['file'],150);				
		if($IMG) $COMMENT = $IMG."<br />".$COMMENT;
	} 
	
	$LINK = "http://{$_SERVER[SERVER_NAME]}/{$ShopPath}{$Main}?channel=board&code={$code}&pmode=view&no={$row[no]}";
    $DATE = date("r",$row['signdate']);
    
	echo "<item>
		   <title><![CDATA[ {$SUBJECT} ]]></title>
           <description><![CDATA[ {$COMMENT} ]]></description>
		   <link><![CDATA[ {$LINK} ]]></link>		
		   <dc:date>{$DATE}</dc:date>
		   </item>
	";
	 	 
} 
?>
</channel>
</rss>