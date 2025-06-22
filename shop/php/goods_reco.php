<?
$tpl->define("main","{$skin}/goods_reco.html");
$tpl->scan_area("main");

include "lib/class.Paging.php";

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$mo1	= isset($_POST['mo1']) ? $_POST['mo1'] : $_GET['mo1'];
$mo2	= isset($_POST['mo2']) ? $_POST['mo2'] : $_GET['mo2'];
	
$ajaxstring = "&mode=reco";

if(($mo1 || $mo1==0) && $mo2) {
   $where .= " && price BETWEEN '{$mo1}' AND '{$mo2}' ";
   $mstring = "&mo1={$mo1}&mo2={$mo2}";
   $SEC_MON = number_format($mo1)."원 ~ ".number_format($mo2)."원";   
   $ajaxstring .= "&mo1={$mo1}&mo2={$mo2}";
   $tpl->parse("is_sec_mon");
   SetCookie("search_yn","Y",0,"/");
}
else SetCookie("search_yn","",-999,"/");

$where .= "&& (SUBSTRING(display,1,1)!='0' || SUBSTRING(display,3,1)!='0')";

$pstring = "&page={$page}";
$sql = "SELECT COUNT(uid) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";

$TOTAL = $mysql->get_one($sql);

if($TOTAL>0) {
	$sql = "SELECT MAX(price) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";
	$tmps = $mysql->get_one($sql);
	if(substr($tmps,0,1)==9) $MaxMoney = "10";
	else $MaxMoney = substr($tmps,0,1)+1;

	for($i=1,$cnt=strlen($tmps);$i<$cnt;$i++){
		$MaxMoney .="0";
	}
}
else $MaxMoney = 0;

$record_num = $limit; 
$limit_arr = isset($LIST_DEFINE['limit_arr']) ? $LIST_DEFINE['limit_arr'] : "10,20,30,50,100";
$tmps = explode(',',$limit_arr);
$LIMIT_OPTION = $LIMIT_OPTION2 = "";
for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
	${"SEC".$tmps[$i]} = "";
	if($tmps[$i]==$limit) $LIMIT_OPTION .= "<option value='{$tmps[$i]}' selected>{$tmps[$i]}개씩 보기</option>";
	else $LIMIT_OPTION .= "<option value='{$tmps[$i]}'>{$tmps[$i]}개씩 보기</option>";
	
	$LIMIT_OPTION2 .= "limitBoxs.addItem('{$tmps[$i]}개씩 보기','{$tmps[$i]}');";
	if($limit==$tmps[$i]) $SEC_LIMIT = $i+1;
}
${"SEC".$limit} = "selected";

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
if($TOTAL <= ($page * $record_num)) $TONUM = $TOTAL;
else $TONUM = $record_num; 
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

if($TOTAL==0) $tpl->parse("no_loop");

if($page) $tpl->parse("is_page");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>