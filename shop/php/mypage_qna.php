<?
include "lib/class.Paging.php";

// 변수 지정
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$record_num	= 15;
$page_num	= 100;

$PGConf['page_record_num'] = $record_num;
$PGConf['page_link_num'] = $page_num;


$sql = "SELECT COUNT(*) FROM  mall_goods_qna WHERE id='{$my_id}'";
$TOTAL = $mysql->get_one($sql);

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
$TONUM = $TOTAL - (($page-1) * $record_num);
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

$tpl->define("main","{$skin}/mypage_qna.html");
$tpl->scan_area("main");

/**************************** GOODS LIST**************************/

if($TOTAL>0) {
	$sql = "SELECT * FROM mall_goods_qna WHERE id = '{$my_id}' ORDER BY uid desc LIMIT {$Pstart},{$record_num}";
	$mysql->query($sql);
	$NUM = $TONUM;

	while($row = $mysql->fetch_array()){		
		$NAME	= htmlspecialchars(stripslashes($row['goods_name']));
		$TITLE	= htmlspecialchars(stripslashes($row['title']));
		$DATE	= date("Y-m-d",$row['signdate']);
		$CONTENT = stripslashes($row['content']);
		$CONTENT = ieHackCheck($CONTENT);
		$POINT	= $row['point']*20;
		$CATE	= $row['cate'];
		$NUMBER	= $row['number'];
		$UID	= $row['uid'];

		if($row['answer']) {
			$ANSWER = stripslashes($row['answer']);
			$ANSWER = ieHackCheck($ANSWER);
			$tpl->parse("is_answer","1");
			$tpl->parse("is_an1","1");
		}
		else $tpl->parse("is_an2","1");
			
		$tpl->parse("loop");
		$tpl->parse("is_answer","2");
		$tpl->parse("is_an1","2");
		$tpl->parse("is_an2","2");
		$NUM--;
	}

	if($TOTAL > $record_num){
		$pg_string = explode(",",$tpl->getPgstring());
		$pg = new paging($TOTAL,$page);
		$pg->addQueryString($Main."?channel=qna"); 
		$PAGING = $pg->print_page($pg_string[0],$pg_string[1],$pg_string[2]);  //페이징 
		$tpl->parse("define_pg");	
	}

	$tpl->parse("is_loop");
}
else {
	$PAGE = 0;
	$TOTAL_PAGE = 0;
	$tpl->parse("no_loop");
}


/**************************** GOODS LIST**************************/

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>