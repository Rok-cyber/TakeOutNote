<?
$tpl->define("main","{$skin}/document.html");
$tpl->scan_area("main");

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'B';
$title_arr = array('A'=>'회사소개','B'=>'이용약관','C'=>'개인정보취급방침','D'=>'이용방법');

$sql = "SELECT * FROM mall_document WHERE mode='{$mode}'";
$row = $mysql->one_row($sql);

$TITLE = $title_arr[$mode];

$DOCU_CODE = stripslashes($row['code']);
$DOCU_CODE = str_replace("{shopName}",$basic[1],$DOCU_CODE);
$DOCU_CODE = str_replace("{name}",$basic[9],$DOCU_CODE);
$DOCU_CODE = str_replace("{email}",$basic[10],$DOCU_CODE);
$DOCU_CODE = str_replace("{tel}",$basic[7],$DOCU_CODE);
$DOCU_CODE = str_replace("{carrLimit}",number_format($cash[11]),$DOCU_CODE);
$DOCU_CODE = str_replace("{carrPrice}",number_format($cash[12]),$DOCU_CODE);
$DOCU_CODE = str_replace("700px","96%",$DOCU_CODE);
$DOCU_CODE = str_replace("698px","95%",$DOCU_CODE);

$CONTENT = $DOCU_CODE;

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>