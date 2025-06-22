<?
$tpl->define("main","{$skin}/affiliate_count_site.html");
$tpl->scan_area("main");

$cnts=0;
$TTL1 = "사이트별"; 

$sql = "SELECT SUM(cnts) as total FROM pcount_agent_affiliate WHERE type='S' {$where}";
if($TOTAL = $mysql->get_one($sql)) {

	$sql = "SELECT count(*) FROM pcount_agent_affiliate WHERE type='S' {$where}";
	$total_record = $mysql->get_one($sql);

	/*********************************** LIMIT CONFIGURATION ***********************************/
	if(!$page = $_GET['page']) $page=1;
	$record_num = 30;
	$page_num = 15;
	$Pstart = $record_num*($page-1);	
	$NUM = $Pstart+1;
	/*********************************** @LIMIT CONFIGURATION ***********************************/

	$sql = "SELECT cnts FROM pcount_agent_affiliate WHERE type='S' {$where} ORDER BY cnts DESC LIMIT 1";
	if(!$MAX = $mysql->get_one($sql)) $MAX=0;

	$sql = "SELECT * FROM pcount_agent_affiliate WHERE type='S' {$where} ORDER BY cnts DESC LIMIT $Pstart,$record_num";
	$mysql->query($sql);
	
	while($data=$mysql->fetch_array()){
		$TTL2 = $data['content'];
		$TTL2 = "<a href='http://{$TTL2}' target=_blank>{$TTL2}</a>";
		$CNT = number_format($data[cnts]);		
		$PER = round($data[cnts]/$TOTAL*100,1);		
		$GRP = round((100*$data[cnts])/$MAX,0);
		$tpl->parse("loop");
		$NUM++;
	}

    if($total_record>$record_num) {
		include ("{$lib_path}/class.Paging.php");
		$pg = new paging($total_record,$page,$record_num,$page_num);
        $pg->addQueryString("?channel=site"); 
        $PAGING = $pg->print_page();  //페이징 
    }

} 
else $TOTAL=$CNT=$PER=$GRP=0; 

$TOTAL = number_format($TOTAL);		

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>