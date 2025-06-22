<?
@set_time_limit(0);
ini_set('memory_limit', '20M');

include "sub_init.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

$skin2 = $skin."/";

$keyword = $_POST['keyword'];
$fname	= isset($_GET['fname']) ? $_GET['fname'] : $_POST['fname'];
$ocnt	= isset($_GET['ocnt']) ? $_GET['ocnt'] : $_POST['ocnt'];
$type   = isset($_GET['type']) ? $_GET['type'] : $_POST['type'];

if(!$fname) $fname = "order";

if($type==2) $TITLE = "도로/건물 명";
else {
	$TITLE = "동/면/읍 명";
	$type = 1;
}

$tpl->define("main","{$skin}/pzipcode.html");
$tpl->scan_area("main");

if($keyword) {
	
	if($type==2) {
		$keyword2 = str_replace(" ","|=|",$keyword);
		$search = iconv("utf-8","euc-kr",$keyword2);
		$url = $_SERVER['HTTP_HOST'].$PHP_SELF;
		$lno = trim(readFiles("../include/LicenseNo.txt"));

		$Result = implode("", socketPost("http://itsmall.kr/zipCode/search.php?search={$search}&url={$url}&lno={$lno}"));				
		$Result = explode("|*|",$Result);
		$Result = iconv("euc-kr","utf-8",$Result[1]);
		
		if($Result=="Max") {
			$tpl->parse("is_search2");
		}
		else {
			$data = explode("|",$Result);
			for($i=$k=0;$i<count($data);$i++){
				$data2 = explode("^",$data[$i]);
				if(!$data2[0]) continue;
				
				$post_no1 = substr($data2[0],0,3);
				$post_no2 = substr($data2[0],3,3);

				$tmps = explode("(",$data2[1]);
				$addr = $tmps[0];
				$addr2 = $data2[1];

				$tpl->parse("loop");			
				$k++;
			}		
			if($k==0) $tpl->parse("no_loop");			
			$tpl->parse("is_search");
		}	
		$tpl->parse("is_type22");
	}
	else {
		$zipfile = file("../include/zip.db");
		$search_count = 0;

		while ($zipcode = each($zipfile)) {
			if(strstr(substr($zipcode[1],9,512), $keyword)) {
				$list[$search_count][zip1] = substr($zipcode[1],0,3);
				$list[$search_count][zip2] = substr($zipcode[1],4,3);    
				$addr = explode(" ", substr($zipcode[1],8));

				if ($addr[sizeof($addr)-1]) {
					$list[$search_count][addr] = str_replace($addr[sizeof($addr)-1], "", substr($zipcode[1],8));
					$list[$search_count][bunji] = trim($addr[sizeof($addr)-1]);
				}
				else $list[$search_count][addr] = substr($zipcode[1],8);

				$list[$search_count][encode_addr] = urlencode($list[$search_count][addr]);
				$search_count++;
			}    
		}

		for ($i=$k=0; $i<count($list); $i++) {				
			$post_no1	= $list[$i][zip1];
			$post_no2	= $list[$i][zip2];
			$addr		= $list[$i][addr];
			$addr2		= $addr.$list[$i][bunji];
			if(eregi("면|동|리|우체국",$list[$i][bunji])) $addr = $addr2;
			if(eregi("아파트",$addr)) {
				$tmps = explode("아파트", $addr);
				$addr = $tmps[0]."아파트";
			}
			$tpl->parse("loop");
			$k++;
		}
		if($k==0) $tpl->parse("no_loop");	
		$tpl->parse("is_search");
	}		
}
else {
	$tpl->parse("is_default");	
	if($type==2) $tpl->parse("is_type21");
}


if($fname=='order') $tpl->parse("is_order");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>