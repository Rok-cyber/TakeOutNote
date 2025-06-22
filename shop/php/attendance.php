<?
$tpl->define("main","{$skin}/attendance.html");
$tpl->scan_area("main");

$sql = "SELECT * FROM mall_attendance WHERE s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."' ORDER BY s_date ASC LIMIT 1";
$row = $mysql->one_row($sql);
$uid = $row['uid'];

if(!$uid) $tpl->parse("no_attendance");
else {
	include "{$lib_path}/lib.lun2sol.php";

	if($row['code_use']=='Y')	{ 
		$H_CODE	= stripslashes($row['code']);		
		$tpl->parse("is_h_up");	
	}
	
	$DATES = date("Y년 m월 d일");
	
	//---- 오늘 날짜
	$thisyear  = date('Y');  // 2000
	$thismonth = date('n');  // 1, 2, 3, ..., 12
	$today     = date('j');  // 1, 2, 3, ..., 31

	//------ $year, $month 값이 없으면 현재 날짜
	if (!$year) $year = $thisyear;
	if (!$month) $month = $thismonth;
	if (!$day) $day = 1;

	$sql = "SELECT count(*) FROM mall_attendance_check WHERE id='{$my_id}' && year='{$thisyear}' && month='{$thismonth}' && day='{$today}' && puid='{$uid}'";
	if($mysql->get_one($sql)==0) {
		if($row['method']=='S') $tpl->parse("is_attend");
		else if($row['method']=='R') $tpl->parse("is_attend2");
	}

	//------ 날짜의 범위 체크
	if (($year > 2038) or ($year < 1900)) alert("연도는 1900~2038년만 가능합니다.","back");
	if (($month > 12) or ($month < 0)) alert("달은 1~12만 가능합니다.","back");

	$prevmonth = $month - 1;
	$nextmonth = $month + 1;
	$prevyear = $nextyear=$year;
	if ($month == 1) {
		$prevmonth = 12;
		$prevyear = $year - 1;
	} 
	elseif ($month == 12) {
		$nextmonth = 1;
		$nextyear = $year + 1;
	}

	$mprev = "{$Main}?channel=attendance&amp;year={$prevyear}&amp;month={$prevmonth}";
	$mnext = "{$Main}?channel=attendance&amp;year={$nextyear}&amp;month={$nextmonth}";
	
	/****************** 휴일 정의 ************************/
	$HOLIDAY = Array();
	$HOLIDAY[] = array(0=>'1-1',1=>'신정'); 
	$HOLIDAY[] = array(0=>'3-1',1=>'삼일절');
	$HOLIDAY[] = array(0=>'5-5',1=>'어린이날');
	$HOLIDAY[] = array(0=>'6-6',1=>'현충일');
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

	if($month < 10) $month2="0".$month;
	else $month2 = $month;
	   
	$sql = "SELECT day FROM mall_attendance_check WHERE uid>0 && year='{$year}' && month='{$month}' && id='{$my_id}' && puid='{$uid}' ORDER BY day ASC";
	$mysql->query($sql);
	$ATTENDANCE = Array();
	$i=0;
	while($row2=$mysql->fetch_array()){  
		$ATTENDANCE[$i] = $row2['day'];
		$i++;
	}
	
	$sql = "SELECT count(*) FROM mall_attendance_check WHERE uid>0 && id='{$my_id}' && puid='{$uid}'";
	$CNTS = number_format($mysql->get_one($sql));	

	$date   = 1;
	$offset = 0;
	$maxdate = date(t, mktime(0, 0, 0, $month, 1, $year));   // the final date of $month

	while ($date <= $maxdate) {
		if ($date < 10) { $WDAY = "&nbsp;".$date; $date2 = "0".$date;}
		else { $WDAY = $date; $date2=$date;}
		
		if($date == '1') {
			$offset = date('w', mktime(0, 0, 0, $month, $date, $year));  // 0: sunday, 1: monday, ..., 6: saturday
			for($i=1;$i<=$offset;$i++){
				$ck = $offset-$i+1;
			    $WDAY2 = date('d',$sdate-((3600*24)*$ck));
				$tpl->parse("is_empt1");		  
			}
		}
		
		if($offset == 0) $styles ="orange";
		else if($offset == 6) $styles ="blue"; 
		else $styles = "dgray";
	   
		for($i=0;$i<count($HOLIDAY);$i++){
			if(in_array("{$month}-{$date}",$HOLIDAY[$i])) {
				$styles = "orange";   
			}     
		}
		
		if(in_array($date,$ATTENDANCE)) {
			$tpl->parse("is_check",1);
		}   
	   
	    if ( $date == $today  &&  $year == $thisyear &&  $month == $thismonth) $bgcolor="#F7F7F7";
		else $bgcolor="#FFFFFF";
	  
		$date++;
		$offset++;

		if ($offset == 7 && $date<=$maxdate) {
			$tpl->parse("is_tr","1");
			$offset = 0;
		}

		$tpl->parse("is_loop");
		$tpl->parse("is_tr","2");
		$tpl->parse("is_check",2);

	} // end of while

	if ($offset != 0) {
		for($i=1;$i<=(7-$offset);$i++){
			$WDAY2 = $i;
			$tpl->parse("is_empt2");		  
		}
	}

	if($row['method']=='R') {

		include "lib/class.Paging.php";

		// 변수 지정
		$page = isset($_GET['page']) ? $_GET['page'] : 1;

		$record_num	= 15;
		$page_num	= 100;

		$PGConf['page_record_num'] = $record_num;
		$PGConf['page_link_num'] = $page_num;


		$sql = "SELECT COUNT(*) FROM  mall_attendance_comment WHERE uid!=0 && puid='{$uid}'";
		$TOTAL = $mysql->get_one($sql);

		/*********************************** LIMIT CONFIGURATION ***********************************/
		$Pstart = $record_num*($page-1);
		$TOTAL_PAGE = ceil($TOTAL/$record_num);	
		$TONUM = $TOTAL - (($page-1) * $record_num);
		$PAGE = $page;
		/*********************************** @LIMIT CONFIGURATION ***********************************/

		if($TOTAL>0) {
			$sql = "SELECT * FROM mall_attendance_comment WHERE uid!=0 && puid='{$uid}' ORDER BY uid desc LIMIT {$Pstart},{$record_num}";
			$mysql->query($sql);
			$NUM = $TONUM;

			while($row2 = $mysql->fetch_array()){		
				$IDS = '';
				for($i=0;$i<strlen($row2['id']);$i++){
					if($i%2==0) $IDS .= substr($row2['id'],$i,1);
					else $IDS .= "*";
				}
				
				if($my_level>8) {
					$DEL_LINK = "php/attendance_ok.php?mode=comment_del&amp;uid={$row2['uid']}&amp;page={$page}";
					$tpl->parse("is_del");	
				}
				
				$COMMENT = htmlspecialchars(stripslashes($row2['comment']));
				$COMMENT = preg_replace("/  /", "&nbsp;&nbsp;", $COMMENT);
				
				$DATE = date('Y-m-d',$row2['signdate']);
				$D_YYYY	= date("Y",$row2['signdate']);
				$D_YY	= date("y",$row2['signdate']);
				$D_MM	= date("m",$row2['signdate']);
				$D_DD	= date("d",$row2['signdate']);
				$D_HH	= date("h",$row2['signdate']);	 
				$D_II	= date("i",$row2['signdate']);	 
				$D_AP	= date("A",$row2['signdate']);	 
				$D_ap	= date("a",$row2['signdate']);	 
					
				$tpl->parse("loop2");
				$NUM--;
			}

			if($TOTAL > $record_num){
				$pg_string = explode(",",$tpl->getPgstring());
				$pg = new paging($TOTAL,$page);
				$pg->addQueryString($Main."?channel=attendance"); 
				$PAGING = $pg->print_page($pg_string[0],$pg_string[1],$pg_string[2]);  //페이징 
				$tpl->parse("define_pg");	
			}

			$tpl->parse("is_loop2");
			$tpl->parse("is_del","2");	
		}
		else {
			$PAGE = 0;
			$TOTAL_PAGE = 0;
			$tpl->parse("no_loop2");
		}
		$tpl->parse("is_attend3");
	
	}

}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>