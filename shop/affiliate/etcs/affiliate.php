<?
$skin_inc = "Y";
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

require "{$lib_path}/class.Template.php";

$path = ".";
$skin = ".";
$MTform = Array(11,'auth','id','name','cell','email','commission','bank_name','bank_num','bank_owner','bank_day','memo');


$tpl = new classTemplate;
$tpl->define("main","{$skin}/affiliate.html");
$tpl->scan_area("main");

$mysql = new  mysqlClass(); //디비 클래스
$sql =  "SELECT * FROM mall_affiliate WHERE id = '{$a_my_id}'";
$row = $mysql->one_row($sql);

for($i=1;$i<=$MTform[0];$i++){  
    $fd = $MTform[$i];
	${"FORM".$i} = stripslashes($row[$fd]);    
	${"FORM".$i} = str_replace("\"","&#034;",${"FORM".$i});
	${"FORM".$i} = str_replace("'","&#039;",${"FORM".$i});
}
   
${"CKD1".$FORM1} = "checked";
${"CKD2".$FORM18} = "checked";
${"CKD3".$FORM19} = "checked";
${"CKD4".$FORM20} = "checked";
${"CKD5".$FORM21} = "checked";

$tmps = explode("-",$row['comp_zipcode']);
$ZIP1 = $tmps[0];
$ZIP2 = $tmps[1];

if(!$FORM26) $FORM26 = "미설정";
else $FORM26 = "매월 {$FORM26}일";

$ACTION = "./affiliate_ok.php";
if($row['file']){
	$IMG = $row['file'];
	$IMG2 = imgSizeCh("{$dir}/",$row['file'],'600');	 

	$tpl->parse("is_img");
}


$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/ 
?>