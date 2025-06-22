<?
@set_time_limit(0);
ini_set('memory_limit', '20M');

include "sub_init.php";

require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;

$skin2 = $skin."/";

$keyword = $_POST['keyword'];
$fname	= isset($_GET['fname']) ? $_GET['fname'] : $_POST['fname'];
$ocnt	= isset($_GET['ocnt']) ? $_GET['ocnt'] : $_POST['ocnt'];
if(!$fname) $fname = "order";

$tpl->define("main","{$skin}/pzipcode.html");
$tpl->scan_area("main");

if($keyword) {

	$zipfile = file("../../include/zip.db");
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

    for ($i=0; $i<count($list); $i++) {				
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
	}

	$tpl->parse("is_search");
}
else $tpl->parse("is_default");

if($fname=='order') $tpl->parse("is_order");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>