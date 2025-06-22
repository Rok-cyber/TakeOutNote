<? 
// require나 include시 중복선언 방지를 위한 부분 
if( !$__PREVIL_CKLOG__ )   
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
	if(!checkLogin($cook_rand))  {

		$my_level = "1";
	$my_name = "";
	$my_email = "";
	$my_homepage="";
	$my_icon="";

	} else {
		@session_start();     //세션 시작
		if(!$_SESSION['mylevel']) {    //세션을 다시 굽는다
		
			session_unregister("myname"); 
			session_unregister("myemail"); 
			session_unregister("myhomepage"); 
			session_unregister("mylevel"); 
			session_unregister("myicon"); 
		 
			require "$bo_path/lib/class.Mysql.php";
			$mysql = new mysqlClass();

			$sql = "SELECT name,email,level,homepage,icon FROM pboard_member WHERE id='{$my_id}'";
			$row = $mysql->one_row($sql);
			$myname		= base64_encode($row['name']);
			$myemail	= base64_encode($row['email']);
			$mylevel	= base64_encode($row['level']);
			$myhomepage = base64_encode($row['homepage']);
			$myicon		= base64_encode($row['icon']);    
		  
			$_SESSION['myname']		= $myname;
			$_SESSION['myemail']	= $myemail;
			$_SESSION['mylevel']	= $mylevel;
			$_SESSION['myhomepage']	= $myhomepage;
			$_SESSION['myicon']		= $myicon;

			//로그인 시간 기록
			$sql = "UPDATE pboard_member SET logtime='".time()."' WHERE id='{$my_id}'";
			$mysql->query($sql);

			############ 디비관련 메모리제거 ####################
			include "$bo_path/close.php";
	   
			movePage($Main);   //리프레쉬시킴
		}
	}// end of if(else)

	$my_level	=base64_decode($_SESSION['mylevel']);
	$my_name	=base64_decode($_SESSION['myname']);
	$my_email	=base64_decode($_SESSION['myemail']);
	$my_homepage=base64_decode($_SESSION['myhomepage']);
    $my_icon	=base64_decode($_SESSION['myicon']);
	if(!$my_level) $my_level="1";
} else {
	$my_level = "1";
	$my_name = "";
	$my_email = "";
	$my_homepage="";
	$my_icon="";
}
 
} // End of if( !$__PREVIL_CKLOG__ ) 
?>