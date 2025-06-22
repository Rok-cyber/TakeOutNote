<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

$skin = ".";
require "{$lib_path}/class.Template.php";
require "{$lib_path}/class.Paging.php";

################ 변수 지정 #####################
$page = isset($_GET['page']) ? $_GET['page'] : '1';
$PGConf['page_record_num']	= 20;
$PGConf['page_link_num']	= 10;
$record_num					= $PGConf['page_record_num'];
$page_num					= $PGConf['page_link_num'];

$defBoard = Array('notice','customer','faq','counsel','sales','cooperation','affil_counsel');  

$sql = "SELECT COUNT(uid) FROM pboard_manager";
$total_record = $mysql->get_one($sql);

####################### LIMIT CONFIGURATION #######################
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$record_num);	
$v_num = $total_record - (($page-1) * $record_num);
####################### LIMIT CONFIGURATION #######################


// 템플릿
$tpl = new classTemplate;
$tpl->define('main','./board_list.html');
$tpl->scan_area('main');

if($total_record > 0) {
	
	####################### QUERY #######################
    $query = "SELECT uid,name,title,signdate FROM pboard_manager ORDER BY uid DESC LIMIT {$Pstart},{$record_num}";
    $mysql->query($query);

	####################### LOOP #######################
	while ($row=$mysql->fetch_array()){
      $NUM = $v_num;	  
	  if($v_num%2 ==0) $BGCOLOR = "#efefef";   
	  else $BGCOLOR = "#ffffff";
	  
	  if(in_array($row['name'],$defBoard)) $disa = 'disabled';
	  else $disa = '';

	  $DEL = "<input type='checkbox' value='{$row[name]}' name='item[]' onfocus='blur();' {$disa}>";
	  $LIST1 = "<a href='board.html?code={$row[name]}' onfocus='this.blur();' class='eng' title='게시판보기'>{$row[name]}</a>";
	  
	  $sql = "SELECT comment,file FROM pboard_{$row[name]}_body where no=1";  //게시판 등록 글수 구하기
	  $data = $mysql->one_row($sql);
	  $record_arr	= explode("|",$data['comment']);
	  $nrecord		= explode("|",$data['file']);
	  $t_record		= $record_arr[0]+$nrecord[0];
	  $LIST2		= $row['title'];
	  $LIST3		= $t_record;
	  $LIST4		= "<a href='board_write.php?mode=modify&uid={$row[uid]}' onfocus='this.blur();' class='eng'>setup</a>";
      $LIST5		= "<a href='#' onfocus='this.blur();' onclick='openAccess({$row[uid]});return false;' class='eng'>setup</a>";
	  $LIST6		= "<a href='#' onfocus='this.blur();' onclick='openCate({$row[uid]});return false;' class='eng'>setup</a>";
	  $LIST7		= "<a href='bo_dump.php?table={$row[name]}' onfocus='this.blur();' class='eng'>backup</a>";
	  $LIST8		= date("y-m-d",$row['signdate']);      

	  $tpl->parse("loop");
	  $v_num--;
	}
	####################### LOOP #######################

	####################### PAGING #####################
	$pg = new paging($total_record,$page);
	$pg->addQueryString("?"); 
	$PAGING = $pg->print_page();  //페이징 

} 
else  $tpl->parse('noloop'); 	
	
$TOTAL	= $total_record;      //토탈수 
$PAGE	= "{$page}/{$total_page}";
$LINK	= "./board_write.php";    // 목록보기 링크 

$tpl->parse('main');
$tpl->tprint('main');

include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
?>