<?
$tpl = new classTemplate;
$tpl->define("main","{$skin}/write.html");
$tpl->scan_area("main");

//@mkdir("../../image/magazine/magazineMain",0707);
//delTree("../../image/magazine/magazineMain/");

if($mode=='modify'){
    $mysql = new  mysqlClass(); //디비 클래스
    $sql =  "SELECT * FROM mall_{$code} WHERE uid = '{$uid}'";
    $row = $mysql->one_row($sql);
    for($i=1;$i<=$MTform[0];$i++){  
        $fd = $MTform[$i];
		${"FORM".$i} = stripslashes($row[$fd]);    
		${"FORM".$i} = str_replace("\"","&#034;",${"FORM".$i});
		${"FORM".$i} = str_replace("'","&#039;",${"FORM".$i});
    }
   
    if($row['file']){
		$IMG = $row['file'];
		$IMG2 = imgSizeCh("{$dir}/",$row['file'],'600');	 
	}

	$TMODE = "수정";
	$READONLY = "readonly";

} 
else {	
	$TMODE = "등록";
}

if($MTcate){
	$OPTION = "<option>선택</option>\n";
	for($i=1;$i<=$MTcate[0];$i++) {
	    if($row['cate'] ==$i) $OPTION .="<option value='{$i}' selected>{$MTcate[$i]}</option>";
	    else $OPTION .="<option value='{$i}'>{$MTcate[$i]}</option>";
	}
}

$ACTION = "./insert.php?code={$code}&mode={$mode}&uid={$uid}{$addstring}";
$LIST	= "board.php?code={$code}{$addstring}";

if($row['file']) $tpl->parse("is_img");


$tpl->parse("main");
$tpl->tprint("main");

?>