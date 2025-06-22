<?
include "../ad_init.php";

############ 파라미터(값) 검사 ####################
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');

$id		= $_GET['id'];

if(!$id) alert('아이디를 입력하세요!','back');

session_start(); 

################# 로그아웃 ################
SetCookie("my_id","",-999,"/"); 
SetCookie("sid","",-999,"/"); 
SetCookie("PHPSESSID","",-999,"/"); 
SetCookie("tempid","",-999,"/");
session_unregister("myname"); 
session_unregister("myemail"); 
session_unregister("myhomepage"); 
session_unregister("mylevel"); 
session_unregister("mysale"); 
session_unregister("mypoint"); 
session_unregister("mycarr"); 
session_unregister("myadult");

 /*
###############################################
     ::: 로그인 쿠키 굽기 함수 :::          
###############################################
*/

function makeLogin($id,$tm,$rand) { 

	$text = $id.$rand ;
	$id = base64_encode($id); 
	SetCookie("my_id",$id,$tm,"/"); 
	SetCookie("sid",md5($text),$tm,"/"); 
} 


################## 로그인하기 위해 입력한 아이디와 비밀번호가 일치하는 레코드를 검색 ##################
$sql = "SELECT passwd,name,email,level,homepage,icon,auth,birth FROM pboard_member WHERE id = '{$id}'";
if(!$row = $mysql->one_row($sql)) alert("회원이 존재 하지 않습니다. ",'back');

$myname = base64_encode($row['name']);
$myemail = base64_encode($row['email']);
$mylevel = base64_encode($row['level']);
$myhomepage = base64_encode($row['homepage']);

$tmps = explode("|",$row['birth']);
if(round($tmps[0])<(date("Y")-19)) $myadult ='Y';
else $myadult = 'N';

  
########## 사용자가 입력한 암호문자열을 암호화한다. ##########
if($auto_in=='Y') $tm = time()+3600*24*365;
else $tm = 0;
	  
$sql = "SELECT code FROM mall_design WHERE name='{$row['level']}' && mode='L'";
$tmps = $mysql->get_one($sql);

if($tmps) {
	$tmps = explode("|",$tmps);
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

session_cache_limiter('nocache, must_revalidate'); 
session_set_cookie_params(0, "/"); 
session_start(); 
$_SESSION['myname']		= $myname;
$_SESSION['myemail']	= $myemail;
$_SESSION['mylevel']	= $mylevel;
$_SESSION['myhomepage']	= $myhomepage;
$_SESSION['mysale']		= $mysale;
$_SESSION['mypoint']	= $mypoint;
$_SESSION['mycarr']		= $mycarr;
$_SESSION['myadult']	= base64_encode($myadult);

makeLogin($id,$tm,$cook_rand); 

################# 로그인 시간 기록 ####################
$sql = "UPDATE pboard_member SET cnts = cnts + 1, logtime='".time()."' WHERE id='{$id}'";
$mysql->query($sql);

################# 장바구니 ####################
SetCookie("tempid",$id,0,"/");

echo "<script>top.location.href='{$SMain}';</script>";
exit;
?>