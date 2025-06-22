<?
include "sub_init.php";

############ 파라미터(값) 검사 ####################
if(eregi(":",$_SERVER['HTTP_HOST'])) {
	$tmps = explode(":",$_SERVER['HTTP_HOST']);
	$_SERVER['HTTP_HOST'] = $tmps[0];
	unset($tmps);
}
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if($_SERVER['REQUEST_METHOD']=='GET') alert('정상적으로 등록하세요!','back');

$url    = isset($_POST['url']) ? $_POST['url'] : $_GET['url'];
$id		= isset($_POST['id']) ? addslashes(trim($_POST['id'])):'';
$passwd = isset($_POST['passwd']) ? addslashes(trim($_POST['passwd'])):'';
$url	= !empty($_POST['url']) ? $_POST['url']:"http://".$_SERVER['HTTP_HOST']."/{$ShopPath}{$Main}";
$channel2 = $_POST['channel2'];
if($channel2 && $channel2!='login') $pt_channel = "?channel={$channel2}";

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
	SetCookie("my_id",$id,$tm,"/"); 
	SetCookie("sid",md5($text),$tm,"/"); 
} 

################## 로그인하기 위해 입력한 아이디와 비밀번호가 일치하는 레코드를 검색 ##################
$sql = "SELECT id,passwd,name,email,level,homepage,icon,auth FROM pboard_member WHERE id = '{$id}'";
if(!$row = $mysql->one_row($sql)) alert("아이디 또는 비밀번호 오류 입니다!",'back');

if($row['auth']=='N') alert(" 죄송합니다! 아직 회원 미승인 상태입니다! \\n 회원 승인처리가 되어야만 이용 하실 수 있습니다. 자세한 사항은 관리자에게 문의 바랍니다",'back');
if($row['level'] == 1) alert("죄송합니다! 조회결과 회원기능 일시정지 상태입니다! 자세한 사항은 관리자에게 문의 바랍니다",'back');

$id = $row['id'];
$db_passwd = $row['passwd'];
$myname = base64_encode($row['name']);
$myemail = base64_encode($row['email']);
$mylevel = base64_encode($row['level']);
$myhomepage = base64_encode($row['homepage']);
 
  
########## 사용자가 입력한 암호문자열을 암호화한다. ##########
if(strcmp($db_passwd,md5($passwd))) alert("아이디 또는 비밀번호 오류 입니다!",'back');    //비번확인
   
if($auto_in=='Y') $tm = time()+3600*24*365;
else $tm = 0;
	  
$sql = "SELECT code FROM mall_design WHERE name='{$row['level']}' && mode='L'";
$tmps = $mysql->get_one($sql);

