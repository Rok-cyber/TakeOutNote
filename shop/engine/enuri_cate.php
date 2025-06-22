<?
@set_time_limit(0);

include "../php/sub_init.php";

$sql = "SELECT code FROM mall_design WHERE mode='X'";
$row = $mysql->get_one($sql);
$row = explode("|",$row);
if($row[8]!=1) alert("에누리엔진페이지 미사용 상태 입니다.","close");
$exword = $row[9];
?>
<HTML>
<HEAD>
<TITLE>:::: NuriBot Search Standard Form ::::</TITLE>
</HEAD>
<BODY topmargin='30'>

<table border="0" cellspacing="1" cellpadding="10" bgcolor="white" width="90%" align='center'>
<tr><td>▒ <b>eNuri Standard Form For Search Page (Category 분류)</b></td></tr>
<table>
<table border="0" cellspacing="1" cellpadding="5" bgcolor="black" width="91%" align='center'>
	<tr bgcolor="#ededed">
		<th width=60 align=center>대분류</th>
		<th>중분류</th>
	</tr>
<?

$sql = "SELECT * FROM mall_cate WHERE cate_dep='1' ORDER BY cate ASC";
$mysql->query($sql);

while($data = $mysql->fetch_array()){

	echo "
		<tr bgcolor='white'>
			<td align=center><a href='./enuri.php?cate={$data['cate']}'>{$data['cate_name']}</a></td>
			<td>
		";

	if($data['cate_sub']==1) {
		$sql = "SELECT * FROM mall_cate WHERE cate_dep='2' && cate_parent='{$data['cate']}' ORDER BY cate ASC";
		$mysql->query2($sql);
		
		$ck = 0;
		while($data2 = $mysql->fetch_array('2')){

			if($ck!=0) $bar = " | ";
			else $bar = "";
			
			echo "{$bar}<a href='./enuri.php?cate={$data2['cate']}'>{$data2['cate_name']}</a>";
			$ck = 1;
		}
	}

	echo "	
			</td>
		</tr>
		";
}

?>
</table>
</BODY>
</HTML>