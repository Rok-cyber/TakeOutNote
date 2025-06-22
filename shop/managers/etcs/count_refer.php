<?
$tpl->define("main","{$skin}/count_{$channel}.html");
$tpl->scan_area("main");

$cnts=0;

$sql = "SELECT SUM(hit) as total FROM pcount_refer";
if($TOTAL = $mysql->get_one($sql)) {

	$sql = "SELECT count(*) FROM pcount_refer";
	$total_record = $mysql->get_one($sql);

	/*********************************** LIMIT CONFIGURATION ***********************************/
	if(!$page = $_GET['page']) $page=1;
	$record_num = 30;
	$page_num = 15;
	$Pstart = $record_num*($page-1);
	$NUM = $Pstart+1;
	/*********************************** @LIMIT CONFIGURATION ***********************************/

	$sql = "SELECT * FROM pcount_refer ORDER BY hit DESC, uid ASC LIMIT $Pstart,$record_num";
	$mysql->query($sql);
	
	while($data=$mysql->fetch_array()){
		$REFER2 = str_replace("ie=utf8","",$data['referer']);
		$REFER = "<a href='{$REFER2}' onfocus='this.blur();' target='_blank'><font class=eng>{$data['referer']}</font></a>";
		$CNT = number_format($data[hit]);		
		$PER = round($data[hit]/$TOTAL*100,1);		
		$tpl->parse("loop");
		$NUM++;
	}

    if($total_record>$record_num) {
		include ("$lib_path/class.Paging.php");
		$pg = new paging($total_record,$page,$record_num,$page_num);
        $pg->addQueryString("?channel=refer"); 
        $PAGING = $pg->print_page();  //페이징 
    }

} else $TOTAL=$CNT=$PER=0; 

$TOTAL = number_format($TOTAL);

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>