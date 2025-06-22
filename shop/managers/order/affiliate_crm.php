<?
ob_start();
include "../ad_init.php";

$affiliate	= $_GET['affiliate'];

function palert($msg) {
    echo "<script>
    alert('\\n $msg \\n');";
    echo "parent.pLightBox.hide();";
    echo "</script>";
	exit;
}

if(!$affiliate) palert('정보가 제대로 넘어오지 못했습니다. 다시 시도하세요!');

require "$lib_path/class.Template.php";

$skin = ".";

$MTform  = array('11','name','id','cell','email','commission','bank_day','bank_name','bank_num','bank_owner','memo','signdate');

$sql =  "SELECT * FROM mall_affiliate WHERE id = '{$affiliate}'";
$row = $mysql->one_row($sql);
if(!$row) alert("삭제된 업체거나 없는 업체 입니다","close5");

for($i=1;$i<=$MTform[0];$i++){  
    $fd = $MTform[$i];
	${"FORM".$i} = stripslashes($row[$fd]);      
}

$FORM4 = "<a href='../member/mail_form.html?m_to={$FORM4}' onfocus='this.blur();'>{$FORM4}</a>";
$FORM11 = date("Y-m-d h:i",$FORM11);

$sql = "SELECT count(*) as cnt, SUM(pay_total) as total FROM mall_order_info WHERE affiliate='{$affiliate}' && order_status!='Z'";
$tmps = $mysql->one_row($sql);

$ORDER1 = number_format($tmps['cnt']);
$ORDER3 = number_format($tmps['total'],$ckFloatCnt);

$sql = "SELECT MAX(signdate) FROM mall_order_info WHERE affiliate='{$affiliate}'";
$tmps = $mysql->get_one($sql);
if($tmps) $ORDER2 = substr($tmps,0,16);


$sql = "SELECT use_cupon, pay_total, use_reserve, carriage, a_commi FROM mall_order_info WHERE order_status='E' && affiliate='{$affiliate}'";	
$mysql->query($sql);

$ORDER4 = 0;
while($row=$mysql->fetch_array()){
	$tmps = $row['pay_total'] + $row['use_reserve'] - $row['carriage'];
	if($row['a_commi']>0) $ORDER4 += $tmps/$row['a_commi'];
}		
$ORDER4 = number_format($ORDER4);

$sql = "SELECT SUM(total) FROM pcount_list_affiliate WHERE uid>0 && affiliate='{$affiliate}'";
$ORDER5 = number_format($mysql->get_one($sql));

$today = date("Y-m-d");
$sql = "SELECT SUM(total) FROM pcount_list_affiliate WHERE uid>0 && affiliate='{$affiliate}' && year = '".substr($today,0,4)."' && month = '".substr($today,5,2)."' && day = '".substr($today,8,2)."'";
$ORDER6 = number_format($mysql->get_one($sql));

$tpl = new classTemplate;
$tpl->define("main","affiliate_crm.html");
$tpl->scan_area("main");

$tpl->parse("main");
$tpl->tprint("main");
?>