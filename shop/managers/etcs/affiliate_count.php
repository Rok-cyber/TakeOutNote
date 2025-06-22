<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;
$skin = ".";

$channel = $_GET['channel'];
if(!$channel) $channel='affili';
$affiliates = $_GET['affiliates'];

//---- 오늘 날짜
$thisyear  = date('Y');  // 2000
$thismonth = date('n');  // 1, 2, 3, ..., 12
$today     = date('j');  // 1, 2, 3, ..., 31

//------ $year, $month 값이 없으면 현재 날짜
$year	= isset($_POST['year'])? $_POST['year'] : $_GET['year'];
$month	= isset($_POST['month'])? $_POST['month'] : $_GET['month'];
$day	= isset($_POST['day'])? $_POST['day'] : $_GET['day'];

if (!$year) $year = $thisyear;
if (!$month) $month = $thismonth;
if (!$day) $day = $today;

//------ 날짜의 범위 체크
if (($year > 2038) or ($year < 1900)) Palert("연도는 1900~2038년만 가능합니다.","back");
if (($month > 12) or ($month < 0)) Palert("달은 1~12만 가능합니다.","back");

$maxdate = date(t, mktime(0, 0, 0, $month, 1, $year));   // the final date of $month
if ($day>$maxdate) $day = $maxdate;

$sql = "SELECT year,month,day FROM pcount_list_affiliate ORDER by uid ASC LIMIT 1";
$data = $mysql->one_row($sql);

if(!$s_year = $data['year']) $s_year = $thisyear;

$tpl->define("main","{$skin}/affiliate_count_top.html");
$tpl->scan_area("main");

$img_arr = Array("","affili","year","month","day","hour","site");
for($i=1;$i<7;$i++){
	if($channel==$img_arr[$i]) ${"tabs".$i} = "tab_on";
	else ${"tabs".$i} = "tab_off";
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

######################## 입점사 설정 ############################
$sql = "SELECT * FROM mall_affiliate ORDER BY uid ASC";
$mysql->query($sql);

while($row=$mysql->fetch_array()){
	if($row['id'] == $affiliates) $sec = 'selected';
	else $sec='';
	$AFFILIATE .= "<option value='{$row['id']}' {$sec}>{$row['id']}</option>\n";
}	

if($affiliates) {
	$where = " && affiliate = '{$affiliates}'";
	$where2 = " && a.affiliate = '{$affiliates}'";
	$addstring = "&affiliates=$affiliates";
}
else {
	$where = " && affiliate !=''";
	$where2 = " && a.affiliate !=''";
}


switch ($channel) {
	case "year" : case "month" : case "day" : case "hour" : case "site" :  			
		include "affiliate_count_{$channel}.php";
	break;
	default : 		
		include "affiliate_count_affili.php"; 
	break;
}

?>

			</div>
		</div>
		<div class="bottom"></div>
		</span>		
	</div>
</div>


<?
include "../html/bottom_inc.html"; // 하단 HTML
?>