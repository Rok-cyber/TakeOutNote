<?
set_time_limit(0);
$file_name = "itsMallMemberList_".date("Ymd",time());
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
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$order		= isset($_GET['order']) ? $_GET['order'] : $_POST['order'];
$dates		= isset($_GET['dates']) ? $_GET['dates'] : $_POST['dates'];
$auth		= isset($_GET['auth']) ? $_GET['auth'] : $_POST['auth'];
$sex		= isset($_GET['sex']) ? $_GET['sex'] : $_POST['sex'];
$level		= isset($_GET['level']) ? $_GET['level'] : $_POST['level'];
$mailling	= isset($_GET['mailling']) ? $_GET['mailling'] : $_POST['mailling'];
$item		= $_POST['item'];

if(!$field) $field = "name";
if(!$dates) $dates = "signdate";

if($word) {
	$addstring .= "&field={$field}&word={$word}";
	$where .= "&& INSTR({$field},'{$word}')";
}

if($sdate1 && $sdate2) {		
    if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
	$addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}&dates={$dates}";	
	if($sdate1==$sdate2) $where .= "&& INSTR(from_unixtime({$dates}),'{$sdate1}') ";
	else $where .= "&& (from_unixtime({$dates}) BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(from_unixtime({$dates}),'{$sdate2}'))";	
}  

if($auth) {
	$addstring .= "&auth={$auth}";
	$where .= " && auth='{$auth}'";
}

if($level) {
	$addstring .= "&level={$level}";
	$where .= " && level='{$level}'";
}


if($sex) {
	$addstring .= "&sex={$sex}";
	$where .= " && sex='{$sex}'";
}

if($mailling) {
	$addstring .= "&mailling={$mailling}";
	$where .= " && mailling='{$mailling}'";
}

$mailstring	= $addstring;

if($order) {
	$addstring .= "&order={$order}";
}
else $order = "signdate DESC";

$sql = "SELECT name, code FROM mall_design WHERE mode='L' && name!='10' ORDER BY name ASC";
$mysql->query($sql);

for($i=2;$i<9;$i++) {
	$row = $mysql->fetch_array();
	while($row['name']!=$i) {
		$LEVEL .= "<option value='{$i}'>LV{$i}</option>";
		$LV[$i] = "LV{$i}";
		if($i==8) break;
		$i++;
	}
	if($row['name']==$i) {
		$tmps = explode("|",$row['code']);
		$LEVEL .= "<option value='{$i}'>".stripslashes($tmps[0])."</option>";
		$LV[$i] = stripslashes($tmps[0]);
	}
}

$LV[1] = "일시정지";
$LV[9] = "부관리자";
$LV[10] = "관리자";

// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./member_excel.html");
$tpl->scan_area("main");


$list_arr = Array();
for($i=0,$cnt=count($item);$i<$cnt;$i++) {
	$list_arr = $item[$i];
	$tpl->parse("is_t{$item[$i]}");
}

$sql = "SELECT * FROM pboard_member WHERE uid >1 {$where} ORDER BY {$order}";
$mysql->query($sql);

while($row = $mysql->fetch_array()){	
	for($i=0,$cnt=count($item);$i<$cnt;$i++) {
		switch($item[$i]) {
			case "jumin1" : 				
				$row[$item[$i]] = $row[$item[$i]]."- *******";
			break;
			case "sex" :
				if($row['sex']=='F') $row[$item[$i]] = '남성';
				else $row[$item[$i]] = '여성';
			break;
	
			case "marr" :
				if($row['marr']=='N') $row[$item[$i]] = '미혼';
				else $row[$item[$i]] = '기혼';
			break;

			case "level" :
				$row[$item[$i]] = $LV[$row['level']];
			break;

			case "reserve" :
				$row[$item[$i]] = number_format($row[$item[$i]],$ckFloatCnt);				
			break;	

			case "signdate" :
				$row[$item[$i]] = date("Y-m-d H:i",$row[$item[$i]]);
			break;			
		}
		${"LIST".$item[$i]} = stripslashes($row[$item[$i]]);
		$tpl->parse("is_{$item[$i]}","1");
	}
	$tpl->parse("loop");
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>