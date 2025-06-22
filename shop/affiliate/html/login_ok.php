<?
$logInPage = 'Y';
include "../ad_init.php";

############ 파라미터(값) 검사 ####################
if(eregi(":",$_SERVER['HTTP_HOST'])) {
	$tmps = explode(":",$_SERVER['HTTP_HOST']);
	$_SERVER['HTTP_HOST'] = $tmps[0];
	unset($tmps);
}

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if($_SERVER['REQUEST_METHOD']=='GET') alert('정상적으로 등록하세요!','back');

$id		= isset($_POST['id']) ? addslashes(trim($_POST['id'])):'';
$passwd = isset($_POST['passwd']) ? addslashes(trim($_POST['passwd'])):'';
$acc_ip = $_SERVER['REMOTE_ADDR'];
$signdate = time();

if(!$id) alert('아이디를 입력하세요!','back');
if(!$passwd) alert('패스워드를 입력하세요','back');

 /*
###############################################
     ::: 로그인 쿠키 굽기 함수 :::          
###############################################
*/

function makeLogin($id,$tm,$rand) { 

	$text = $id.$rand ;	
	$id = base64_encode($id); 
	SetCookie("a_my_id",$id,$tm,"/"); 
	SetCookie("a_sid",md5($text),$tm,"/"); 
} 

################## 로그인하기 위해 입력한 아이디와 비밀번호가 일치하는 레코드를 검색 ##################
$sql = "SELECT passwd, name, email ,auth, commission FROM mall_affiliate WHERE id = '{$id}'";
if(!$row = $mysql->one_row($sql)) alert("아이디 또는 비밀번호 오류 입니다!",'back');

if($row['acc_auth']=='N') alert(" 죄송합니다! 아직 회원 미승인 상태입니다! \\n Affiliate 승인처리가 되어야만 이용 하실 수 있습니다. 자세한 사항은 쇼핑몰 고객센터에 문의 바랍니다",'back');

$db_passwd = $row['passwd'];
$a_myname = base64_encode($row['name']);
$a_myemail = base64_encode($row['email']);
$a_mycommi = base64_encode($row['commission']);

########## 사용자가 입력한 암호문자열을 암호화한다. ##########
if(strcmp($db_passwd,md5($passwd))){
	alert("아이디 또는 비밀번호 오류 입니다!",'back');    //비번확인
}
 
if($auto_in=='Y') $tm = time()+3600*24*365;
else $tm = 0;
	  
session_cache_limiter('nocache, must_revalidate'); 
session_set_cookie_params(0, "/"); 
session_start(); 
$_SESSION['a_myname']		= $a_myname;
$_SESSION['a_myemail']		= $a_myemail;
$_SESSION['a_mycommi']		= $a_mycommi;

makeLogin($id,$tm,$cook_rand); 

$url = "http://".$_SERVER["HTTP_HOST"]."/{$ShopPath}affiliate/html/index.html";		
movePage($url);	  
?>