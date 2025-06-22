<?
@set_time_limit(0);
ini_set('memory_limit', '20M');

include "../ad_init.php";

require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;

$skin = ".";
$keyword = $_POST['keyword'];
$num	= isset($_POST['num']) ? $_POST['num'] : $_GET['num'];
$tmps = '';

$tpl->define("main","{$skin}/zipcode.html");
$tpl->scan_area("main");

if($keyword) {

	$zipfile = file("{$inc_path}/zip.db");
	$search_count = 0;

    while ($zipcode = each($zipfile)) {
        if(strstr(substr($zipcode[1],9,512), $keyword)) {
           
			$list[$search_count][zip1] = substr($zipcode[1],0,3);
            $list[$search_count][zip2] = "000";    
            $addr = explode(" ", substr($zipcode[1],8));

            if ($addr[sizeof($addr)-1]) {
                $list[$search_count][addr] = str_replace($addr[sizeof($addr)-1], "", substr($zipcode[1],8));               
            }
            else $list[$search_count][addr] = substr($zipcode[1],8);
			
			$ck_tmps = explode(" ",$list[$search_count][addr]); 
			$ck_tmps = $ck_tmps[0]." ".$ck_tmps[1];

			if($tmps==$ck_tmps) continue;
			if(!eregi($keyword,$ck_tmps)) continue;

            $list[$search_count][encode_addr] = urlencode($list[$search_count][addr]);
			$list[$search_count][addr] = $ck_tmps;
			$tmps = $ck_tmps;
            $search_count++;
        }    
    }

    for ($i=0; $i<count($list); $i++) {				
		$post_no1	= $list[$i][zip1];
		$post_no2	= $list[$i][zip2];
		$addr		= $list[$i][addr];
		$tpl->parse("loop");
	}

	$tpl->parse("is_search");
}
else $tpl->parse("is_default");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>