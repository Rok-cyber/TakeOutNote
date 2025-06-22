<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
include "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$status		= isset($_GET['status']) ? $_GET['status'] : $_POST['status'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];

$skin = ".";

if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	$where .= "&& INSTR({$field},'{$word}')";
}  
else $field = "order_num";

if($status) {
	$addstring .="&status={$status}";	
	$where .= " && status='".substr($status,0,1)."' && status2='".substr($status,1,1)."' ";
}
else {
	$where = "&& !(status='Z' && status2='B') && !(status='X' && status2='C')";
}

if($limit) $addstring .="&limit={$limit}";	
else $limit = "10";

$addstring3 = $addstring2.$addstring;
if($page>1) $addstring .="&page={$page}";

$PGConf['page_record_num'] = $limit;
$PGConf['page_link_num']='10';
$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];


$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-6 MONTH', time()));


$sql = "SELECT COUNT(*) FROM mall_order_change WHERE status2!='D' {$where}";
$total_record = $mysql->get_one($sql);

/*********************************** LIMIT  CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$record_num);	
$v_num = $total_record - (($page-1) * $record_num);
if($v_num<1 && $page>1) {
	$page = 1;
	$Pstart = $record_num*($page-1);
	$v_num = $total_record - (($page-1) * $record_num);
}
/*********************************** @LIMIT  CONFIGURATION ***********************************/

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./order_cancel_list.html");
$tpl->scan_area("main");
$tpl->parse("is_man1");

if($total_record > 0) {
	
	/*********************************** QUERY **********************************/
    $query = "SELECT * FROM mall_order_change WHERE status2!='D' {$where} ORDER BY uid DESC LIMIT {$Pstart},{$record_num}";
	$mysql->query($query);		
	/*********************************** QUERY  ***********************************/

	/*********************************** LOOP  ***********************************/
    
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;

		if($v_num%2 ==0) $BGCOLOR = "#efefef";
		else $BGCOLOR = "#ffffff";

		$DEL = "<input type='checkbox' value='$row[order_num]' name='item[]'  onfocus='blur();'>";
		$ORDER_NUM = $row['order_num'];
		$UID = $row['uid'];

		$LIST2 = "{$row['order_num']}"; 		

		$sql = "SELECT p_name FROM mall_order_goods WHERE order_num='{$row['order_num']}' && uid IN({$row['sgoods']})";
		$mysql->query2($sql);
		$tmp_name = "";
		while($row2 = $mysql->fetch_array(2)){
			if(!$tmp_name) $tmp_name = "- {$row2['p_name']}";
			else $tmp_name = "<br />- {$row2['p_name']}";
		}
		$LIST3 = $tmp_name;
		$LIST4 = stripslashes($row['message']);
		$LIST5 = $status_arr2[$row['status'].$row['status2']];	
		$LIST6 = substr($row['status_date'],0,16);
		$LIST7 = stripslashes($row['name']);

		if($row['status']=='X') {
			switch($row['status2']) {
				case "A" : 
					$STATE = "<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change.html?order_num={$ORDER_NUM}&uid={$UID}&status=X','iframe','750','550','■ 반품요청 승인처리','20');\">반품승인처리</span>";
				break;
				case "B" : 
					$STATE = "<span id='nextIcon' class='hand small' onclick='location.href=\"order_change_ok.php?mode=return&order_num={$ORDER_NUM}&uid={$UID}{$addstring}\";'>회수완료처리</span>";
				break;	
				case "C" :
					$STATE = "<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change_refund.php?order_num={$ORDER_NUM}&uid={$UID}','iframe','750','550','■ 반품상품 환불처리','20');\">환불처리</span>";
				break;
			}
		}
		else if($row['status']=='Y') {
			switch($row['status2']) {
				case "A" : 
					$STATE = "<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change.html?order_num={$ORDER_NUM}&uid={$UID}&status=Y','iframe','750','550','■ 교환요청 승인처리','20');\">교환승인처리</span>";
				break;
				case "B" : 
					$STATE = "<span id='nextIcon' class='hand small' onclick='location.href=\"order_change_ok.php?mode=return&order_num={$ORDER_NUM}&uid={$UID}{$addstring}\";'>회수완료처리</span>";
				break;
				case "C" : 
					$STATE = "<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change_send.php?order_num={$ORDER_NUM}&uid={$UID}','iframe','750','450','■ 교환상품 발송처리','20');\">교환발송처리</span>";
				break;

			}
		}
		else if($row['status']=='Z') {
			switch($row['status2']) {
				case "A" : 
					$STATE = "<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change.html?order_num={$ORDER_NUM}&uid={$UID}&status=Z','iframe','750','550','■ 취소요청 승인처리','20');\">취소승인처리</span>";
				break;
				case "B" :
					$STATE2 .= "&nbsp;&nbsp;<span id='nextIcon' class='hand small' onclick=\"pLightBox.show('order_change_refund.php?order_num={$ORDER_NUM}&uid={$UID}','iframe','750','550','■ 취소상품 환불처리','20');\">환불처리</span>";
				break;
			}
		}
		    
		$tpl->parse("is_man2","1");
		$tpl->parse("loop");		
		$v_num--;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?".$addstring3); 
	$PAGING = $pg->print_page();  //페이징 
} 
else $tpl->parse("is_loop");
/*********************************** LOOP  ***********************************/

$TOTAL = $total_record;      //토탈수 
	
$PAGE = "$page/$total_page";
$ACTION = "{$_SERVER['PHP_SELF']}?{$addstring2}";   //검색 경로
$CANCEL = "{$_SERVER['PHP_SELF']}?{$addstring2}";
$tpl->parse("is_man3");
$tpl->parse("main");
$tpl->tprint("main");

 include "../html/bottom_inc.html"; // 하단 HTML?>