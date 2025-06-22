<?
$tpl->define("main","{$skin}/goods_today.html");
$tpl->scan_area("main");

if($main_dsp[1]=='1')	{ $H_CODE	= $main_dsp[2];		$tpl->parse("is_h_up");	}

include "lib/class.Paging.php";

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
$mo1	= isset($_POST['mo1']) ? $_POST['mo1'] : $_GET['mo1'];
$mo2	= isset($_POST['mo2']) ? $_POST['mo2'] : $_GET['mo2'];

if(($mo1 || $mo1==0) && $mo2) {
	$ajaxstring .= "&mo1={$mo1}&mo2={$mo2}";
    $SEC_MON = number_format($mo1)."원 ~ ".number_format($mo2)."원";   
	$tpl->parse("is_sec_mon");
	SetCookie("search_yn","Y",0,"/");
}
else SetCookie("search_yn","",-999,"/");

@session_start();
if($_SESSION['today_view']) {
	$tmp = explode(',',$_SESSION['today_view']);
	$cnt = count($tmp);
		
	$TOTAL = 0;
	$max_price = 0;
	for($i=0,$Tcnt=0;$i<=$cnt;$i++){
		$tmp2 = explode(":",$tmp[$i]);			
		$sql = "SELECT price FROM mall_goods WHERE uid='{$tmp2[1]}'";
		if($price=$mysql->get_one($sql)){						
			if($price>=$mo1 && $price<=$mo2) $TOTAL++;
			if($price>$max_price) $max_price = $price;
		}				

	}	
	if(!$mo1 && !$mo2) $TOTAL = $cnt -1;
}
else $TOTAL = 0;

if($TOTAL>0) {
	$tmps = $max_price;
	if(substr($tmps,0,1)==9) $MaxMoney = "10";
	else $MaxMoney = substr($tmps,0,1)+1;

	for($i=1,$cnt=strlen($tmps);$i<$cnt;$i++){
		$MaxMoney .="0";
	}
}
else $MaxMoney = 0;


if($TOTAL==0) $tpl->parse("no_loop");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>