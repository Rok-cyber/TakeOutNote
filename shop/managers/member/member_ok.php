<?
include "../ad_init.php";

###################### 변수 정의 ##########################
$field		= $_GET['field'];
$word		= $_GET['word'];
$sdate1		= $_GET['sdate1'];
$sdate2		= $_GET['sdate2'];
$dates		= $_GET['dates'];
$auth		= $_GET['auth'];
$sex		= $_GET['sex'];
$mailling	= $_GET['mailling'];
$sms		= $_GET['sms'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$uid		= $_GET['uid'];
$mode		= $_GET['mode'];
$levels		= $_GET['levels'];

if(!$uid && $mode =='modify') alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');

// 검색
if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($page) $addstring .="&page={$page}";
if($auth) $addstring .= "&auth={$auth}";
if($sex) $addstring .= "&sex={$sex}";
if($mailling) $addstring .= "&mailling={$mailling}";
if($sms) $addstring .= "&sms={$sms}";
if($dates && $sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}&dates={$dates}";	
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($levels) $addstring .="&levels={$levels}";

echo "<iframe name='HFrm' border='0' frameborder='0' framespacing='0' marginheight='0' marginwidth='0' scrolling='no'  src='blank.html'></iframe>";

switch($mode) {  

	case "excel" :
		if(!eregi("none",$_FILES["excels"]['tmp_name']) && $_FILES["excels"]['tmp_name']) {
			
			function checkString($str){
				$str = str_replace("|#|",",",$str);
				$str = str_replace('"""','|#|',$str);
				$str = str_replace('"','',$str);
				$str = str_replace('|#|','"',$str);
				$str = iconv("euc-kr","utf-8",$str);
				return addslashes(trim($str));
			}

			
			$ext = getExtension($_FILES["excels"]['name']);
			if($ext!="csv") {			
				alert("엑셀 파일이 아닙니다.","back");
			}
			
			$encode = $_POST['encode'];
			$benefit = $_POST['benefit'];

			$tmps = readFiles($_FILES["excels"]['tmp_name']);
			$tmps = explode("\"",$tmps);
			for($i=1,$cnt=count($tmps);$i<$cnt;$i+=2) {
				$tmps[$i] = str_replace(",","|#|",$tmps[$i]);
			}
			$tmps = join("\"",$tmps);

			$f_id = Array();

			$tmps = explode("\n",$tmps);
			for($i=$l=1,$cnt=count($tmps);$i<$cnt;$i++) {
				$data = explode(",",$tmps[$i]);
				
				if(!$data[0] || !$data[1] || !$data[2] || !$data[8]) continue;

				$name	= checkString($data[0]);
				$id		= checkString($data[1]);
				$passwd	= checkString($data[2]);
				$jumin	= checkString($data[3]);
				$tel 	= checkString($data[4]);
				$hphone	= checkString($data[5]);
				$zipcode	= checkString($data[6]);
				$addr	= checkString($data[7]);
				$email	= checkString($data[8]);
				$birth	= checkString($data[9]);
				$uy		= checkString($data[10]);
				$sex	= checkString($data[11]);
				$mailling	= checkString($data[12]);
				$sms	= checkString($data[13]);
				$reserve	= checkString($data[14]);
				$level	= checkString($data[15]);
				$add1	= checkString($data[16]);
				$add2	= checkString($data[17]);

				$reserve = preg_replace('/[^0-9\-]/', '',$reserve);
				$level = preg_replace('/[^0-9\-]/', '',$level);
				if($level<1 || $level >4) $level = 2;

				
				$tmps2 = explode("-",$jumin);
				$jumin1		= $tmps2[0];
				$jumin2		= $tmps2[1];
				$tel = str_replace("-"," - ",$tel);
				$hphone = str_replace("-"," - ",$hphone);
				$zipcode = str_replace("-"," - ",$zipcode);

				$tmps2 = explode("-",$birth);
				
				$bir1 = $tmps2[0];
				$bir2 = $tmps2[1];
				$bir3 = $tmps2[2];
				if($uy=='U') $uy="음력";
				else if($uy=='Y') $uy = "양력";

				if($bir1 && $bir2 && $bir3) $birth = "$bir1|$bir2|$bir3|$uy";
				else $birth = "";

				$signdate = time();
				$signdate2 = date("Y-m-d H:i:s",time());

				$right = strrchr($email, "@"); 
				$mail_server =  substr($right,1); 

				############ 주민등록번호 & 아이디 중복여부 확인 ####################  
				
				if($jumin1 && $jumin2){
					$sql = "SELECT COUNT(uid) FROM pboard_member WHERE uid > 1 && jumin1='{$jumin1}' && jumin2='".md5($jumin2)."'";
					if($mysql->get_one($sql)>0) {
						$f_id[] = $id;
						continue;
					}
				}
				
				$sql = "SELECT COUNT(uid) FROM pboard_member WHERE  uid>1 && id='{$id}'";
				if($mysql->get_one($sql)>0) {
					$f_id[] = $id;
					continue;
				}
				
				if($encode!='Y') $passwd = md5($passwd);

				if($my_level<=$level) $level=$my_level;

				############ 등록 ####################
				$sql = "INSERT INTO pboard_member (uid, id, name, passwd, jumin1, jumin2, tel, hphone, zipcode, address, email, mail_server, homepage, msn, birth, sex, marr, edu, hobby, job, jobname, info, level, reserve, mailling, sms, auth, add1, add2, signdate) VALUES ('', '{$id}', '{$name}','{$passwd}','{$jumin1}', '".md5($jumin2)."', '{$tel}', '{$hphone}', '{$zipcode}', '{$addr}', '{$email}', '{$mail_server}', '{$homepage}', '{$mess}', '{$birth}', '{$sex}', '{$marr}', '{$edu}', '{$hobby}', '{$job}', '{$jobname}', '{$minfo}', '{$level}', '{$reserve}', '{$mailling}', '{$sms}', 'Y', '{$add1}', '{$add2}', '{$signdate}')";				
				$mysql->query($sql);
				
				if($reserve>0) {
					$sql = "INSERT INTO mall_reserve VALUES ('','{$id}','회원 초기 적립금 지급','{$reserve}','','','B','{$signdate2}')";
					$mysql->query($sql);
				}
				
				if($benefit=='Y') {
					############ 적립급 처리 ############
					$sql = "SELECT code FROM mall_design WHERE mode='B'";
					$tmp_cash = $mysql->get_one($sql);
					$cash = explode("|*|",stripslashes($tmp_cash));
					//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비
					if($cash[6]=='1' && $cash[7]>0) {   // 가입 적립금
						$subject = "회원가입 축하 적립금";						
						$sql = "INSERT INTO mall_reserve VALUES ('','{$id}','{$subject}','{$cash[7]}','','','B','{$signdate2}')";
						$mysql->query($sql);
						$reserve = $cash[7];
					} else $reserve = 0;
					############ 적립급 처리 ############

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
				}
				$l++;
			}
			$f_cnt = count($f_id);			
			if($f_cnt>0) {
				$f_id = join(",",$f_id);
				$f_str = "아이디 및 주민번호 중복인 회원 {$f_cnt}명({$f_id})을 제외한 ";
			}
			$msg = $f_str.($l-1)."명의 회원을 일괄 등록했습니다!";	
			
			alert($msg,"member_adds.html");
		}
		else {
			alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		}
	break;

	case 'del' :   //삭제	

		################ 삭제처리  
		$item = $_POST['item'];
		for($i = 0, $ct_num = count($item); $i < $ct_num; $i++) {
			
			$sql = "SELECT level, id FROM pboard_member WHERE uid>1 && uid = '{$item[$i]}'";
			$tmps = $mysql->one_row($sql);
			if($tmps['level']==10) alert("관리자는 삭제가 되지 않습니다.","back");
		
			$sql = "DELETE FROM pboard_member WHERE uid>1 && uid = '{$item[$i]}'";
			$mysql->query($sql);

			$sql = "DELETE FROM mall_reserve WHERE id='{$tmps['id']}'";
			$mysql->query($sql);

			$sql = "DELETE FROM mall_wish WHERE id='{$tmps['id']}'";
			$mysql->query($sql);

			$sql = "DELETE FROM mall_cart WHERE tempid='{$tmps['id']}'";
			$mysql->query($sql);

			$sql = "UPDATE mall_order_info SET id = 'del' WHERE id='{$tmps['id']}'";
			$mysql->query($sql);

			$sql = "UPDATE mall_goods_point SET id = 'del' WHERE id='{$tmps['id']}'";
			$mysql->query($sql);

			$sql = "UPDATE mall_goods_qna SET id = 'del' WHERE id='{$tmps['id']}'";
			$mysql->query($sql);

			$sql = "DELETE FROM mall_cupon WHERE id='{$tmps['id']}'";
			$mysql->query($sql);
			
			$sql = "SELECT name FROM pboard_manager";
			$mysql->query($sql);
			while($row = $mysql->fetch_array()){
				$sql = "UPDATE pboard_{$row['name']}_body SET id = 'del' WHERE id='{$tmps['id']}'";
				$mysql->query2($sql);
			}		

		} 
		$msg = "$i 명의 회원을 삭제했습니다!";
	break;

	case 'log_del' :   //삭제	
		$item = $_POST['item'];
		for($i = 0, $ct_num = count($item); $i < $ct_num; $i++) {
			$sql = "DELETE FROM pboard_maillog where uid='{$item[$i]}'";
			$mysql->query($sql);
		}		
		$msg = "$i 건의 메일발송내역을 삭제했습니다!";
	break;

	case 'modify' :   //수정
		$level = $_POST['level'];
		$passwd = $_POST['passwd'];
		$mailling = $_POST['mailling'];
		$sms = $_POST['sms'];

		$name	= addslashes($_POST['name']);
		if($name) $pname = "name = '{$name}', ";
		if($mailling) $pmailling = "mailling = '{$mailling}', ";
		if($sms) $psms = "sms = '{$sms}', ";
		if($passwd) $pw = "passwd = '".md5($passwd)."', ";
		if($my_level<=$level) $level=$my_level;

		$sql = "UPDATE pboard_member SET {$pname} {$pmailling} {$psms} {$pw} level='{$level}', auth = '{$_POST['mauth']}' WHERE uid = '{$uid}' && level <= {$my_level}";
		
		$mysql->query($sql);
		$msg = "회원 정보를 수정했습니다."; 
	break;

	case 'level' : 
		$num = $_POST['num'];
		
		$code = $_POST['name'.$num]."|".$_POST['permi'.$num]."|".$_POST['sale'.$num]."|".$_POST['point'.$num]."|".$_POST['carr'.$num];

		$sql = "SELECT count(*) FROM mall_design WHERE name='{$num}' && mode='L'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_design VALUES('','{$num}','{$code}','L')";
		}
		else $sql = "UPDATE mall_design SET code='{$code}' WHERE name='{$num}' && mode='L'";

		$mysql->query($sql);
		alert("레벨{$num}의 정보가 수정 되었습니다.","member_level.php");

	break;	

	case 'permi' :
		if($my_level<10) alert("관리자페이지 권한 설정은 관리자만 가능 합니다.","back");
		for($i=1;$i<4;$i++) {
			$code = $_POST['per'.$i.'1']."|".$_POST['per'.$i.'2']."|".$_POST['per'.$i.'3']."|".$_POST['per'.$i.'4']."|".$_POST['per'.$i.'5']."|".$_POST['per'.$i.'6'];
			
			$sql = "SELECT count(*) FROM mall_design WHERE name='{$i}' && mode='P'";
			if($mysql->get_one($sql)==0) {
				$sql = "INSERT INTO mall_design VALUES('','{$i}','{$code}','P')";
			}
			else $sql = "UPDATE mall_design SET code='{$code}' WHERE name='{$i}' && mode='P'";
			$mysql->query($sql);
		}
		alert("관리자페이지 권한 정보가 수정 되었습니다.","member_level.php");
	break;

	case 'etc' :   //수정
		$etc = addslashes($_POST['etc']);
		$mid = $_POST['mid'];
		$sql = "UPDATE pboard_member SET etc='{$etc}' WHERE id = '{$mid}'";
		$mysql->query($sql);
		exit;
	break;

	case "new" :
		############ 사용금지 아이디 불러오기 ####################
		$sql = "SELECT address,info FROM pboard_member WHERE uid=1";
		$data = $mysql->one_row($sql);

		$options	= explode("|",$data['address']);
		$w_word		= explode("|",$data['info']);
		
		$name		= addslashes($_POST['name']);
		$id			= addslashes($_POST['id']);
		$passwd		= addslashes($_POST['passwd']);

		$addr		= addslashes($_POST['addr1']);
		$email		= addslashes($_POST['email']);
		if(!mailCheck($email) && $email) {
			alert("입력하신 $email 은 존재하지 않는 메일주소입니다.\\n다시 한번 확인하여 주시기 바랍니다.",'back');
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

		$level		= isset($_POST['level']) ? addslashes($_POST['level']) : 2;

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
			$sql = "INSERT INTO mall_reserve VALUES ('','{$id}','{$subject}','{$cash[7]}','','','B','{$signdate2}')";
			$mysql->query($sql);
			$reserve = $cash[7];
		} else $reserve = 0;
		############ 적립급 처리 ############
	  
		$sql = "SELECT code FROM mall_design WHERE mode='T'";
		$tmps = $mysql->get_one($sql);
		$tmps = explode("|",$tmps);
		if($tmps[1]=="2") $auth = "N";
		else $auth = "Y";

		if($my_level<=$level) $level=$my_level;

		############ 등록 ####################
		$sql = "INSERT INTO pboard_member (uid, id, name, passwd, jumin1, jumin2, tel, hphone, zipcode, address, email, mail_server, homepage, msn, birth, sex, marr, edu, hobby, job, jobname, info, level, reserve, mailling, add1, add2, add3, add4, add5, carriage1, carriage2, message1, message2, message3, auth, signdate) VALUES ('', '{$id}', '{$name}','".md5($passwd)."','{$jumin1}', '".md5($jumin2)."', '{$tel}', '{$hphone}', '{$zipcode}', '{$addr}', '{$email}', '{$mail_server}', '{$homepage}', '{$mess}', '{$birth}', '{$sex}', '{$marr}', '{$edu}', '{$hobby}', '{$job}', '{$jobname}', '{$minfo}', '{$level}', '{$reserve}', '{$mailling}', '{$add1}', '{$add2}', '{$add3}', '{$add4}', '{$add5}', '{$carriage1}', '{$carriage2}', '{$message1}', '{$message2}', '{$message3}', '{$auth}', '{$signdate}')";
		$mysql->query($sql);

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
		$msg = "회원가입이 정상적으로 처리 되었습니다";		
		$addstring = "";
	break;

	default : alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');
break;

} //End of switch


if($mode == 'log_del') alert($msg,"maillog_list.php?{$addstring}");
else alert($msg,"member_list.php?{$addstring}");

?>

