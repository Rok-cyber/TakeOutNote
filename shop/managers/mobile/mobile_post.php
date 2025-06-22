<?
ob_start();
######################## lib include
include "../ad_init.php";

$code = "mall_mobile";
$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
$dir = "../../image/mobile";

switch($mode) {
     case "conf" : 			
	    $use = addslashes($_POST['use']);    
		$pc_use = addslashes($_POST['pc_use']);    

		$sql = "SELECT code FROM mall_mobile WHERE mode='C'";
		$tmps = $mysql->get_one($sql);
		$basic = explode("|*|",stripslashes($tmps));

		if(!eregi("none",$_FILES['img1']['tmp_name']) && $_FILES['img1']['tmp_name']) {									
			$img1 = upFile($_FILES['img1']['tmp_name'],$_FILES['img1']['name'],$dir,'','true','logo');
		}
		else $img1 = $basic[2];

		if(!eregi("none",$_FILES['img2']['tmp_name']) && $_FILES['img2']['tmp_name']) {									
			$img2 = upFile($_FILES['img2']['tmp_name'],$_FILES['img2']['name'],$dir,'','true','icon');
		}
		else $img2 = $basic[3];

		$data = "{$use}|*|{$pc_use}|*|{$img1}|*|{$img2}";

		$sql = "SELECT count(*) FROM {$code} WHERE mode='C'";	        
		if($mysql->get_one($sql) >0) $sql = "UPDATE {$code} SET code='{$data}' WHERE mode='C'";
		else $sql = "INSERT INTO {$code} VALUES('','','{$data}','C')";			
						
		$msg="모바일샵 기본 설정이 변경 되었습니다.";
		$mysql->query($sql);
		alert($msg,"conf.php");
     break;	

	 case "center" : 			
		$tel = addslashes($_POST['tel']);    
		$fax = addslashes($_POST['fax']);		
		$email = addslashes($_POST['email']);    
		$time1 = addslashes($_POST['time1']);    
		$time2 = addslashes($_POST['time2']);    
		$time3 = addslashes($_POST['time3']);    
		$time4 = addslashes($_POST['time4']);    
		
		$data = "{$tel}|*|{$fax}|*|{$email}|*|{$time1}|*|{$time2}|*|{$time3}|*|{$time4}";

		$sql = "SELECT count(*) FROM {$code} WHERE mode='T'";	        
		if($mysql->get_one($sql) >0) $sql = "UPDATE {$code} SET code='{$data}' WHERE mode='T'";
		else $sql = "INSERT INTO {$code} VALUES('','','{$data}','T')";			
						
		$msg="고객센터 정보설정이 변경 되었습니다.";
		$mysql->query($sql);
		alert($msg,"center.php");
     break;	

	 case "skin" :    		
		$cg_skin = $_POST['skin'];
		if(!$cg_skin) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하세요!','back');
        
		$sql = "UPDATE mall_mobile SET code='{$cg_skin}' WHERE mode='S'";		

        $msg = "스킨을 변경했습니다!";
		$mysql->query($sql);
		alert($msg,"skin.php");
		
    break;

	 default: alert("정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.","back");
	 break;
}
?>
