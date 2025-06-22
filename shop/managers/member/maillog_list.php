<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

require "{$lib_path}/class.Template.php";
require "{$lib_path}/class.Paging.php";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? $_GET['word'] : $_POST['word'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$skin = ".";

if(!$limit) {
	$limit = 10;
	$PGConf['page_record_num'] = 10;
}
else {
	$PGConf['page_record_num'] = $limit;
	$addstring = "&limit={$limit}";
}

$PGConf['page_link_num'] = 10;

$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];

if(!$field) $field = "subject";
if($word) {
	$addstring .= "&field={$field}&word={$word}";
	$where .= "&& $field like '%$word%' ";
}

$pagestring = $addstring;
$addstring .= "&page={$page}";

$sql = "SELECT COUNT(uid) FROM pboard_maillog WHERE uid != 0 $where";

$total_record = $mysql->get_one($sql);

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$record_num);	
$v_num = $total_record - (($page-1) * $record_num);
/*********************************** @LIMIT CONFIGURATION ***********************************/

/*********************** 페이지 계산 **************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./maillog_list.html");
$tpl->define("loop","main");
$tpl->define("noloop","main");

if($total_record > 0) {
	
/*********************************** QUERY ***********************************/
    $query = "SELECT * FROM pboard_maillog WHERE uid !=0 {$where} ORDER BY uid DESC LIMIT $Pstart,$record_num";
    $mysql->query($query);
/*********************************** QUERY ***********************************/

/*********************************** LOOP ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#efefef";
		else $BGCOLOR = "#ffffff";
	  
		$DEL = "<input type='checkbox' value='$row[uid]' name='item[]'>";
		$LIST1 = "<a href='./maillog_view.php?uid=$row[uid]{$addstring}' onfocus='this.blur();'>$row[subject]</a>";
		$m_to = explode(",",$row['m_to']);
		if(count($m_to) >1) $LIST2 = $m_to[0]." + ".(count($m_to) -1);
		else $LIST2 = $row['m_to'];
	  
		$LIST3 = $row['m_true'];
		$LIST4 = $row['m_false'];
		if($row['m_total']!=$row['m_cnt']) $LIST5 = "전송중단";
		else $LIST5 = "전송완료";
	
		$MTIME = round(($row['e_time'] - $row['s_time']),2)."초";
		$DATE = date("y-m-d",$row['signdate']);
       
		$tpl->parse("loop");
		$v_num--;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?{$pagestring}");
	$PAGING = $pg->print_page();  //페이징 

} 
else $tpl->parse("noloop"); 	
/*********************************** LOOP ***********************************/

$TOTAL = $total_record;      //토탈수 
$PAGE = "$page/$total_page";
$LINK1 = "";    // 목록보기 링크 

$ACTION = $_SERVER['PHP_SELF'];   //검색 경로
$CANCEL = $_SERVER['PHP_SELF'];

$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
?>