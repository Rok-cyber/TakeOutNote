<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;
$skin = ".";

$channel = $_GET['channel'];
if(!$channel) $channel='hour';

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

$prevmonth = $month - 1;
$nextmonth = $month + 1;
$prevyear = $nextyear=$year;
if ($month == 1) {
    $prevmonth = 12;
    $prevyear = $year - 1;
} elseif ($month == 12) {
    $nextmonth = 1;
    $nextyear = $year + 1;
}

$tpl->define("main","{$skin}/count_top.html");
$tpl->scan_area("main");

$date   = 1;
$ck_row=0; //프레임 사이즈 조절을 위한 체크인자

$offset = date('w', mktime(0, 0, 0, $month, $date, $year));  // 0: sunday, 1: monday...
for($i=0;$i<$offset;$i++) $blank .= "</td><td>";

while ($date <= $maxdate) {
   if ($offset == 7) {
	   $tpl->parse("is_tr","1");
       $offset = 0;
   }
   if ($date < 10) $DATES = "&nbsp;".$date;
   else $DATES = $date;   

   if($offset == 0) $style ="holy";
   else if($offset == 6) $style ="satur";
   else $style = "";
   if($year==$thisyear && $month==$thismonth && $today==$date) $style="today";
   
   $tpl->parse("loop");
   $tpl->parse("is_tr","2");
   $date++;
   $offset++;
} 

$sql = "SELECT year,month,day FROM pcount_list ORDER by uid ASC LIMIT 1";
$data = $mysql->one_row($sql);

if(!$s_year = $data[year]) $s_year = $thisyear;
if(!$s_month = $data[month]) $s_month = $thismonth;
if(!$s_day = $data[day]) $s_day = $today;

$afterday = (int)( mktime (0,0,0,$thismonth,$today,$thisyear) - mktime (0,0,0,$s_month,$s_day,$s_year) ) / 86400 +1; 

$SINCE = "{$s_year}-{$s_month}-{$s_day} ({$afterday}days)";

$sql = "SELECT total,total2 FROM pcount_list WHERE year='{$thisyear}' && month='{$thismonth}' && day='{$today}'";
$data = $mysql->one_row($sql);
if($data[total]) $TODAY = number_format($data[total]);
else $TODAY=0;
if($data[total2]) $TODAY2 = number_format($data[total2]);
else $TODAY2=0;

$yesterday = date("Y-m-d",(mktime (0,0,0,$thismonth,$today,$thisyear)-86400)); 
$yesterday = explode("-",$yesterday);
$sql = "SELECT total,total2 FROM pcount_list WHERE year='{$yesterday[0]}' && month='{$yesterday[1]}' && day='{$yesterday[2]}'";
$data = $mysql->one_row($sql);
if($data[total]) $YESTERDAY = number_format($data[total]);
else $YESTERDAY=0;
if($data[total2]) $YESTERDAY2 = number_format($data[total2]);
else $YESTERDAY2=0;

$sql = "SELECT sum(total) as total, sum(total2) as total2 FROM pcount_list WHERE year='{$thisyear}' && month='{$thismonth}'";
$data = $mysql->one_row($sql);
if($data[total]) $MONTH = number_format($data[total]);
else $MONTH=0;
if($data[total2]) $MONTH2 = number_format($data[total2]);
else $MONTH2=0;

$sql = "SELECT total,year,month,day FROM pcount_list ORDER BY total DESC LIMIT 1";
$data = $mysql->one_row($sql);
$data[total] = number_format($data[total]);
$MAXI = "{$data[total]} ({$data[year]}-{$data[month]}-{$data[day]})";

$sql = "SELECT total,year,month,day FROM pcount_list WHERE !(year='{$thisyear}' && month='{$thismonth}' && day='{$today}') ORDER BY total ASC LIMIT 1";
$data = $mysql->one_row($sql);
$data[total] = number_format($data[total]);
$MINI = "{$data[total]} ({$data[year]}-{$data[month]}-{$data[day]})";

$sql = "SELECT sum(total) as total, sum(total2) as total2 FROM pcount_list";
$data = $mysql->one_row($sql);

$P_TOTAL = number_format($pinfo[8]); //초기값
if($data[total]) {	
	$C_TOTAL = number_format($data[total]);
	$TOTAL = number_format($data[total] + $pinfo[8]);
} else {
	$C_TOTAL=0;
	$TOTAL = $P_TOTAL;
}
if($P_TOTAL) $TOTALS = "({$C_TOTAL}+{$P_TOTAL})";

if($data[total2]) $TOTAL2 = number_format($data[total2]);
else $TOTAL2=0;

if($afterday) {
	$AVER = number_format($data[total]/$afterday);
	$AVER2 = number_format($data[total2]/$afterday);
} else $AVER=$AVER2=0; 

$img_arr = Array("","year","month","day","hour","browser","os","site","refer","keyword");
for($i=1;$i<10;$i++){
	if($i<5 || $i==6) $tmps = "2";
	else $tmps = '';

	if($channel==$img_arr[$i]) ${"tabs".$i} = "tab{$tmps}_on";
	else ${"tabs".$i} = "tab{$tmps}_off";
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

$data=$afterday=$TODAY=$TODAY2=$YESTERDAY=$YESTERDAY2=$MONTH=$MONTH2=$AVER=$AVER2=$TOTAL=$TOTAL2=$MINI=$MAXI='';

switch ($channel) {
	case "year" : case "month" : case "day" : case "hour" : case "browser" : case "os" : case "site" : case "refer" : case "keyword" :   			include "count_{$channel}.php";
	break;
	default : 		
		include "count_hour.php"; 
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