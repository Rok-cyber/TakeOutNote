<?
include "sub_init.php";

$jmode	= isset($_POST['jmode']) ? $_POST['jmode'] : $_GET['jmode'];

############ 파라미터(값) 검사 ####################
if(eregi(":",$_SERVER['HTTP_HOST'])) {
	$tmps = explode(":",$_SERVER['HTTP_HOST']);
	$_SERVER['HTTP_HOST'] = $tmps[0];
	unset($tmps);
}
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if($_SERVER['REQUEST_METHOD']=='GET') alert('정상적으로 등록하세요!','back');


$id		= addslashes($_POST['id']);
$name	= addslashes($_POST['name']);
$passwd	= addslashes($_POST['passwd']);
$minfo	= addslashes($_POST['info']);
$email	= addslashes($_POST['email']);
$addr	= addslashes($_POST['addr1']);	

if(((!$id || !$passwd || !$email) && $jmode=='new') || !$jmode) alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');

if($jmode=='new' || $jmode=='modify') {
	############ 사용금지 아이디 불러오기 ####################
	$sql = "SELECT address,info FROM pboard_member WHERE uid=1";
	$data = $mysql->one_row($sql);

	$options	= explode("|",$data['address']);
	$w_word		= explode("|",$data['info']);

	$addr		= addslashes($_POST['addr1']);
	$email		= addslashes($_POST['email']);
	if(!mailCheck($email) && $email) {
		alert("입력하신 {$email} 은 존재하지 않는 메일주소입니다.\\n다시 한번 확인하여 주시기 바랍니다.",'back');
	}
	$homepage	= addslashes($_POST['homepage']);
	if(!eregi("http://",$homepage) && $homepage) $homepage="http://$homepage";
	$mess		= addslashes($_POST['mess']);
	$jobname	= addslashes($_POST['jobname']);
	$jumin1		= preg_replace('/[^0-9\-]/', '', $_POST['jumin1']);
	$jumin2		= preg_replace('/[^0-9\-]/', '', $_POST['jumin2']);
	$tel1		= addslashes($_POST['tel11']);
	$tel2		= addslashes($_POST['tel12']);
	$tel3		= addslashes($_POST['tel13']);
	$phone1		= addslashes($_POST['phone11']);
	$phone2		= addslashes($_POST['phone12']);
	$phone3		= addslashes($_POST['phone13']);
	$zip1		= addslashes($_POST['zip11']);
	$zip2		= addslashes($_POST['zip12']);
	$bir1		= addslashes($_POST['bir1']);
	$bir2		= addslashes($_POST['bir2']);
	$bir3		= addslashes($_POST['bir3']);
	$sex		= addslashes($_POST['sex']);
	$marr		= addslashes($_POST['marr']);
	$uy			= addslashes($_POST['uy']);
	$edu		= addslashes($_POST['edu']);
	$hobby		= addslashes($_POST['hobby']);
	$job		= addslashes($_POST['job']);
	$jobname	= addslashes($_POST['jobname']);
	$mailling	= addslashes($_POST['mailling']);
	$sms		= addslashes($_POST['sms']);

	$add1		= isset($_POST['add1']) ? addslashes($_POST['add1']) : '';
	$add2		= isset($_POST['add2']) ? addslashes($_POST['add2']) : '';
	$add3		= isset($_POST['add3']) ? addslashes($_POST['add3']) : '';
	$add4		= isset($_POST['add4']) ? addslashes($_POST['add4']) : '';
	$add5		= isset($_POST['add5']) ? addslashes($_POST['add5']) : '';

	$name2		= addslashes($_POST['name2']);
	$tel21		= addslashes($_POST['tel21']);
	$tel22		= addslashes($_POST['tel22']);
	$tel23		= addslashes($_POST['tel23']);
	$phone21	= addslashes($_POST['phone21']);
	$phone22	= addslashes($_POST['phone22']);
	$phone23	= addslashes($_POST['phone23']);
	$zip21		= addslashes($_POST['zip21']);
	$zip22		= addslashes($_POST['zip22']);
	$addr2		= addslashes($_POST['addr2']);

	$name3		= addslashes($_POST['name3']);
	$tel31		= addslashes($_POST['tel31']);
	$tel32		= addslashes($_POST['tel32']);
	$tel33		= addslashes($_POST['tel33']);
	$phone31	= addslashes($_POST['phone31']);
	$phone32	= addslashes($_POST['phone32']);
	$phone33	= addslashes($_POST['phone33']);
	$zip31		= addslashes($_POST['zip31']);
	$zip32		= addslashes($_POST['zip32']);
	$addr3		= addslashes($_POST['addr3']);

	$message1	= addslashes($_POST['message1']);
	$message2	= addslashes($_POST['message2']);
	$message3	= addslashes($_POST['message3']);

	if($tel1 && $tel2 && $tel3) $tel = "$tel1 - $tel2 - $tel3";
	else $tel = "";
	if($phone1 && $phone2 && $phone3) $hphone = "$phone1 - $phone2 - $phone3";
	else $hphone = "";
	if($zip1 && $zip2) $zipcode = "$zip1 - $zip2";
	else $zipcode = "";

	if($tel21 && $tel22 && $tel23) $tel2 = "$tel21 - $tel22 - $tel23";
	else $tel2 = '';
	if($phone21 && $phone22 && $phone23) $hphone2 = "$phone21 - $phone22 - $phone23";
	else $hphone2 = '';
	if($zip21 && $zip22) $zipcode2 = "$zip21 - $zip22";
	else $zipcode2 = "";

	if($tel31 && $tel32 && $tel33) $tel3 = "$tel31 - $tel32 - $tel33";
	else $tel3 = '';
	if($phone31 && $phone32 && $phone33) $hphone3 = "$phone31 - $phone32 - $phone33";
	else $hphone3 = '';
	if($zip31 && $zip32) $zipcode3 = "$zip31 - $zip32";
	else $zipcode3 = "";
	
	if($bir1 && $bir2 && $bir3) $birth = "$bir1|$bir2|$bir3|$uy";
	else $birth = "";

	$carriage1 = "{$name2}|{$tel2}|{$hphone2}|{$zipcode2}|{$addr2}";
	$carriage2 = "{$name3}|{$tel3}|{$hphone3}|{$zipcode3}|{$addr3}";

	$signdate = time();

	$right = strrchr($email, "@"); 
	$mail_server =  substr($right,1); 
}

