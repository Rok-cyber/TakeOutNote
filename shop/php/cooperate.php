<?
$tpl->define("main","{$skin}/cooperate.html");
$tpl->scan_area("main");

/***********************  BANNER  ********************************/
$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '9' && status='1' ORDER BY rank ASC";
$mysql->query($sql);
while($row_ban = $mysql->fetch_array()){
	if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
	if($row_ban['link']) {
		$BLINK = str_replace("&","&amp;",$row_ban['link']);
		if($row_ban['target']=='2') $BTARGET = "target='_blank'";
		else $BTARGET = "";
	}
	else $BLINK = "#\" onclick=\"return false;";

	$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner3'],stripslashes($row_ban['name']));
	$tpl->parse("loop_banner");	
}
unset($row_ban,$BANNER,$BLINK,$BTARGET);
/***********************  BANNER  ********************************/

$sql = "SELECT code FROM mall_cate WHERE cate='999000000000'";
$code = explode("|*|",stripslashes($mysql->get_one($sql)));

if($code[0]=='Y') {
	$H_CODE	= $code[1];		
	$tpl->parse("is_h_up");	
}

include "lib/class.Paging.php";

$limit	= isset($_POST['limit']) ? $_POST['limit'] : $_GET['limit'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$order	= isset($_POST['order']) ? $_POST['order'] : $_GET['order'];
$type	= isset($_POST['type']) ? $_POST['type'] : $_GET['type'];
$today	= date("Y-m-d H:i");

$where =" && SUBSTRING(cate,1,3)='999' ";

if($search) {
	$fields_arr = Array("tag"=>"태그","name"=>"상품명","brand"=>"브랜드");
	$sstring .= "&search=".urlencode($search)."&field={$field}";
	
	if($field=='tag') $where .= "  && INSTR({$field},',{$search},')";
	else if($field=='brand') {
		$sql = "SELECT uid FROM mall_brand WHERE INSTR(name,',{$search},') || INSTR(tag,'{$search}')";
		$mysql->query($sql);
		$tmps = array();
		while($row = $mysql->fetch_array()){
			$tmps[] = $row['uid'];
		}
		if($tmps[0]) {
			$brand = join(",",$tmps);
			$where .= "  && brand IN ({$brand})";
		}
	}
	else if($field=="name") $where .= "  && (INSTR(name,'{$search}') || INSTR(search_name,'{$search}'))";
	else $where .= "  && INSTR({$field},'{$search}')";

	$fields = $fields_arr[$field];
	$tpl->parse("is_search");
}

switch($order){
	case "edate" :
		$ORDER = "coop_edate ASC";
		$SEC_ORDER = 2;
	break;
	case "sale" :
		$ORDER = "coop_sale DESC";
		$SEC_ORDER = 3;
	break;
	case "cnt" :
		$ORDER = "coop_cnt DESC";
		$SEC_ORDER = 4;
	break;
	default : 
		$ORDER = " sequence ASC, uid DESC";
		$SEC_ORDER = 1;
	break;	
	$addstring = "&amp;order={$order}";
}

if(!$limit) $limit = $LIST_DEFINE['limit'];

$LINK_TYPE = "{$Main}?channel=cooperate{$addstring}{$sstring}";

if($type) {
	switch($type) {
		case "1" : case "2" :
			$where .= " && (coop_sdate<='{$today}' && coop_edate>='{$today}')";		    
			if($type==2) $where .= " && coop_sale>0 ";
		break;
		case "3" : 
			$where .= " && coop_edate<'{$today}'";		    
		break;
		case "4" :
			$where .= " && coop_sdate>'{$today}'";		    
		break;
	}
	$addstring = "&amp;type={$type}";
}
$types = $types1 = $types2 = $types3 = $types4 = "off";
${"types".$type} = "on";

$pstring = "&page={$page}";
$sql = "SELECT COUNT(uid) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";

$TOTAL = $mysql->get_one($sql);
$record_num = $limit; 
$page_num	= 100;

$PGConf['page_record_num'] = $record_num;
$PGConf['page_link_num'] = $page_num;

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$TOTAL_PAGE = ceil($TOTAL/$record_num);	
if($TOTAL <= ($page * $record_num)) $TONUM = $TOTAL;
else $TONUM = $record_num; 
$PAGE = $page;
/*********************************** @LIMIT CONFIGURATION ***********************************/

if($TOTAL>0) {
	$sql = "SELECT uid,cate,number,name,price,image3,image4,icon,comp,reserve,c_cnt,tag,s_qty,qty,coop_sdate,coop_edate,coop_cnt,coop_pay FROM mall_goods WHERE s_qty !='3' && type='A' {$where} ORDER BY {$ORDER} LIMIT {$Pstart},{$limit}";	
	$mysql->query($sql);
	
	$ShopPath2 = $ShopPath;
	while($data = $mysql->fetch_array()){
		$gData	= getDisplay($data,'image3');		// 디스플레이 정보 가공 후 가져오기
		
		$LINK	= $gData['link'];		
		$IMAGE	= $gData['image'];
		$NAME	= $gData['name'];
		$COMP	= $gData['comp'];
		$PRICE	= $gData['price'];
		$CPRICE	= $gData['cprice'];
		$CP_PRICE= $gData['cp_price'];
		$ICON	= $gData['icon'];
		$RESE	= $gData['reserve'];
		$CCNT	= $gData['c_cnt'];
		$UID	= $data['uid'];
			
		$TAG	= "";
		$tag	= explode(",",stripslashes($data['tag']));

		for($i=1,$cnt=count($tag);$i<$cnt-1;$i++){			
			if($TAG) $TAG .= ", ";
			if(trim($tag[$i])) $TAG .= "<a href='{$Main}?channel=search&field=tag&search=".urlencode($tag[$i])."' class='small'>{$tag[$i]}</a>";
		}

		if($data['coop_sdate']>$today) {
			$TYPE = 4;
			$TYPE2 = "공구 준비중";
		}
		else if($data['coop_edate']>$today) {
			$TYPE = 1;
			$TYPE2 = "공구 진행중";
			if($data['coop_price']==$data['price']) {
				$TYPE = 2;
				$TYPE2 = "공구가 확정";
			}
		}
		else {
			$TYPE = 3;
			$TYPE2 = "공구 마감";
		}
		
		$data['coop_sdate'] = strtotime($data['coop_sdate']);
		$S_YYYY	= date("Y",$data['coop_sdate']);
		$S_YY	= date("y",$data['coop_sdate']);
		$S_MM	= date("m",$data['coop_sdate']);
		$S_DD	= date("d",$data['coop_sdate']);
		$S_HH	= date("H",$data['coop_sdate']);	 
		$S_II	= date("i",$data['coop_sdate']);	 

		$data['coop_edate'] = strtotime($data['coop_edate']);
		$E_YYYY	= date("Y",$data['coop_edate']);
		$E_YY	= date("y",$data['coop_edate']);
		$E_MM	= date("m",$data['coop_edate']);
		$E_DD	= date("d",$data['coop_edate']);
		$E_HH	= date("H",$data['coop_edate']);	 
		$E_II	= date("i",$data['coop_edate']);	 

		$participation = $data['coop_cnt'];		
		
		$sql = "SELECT * FROM mall_goods_cooper WHERE guid='{$UID}' ORDER BY qty ASC";
		$mysql->query2($sql);
		
		$coop_arr = Array();
		while($data2=$mysql->fetch_array(2)) {
			if($data2['qty'] && $data2['price']) {
				$coop_arr[] = Array($data2['qty'],$data2['price']);				
			}
		}
		
		$PRICE	= str_replace("원","",$PRICE);		
		$PRICE1 = $PRICE2 = $PRICE3 = $sec_price = '';
		$COOP_PER = $COOP_PER2 = 0;
		$COOP_ALIGN = "left";
		$cnt	=count($coop_arr);
		$START_CNT	= $coop_arr[0][0];		
		$END_CNT	= $coop_arr[$cnt-1][0];
		
		if($data['coop_pay']=='Y') {
			$PRICE2 = number_format($coop_arr[0][1],$ckFloatCnt);
			$PRICE1 = $PRICE;
			$tpl->parse("is_price1","1");
			if($participation>0) {
				$PER = round(100*$participation/$START_CNT);				
				$COOP_PER = $COOP_PER2 =round(40*$PER/100);	
				if($COOP_PER2>=100) $COOP_PER2 = 100;
				if($COOP_PER>=100) {
					$COOP_ALIGN = "right";
					$COOP_PER = 0;
				}
				if($participation>=$START_CNT) $tpl->parse("is_come","1");
			}						
		}
		else if($coop_arr[0][0]>$participation) {
			$PRICE2 = $PRICE;
			$PRICE3 = number_format($coop_arr[0][1],$ckFloatCnt);					
			$tpl->parse("is_price3","1");

			$PER = round(100*$participation/$START_CNT);				
			$COOP_PER = $COOP_PER2 =round(40*$PER/100);	
		}
		else {	
			if($cnt==1) {
				$PRICE2 = number_format($coop_arr[0][1],$ckFloatCnt);
				$i=0;
			}
			else {
				if($coop_arr[$cnt-1][0]<$participation) {
					$PRICE2 = number_format($coop_arr[$cnt-1][1],$ckFloatCnt);
					$i = $cnt-1;
				}
				else {
					for($i=0;$i<$cnt;$i++) {								
						if($coop_arr[$i][0]>=$participation) {
							$PRICE2 = number_format($coop_arr[$i][1],$ckFloatCnt);						
							break;
						}
					}
				}
			}
			$i++;
			$PRICE1 = $PRICE;			
			if($i==$cnt) {
				$tpl->parse("is_price1","1");
			}
			else {
				$PRICE3 = number_format($coop_arr[$i][1],$ckFloatCnt);
				$tpl->parse("is_price1","1");
				$tpl->parse("is_price3","1");				
			}
			
			if($END_CNT<=$participation) $COOP_PER = 0;
			else {
				$PER = round(100*$participation/$END_CNT);				
				$COOP_PER = 30 - (round(40*$PER/100));			
			}
			$COOP_PER2 = 100 - $COOP_PER;
			$COOP_ALIGN = "right";
			$tpl->parse("is_come","1");
		}
		if($COOP_PER==0) $COOP_PER = 1;

		$now_price = str_replace(",","",$PRICE2);
		$start_price = str_replace(",","",$PRICE);
		$SALE = 100 - round((100*$now_price)/$start_price);

		$SHARE_GOODS = "{$basic[1]} [{$NAME}] ";
		$SHARE_GOODS = urlencode($SHARE_GOODS);
		$SHARE_URL	= "http://".$_SERVER["SERVER_NAME"]."/{$ShopPath2}{$Main}?channel=view/{$UID}";
		$SHARE_TAG = substr($data['tag'],1,-1);
		$SHARE_TAG = urlencode($TAG);

		$tpl->parse("loop_goods");
		$tpl->parse("is_price1","2");
		$tpl->parse("is_price3","2");
		$tpl->parse("is_come","2");
		unset($coop_arr);
	}	
	
	if($TOTAL > $record_num){
		$pg_string = explode(",",$tpl->getPgstring());
		$pg = new paging($TOTAL,$page);
		$pg->addQueryString($Main."?channel=cooperate{$addstring}{$sstring}"); 
		$PAGING = $pg->print_page($pg_string[0],$pg_string[1],$pg_string[2]);  //페이징 
		$tpl->parse("define_pg");	
	}
}
else $tpl->parse("no_goods");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>