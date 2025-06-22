<?
$acc_ip = $_SERVER['REMOTE_ADDR'];  //접속 ip 

$YEAR	= date('Y');     
$MONTH	= date('m');
$DAY	= date('d');
$TIME	= date('H');
$ck_date= date('Ymd');

$sql = "SELECT ck_ip,ck_date FROM pcount_check_affiliate WHERE ck_ip = '{$acc_ip}'";
$row_ct = $mysql->one_row($sql);

if((!$row_ct['ck_date']) || $row_ct['ck_date'] < $ck_date) { //날이 바뀌면
    $sql = "DELETE FROM pcount_check_affiliate";
	$mysql->query($sql);	
	$row_ct['ck_ip']='';	
}	

if(!$row_ct['ck_ip']) { //아이피 체크 후 처음 접속이면 ... 카운팅
   
    $sql = "INSERT INTO pcount_check_affiliate VALUES('','{$acc_ip}','{$ck_date}','{$affiliate}')";  //아이피 저장
    $mysql->query($sql);

    /*********** 카운팅 ************/
    $sql = "SELECT count(*) FROM pcount_list_affiliate WHERE year = '{$YEAR}' && month = '{$MONTH}' && day = '{$DAY}' && affiliate ='{$affiliate}'";  // 카운팅...
   
    if($mysql->get_one($sql) == '1') {	   
	    $sql = "UPDATE pcount_list_affiliate SET h_{$TIME} = h_{$TIME} + 1, total = total + 1 WHERE year = '{$YEAR}' && month = '{$MONTH}' && day = '{$DAY}' && affiliate ='{$affiliate}'";	   
    } 
	else {
	    $sql = "INSERT INTO pcount_list_affiliate (uid,year,month,day,h_{$TIME},total,affiliate) VALUES('','{$YEAR}','{$MONTH}','{$DAY}','1','1','{$affiliate}')";
    }
    $mysql->query($sql);

	/*********** 사이트 카운팅 ************/
    $sql = "SELECT count(*) FROM pcount_agent_affiliate WHERE type = 'S' && content = '{$sites}' && affiliate ='{$affiliate}'";  

    if($mysql->get_one($sql) == '0') {	   
	    $sql = "INSERT INTO pcount_agent_affiliate (type,content,cnts,affiliate) VALUES('S','{$sites}','1','{$affiliate}')";
    } 
	else {
	    $sql = "UPDATE pcount_agent_affiliate SET cnts = cnts + 1 WHERE type = 'S' && content = '{$sites}' && affiliate ='{$affiliate}'";	   	   
    }
    $mysql->query($sql);  
}

//페이지뷰

/*********** 카운팅 ************/
$sql = "UPDATE pcount_list_affiliate SET h2_{$TIME} = h2_{$TIME} + 1, total2 = total2 + 1 WHERE year = '{$YEAR}' && month = '{$MONTH}' && day = '{$DAY}' && affiliate ='{$affiliate}'";	   
$mysql->query($sql);

unset($acc_ip,$YEAR,$MONTH,$DAY,$TIME,$ck_date,$sql,$row_ct,$data);
?>