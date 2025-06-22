<?
set_time_limit(0); 
######################## lib include
include "../ad_init.php";
$MTmod = Array(7,'name','passwd','cell','email','bank_name','bank_num','bank_owner');

// DATE
$signdate = time();

$sql = "UPDATE mall_affiliate SET ";
for($i=1;$i<=($MTmod[0]);$i++){  
    $fd = $MTmod[$i];
	$_POST[$fd] = addslashes($_POST[$fd]);
	
	if($fd=='passwd') {
		if($_POST[$fd]) $sql .= ", {$fd} =  '".md5($_POST[$fd])."'";		
	}
	else {
		if($i=='1') $sql .= $MTmod[$i]." =  '{$_POST[$fd]}'";		
	    else $sql .= ", {$MTmod[$i]} =  '{$_POST[$fd]}'";		
	}					
}
					
$sql .= " WHERE id='{$a_my_id}'";			 				 						
$msg = "수정했습니다";
$mysql->query($sql);

alert($msg,"affiliate.php");
?>