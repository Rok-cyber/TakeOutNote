<?php

function rtnCate($cate, $len=0) {
	global $mysql, $Main;
	if($len) {
		for($i=$len;$i<12;$i++) {
			$cate .= "0";
		}			
	}
	$sql = "SELECT cate_name, cate_dep, cate, list_mode FROM mall_cate WHERE cate='{$cate}'";
	$row = $mysql->one_row($sql);
	$row['cate_name'] = stripslashes($row['cate_name']);
	$row['location'] = "<a href='{$Main}?channel=cate&amp;cate={$row['cate']}'>{$row['cate_name']}</a>";
	return $row;
}

function getCartId($id) {
	if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
		if($id) $tempid = $my_id;
		else $tempid = md5(uniqid(rand()));
		SetCookie("tempid",$tempid,0,"/");		
	} 
	else $tempid = $_COOKIE['tempid'];

	return $tempid;
}

?>