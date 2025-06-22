<? 
// require나 include시 중복선언 방지를 위한 부분 

if(!isset($__PREVIL_CKALOG__))   
{ 
  $__PREVIL_CKALOG__ = 1; 

/*
###############################################
     ::: 로그인 체크함수 :::          
###############################################
*/

function checkVLogin($rand) { 
    global $_COOKIE; 

	$get_userid = base64_decode($_COOKIE['a_my_id']); 
    $get_sid	= $_COOKIE['a_sid']; 
 	if(!$get_userid || !$get_sid) return false; 
    $get_userid .= $rand;  
    $real_sid	= md5($get_userid); 
    if($get_sid == $real_sid) return true; 
	else return false; 
} 

############ 로그인 쿠키체크 ####################
if($_COOKIE['a_my_id']) {
	$a_my_id = base64_decode($_COOKIE['a_my_id']); 	
	if(!checkVLogin($cook_rand))  Error("올바른 경로가 아닙니다~");	
	else {
		@session_start();     //세션 시작
		if(!$_SESSION['a_myname']) {    //세션을 다시 굽는다
		
			$_SESSION["a_myname"] = ''; 
			$_SESSION["a_myemail"] = '';
			$_SESSION["a_mycommi"] = '';
					 
			require "{$lib_path}/class.Mysql.php";
			$mysql = new mysqlClass();

			$sql = "SELECT name, email, commission FROM mall_affiliate WHERE id='{$a_my_id}'";
			$row = $mysql->one_row($sql);
			$a_myname = base64_encode($row['name']);
			$a_myemail = base64_encode($row['email']);
			$a_mycommi = base64_encode($row['commission']);

			$_SESSION['a_myname']		= $a_myname;
			$_SESSION['a_myemail']		= $a_myemail;
			$_SESSION['a_mycommi']		= $a_mycommi;
		}
	}// end of if(else)

	$a_my_name	=base64_decode($_SESSION['a_myname']);
	$a_my_email	=base64_decode($_SESSION['a_myemail']);
	$a_my_commi	=base64_decode($_SESSION['a_mycommi']);
} 
else {
	$a_my_name = "";
	$a_my_email = "";
	$a_my_commi = "";
}
 
} // End of if(!isset($__PREVIL_CKALOG__)) 
?>