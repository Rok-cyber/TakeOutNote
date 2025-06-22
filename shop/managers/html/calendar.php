<?
//--------------------------------------------------------------------
//  PREVIL Calendar
//
//  - calendar.php / lun2sol.php
//
//  - Programmed by previl(previl@hanmail.net)
//  
//--------------------------------------------------------------------
include "../ad_init.php";
include "lun2sol.php";   //양음변환 인클루드

function ErrorMsg($msg) {
	echo " <script>                ";
	echo "   window.alert('$msg'); ";
	echo "   history.go(-1);       ";
	echo " </script>               ";
	exit;
}

function SkipOffset($no,$sdate='',$edate='') {  
	for($i=1;$i<=$no;$i++) { 
		$ck = $no-$i+1;
		if($sdate) $num = date('d',$sdate-((3600*24)*$ck));
		if($edate) $num=$i;
		echo "  <TD align=center><font class=num2>$num</font></TD> \n";	
	}
}

$cellh  = 20;  // date cell height
$tablew = 174; //table width

$thisyear  = date('Y');  // 2000
$thismonth = date('n');  // 1, 2, 3, ..., 12
$today     = date('j');  // 1, 2, 3, ..., 31

//------ $year, $month 값이 없으면 현재 날짜
$year	= isset($_GET['year']) ? $_GET['year'] : $thisyear;
$month	= isset($_GET['month']) ? $_GET['month'] : $thismonth;
$day	= isset($_GET['day']) ? $_GET['day'] : $today;

//------ 날짜의 범위 체크
if (($year > 2038) or ($year < 1900)) ErrorMsg("연도는 1900~2038년만 가능합니다.");
if (($month > 12) or ($month < 0)) ErrorMsg("달은 1~12만 가능합니다.");

$maxdate = date(t, mktime(0, 0, 0, $month, 1, $year));   // the final date of $month

if ($day>$maxdate) ErrorMsg("$month 월 에는 $lastday 일이 마지막 날입니다.");

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

/****************** 휴일 정의 ************************/
$HOLIDAY = Array();
$HOLIDAY[] = array(0=>'1-1',1=>'신정'); 
$HOLIDAY[] = array(0=>'3-1',1=>'삼일절');
//$HOLIDAY[] = array(0=>'4-5',1=>'식목일');
$HOLIDAY[] = array(0=>'5-5',1=>'어린이날');
$HOLIDAY[] = array(0=>'6-6',1=>'현충일');
$HOLIDAY[] = array(0=>'7-17',1=>'제헌절');
$HOLIDAY[] = array(0=>'8-15',1=>'광복절');
$HOLIDAY[] = array(0=>'10-3',1=>'개천절');
$HOLIDAY[] = array(0=>'12-25',1=>'성탄절');

$tmp = lun2sol($year."0101");   //설날
$HOLIDAY[] = array(0=>date("n-j",($tmp-(3600*24))),1=>'설연휴');
$HOLIDAY[] = array(0=>date("n-j",$tmp),1=>'설날');
$HOLIDAY[] = array(0=>date("n-j",($tmp+(3600*24))),1=>'설연휴');;

$tmp = lun2sol($year."0408");   //석탄일
$HOLIDAY[] = array(0=>date("n-j",$tmp),1=>'석탄일');

$tmp = lun2sol($year."0815");   //추석
$HOLIDAY[] = array(0=>date("n-j",($tmp-(3600*24))),1=>'추석연휴');;
$HOLIDAY[] = array(0=>date("n-j",$tmp),1=>'추석');;
$HOLIDAY[] = array(0=>date("n-j",($tmp+(3600*24))),1=>'추석연휴');;

unset($tmp);
/****************** 휴일 정의 ************************/

if($month < 10) $month2="0".$month;
else $month2 = $month;

