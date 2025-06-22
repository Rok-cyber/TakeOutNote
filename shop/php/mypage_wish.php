<?
include "lib/class.Paging.php";

// 변수 지정
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$record_num	= 15;
$page_num	= 100;

$PGConf['page_record_num'] = $record_num;
$PGConf['page_link_num'] = $page_num;

$sql = "SELECT count(*) FROM mall_wish a, mall_goods b WHERE a.id='{$my_id}' && a.p_number=b.uid";
$TOTAL = $mysql->get_one($sql);
/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
$TONUM = $TOTAL - (($page-1) * $record_num);
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

$tpl->define("main","{$skin}/mypage_wish.html");
$tpl->scan_area("main");

/**************************** GOODS LIST**************************/

if($TOTAL>0) {
	$sql = "SELECT a.uid as uid2, a.memo, a.signdate, b.uid, b.cate, b.number, b.name, b.price, b.price_ment, b.image4, b.icon, b.reserve, b.event FROM mall_wish a, mall_goods b WHERE a.id='{$my_id}' && a.p_number=b.uid order by a.uid desc LIMIT $Pstart,$record_num";
	$mysql->query($sql);
	
	$i =1;
	while($data = $mysql->fetch_array()){

		$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기

		$LINK	= $gData['link'];
		$IMAGE	= $gData['image'];
		$NAME	= $gData['name'];	
		$ICON	= $gData['icon'];			
		$PRICE	= $gData['price'];
		$PRICE2 = str_replace("원","",$PRICE);
		$UID	= $data['uid2']; 
		$CATE	= $data['cate'];
		$QLINK	= $data['uid'];
		$RESE	= $gData['reserve'];	
		$DRAGD	= $gData['dragd'];
		$MEMO   = stripslashes($data['memo']);
		$DATE	= date("Y-m-d",$data['signdate']);
		$LOC = getLocation($data['cate'],'1');
		
		$tpl->parse('loop');	   				
		$i++;
	}

	if($TOTAL > $record_num){
		$pg_string = explode(",",$tpl->getPgstring());
		$pg = new paging($TOTAL,$page);
		$pg->addQueryString($Main."?channel=wish"); 
		$PAGING = $pg->print_page($pg_string[0],$pg_string[1],$pg_string[2]);  //페이징 
		$tpl->parse("define_pg");	
	}

	$tpl->parse("is_loop");
}
else {
	$PAGE = 0;
	$TOTALPAGE = 0;
	$tpl->parse("no_loop");
}


/**************************** GOODS LIST**************************/

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>