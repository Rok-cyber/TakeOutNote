<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$search	= $_GET['search'];
$comp	= $_GET['comp'];
$mo1	= $_GET['mo1'];
$mo2	= $_GET['mo2'];
$cate	= $_GET['cate'];
$cate2	= $_GET['cate2'];
$order	= $_GET['order'];
$Pstart	= $_GET['Pstart'];
$limit	= $_GET['limit'];
$brand	= $_GET['brand'];
$special= $_GET['special'];
$event	= $_GET['event'];
$type	= $_GET['type'];
$field  = $_GET['field'];
$mode	= $_GET['mode'];
$vendor	= $_GET['vendor'];
$sold_ck = 0;

if(!$limit || strlen($Pstart)==0) { 	
	echo "<error>Error</error></root>"; 
	exit;
}

if($search) {
	$tmp_search = explode("|*|",$search);
	for($i=0;$i<count($tmp_search);$i++){
		$tmp_search2 = str_replace(" ","",$tmp_search[$i]);

		if($field=='tag') $where .= "  && INSTR({$field},',{$tmp_search[$i]},')";
		else if($field=='brand') {
			$sql = "SELECT uid FROM mall_brand WHERE INSTR(name,'{$tmp_search[$i]}') || INSTR(tag,'{$tmp_search[$i]}')";
			$mysql->query($sql);
			$tmps = array();
			while($row = $mysql->fetch_array()){
				$tmps[] = $row['uid'];
			}
			if($tmps[0]) {
				$brands = join(",",$tmps);
				$where .= "  && brand IN ({$brands})";
			}
		}
		else if($field) {
			if($field=="name") $where .= "  && (INSTR(name,'{$tmp_search[$i]}') || INSTR(search_name,'{$tmp_search2}'))";
				else $where .= "  && INSTR({$field},'{$tmp_search[$i]}')";
		}
		else {
			$sql = "SELECT uid FROM mall_brand WHERE INSTR(name,'{$tmp_search[$i]}') || INSTR(tag,'{$tmp_search[$i]}')";
			$mysql->query($sql);
			$tmps = array();
			while($row = $mysql->fetch_array()){
				$tmps[] = $row['uid'];
			}
			if($tmps[0]) {
				$brands = join(",",$tmps);
				$brand_where = " || brand IN ({$brands})";
			}
			else $brand_where = '';
			$where .= "  && (INSTR(name,'{$tmp_search[$i]}') || INSTR(search_name,'{$tmp_search2}') || INSTR(tag,',{$tmp_search[$i]},') {$brand_where})";
		}
	}	
}

$sql = "SELECT access_level, cate FROM mall_cate";
if($cate) $sql .= " WHERE SUBSTRING(cate,1,3) = '".substr($cate,0,3)."'";
	
$mysql->query($sql);
while($tmps=$mysql->fetch_array()) {
	if($tmps['access_level'] && $my_level<9) {
		$access_level = explode("|",$tmps['access_level']);
		if(($access_level[1]=='!=' && $access_level[0]!=$my_level) || ($access_level[1]=='<' && $access_level[0]>$my_level)) {
			$where .= " && cate != '{$tmps['cate']})' ";				
		}
	}
}
unset($access_level);


if($mode) {
	if($mode=='new') {
		$dates = strtotime('-1 MONTH', time());
		$where .= "&& signdate > '{$dates}'";
	}
	else {
		$where .= "&& (SUBSTRING(display,1,1)!='0' || SUBSTRING(display,3,1)!='0')";
	}
}

if($comp) $where .= "&& INSTR(comp,'{$comp}')";
if(($mo1 || $mo1==0) && $mo2) $where .= " && price BETWEEN '{$mo1}' AND '{$mo2}' ";