switch($jmode) {
	case "passwd" :
		if(!$my_id) alert("로그인이 되지 않았습니다.","back");
		$orig_passwd = $_POST['orig_passwd'];
		$passwd = $_POST['passwd'];

		$sql = "SELECT passwd FROM pboard_member WHERE id='{$my_id}'";
		$ori_pw = $mysql->get_one($sql);
		
		if(md5($orig_passwd)!=$ori_pw) alert("비밀번호가 일치 하지 않습니다. 다시 입력 하시기 바랍니다.","back");

		$sql = "UPDATE pboard_member SET passwd ='".md5($passwd)."' WHERE id='{$my_id}'";
		$mysql->query($sql);
		$msg = "비밀번호가 변경 되었습니다.";

		alert($msg,"http://".$_SERVER['HTTP_HOST']."/{$ShopPath}{$Main}?channel=mypage");
	break;
	case "del" :
		if(!$my_id) alert("로그인이 되지 않았습니다.","back");
		if($my_level==10) alert("관리자는 탈퇴가 되지 않습니다.","back");

		$passwd = $_POST['passwd'];

		$sql = "SELECT passwd FROM pboard_member WHERE id='{$my_id}'";
		$ori_pw = $mysql->get_one($sql);
		
		if(md5($passwd)!=$ori_pw) alert("비밀번호가 일치 하지 않습니다. 다시 입력 하시기 바랍니다.","back");
		
		$sql = "DELETE FROM pboard_member WHERE id='{$my_id}' && uid>1";
		$mysql->query($sql);

		$sql = "DELETE FROM mall_reserve WHERE id='{$my_id}'";
		$mysql->query($sql);

		$sql = "DELETE FROM mall_wish WHERE id='{$my_id}'";
		$mysql->query($sql);

		$sql = "DELETE FROM mall_cart WHERE tempid='{$my_id}'";
		$mysql->query($sql);

		$sql = "DELETE FROM mall_cupon WHERE id='{$my_id}'";
		$mysql->query($sql);

		$reason = $_POST['reason'];
		$message = addslashes($_POST['message']);
		$sql = "SELECT count(*) FROM mall_order_info WHERE id='{$my_id}'";
		$ocnt = $mysql->get_one($sql);
		$signdate = time();

		$sql = "INSERT INTO mall_member_quit VALUES ('','{$my_name}','{$reason}','{$ocnt}','{$message}','{$signdate}')";
		$mysql->query($sql);

		$sql = "UPDATE mall_order_info SET id = 'del' WHERE id='{$my_id}'";
		$mysql->query($sql);

		$sql = "UPDATE mall_goods_point SET id = 'del' WHERE id='{$my_id}'";
		$mysql->query($sql);

		$sql = "UPDATE mall_goods_qna SET id = 'del' WHERE id='{$my_id}'";
		$mysql->query($sql);

		$sql = "SELECT name FROM pboard_manager";
		$mysql->query($sql);
		while($row = $mysql->fetch_array()){
			$sql = "UPDATE pboard_{$row['name']}_body SET id = 'del' WHERE id='{$my_id}'";
			$mysql->query2($sql);
		}

		################# 회원로그아웃 ################
		session_start(); 
		SetCookie("my_id","",-999,"/"); 
		SetCookie("sid","",-999,"/"); 
		SetCookie("PHPSESSID","",-999,"/"); 
		session_unregister("myname"); 
		session_unregister("myemail"); 
		session_unregister("myhomepage"); 
		session_unregister("mylevel"); 
		session_unregister("mysale"); 
		session_unregister("mypoint"); 
		session_unregister("mycarr"); 

		$msg="회원님 정보를 모두 삭제 하였습니다! 언제든지 다시 가입하실 수 있습니다!";
		alert($msg,"../{$Main}");

	break;
	case "new" :
		############ 주민등록번호 & 아이디 중복여부 확인 ####################  
		if($jumin1 && $jumin2){
			$sql = "SELECT COUNT(uid) FROM pboard_member WHERE uid > 1 && jumin1='{$jumin1}' && jumin2='".md5($jumin2)."'";
			if($mysql->get_one($sql)>0) alert('이미 등록된 주민등록번호가 존재합니다.','back');
		}
		
		$x_id = explode(",",$w_word[4]);
		$x_id[] = "del";
		$x_id[] = "guest";
		for($i=0;$i<count($x_id);$i++){
			if($x_id[$i]==$id) alert('사용금지 아이디입니다.\\n다른 아이디를 이용하세요','back');
		}
		
		$sql = "SELECT COUNT(uid) FROM pboard_member WHERE  uid>1 && id='{$id}'";
		if($mysql->get_one($sql)>0) alert('중복된 아이디입니다.\\n다른 아이디를 이용하세요','back');

		############ 적립급 처리 ############
		$sql = "SELECT code FROM mall_design WHERE mode='B'";
		$tmp_cash = $mysql->get_one($sql);
		$cash = explode("|*|",stripslashes($tmp_cash));
		//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비
		if($cash[6]=='1' && $cash[7]>0) {   // 가입 적립금
			$subject = "회원가입 축하 적립금";
			$signdate2 = date("Y-m-d H:i:s",time());
			$sql = "INSERT INTO mall_reserve (uid, id, subject, reserve, order_num, goods_num, status, signdate) VALUES ('','{$id}','{$subject}','{$cash[7]}','','','B','{$signdate2}')";
			$mysql->query($sql);
			$reserve = $cash[7];
		} else $reserve = 0;
		############ 적립급 처리 ############
	  
		$sql = "SELECT code FROM mall_design WHERE mode='T'";
		$tmps = $mysql->get_one($sql);
		$tmps = explode("|",$tmps);
		if($tmps[1]=="2") $auth = "N";
		else $auth = "Y";

		if(!$mailling) $mailling = 'Y';
		if(!$sms) $sms = 'Y';

		############ 등록 ####################
		$sql = "INSERT INTO pboard_member (uid, id, name, passwd, jumin1, jumin2, tel, hphone, zipcode, address, email, mail_server, homepage, msn, birth, sex, marr, edu, hobby, job, jobname, info, level, reserve, mailling,sms, add1, add2, add3, add4, add5, carriage1, carriage2, message1, message2, message3, auth, signdate, cnts, logtime) VALUES ('', '{$id}', '{$name}','".md5($passwd)."','{$jumin1}', '".md5($jumin2)."', '{$tel}', '{$hphone}', '{$zipcode}', '{$addr}', '{$email}', '{$mail_server}', '{$homepage}', '{$mess}', '{$birth}', '{$sex}', '{$marr}', '{$edu}', '{$hobby}', '{$job}', '{$jobname}', '{$minfo}', '2', '{$reserve}', '{$mailling}','{$sms}', '{$add1}', '{$add2}', '{$add3}', '{$add4}', '{$add5}', '{$carriage1}', '{$carriage2}', '{$message1}', '{$message2}', '{$message3}', '{$auth}', '{$signdate}', '1', '{$signdate}')";
		$mysql->query($sql);

		############ 회원 가입 축하메일 보내기 ############		
		$URL = "http://".$_SERVER['HTTP_HOST']."/{$ShopPath}";		
		$sql = "SELECT code FROM mall_design WHERE mode = 'F'";
		$mail_img = explode("|*|",stripslashes($mysql->get_one($sql)));	
		if($mail_img[2]=='1') {
			$mail_type = "regist";
			if($mail_img[0]) $MAIL_IMG = "<a href='{$URL}' target='_blank'><img src='{$URL}image/design/{$mail_img[0]}' width='760' border='0' alt='Mail Image' /></a>";	
			$MAIL_COMMENT = stripslashes($mail_img[3]);		
			$MAIL_COMMENT = str_replace("{name}",$name,$MAIL_COMMENT);
			include "mail_form.php";   //메일 양식 인클루드		
			$mail_form = str_replace("{shopName}",$basic[1],$mail_form);
			pmallMailSend($email, "{$basic[1]} 회원가입을 진심으로 환영합니다.", $mail_form);	
		}
		############ 회원 가입 축하메일 보내기 ############

		############ 회원 가입 축하SMS 보내기 #############
		if($hphone) {
			$code_arr = Array();
			$code_arr['name'] = $name;
			pmallSmsAutoSend($hphone,"join",$code_arr);
		}
		############ 회원 가입 축하SMS 보내기 #############

		############ 쿠폰발급 #############################
		$sql = "SELECT uid,sqty,qty FROM mall_cupon_manager WHERE type='2'";
		$mysql->query($sql);
		
		while($row=$mysql->fetch_array()){
			$cks = 1;
			if($row['sqty']=='1') {
				if($row['qty']<1) $cks = 0;
				else {
					$sql = "UPDATE mall_cupon_manager SET qty = qty -1 WHERE uid='{$num}'";
					$mysql->query2($sql);
				}		
			}

			if($cks==1) {
				$sql = "INSERT INTO mall_cupon VALUES('','{$row['uid']}','','{$id}','A','','','{$signdate}')";
				$mysql->query2($sql);			
			}
		}		
		############ 쿠폰발급 #############################

		############ 로그인 처리 ########################	
		if($auth=='Y') {
			$sql = "SELECT code FROM mall_design WHERE name='2' && mode='L'";
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

			session_cache_limiter('nocache, must_revalidate'); 
			session_set_cookie_params(0, "/"); 
			session_start(); 
			$_SESSION['myname']		= base64_encode($name);
			$_SESSION['myemail']	= base64_encode($email);
			$_SESSION['mylevel']	= base64_encode(2);
			$_SESSION['myhomepage']	= base64_encode($homepage);
			$_SESSION['mysale']		= base64_encode($mysale);
			$_SESSION['mypoint']	= base64_encode($mypoint);
			$_SESSION['mycarr']		= base64_encode($mycarr);

			if($_COOKIE['tempid'] && $_COOKIE['tempid'] != "NULL") {
				$sql = "UPDATE mall_cart SET tempid='{$id}' WHERE tempid = '{$_COOKIE['tempid']}'";
				$mysql->query($sql);
				SetCookie("tempid",$id,0,"/");
			} 
			else SetCookie("tempid",$id,0,"/");

			$text = $id.$cook_rand ;
			$id = base64_encode($id); 
			SetCookie("my_id",$id,0,"/"); 
			SetCookie("sid",md5($text),0,"/"); 			
		}
		############ 로그인 처리 ########################		

		movePage("http://".$_SERVER['HTTP_HOST']."/{$ShopPath}{$Main}?channel=regist2&name={$name}");
	break;

	case "modify" :
		
		$sql = "SELECT passwd FROM pboard_member WHERE id='{$my_id}'";
		$ori_pw = $mysql->get_one($sql);
		
		if(md5($passwd)!=$ori_pw) alert("비밀번호가 일치 하지 않습니다. 다시 입력 하시기 바랍니다.","back");
	 
		############ 가입양식 불러오기 ####################
		$mysql = new mysqlClass();
		$sql = "SELECT address FROM pboard_member WHERE uid=1";
		$data = $mysql->get_one($sql);
		$options = explode("|",$data);
	 
		$ctel = ($options[2]!='0') ? ", tel = '{$tel}'" : "";		
		$chphone = ($options[3]!='0') ? ", hphone='{$hphone}'" : "";
		$caddress = ($options[4]!='0') ? ", zipcode='{$zipcode}', address='{$addr}'" : "";
		$cemail = ($options[5]!='0') ? ", email='{$email}'" : ""; 
		$cserve = ($options[5]!='0') ? ", mail_server='{$mail_server}'" : ""; 
		$chomepage = ($options[6]!='0') ? ", homepage='{$homepage}'" : "";
		$cmsn = ($options[7]!='0') ? ", msn='{$mess}'" : "";
		$cbirth = ($options[8]!='0') ? ", birth='{$birth}'" : "";
		$csex = ($options[9]!='0') ? ", sex='{$sex}'" : "";
		$cmarr = ($options[10]!='0') ? ", marr='{$marr}'" : "";
		$cedu = ($options[11]!='0') ? ", edu='{$edu}'" : "";
		$chobby = ($options[12]!='0') ? ", hobby='{$hobby}'" : "";
		$cjob = ($options[13]!='0') ? ", job='{$job}'" : "";
		$cjobname = ($options[14]!='0') ? ", jobname='{$jobname}'" : "";
		$cinfo = ($options[15]!='0') ? ", info='{$minfo}'" : "";
		$cmailling = ($options[16]!='0') ? ", mailling='{$mailling}'" : "";
		$csms = ($options[26]!='0') ? ", sms='{$sms}'" : "";
		
		############ 등록된 아이콘 삭제 ####################
		if($options[17]!='0' && ($icon_name || $del_icon=='Y')) {
			$sql = "SELECT icon FROM pboard_member WHERE id='{$my_id}'";
			if($d_icon = $mysql->get_one($sql)) {
				delFile("{$bo_path}/icon/{$d_icon}");
			}
			$cicon = ", icon = '{$icon_name}'";
		}
		else $cicon = "";

	  
		############ 수정 ####################
		$sql = "UPDATE pboard_member SET add1='{$add1}', add2='{$add2}', add3='{$add3}', add4='{$add4}', add5='{$add5}', carriage1 = '{$carriage1}', carriage2 = '{$carriage2}', message1 = '{$message1}', message2 = '{$message2}', message3 = '{$message3}' {$ctel} {$chphone} {$caddress} {$cemail} {$cserve} {$chomepage} {$cmsn} {$cbirth} {$csex} {$cmarr} {$cedu} {$chobby} {$cjob} {$cjobname} {$cinfo} {$cmailling} {$csms} {$cicon} WHERE id='{$my_id}'";
		$mysql->query($sql);
		$msg = "회원 정보가 변경되었습니다.";

		$sql = "SELECT code FROM mall_design WHERE name='{$my_level}' && mode='L'";
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

		session_start();
		session_unregister("myname"); 
		session_unregister("myemail"); 
		session_unregister("myhomepage"); 
		session_unregister("mylevel"); 
		session_unregister("mysale"); 
		session_unregister("mypoint"); 
		session_unregister("mycarr"); 
		session_unregister("myadult");


		$myname = base64_encode($name);
		$myemail = base64_encode($email);
		$mylevel = base64_encode($level);
		$myhomepage = base64_encode($homepage);
		$mysale = base64_encode($mysale);
		$mypoint = base64_encode($mypoint);
		$mycarr = base64_encode($mycarr);

		session_register("myname"); 
		session_register("myemail"); 
		session_register("mylevel"); 
		session_register("myhomepage"); 
		session_register("mysale"); 
		session_register("mypoint"); 
		session_register("mycarr"); 

		alert($msg,"http://".$_SERVER['HTTP_HOST']."/{$ShopPath}{$Main}?channel=modify");
	break;    
}

?>