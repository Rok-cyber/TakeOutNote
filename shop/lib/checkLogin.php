<? 
// require나 include시 중복선언 방지를 위한 부분 

if(!isset($__PREVIL_CKLOG__))   
{ 
  $__PREVIL_CKLOG__ = 1; 

/*
###############################################
     ::: 로그인 체크함수 :::          
###############################################
*/

function checkLogin($rand) { 
    global $_COOKIE; 

	$get_userid = base64_decode($_COOKIE['my_id']); 
    $get_sid	= $_COOKIE['sid']; 
 	if(!$get_userid || !$get_sid) return false; 
    $get_userid .= $rand;  
    $real_sid	= md5($get_userid); 
    if($get_sid == $real_sid) return true; 
	else return false; 
} 

############ 로그인 쿠키체크 ####################
if($_COOKIE['my_id']) {
	$my_id = base64_decode($_COOKIE['my_id']); 	
	if(!checkLogin($cook_rand))  Error("올바른 경로가 아닙니다~");	
	else {
		@session_start();     //세션 시작
		if(!$_SESSION['mylevel']) {    //세션을 다시 굽는다
		
			$_SESSION["myname"] = '';  
			$_SESSION["myemail"] = '';  
			$_SESSION["myhomepage"] = '';  
			$_SESSION["mylevel"] = '';  
			$_SESSION["mysale"] = '';  
			$_SESSION["mypoint"] = '';  
			$_SESSION["mycarr"] = '';
		 
			require "{$lib_path}/class.Mysql.php";
			$mysql = new mysqlClass();

			$sql = "SELECT name,email,level,homepage FROM pboard_member WHERE id='{$my_id}'";
			$row = $mysql->one_row($sql);
			$myname		= base64_encode($row['name']);
			$myemail	= base64_encode($row['email']);
			$mylevel	= base64_encode($row['level']);
			$myhomepage = base64_encode($row['homepage']);			

			$sql = "SELECT code FROM mall_design WHERE name='{$row['level']}' && mode='L'";
			$tmps = $mysql->get_one($sql);
			
			$tmps = explode("|",$tmps);
			if($tmps) {
				$mysale = $tmps[2];
				$mypoint = $tmps[3];
				$mycarr = $tmps[4];
			}
			else {
				$mysale = 0;
				$mypoint = 0;
				$mycarr = 'N';
			}

			$mysale = base64_encode($mysale);
			$mypoint = base64_encode($mypoint);
			$mycarr = base64_encode($mycarr);

		  
			$_SESSION['myname']		= $myname;
			$_SESSION['myemail']	= $myemail;
			$_SESSION['mylevel']	= $mylevel;
			$_SESSION['myhomepage']	= $myhomepage;
			$_SESSION['mysale']		= $mysale;
			$_SESSION['mypoint']	= $mypoint;
			$_SESSION['mycarr']		= $mycarr;

			//로그인 시간 기록
			$sql = "UPDATE pboard_member SET logtime='".time()."' WHERE id='{$my_id}'";
			$mysql->query($sql);
		}
	}// end of if(else)

	$my_level	=base64_decode($_SESSION['mylevel']);
	$my_name	=base64_decode($_SESSION['myname']);
	$my_email	=base64_decode($_SESSION['myemail']);
	$my_homepage=base64_decode($_SESSION['myhomepage']);    
	$my_sale	=base64_decode($_SESSION['mysale']);
	$my_point	=base64_decode($_SESSION['mypoint']);
	$my_carr	=base64_decode($_SESSION['mycarr']);
	if(!$my_level) $my_level="1";
} 
else {
	$my_level = "1";
	$my_name = "";
	$my_email = "";
	$my_homepage="";
	$my_sale="";
	$my_point="";
	$my_carr="";
}
 
} // End of if(!isset($__PREVIL_CKLOG__)) 
?>