if($cate2) {
	$where .= "&& (cate='{$cate2}' || INSTR(mcate,'{$cate2}'))";
	$cstring .= "&cate2={$cate2}";
	$sql = "SELECT soldout FROM mall_cate WHERE cate='{$cate2}'";
	$sold_ck = $mysql->get_one($sql);
}
if($cate) {
	$cate_len = strlen($cate);
	switch($cate_len) {
        case "3" : 
			$where .= " && (SUBSTRING(cate,1,3) = '{$cate}' || INSTR(mcate,',{$cate}'))";
			$cate2 = "{$cate}000000000";
			$sql = "SELECT soldout FROM mall_cate WHERE cate='{$cate2}'";
		break;
		case "6" : 
			$where .= " && (SUBSTRING(cate,1,6) = '{$cate}' || INSTR(mcate,',{$cate}'))";
			$cate2 = "{$cate}000000";
			$sql = "SELECT soldout FROM mall_cate WHERE cate='{$cate2}'";
		break;		
		case "9" : 
			$where .= " && (SUBSTRING(cate,1,9) = '{$cate}' || INSTR(mcate,',{$cate}'))";
			$cate2 = "{$cate}000";
			$sql = "SELECT soldout FROM mall_cate WHERE cate='{$cate2}'";
		break;		
    }	
	$cstring .= "&cate={$cate}";
	$sold_ck = $mysql->get_one($sql);
}

switch($order){
	case "price" : 
		$ORDER = " price ASC";
	break;
	case "price2" : 
		$ORDER = " price DESC";
	break;
	case "best" : 
		$ORDER = " o_cnt DESC";
	break;
	case "uid2" : 
		$ORDER = " uid ASC";
	break;
	default : 
		$ORDER = " sequence ASC, uid DESC";
	break;	
}

if($brand) $where .= " && brand='{$brand}'";
if($special) $where .= " && INSTR(special,',{$special},')";
if($event) $where .= " && event='{$event}'";	
if($vendor) $where .= " && vendor='{$vendor}'";	

/**************************** GOODS LIST**************************/
if($sold_ck==1) {
	$sql = "SELECT if(s_qty=2 || (s_qty=4 && qty<1),1,0) as empt, uid,cate,number,name,price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && type='A' && cate!='999000000000' {$where}  ORDER BY empt , {$ORDER} LIMIT {$Pstart},{$limit}";
}
else {
	$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && type='A' && cate!='999000000000' {$where} ORDER BY {$ORDER} LIMIT {$Pstart},{$limit}";
}

$mysql->query($sql);

while($data = $mysql->fetch_array()){
	if($cate2) { $tmp_cate=$data['cate']; $data['cate'] = $cate2; }
	else $tmp_cate = '';
	$gData	= getDisplay($data,'image3');		// 디스플레이 정보 가공 후 가져오기
	$LINK	= $gData['link'];

	$page = ($Pstart/$limit)+1;
	
	$IMAGE	= "../".$gData['image'];
	$NAME	= $gData['name'];
	$COMP	= $gData['comp'];
	$PRICE	= $gData['price']; //판매가
	$CPRICE	= $gData['cprice'];  //소비자가
	$CP_PRICE= $gData['cp_price'];  //쿠폰가
	$ICON	= $gData['icon'];
	$RESE	= $gData['reserve'];
	$CCNT	= $gData['c_cnt'];
	if($tmp_cate) $data['cate'] = $tmp_cate;
	$UID	= $data['uid'];
	$CATE	= $data['cate'];
	$EVENT	= $data['event'];
	$OPRICE = $gData['oprice'];
		
	if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) {
		$SOUT = "1";
	} 
	else $SOUT = "";

	echo "
	  <item>
		<link><![CDATA[{$LINK}]]></link>
		<image><![CDATA[{$IMAGE}]]></image>
		<name><![CDATA[{$NAME}]]></name>
		<comp><![CDATA[{$COMP}]]></comp>
		<price><![CDATA[{$PRICE}]]></price>
		<cprice><![CDATA[{$CPRICE}]]></cprice>
		<icon><![CDATA[{$ICON}]]></icon>
		<rese><![CDATA[{$RESE}]]></rese>
		<ccnt><![CDATA[{$CCNT}]]></ccnt>
		<uid><![CDATA[{$UID}]]></uid>		
		<sout><![CDATA[{$SOUT}]]></sout>	
		<cp_price><![CDATA[{$CP_PRICE}]]></cp_price>	
		<cate><![CDATA[{$CATE}]]></cate>	
		<event><![CDATA[{$EVENT}]]></event>	
		<oprice><![CDATA[{$OPRICE}]]></oprice>	
	  </item>\n	";	
}

echo "</root>";
?>