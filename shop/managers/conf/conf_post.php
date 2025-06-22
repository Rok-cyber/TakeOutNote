<?
ob_start();
######################## lib include
include "../ad_init.php";

$code = "mall_design";
$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];

switch($mode) {
    case "basic" :    
			$basic = "{$_POST[s_url]}|*|{$_POST[s_title]}|*|{$_POST[s_name]}|*|{$_POST[s_boss]}|*|{$_POST[s_num1]}|*|{$_POST[s_num2]}|*|{$_POST[s_addr]}|*|{$_POST[s_tel]}|*|{$_POST[s_fax]}|*|{$_POST[s_admin]}|*|{$_POST[s_email]}|*|{$_POST[title]}|*|{$_POST[keyword]}|*|{$_POST[r_word]}|*|{$_POST[c_info]}";
			$basic = addslashes($basic);
			$sql = "SELECT count(*) FROM {$code} WHERE mode='A'";
			
			if($mysql->get_one($sql) >0) 	$sql = "UPDATE {$code} SET code='{$basic}' WHERE mode='A'";
			else $sql = "INSERT INTO $code VALUES('','','{$basic}','A')";
			
			$msg="쇼핑몰 기본정보및 사업자 정보를 수정했습니다.";
     break;	

	 case "cash" :    
			$_POST['c_money3'] = $_POST['c_money31']."|".$_POST['c_money32']."|".$_POST['c_money33'];
			$_POST['addCarr'] = $_POST['addCarr1']."|".$_POST['addCarr2']."|".$_POST['addCarr3'];
			
			$cash = "{$_POST['c_cash1']}|*|{$_POST['c_cash2']}|*|{$_POST['c_card']}|*|{$_POST['c_id']}|*|{$_POST['c_money']}|*|{$_POST['b_info']}|*|{$_POST['r_use']}|*|{$_POST['r_join']}|*|{$_POST['r_order']}|*|{$_POST['r_money']}|*|{$_POST['c_use']}|*|{$_POST['c_money1']}|*|{$_POST['c_money2']}|*|{$_POST['c_money3']}|*|{$_POST['addCarr']}|*|{$_POST['c_key']}|*|{$_POST['c_test']}|*|{$_POST['c_cash3']}|*|{$_POST['c_cash4']}|*|{$_POST['c_cash5']}|*|{$_POST['c_halbu']}|*|{$_POST['c_interest']}|*|{$_POST['c_interest2']}|*|{$_POST['e_use']}";
	        $cash = addslashes($cash);
	        $sql = "SELECT count(*) FROM {$code} WHERE mode='B'";
			
			if($mysql->get_one($sql) >0) 	$sql = "UPDATE {$code} SET code='{$cash}' WHERE mode='B'";
			else $sql = "INSERT INTO $code VALUES('','','{$cash}','B')";			
						
			$msg="결제/적립금/배송정책을 수정 했습니다.";
     break;	

	 case "info" : 			
	    $info = isset($_POST['info']) ? addslashes($_POST['info']):'';    
		$sql = "SELECT count(*) FROM {$code} WHERE mode='D'";
	        
		if($mysql->get_one($sql) >0) $sql = "UPDATE {$code} SET code='{$info}' WHERE mode='D'";
		else $sql = "INSERT INTO {$code} VALUES('','','{$info}','D')";			
						
		$msg="배송/반품/환불 정보를 수정 했습니다.";
     break;	

	 case "ssl" : 			
	    $apply = join("|",$_POST['s_apply']);
		$ssl = "{$_POST['s_use']}|*|{$_POST['s_port']}|*|{$apply}";
		$sql = "SELECT count(*) FROM {$code} WHERE mode='W'";
	        
		if($mysql->get_one($sql) >0) $sql = "UPDATE {$code} SET code='{$ssl}' WHERE mode='W'";
		else $sql = "INSERT INTO {$code} VALUES('','','{$ssl}','W')";			
						
		$msg="SSL 정보를 수정 했습니다.";
     break;	

	 case "confirm" : 			
		if(!$_POST['c_use2']) $_POST['c_use2'] = 0;
	    $confirm = "{$_POST['c_use']}|*|{$_POST['c_id']}|*|{$_POST['c_use2']}|*|{$_POST['c_pw']}";
		$sql = "SELECT count(*) FROM {$code} WHERE mode='Y'";
	        
		if($mysql->get_one($sql) >0) $sql = "UPDATE {$code} SET code='{$confirm}' WHERE mode='Y'";
		else $sql = "INSERT INTO {$code} VALUES('','','{$confirm}','Y')";			
						
		$msg="실명확인 정보를 수정 했습니다.";
     break;	

	 case "etc" : 			
	    $tag = isset($_POST['tag']) ? addslashes($_POST['tag']):'1';    
		$auth = isset($_POST['auth']) ? addslashes($_POST['auth']):'1';    
		$agree = isset($_POST['agree']) ? addslashes($_POST['agree']):'2';    
		$e_date = addslashes($_POST['e_date']);    
		$cash_dc = addslashes($_POST['cash_dc']);    

		$sql = "SELECT count(*) FROM {$code} WHERE mode='T'";

		$info = $tag."|".$auth."|".$agree."|".$e_date."|".$cash_dc;
	        
		if($mysql->get_one($sql) >0) $sql = "UPDATE {$code} SET code='{$info}' WHERE mode='T'";
		else $sql = "INSERT INTO {$code} VALUES('','','{$info}','T')";			
						
		$msg="기타정책을 수정 했습니다.";		
     break;	

	 case "deli" : 			
	    $b_mode = $_POST['b_mode'];
        
		if($b_mode=='ins') {
			$name = addslashes($_POST['name']);
			$codes = addslashes($_POST['code']);    
			$sql = "INSERT INTO {$code} VALUES('','{$name}','{$codes}','Z')";			
			$msg="택배회사를 등록 했습니다.";

			$go_url = 'conf.html';
			$mysql->query($sql);
			alert($msg,$go_url);
		}
		else {
			$b_num = $_POST['b_num'];
			if(!$b_num) alert('정보가 제대로 넘어오지 못했습니다.','back');
			$name = addslashes($_POST['re_name']);
			$codes = addslashes($_POST['re_code']);   
			if($b_mode=='del') {
				$sql = "DELETE FROM {$code} WHERE mode='Z' && uid='{$b_num}'";
				$msg="택배회사를 삭제 했습니다.";
			}
			else {
				$sql = "UPDATE {$code} SET name='{$name}', code='{$codes}' WHERE mode='Z' && uid='{$b_num}'";
				$msg="택배회사 정보를 수정 했습니다.";
			}

			$go_url = 'conf.html';
			$mysql->query($sql);
			alert($msg,$go_url);

		}
     break;	

	 default: alert("정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.","back");
	 break;
}

$mysql->query($sql);
socketPost($itsMall);
alert($msg,"close");
?>
