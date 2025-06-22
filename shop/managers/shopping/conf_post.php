<?
######################## lib include
include "../ad_init.php";
$img_path = "../../image/icon";

$mode = isset($_GET['mode'])? $_GET['mode']:'';
$b_mode = isset($_GET['b_mode'])? $_GET['b_mode']:$_POST['b_mode'];

if($mode=='icon_i') {
    
	if(!eregi("none",$_FILES['icon']['tmp_name']) && $_FILES['icon']['tmp_name']) {	     
		$file = upFile($_FILES['icon']['tmp_name'],$_FILES['icon']['name'],$img_path,'','true');			
	}  
	    
    $sql = "INSERT INTO mall_goods_conf VALUES('','{$file}','','I','')";
	$msg = "아이콘을 등록했습니다!";

}  
else if($mode=='icon_d') {
	
	$icon_arr = isset($_POST['icon_arr'])? $_POST['icon_arr']:'';	
	$ct_num = sizeof($icon_arr);         
    if($ct_num<1)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

    for($i = 0; $i < $ct_num; $i++) {
	     $sql = "SELECT name FROM mall_goods_conf WHERE mode='I' && uid = '{$icon_arr[$i]}'";
		 $img = $mysql->get_one($sql);
		 delFile($img_path.$img);
		 $sql = "DELETE FROM mall_goods_conf WHERE mode='I' && uid='{$icon_arr[$i]}'";
		 $mysql->query($sql);
    } 
	
	alert('아이콘을 삭제 했습니다!','goods_conf.html');
     
}  
else {    
	$mmode['option']	= "O";	
	$mmode['comp']		= "C";
	$mmode['unit']		= "U";
	$mmode['made']		= "M";	
	$mmsg['option']		= "옵션";
	$mmsg['comp']		= "제조사";
	$mmsg['unit']		= "판매단위";
	$mmsg['made']		= "원산지";

	switch($b_mode) {
		case "ins" : 
			$name = isset($_POST['name'])? $_POST['name']:'';	
			if(!$name) alert('정보가 제대로 넘어오지 못 했습니다.\\n다시 시도해 주시기 바랍니다.','back');
			chrtrim($name); // 공백쿼리검사 함수호출
			$name= addslashes($name);
									  
			/* 고유번호 */
			$sql = "SELECT max(number) FROM mall_goods_conf WHERE mode='{$mmode[$mode]}'";
			$next_num = $mysql->get_one($sql);
			if(!$next_num) $next_num=1;
			else $next_num++;

			$sql = "INSERT INTO mall_goods_conf VALUES('','{$name}','{$next_num}','{$mmode[$mode]}','')";
			$msg = $mmsg[$mode]."를 추가했습니다!";
		break;
			
		case "mod" :
			$re_name	= isset($_POST['re_name'])? $_POST['re_name']:'';	
			$b_num		= isset($_POST['b_num'])? $_POST['b_num']:'';	
			if(!$re_name || !$b_num) alert('정보가 제대로 넘어오지 못 했습니다.\\n다시 시도해 주시기 바랍니다.','back');
			chrtrim($re_name); // 공백쿼리검사 함수호출
			$re_name= addslashes($re_name);
			$sql = "UPDATE mall_goods_conf SET name='{$re_name}' WHERE uid='{$b_num}'";
			$msg = $mmsg[$mode]."명을 수정했습니다!";                
		break;
		
		case "del" :
			$b_num = isset($_POST['b_num'])? $_POST['b_num']:'';	
			if(!$b_num) alert('정보가 제대로 넘어오지 못 했습니다.\\n다시 시도해 주시기 바랍니다.','back');
			$sql = "DELETE FROM mall_goods_conf WHERE uid='{$b_num}'";
			$msg = $mmsg[$mode]."를 삭제했습니다!";                
		break;
	}			
} //end of if

$mysql->query($sql);
alert($msg,'goods_conf.html');
?>
