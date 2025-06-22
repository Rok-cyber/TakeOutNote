<?
$file_name = "itsMallGoodsList_".date("Ymd",time());
header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-Disposition: attachment; filename={$file_name}.xls" ); 
header( "Content-Description: Gamza Excel Data" ); 
header( "Content-type: application/vnd.ms-excel;charset=utf-8" ); 

######################## lib include
include "../ad_init.php";

require "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
$smoney1	= isset($_GET['smoney1']) ? $_GET['smoney1'] : $_POST['smoney1'];
$smoney2	= isset($_GET['smoney2']) ? $_GET['smoney2'] : $_POST['smoney2'];
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : $_POST['seccate'];
$s_qty		= isset($_GET['s_qty']) ? $_GET['s_qty'] : $_POST['s_qty'];
$item		= $_POST['item'];

$skin = ".";

##################### addstring ############################
if($field && $word) {
	$addstring .= "&field={$field}&word=".urlencode($word);
	$where .= "&& INSTR({$field},'{$word}') ";
} else $field = "name";

if($seccate) {
	if(substr($seccate,3,9)=='000000000') {
		$where .=  " && SUBSTRING(cate,1,3) = '".substr($seccate,0,3)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = " ==== 2차분류 ==== ";
		$cate3 = " ==== 3차분류 ==== ";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else if(substr($seccate,6,6)=='000000') {
		$where .=  " && SUBSTRING(cate,1,6) = '".substr($seccate,0,6)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = " ==== 3차분류 ==== ";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else if(substr($seccate,9,3)=='000') {
		$where .=  " && SUBSTRING(cate,1,9) = '".substr($seccate,0,9)."'";	
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = substr($seccate,0,9)."000";
		$cate4 = " ==== 4차분류 ==== ";
	} 
	else {
		$where .= " && cate = '{$seccate}'";
		$cate1 = substr($seccate,0,3)."000000000";
		$cate2 = substr($seccate,0,6)."000000";
		$cate3 = substr($seccate,0,9)."000";
		$cate4 = $seccate;
	}
	$addstring .="&seccate={$seccate}";
}

if($smoney1 && $smoney2) {
    if($smoney1 > $smoney2) {$smoney1 = $tmp; $smoney1 = $smoney2; $smoney2 = $tmp;}
	$addstring .= "&smoney1=$smoney1&smoney2=$smoney2";
	if($smoney1==$smoney2) $where .= "&& INSTR(price,'{$smoney1}') ";
	else $where .= "&& price BETWEEN '{$smoney1}' AND '{$smoney2}' ";
}

if($sdate1 && $sdate2) {	
    if($sdate1 > $sdate2) { $tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&sdate1=$sdate1&sdate2=$sdate2";	
	if($sdate1==$sdate2) $where .= "&& INSTR(from_unixtime(signdate),'{$sdate1}') ";
	else $where .= "&& ( from_unixtime(signdate) BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(from_unixtime(signdate),'{$sdate2}'))";		
}  

if($s_qty) {
	$addstring .="&s_qty={$s_qty}";
	if($s_qty==2) $where .= " && (s_qty = '2' || (s_qty = '4' && qty=0)) ";
	else if($s_qty==3) $where .= " && (s_qty = '3' || type='B') ";
	else $where .= " && s_qty = '{$s_qty}' ";
}


if($order) $addstring .="&order={$order}";	
else $order = "uid DESC";

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비


// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./goods_excel.html");
$tpl->scan_area("main");


######################## 브랜드 설정 ############################
$sql = "SELECT * FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);

while($row=$mysql->fetch_array()){
	$row['name'] = stripslashes($row['name']);
	$brand_arr[$row['uid']] = $row['name'];
}

$list_arr = Array();
for($i=0,$cnt=count($item);$i<$cnt;$i++) {
	$list_arr = $item[$i];
	$tpl->parse("is_t{$item[$i]}");
}

$sql = "SELECT * FROM mall_goods WHERE uid>0  && cate!='999000000000' {$where} ORDER BY {$order}";
$mysql->query($sql);

while($row = $mysql->fetch_array()){
	for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			
		/*if(eregi('image',$item[$i])) {
			$row[$item[$i]] = explode("/",$row[$item[$i]]);
			$row[$item[$i]] = $row[$item[$i]][1];
		}*/
		
		${"LIST".$item[$i]} = stripslashes($row[$item[$i]]);
		
		if($item[$i]=='brand') ${"LIST".$item[$i]} = stripslashes($brand_arr[$row['brand']]);
		else if($item[$i]=='qty') {
			switch($row['s_qty']){
				case "1" : $LISTqty = "무제한";
				break;
				case "2" : $LISTqty = "품절";
				break;
				case "3" : $LISTqty = "상품숨김";
				break;
				case "4" : $LISTqty = $row['qty'];
				break;
			}
		}
		else if($item[$i]=='option') {
			$sql = "SELECT * FROM mall_goods_option WHERE guid='{$row['uid']}'";
			$mysql->query2($sql);
			$tmps = array();
			while($row2 = $mysql->fetch_array('2')){
				$tmps[] = "{$row2['option1']},{$row2['option2']},{$row2['price']},{$row2['qty']}";
			}
			if($tmps[0]) $LISToption = join("|",$tmps);
			else $LISToption = "";
		}
		else if($item[$i]=='explan') {
			$LISTexplan	= str_replace("<","&lt;",$LISTexplan);
			$LISTexplan	= str_replace(">","&gt;",$LISTexplan);
			$LISTexplan	= str_replace("\"","&quot;",$LISTexplan);	
			$LISTexplan	= str_replace("&nbsp;","",$LISTexplan);	
		}
		else if($item[$i]=='tag') {
			if($row['tag']==',,') $LISTtag = "";
			else $LISTtag = substr($row['tag'],1,-1);
		}

		$tpl->parse("is_{$item[$i]}","1");
	}
	$tpl->parse("loop");
}


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>