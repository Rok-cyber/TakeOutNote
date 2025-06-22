<?
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'A';
if(!$mode) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');

$sql = "SELECT * FROM mall_document WHERE mode='{$mode}'";

if(!$row = $mysql->one_row($sql)) alert('페이지가 존재하지 않습니다.\\n\\n다시 확인해 주시기 바랍니다.','back');

$DOCU_CODE = stripslashes($row['code']);
$DOCU_CODE = str_replace("{shopName}",$basic[1],$DOCU_CODE);
$DOCU_CODE = str_replace("{name}",$basic[9],$DOCU_CODE);
$DOCU_CODE = str_replace("{email}",$basic[10],$DOCU_CODE);
$DOCU_CODE = str_replace("{tel}",$basic[7],$DOCU_CODE);
$DOCU_CODE = str_replace("{carrLimit}",number_format($cash[11]),$DOCU_CODE);
$DOCU_CODE = str_replace("{carrPrice}",number_format($cash[12]),$DOCU_CODE);

if(eregi("{bankInfo}",$DOCU_CODE) && $cash[5]){
	$bank_info = explode("|",$cash[5]);
	for($i=0,$cnt=(count($bank_info)-1);$i<$cnt;$i++) {
		$bank_info2 = explode(",",$bank_info[$i]);
		if($bank_info3) $bank_info3 .= "<br/>예금주 : {$bank_info2[2]}, {$bank_info2[0]} : {$bank_info2[1]}";
		else $bank_info3 .= "예금주 : {$bank_info2[2]}, {$bank_info2[0]} : {$bank_info2[1]}";
	}
	$DOCU_CODE = str_replace("{bankInfo}",$bank_info3,$DOCU_CODE);
}

$tpl->define("main","{$skin}/customer_docu.html");
$tpl->scan_area("main");

$tpl->parse("is_title_mode{$mode}");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>