$sql = "SELECT * FROM mall_schedule_date WHERE uid!='' && date like '{$year}-{$month2}%' && SM!='M' ORDER BY date ASC";
$mysql->query($sql);
$SUBJECT = Array();
while($row=$mysql->fetch_array()){  
	$SUBJECT[] = array(0=>substr($row['date'],5,5),1=>$row['uid'],2=>stripslashes($row['subject']));
}

if($month<3) $year2 = $year - 1;
else $year2 = $year;
$sql = "SELECT * FROM mall_schedule_date WHERE uid!='' && date like '{$year}%' && sm='M' ORDER BY date ASC";
$mysql->query($sql);
while($row=$mysql->fetch_array()){  
	$tmp2 = explode("-",$row['date']);
	$tmp = lun2sol("{$tmp2[0]}{$tmp2[1]}{$tmp2[2]}");   
	$SUBJECT[] = array(0=>substr(date("Y-m-d",$tmp),5,5),1=>$row['uid'],2=>stripslashes($row['subject']));
}

echo("
<html>
<head>
<title>Calendar</title>
<meta http-equiv='content-type' content='text/html; charset=utf-8'>

<style>
body, table, tr, td, select, textarea, input{ font-family:굴림체, seoul, arial, helvetica; 	font-size: 9pt; color: #505050; }
.small {font-family:돋움,돋움체,Tahoma; font-size:8pt;letter-spacing:-1px;text-decoration:none} 
.orange { color:#ED6C1F; }
.blue	{ color:#3399FF; }
.bold	{ font-weight:bold;}
.eng {font-family:verdana; font-size:11px; letter-spacing:0px;}
font {font-family:굴림체; font-size: 12px; color:#505050;}
font.week {font-family:돋움,돋움체; color:#ffffff;font-size:8pt;letter-spacing:-1}
font.num {font-family:tahoma; font-size:12px;}
font.holy {font-family:tahoma; font-size:12px; color:#FF6C21;}
font.holy2 {font-family:tahoma; font-size:12px; color:#3399ff;}
font.holy3 {font-family:tahoma; font-size:12px; color:#00BB00;}
font.num2 {font-family:tahoma; font-size:12px; color:#dadada;}
</style>

</head>
<body topmargin=0 leftmargin=0>       

<table width=164 cellspacing=0 cellpadding=0 border=0>
<tr><td height='6'></td>
<tr><td>

<TABLE cellSpacing=0 cellPadding=0 width=$tablew border=0>
<TR><TD align=center>
        <TABLE cellSpacing=0 cellPadding=0 width=100% border=0>
		<TR><TD width=15% align=center>
		        <a href=$PHP_SELF?year=$prevyear&month=$prevmonth&day=1 onfocus='this.blur()'><img src='img/c_pre.gif' border=0 onfocus='this.blur();' align=absmiddle alt='이전달' title='이전달' /></a>        
            </TD>
			<TD width=70% align=center>
				<font class='eng bold blue'>{$year}</font><font class='small bold blue'>년</font> <font class='eng bold blue'>{$month}</font><font class='small bold blue'>월</font>
            </TD>
			<TD width=15% align=center>
				<a href=$PHP_SELF?year=$nextyear&month=$nextmonth&day=1 onfocus='this.blur()'><img src='img/c_next.gif' border=0 onfocus='this.blur();' align=absmiddle alt='다음달' title='다음달' /></a>
	        </TD>
		</TR>		
		</TABLE>
    </TD>
</TR>
<TR><TD height=3></TD></TR>
<TR><TD align=center>
        <TABLE cellSpacing=0 cellPadding=0 width=90% border=0>  
		<TR>
		    <TD bgcolor=#bbbbbb><TABLE cellSpacing=0 cellPadding=0 width=1 height=1 border=0><TR><TD bgcolor=#ffffff></TD></TR></TABLE></TD>
		    <TD colspan=7 bgcolor=#bbbbbb height=1></TD>
			<TD bgcolor=#bbbbbb align=right><TABLE cellSpacing=0 cellPadding=0 width=1 height=1 order=0><TR><TD bgcolor=#ffffff></TD></TR></TABLE></TD>
		</TR>
		<TR><TD colspan=9 bgcolor=#bbbbbb height=3></TD></TR>
		<TR bgcolor=#bbbbbb>
		    <TD width=1%></TD>
			<TD width=14% align=center><font class=week>일</font></TD>            
			<TD width=14% align=center><font class=week>월</font></TD>
			<TD width=14% align=center><font class=week>화</font></TD>
			<TD width=14% align=center><font class=week>수</font></TD>
			<TD width=14% align=center><font class=week>목</font></TD>
			<TD width=14% align=center><font class=week>금</font></TD>
			<TD width=14% align=center><font class=week>토</font></TD>
			<TD width=1%></TD>
        </TR>
		<TR><TD colspan=9 bgcolor=#bbbbbb height=1></TD></TR>
		<TR>
		    <TD bgcolor=#bbbbbb><TABLE cellSpacing=0 cellPadding=0 width=1 height=1 border=0><TR><TD bgcolor=#ffffff></TD></TR></TABLE></TD>
		    <TD colspan=7 bgcolor=#bbbbbb height=1></TD>
			<TD bgcolor=#bbbbbb align=right><TABLE cellSpacing=0 cellPadding=0 width=1 height=1 order=0><TR><TD bgcolor=#ffffff></TD></TR></TABLE></TD>
		</TR>
");

echo("
		<TR height=$cellh><TD></TD>
        <!-- 날짜 테이블 -->
");

$date   = 1;
$offset = 0;
$ck_row=0; //프레임 사이즈 조절을 위한 체크인자

while ($date <= $maxdate) {   
	if ($date < 10) { $date2 = "&nbsp;".$date; $date3 = "0".$date; }
	else $date2 = $date3 = $date; 
	if($date == '1') {
		$offset = date('w', mktime(0, 0, 0, $month, $date, $year));  // 0: sunday, 1: monday, ..., 6: saturday
		SkipOffset($offset,mktime(0, 0, 0, $month, $date, $year));
	}

	if($offset == 0) $style ="holy";
	else if($offset == 6) $style ="holy2";
	else $style = "num";
   
	$title = '';
	for($i=0;$i<count($HOLIDAY);$i++){	   
		if($HOLIDAY[$i][0] =="$month-$date") {
			$style = "holy"; 
			$title = "{$month}월 {$date}일은 ".$HOLIDAY[$i][1]." 입니다";
			break;
		}	   
	}

	$cnt = 0;
	for($i=0;$i<count($SUBJECT);$i++){
		if(in_array("{$month2}-{$date3}",$SUBJECT[$i])) {
			$style = "holy3"; 
			if($title) $title .= "\n";
			$title .= "{$month}월 {$date}일 일정\n".$SUBJECT[$i][2];
			$cnt++;
		}     
	}

	if($title) {
		$date2 = "<font title='{$title}' class='{$style}' style=cursor:hand>{$date2}</font>"; 
	}

	if ( $date == $today  &&  $year == $thisyear &&  $month == $thismonth) echo "<TD align=center valign=middle><TABLE cellpadding=0 cellspacing=0 border=0 width=20 height=20><TR><TD background='img/date_bg.gif' align=center><font class=num>$date2</font></TD></TR></TABLE></TD> \n";
	else echo "  <TD align=center><font class={$style}>{$date2}</font></TD> \n";
  
	$date++;
	$offset++;

	if($offset == 7) {
		echo "<TD></TD></TR> \n";
		if ($date <= $maxdate) {
			echo "<TR height=$cellh><TD></TD>\n";
			$ck_row++;
		}
		$offset = 0;
	}

} // end of while

if ($offset != 0) {
	SkipOffset((7-$offset),'','1');
	echo "<TD></TD></TR> \n";
}
echo("
<!-- 날짜 테이블 끝 -->
        </TD>
     </TR>
	 </TABLE>
<TR><TD height=3></TD></TR>
</TABLE>

	</td>
</tr> 
</table>
</body>
</html>
")
?>