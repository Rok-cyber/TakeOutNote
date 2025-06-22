<?
ob_start();
include "../ad_init.php";

$mid	= $_GET['mid'];

function palert($msg) {
    echo "<script>
    alert('\\n $msg \\n');";
    echo "parent.pLightBox.hide();";
    echo "</script>";
	exit;
}

if(!$mid) palert('정보가 제대로 넘어오지 못했습니다. 다시 시도하세요!');

require "$lib_path/class.Template.php";

$skin = ".";

$sql = "SELECT name, code FROM mall_design WHERE mode='L' && name!='10' ORDER BY name ASC";
$mysql->query($sql);

for($i=2;$i<9;$i++) {
	$row = $mysql->fetch_array();
	while($row['name']!=$i) {
		$LV[$i] = "LV{$i}";
		if($i==8) break;
		$i++;
	}
	if($row['name']==$i) {
		$tmps = explode("|",$row['code']);
		$LV[$i] = stripslashes($tmps[0]);
	}
}

$LV[1] = "일시정지";
$LV[9] = "부관리자";
$LV[10] = "관리자";


$MTform  = array('26','name','id','jumin1','jumin2','tel','hphone','zipcode','address','email','homepage','msn','birth','sex','marr','edu','hobby','job','jobname','etc','mailling','icon','reserve','level','logtime','signdate','cnts');

$sql	= "SELECT address,info FROM pboard_member WHERE uid=1";
$data	= $mysql->one_row($sql);
$options	= explode("|",$data['address']);

$sql =  "SELECT * FROM pboard_member WHERE id = '{$mid}'";
$row = $mysql->one_row($sql);
if(!$row) alert("삭제된 회원이거나 없는 회원 입니다","close5");

for($i=1;$i<=$MTform[0];$i++){  
    $fd = $MTform[$i];
	${"FORM".$i} = stripslashes($row[$fd]);      
}

$FORM9 = "<a href='./mail_form.html?m_to=$FORM9' onfocus='this.blur();'>$FORM9</a>";

if($FORM10) {
	 if(!eregi("http://",$FORM10)) $FORM10="http://".$FORM10;
	$FORM10 = "<a href='$FORM10' onfocus='this.blur();' target='ablink'>$FORM10</a>";
}
if($FORM12) { 
	$bir = explode("|",$FORM12);
	$FORM12 = "$bir[0]년 $bir[1]월 $bir[2]일 ($bir[3])";
}
if($FORM13) { 
	if($FORM13 == 'M') $FORM13 = "남자";
	else if($FORM13 == 'F') $FORM13 = "여자";
	else $FORM13 = "";
}
if($FORM14) { 
	if($FORM14 == 'N') $FORM14 = "미혼";
	else $FORM14 = "기혼";
}
if($FORM20) { 
	if($FORM20 == 'N') $FORM20 = "메일수신 허용안함";
	else $FORM20 = "메일수신 허용함";
}
if($FORM21) $FORM21 = "<img src='../../icon/$FORM21' align=absmiddle>";
$FORM24 = date("Y-m-d h:i",$FORM24);
$FORM25 = date("Y-m-d h:i",$FORM25);
$FORM26 = number_format($FORM26);

$sql = "SELECT code FROM mall_design WHERE name='{$row['level']}' && mode='L'";
$tmps = $mysql->get_one($sql);
if($tmps) {
	$tmps = explode("|",$tmps);
	$FORM27 = $tmps[2];
	$FORM28 = $tmps[2];
} else $FORM27 = $FORM28 = 0;
	
$FORM30 = $LV[$row['level']];


$sql = "SELECT SUM(IF(status='A',reserve,0)) as sum1, SUM(IF(status='B',reserve,0)) as sum2, SUM(IF(status='C',reserve,0)) as sum3 FROM  mall_reserve WHERE id='{$mid}'";
$tmps = $mysql->one_row($sql);
$MONEY1 = $tmps['sum1'];
$MONEY2 = $tmps['sum2'];
$MONEY3 = $tmps['sum3'];

$TOTAL_MONEY = number_format($MONEY1 + $MONEY2 - $MONEY3,$ckFloatCnt);
$TOTAL_USE = number_format($MONEY2 - $MONEY3,$ckFloatCnt);
$MONEY1	= number_format($MONEY1,$ckFloatCnt);

$sql = "SELECT SUM(IF(status='A',1,0)) as cnt1, SUM(IF(status='B',1,0)) as cnt2 FROM mall_cupon WHERE id='{$mid}'";
$tmps = $mysql->one_row($sql);
$ABLE_CUPON = number_format($tmps['cnt1']);
$USE_CUPON = number_format($tmps['cnt2']);

$sql = "SELECT count(*) as cnt, SUM(pay_total) as total, SUM(use_reserve) as reserve FROM mall_order_info WHERE id='{$mid}' && order_status!='Z'";
$tmps = $mysql->one_row($sql);
$ORDER1 = number_format($tmps['cnt']);
$ORDER3 = number_format($tmps['total'],$ckFloatCnt);
if($tmps['cnt']>0) $ORDER4 = number_format(round($tmps['total']/$tmps['cnt']),$ckFloatCnt);
else $ORDER4 = 0;
$ORDER5 = number_format($tmps['reserve'],$ckFloatCnt);

$sql = "SELECT count(*) as cnt, SUM(IF(status='A',1,0)) as acnt, SUM(IF(status='B',1,0)) as bcnt, SUM(IF(status='D',1,0)) as ccnt FROM mall_cooperate WHERE id='{$mid}'";
$tmps = $mysql->one_row($sql);
$COOPER1 = number_format($tmps['cnt']);
$COOPER2 = number_format($tmps['acnt']);
$COOPER3 = number_format($tmps['bcnt']);
$COOPER4 = number_format($tmps['ccnt']);

$sql = "SELECT pay_type, count(*) as cnt FROM mall_order_info GROUP BY pay_type ORDER BY cnt desc LIMIT 1";
$tmps = $mysql->one_row($sql);
if($tmps) {
	$type_arr = Array("C"=>"카드","B"=>"무통장","E"=>"에스크로","H"=>"핸드폰");
	$ORDER6 = $type_arr[$tmps['pay_type']];
} else $ORDER6 = '';


$sql = "SELECT MAX(signdate) FROM mall_order_info WHERE id='{$mid}'";
$tmps = $mysql->get_one($sql);
if($tmps) $ORDER2 = substr($tmps,0,16);

$sql = "SELECT count(*) FROM mall_goods_point WHERE id='{$mid}'";
$BOARD1 = number_format($mysql->get_one($sql));

$sql = "SELECT count(*) FROM mall_goods_qna WHERE id='{$mid}'";
$BOARD2 = number_format($mysql->get_one($sql));

$sql = "SELECT count(*) FROM pboard_counsel_body WHERE id='{$mid}' && memo!='1'";
$BOARD3 = number_format($mysql->get_one($sql));

$sql = "SELECT count(*) FROM pboard_customer_body WHERE id='{$mid}' && memo!='1'";
$BOARD4 = number_format($mysql->get_one($sql));

$tpl = new classTemplate;
$tpl->define("main","member_crm.html");
$tpl->scan_area("main");

$tpl->parse("main");
$tpl->tprint("main");
?>