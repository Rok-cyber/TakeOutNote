<?
$tpl->define("main","{$skin}/cate.html");
$tpl->scan_area("main");

$cate = isset($_GET['cate']) ? $_GET['cate'] : $_POST['cate'];

if($cate) {
	$row = rtnCate($cate);
	$SCATE_NAME = $row['cate_name'];

	$CATE = $row['cate'];
	$CATE_NAME	 = stripslashes($row['cate_name'])." 전체보기"; 				
	$CHANNEL = "list";	

	if(substr($row['cate'],3,3)=='000') $ck_len = 3;
	else if(substr($row['cate'],6,3)=='000') $ck_len = 6;
	else $ck_len = 9;
	
	$sql = "SELECT count(*) FROM mall_goods WHERE (SUBSTRING(cate,1,{$ck_len}) = '".substr($row['cate'],0,$ck_len)."' || INSTR(mcate,',".substr($row['cate'],0,$ck_len)."')) && s_qty !='3' && type='A'";			    
	$CCNT = $mysql->get_one($sql);	

	$tpl->parse("loop_cate");	

	for($i=1;$i<=$row['cate_dep'];$i++) {
		$cate2 = substr($cate,0,$i*3);
		$row2 = rtnCate($cate2,$i*3);
		
		if($row['cate_dep']==$i) $CATE_LOCA = $row2['cate_name'];
		else $CATE_LOCA = $row2['location'];
		$tpl->parse("loop_scate");		
	}

	$where = " && cate_dep='".($row['cate_dep']+1)."' && cate_parent='{$row['cate']}'";	
	$tpl->parse("is_cate");
}
else {
	$SCATE_NAME = "카테고리";
	$where = "&& cate_dep ='1'";	
}
$sql = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate != '999000000000' && valid ='1' {$where} ORDER BY number ASC";
$mysql->query($sql);

while($row = $mysql->fetch_array()){	
	
	$CATE = $row['cate'];
	$CATE_NAME	 = stripslashes($row['cate_name']); 

	if(substr($row['cate'],3,3)=='000') $ck_len = 3;
	else if(substr($row['cate'],6,3)=='000') $ck_len = 6;
	else $ck_len = 9;
	
	$sql = "SELECT count(*) FROM mall_goods WHERE (SUBSTRING(cate,1,{$ck_len}) = '".substr($row['cate'],0,$ck_len)."' || INSTR(mcate,',".substr($row['cate'],0,$ck_len)."')) && s_qty !='3' && type='A'";		    
	$CCNT = $mysql->get_one($sql);	

	if($row['cate_sub']==1) $CHANNEL = "cate";
	else $CHANNEL = "list";
	
	$tpl->parse("loop_cate");	
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>