if($tmps) {
	$tmps = explode("|",$tmps);
	$mysale = ($tmps[2]) ? $tmps[2] : 0;
	$mypoint = ($tmps[3]) ? $tmps[3] : 0;
	$mycarr = ($tmps[4]) ? $tmps[4] : 'N';
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

makeLogin($id,$tm,$cook_rand); 

if($row['level']==10) socketPost($itsMall);

################# 로그인 시간 기록 ####################
$sql = "UPDATE pboard_member SET cnts = cnts + 1, logtime='".time()."' WHERE id='{$id}'";
$mysql->query($sql);

################# 장바구니 ####################
if($_COOKIE['tempid'] && $_COOKIE['tempid'] != "NULL") {
	$sql = "UPDATE mall_cart SET tempid='{$id}' WHERE tempid = '{$_COOKIE['tempid']}'";
	$mysql->query($sql);	
} 
SetCookie("tempid",$id,0,"/");

################# 아이디 저장 ####################
if($_POST['save_id']=='Y') {
	SetCookie("s_id",base64_encode($id),$tm,"/");
}
else SetCookie("s_id","",-999,"/");

############ 출석체크 로그인시 ####################
$sql = "SELECT * FROM mall_attendance WHERE s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."' ORDER BY s_date ASC LIMIT 1";
if($row = $mysql->one_row($sql)){
	if($row['method']=='L') {
		$uid = $row['uid'];
		$my_id = $id;
		$signdate = date("Y-m-d H:i:s",time());
		$todays	= date("Y-m-d");
		$thisyear  = date('Y');  // 2000
		$thismonth = date('n');  // 1, 2, 3, ..., 12
		$today     = date('j');  // 1, 2, 3, ..., 31

		$sql = "SELECT count(*) FROM mall_attendance_check WHERE id='{$my_id}' && year='{$thisyear}' && month='{$thismonth}' && day='{$today}' && puid='{$uid}'";
		if($mysql->get_one($sql)==0) {
			$msg1 = stripslashes($row['msg1']);
			$msg2 = stripslashes($row['msg2']);
			$msg3 = stripslashes($row['msg3']);
			$msg4 = stripslashes($row['msg4']);
			
			$prevyear  = date('Y',time()-86400);  // 2000
			$prevmonth = date('n',time()-86400);  // 1, 2, 3, ..., 12
			$prevday     = date('j',time()-86400);  // 1, 2, 3, ..., 31

			$sql = "SELECT continuity FROM mall_attendance_check WHERE id='{$my_id}' && year='{$prevyear}' && month='{$prevmonth}' && day='{$prevday}' && puid='{$uid}'";
			if($conti=$mysql->get_one($sql)) $conti++;
			else $conti = 1;

			$sql = "SELECT MAX(total) FROM mall_attendance_check WHERE id='{$my_id}' && puid='{$uid}'";
			if($total=$mysql->get_one($sql)) $total++;
			else $total = 1;

			$sql = "INSERT INTO mall_attendance_check VALUES ('','{$uid}','{$my_id}','{$thisyear}','{$thismonth}','{$today}','{$total}','{$conti}',0,0)";
			$mysql->query($sql);

			$sql = "SELECT MAX(uid) FROM mall_attendance_check WHERE id='{$my_id}' && puid='{$uid}'";
			$luid = $mysql->get_one($sql);

			$subject = "[출석체크-".stripslashes($row['name'])."] 이벤트 성공 적립금 지급";
			switch($row['type']) {
				case "A" : case "T" :
					if($row['type']=='T') {
						$sql = "SELECT count(*) FROM mall_attendance_check WHERE id='{$my_id}' && puid='{$uid}'";
						$conti = $mysql->get_one($sql);
					}
				
					if($conti==$row['condi']) {
						$sql = "SELECT count(*) FROM mall_attendance_check WHERE puid='{$uid}' && id='{$my_id}' && success=1";
						if($mysql->get_one($sql)==0) {
							if($row['stype']=='A') {
								$sql = "INSERT INTO mall_reserve VALUES ('','{$my_id}','{$subject}','{$row['point']}','','','B','{$signdate}')";
								$mysql->query($sql);
								
								$sql = "SELECT MAX(uid) FROM mall_reserve WHERE id='{$my_id}'";
								$ruid = $mysql->get_one($sql);
								
								$sql = "UPDATE pboard_member SET reserve = reserve + '{$row['point']}' WHERE id = '{$my_id}'"; 
								$mysql->query($sql);	
								
								$sql = "UPDATE mall_attendance_check SET success=1, reserve={$ruid} WHERE uid='{$luid}' && id='{$my_id}'";
								$mysql->query($sql);

								//alert($msg3,"../{$Main}?channel=attendance");
							}
							else {
								$sql = "UPDATE mall_attendance_check SET success=1 WHERE uid='{$luid}' && id='{$my_id}' && puid='{$uid}'";
								$mysql->query($sql);
								
								//alert($msg4,"../{$Main}?channel=attendance");
							}
						}
					}
					
					//alert($msg1,"../{$Main}?channel=attendance");

				break;

				case "D" :
					if($row['stype']=='A') {
						$sql = "INSERT INTO mall_reserve VALUES ('','{$my_id}','{$subject}','{$row['point']}','','','B','{$signdate}')";
						$mysql->query($sql);
						
						$sql = "SELECT MAX(uid) FROM mall_reserve WHERE id='{$my_id}'";
						$ruid = $mysql->get_one($sql);

						$sql = "UPDATE pboard_member SET reserve = reserve + '{$row['point']}' WHERE id = '{$my_id}'"; 
						$mysql->query($sql);	
						
						$sql = "UPDATE mall_attendance_check SET success=1, reserve={$ruid} WHERE uid='{$luid}' && id='{$my_id}' && puid='{$uid}'";
						$mysql->query($sql);

						//alert($msg3,"../{$Main}?channel=attendance");
					}
					else {
						$sql = "UPDATE mall_attendance_check SET success=1 WHERE uid='{$luid}' && id='{$my_id}' && puid='{$uid}'";
						$mysql->query($sql);
						
						//alert($msg4,"../{$Main}?channel=attendance");
					}
				break;	
			}

		}
	}
}
############ 출석체크 로그인시 ####################

############ 사용자가 요청한 URL 리다이렉션 ####################
switch($url) {
	case "admin" : 
		$url = "http://".$_SERVER['HTTP_HOST']."/{$ShopPath}managers/html/index.html";		
	break;

	case "order" :
		echo "<script>parent.location.href='http://".$_SERVER['HTTP_HOST']."/{$ShopPath}{$Main}?channel=order_form';</script>";
		exit;
	break;

	case "direct" :
		echo "<script>parent.document.cartForm.submit();</script>";
		exit;
	break;

	case "direct2" :
		echo "<script>parent.document.qCartForm.submit();</script>";
		exit;
	break;

	case "cart" :
		echo "<script>parent.location.href='http://".$_SERVER['HTTP_HOST']."/{$ShopPath}{$Main}?channel=cart';</script>";
		exit;
	break;	

	case "view" : case "login" :
		echo "<script>parent.location.reload();</script>";
		exit;
	break;	

	case "osearch" :
		$url = "../{$Main}?channel=order";	
	break;

	case "cupon" :
		$num = isset($_POST['num']) ? $_POST['num'] : '';
		$gid = isset($_POST['gid']) ? $_POST['gid'] : '';
		$url = "pcupon_down.php?num={$num}&gid={$gid}";	
	break;

	case "vorder" :
		$num = isset($_POST['num']) ? $_POST['num'] : '';
		$url = "http://".$_SERVER['HTTP_HOST']."/{$ShopPath}/php/vorder_ok.php?num={$num}";	
	break;

	case "cooper" :
		echo "<script>parent.ckGoods('cooper',1);</script>";
		exit;
	break;
}
movePage($url.$pt_channel);	  

?>