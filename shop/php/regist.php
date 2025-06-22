<?

$jmode = $_POST['jmode'];

############ 가입양식 불러오기 ####################
$mysql = new mysqlClass();
$sql = "SELECT address,info FROM pboard_member WHERE uid=1";
$data = $mysql->one_row($sql);

$options = explode("|",stripslashes($data['address']));
$w_word = explode("|",stripslashes($data['info']));

$sql	= "SELECT code FROM mall_design WHERE mode='W'";
$tmp	= $mysql->get_one($sql);
$ssl	= explode("|*|",$tmp);
$sMain = "./";
if($ssl[0]==1) {
	$tmp    = explode("|",$ssl[2]);	
	if(in_array(4,$tmp)) {
		if($ssl[1]) $sport = ":{$ssl[1]}";
		$sMain = "https://".$_SERVER['HTTP_HOST']."{$sport}/{$ShopPath}";	
		unset($sport);
	}
}

if($my_id) $jmode='modify';
if($jmode=='modify'){
	############ 회원정보 불러오기 ####################
	$sql = "SELECT * FROM pboard_member WHERE id='$my_id' && uid >1";
	if(!$row = $mysql->one_row($sql)) alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');	
	$JUMIN1 = stripslashes($row['jumin1']);
	$ADDR1 = stripslashes($row['address']);
	$EMAIL = stripslashes($row['email']);
	$HOMEPAGE = stripslashes($row['homepage']);
	$MESS = stripslashes($row['msn']);
	$JOBNAME = stripslashes($row['jobname']);
	$MINFO = stripslashes($row['info']);
	$ICON = stripslashes($row['icon']);

	$tmps = explode("@",$EMAIL);
	$EMAIL1 = $tmps[0];
	$EMAIL2 = $tmps[1];

	$tel = explode(" - ",$row['tel']);
	$TEL11 = !empty($tel[0]) ? $tel[0] : '02';
	$TEL12 = $tel[1];
	$TEL13 = $tel[2];
	$phone = explode(" - ",$row['hphone']);
	$PHONE11 = !empty($phone[0]) ? $phone[0] : '010';
	$PHONE12 = $phone[1];
	$PHONE13 = $phone[2];
	$zip = explode(" - ",$row['zipcode']);
	$ZIP11 = $zip[0];
	$ZIP12 = $zip[1];
	$bir = explode("|",$row['birth']);
	$BIR1 = $bir[0];
	$BIR2 = $bir[1];
	$BIR3 = $bir[2];

	$tmps = explode("|",stripslashes($row['carriage1']));
	$NAME2 = $tmps[0];
	$tel = explode(" - ",$tmps[1]);
	$TEL21 = !empty($tel[0]) ? $tel[0] : '02';
	$TEL22 = $tel[1];
	$TEL23 = $tel[2];
	$phone = explode(" - ",$tmps[2]);
	$PHONE21 = !empty($phone[0]) ? $phone[0] : '010';
	$PHONE22 = $phone[1];
	$PHONE23 = $phone[2];
	$zip = explode(" - ",$tmps[3]);
	$ZIP21 = $zip[0];
	$ZIP22 = $zip[1];
	$ADDR2 = $tmps[4];

	$tmps = explode("|",stripslashes($row['carriage2']));
	$NAME3 = $tmps[0];
	$tel = explode(" - ",$tmps[1]);
	$TEL31 = !empty($tel[0]) ? $tel[0] : '02';
	$TEL32 = $tel[1];
	$TEL33 = $tel[2];
	$phone = explode(" - ",$tmps[2]);
	$PHONE31 = !empty($phone[0]) ? $phone[0] : '010';
	$PHONE32 = $phone[1];
	$PHONE33 = $phone[2];
	$zip = explode(" - ",$tmps[3]);
	$ZIP31 = $zip[0];
	$ZIP32 = $zip[1];
	$ADDR3 = $tmps[4];

	$MESSAGE1 = stripslashes($row['message1']);
	$MESSAGE2 = stripslashes($row['message2']);
	$MESSAGE3 = stripslashes($row['message3']);

	if($bir[3] =='음력') $CHECK1 = "checked";
	else $CHECK2 = "checked";
	if($row['sex'] =='M') $CHECK3 = "checked";
	else if($row['sex'] =='F') $CHECK4 = "checked";
	if($row['marr'] =='N') $CHECK5 = "checked";
	else $CHECK6 = "checked";
	if($row['mailling']=='Y') $CHECK7 = "checked";
	else $CHECK8 = "checked";
	if($row['sms']=='Y') $CHECK9 = "checked";
	else $CHECK10 = "checked";
	
} else {
	
	$JTITLE = "회원 가입하기";
	$CHECK1 = "checked";
	$CHECK3 = "checked";
	$CHECK5 = "checked";
	$CHECK7 = "checked";
	$CHECK9 = "checked";
}

if(!$jmode){  

 	############ 템플릿 ####################	
	$tpl = new classTemplate;
	$tpl->define("main",$skin."/regist_01.html");
	$tpl->scan_area("main");
	
	$sql = "SELECT * FROM mall_document WHERE mode='B'";
	$row = $mysql->one_row($sql);

	$USED = stripslashes($row['code']);
	$USED = str_replace("{shopName}",$basic[1],$USED);
	$USED = str_replace("700px",$SKIN_DEFINE['regist_docu']."px",$USED);

	$sql = "SELECT * FROM mall_document WHERE mode='C'";
	$row = $mysql->one_row($sql);

	$PRIV = stripslashes($row['code']);
	$PRIV = str_replace("{shopName}",$basic[1],$PRIV);
	$PRIV = str_replace("{name}",$basic[9],$PRIV);
	$PRIV = str_replace("{email}",$basic[10],$PRIV);
	$PRIV = str_replace("{tel}",$basic[7],$PRIV);
	$PRIV = str_replace("700px",$SKIN_DEFINE['regist_docu']."px",$PRIV);
	$PRIV = str_replace("698px",$SKIN_DEFINE['regist_docu']."px",$PRIV);

	$sql	= "SELECT code FROM mall_design WHERE mode='Y'";
	$tmp	= $mysql->get_one($sql);
	$confirm	= explode("|*|",$tmp);
	
	if($confirm[0]==1) {
		if($confirm[2]==1) {
			$CTITLE = "성인인증";
			$CIMG = "adult";
		}
		else {
			$CTITLE = "실명인증";
			$CIMG = "confirm";
		}

		$tpl->parse("is_confirm1");
		$tpl->parse("is_confirm2");
		$tpl->parse("is_confirm3");
	}
	else $tpl->parse("is_default1"); 


	$tpl->parse("main");
	$tpl->tprint("main");
    $tpl->close();

} 
else {

if($jmode=='new') {
	$sql	= "SELECT code FROM mall_design WHERE mode='Y'";
	$tmp	= $mysql->get_one($sql);
	$confirm	= explode("|*|",$tmp);
	
	if($confirm[0]==1) {
		$rtnName = $_POST['name'];
		$rtnNo = previlDecode($_POST['jumin']);
		
		if(!$rtnName || !$rtnNo) alert("실명인증이 이루어 지지 않았습니다. 다시 시도 하시기 바랍니다.","{$Main}?channel=regist");
		if($rtnName!=$rtnNo) alert("실명인증이 이루어 지지 않았습니다. 다시 시도 하시기 바랍니다.","{$Main}?channel=regist");	
	}
}

$is_arr = Array('','is_jumin','is_tel','is_phone','is_addr','','is_homepage','is_mess','is_bir','is_sex','is_marr','is_edu','is_hobby','is_job','is_jobname','is_info','is_mailling','','','','','is_add1','is_add2','is_add3','is_add4','is_add5','is_sms');
$is_arr2 = Array('','jumin1','tel11','phone11','zip11','','homepage','mess','bir1','','','edu','hobby','job','jobname','info','mailling','','','','','add1','add2','add3','add4','add5','sms');
$is_arr3 = Array('','주민번호앞자리를 입력하세요!','전화번호 앞자리를 입력하세요!','이동전화번호 앞자리를 입력하세요!','우편번호 앞자리를 입력하세요!','','홈페이지를 입력하세요!','메신저를 입력하세요!','생년월일을 입력하세요!','','','최종학력을 선택하세요!','취미를 선택하세요!','직업을 선택하세요!','직장명을 입력하세요!','남기는 말씀을  입력하세요!','','','','','',$w_word[5].'을(를) 입력하세요!',$w_word[6].'을(를) 입력하세요!',$w_word[7].'을(를) 입력하세요!',$w_word[8].'을(를) 입력하세요!',$w_word[9].'을(를) 입력하세요!','');


########### 최종학력 ####################
if($options[11] == '1' or $options[11] =='2'){
   $edu_arr = explode(",",$w_word[0]);
   for($i=0;$i<count($edu_arr);$i++){
	   if($edu_arr[$i] == $row[edu]) $EDU .= "<option value=$edu_arr[$i] selected>$edu_arr[$i]</option>\n";
	   else $EDU .= "<option value=$edu_arr[$i]>$edu_arr[$i]</option>\n";
   }
}

########### 취미 ####################
if($options[12] == '1' or $options[12] =='2'){
   $hob_arr = explode(",",$w_word[1]);
   for($i=0;$i<count($hob_arr);$i++){
	   if($hob_arr[$i] == $row[hobby]) $HOBBY .= "<option value='{$hob_arr[$i]}' selected>{$hob_arr[$i]}</option>\n";
	   else $HOBBY .= "<option value='{$hob_arr[$i]}'>{$hob_arr[$i]}</option>\n";
   }
}

########### 직업 ####################
if($options[13] == '1' or $options[13] =='2'){
   $job_arr = explode(",",$w_word[2]);
   for($i=0;$i<count($job_arr);$i++){
	   if($job_arr[$i] == $row[job]) $JOB .= "<option value='{$job_arr[$i]}' selected>{$job_arr[$i]}</option>\n";
	   else $JOB .= "<option value='{$job_arr[$i]}'>{$job_arr[$i]}</option>\n";
   }  
}


########### 입력양식 검사 스크립트 ####################
?>
<SCRIPT LANGUAGE="JavaScript">
<!--

/***************** 입력 체크 스크립트 *********************/
function checkIt() {   
	if(f.ckBtn.value==1) {
		alert("처리 중 입니다. 버튼은 한번만 누르시기 바랍니다.");
		return false;
	}  	       	
<?

if($jmode=='new') { 
	echo "
		if(ck_value('name','이름을 입력하세요!')==1) return false;
		if(ck_value('id','아이디를 입력하세요!')==1) return false;
		if(ck_value('passwd','비밀번호를 입력하세요!')==1) return false;
		if(ck_value('repasswd','비밀번호 확인을 입력하세요!')==1) return false;
		if(!f.email.value) {
			f.email.value = '';
			f.email1.value = '';
			f.email2.value = '';
			alert('이메일 주소를 입력하세요!');
			f.email1.focus();
			return false;
		}
	";
} 
else {
	echo "
		if(ck_value('passwd','비밀번호 확인을 입력하세요!')==1) return false;
		if(!f.email.value) {
			f.email.value = '';
			f.email1.value = '';
			f.email2.value = '';
			alert('이메일 주소를 입력하세요!');
			f.email1.focus();
			return false;
		}			
	";
}

for($i=1;$i<27;$i++){
	if($i==9 || $i==10 || ($i>15 && $i<21)) continue;

	if($options[$i] == '2')  {
		if($i!=1 || $jmode=='new') echo "if(ck_value('$is_arr2[$i]','$is_arr3[$i]')==1) return false;\n";
		if($i==1 && $jmode=='new') echo "if(ck_value('jumin2','주민번호 뒷자리를 입력하세요!')==1) return false;\n";
		else if($i==2) echo "if(ck_value('tel12','전화번호 가운데자리를 입력하세요!')==1) return false;\n if(ck_value('tel13','전화번호 뒷자리를 입력하세요!')==1) return false;\n";
		else if($i==3) echo "if(ck_value('phone12','이동전화번호 가운데자리를 입력하세요!')==1) return false;\nif(ck_value('phone13','이동전화번호 뒷자리를 입력하세요!')==1) return false;\n";
		else if($i==4) echo "if(ck_value('zip12','우편번호 뒷자리를 입력하세요!')==1) return false;\nif(ck_value('addr1','주소를 입력하세요!')==1) return false;\n";
		else if($i==8) echo "if(ck_value('bir2','생년월일을 입력하세요!')==1) return false;\nif(ck_value('bir3','생년월일을 입력하세요!')==1) return false;\n";
	}
}		
?>
	f.ckBtn.value = 1;
	f.submit();
return false;
}

// -->
</SCRIPT>
<?

############ 템플릿 ####################
$tpl = new classTemplate;
if($my_id) $tpl->define("main","{$skin}/mypage_modify.html");
else $tpl->define("main","{$skin}/regist_02.html");
$tpl->scan_area("main");
$ACTION = "{$sMain}php/regist_ok.php";

if($jmode=='modify') { $tpl->parse("is_mid"); $SECEDER="php/regist_ok.php?jmode=del"; }
else if($jmode=='new') $tpl->parse("is_id"); 
	
for($i=1;$i<27;$i++){
	if($i>16 && $i<21) continue;
	if($options[$i] == '1' || $options[$i] == '2') {
		if($options[$i] == '2') $REQUIRE = "rq";
		else $REQUIRE = "";

		if($i>20) {
			${"TADD".($i-20)} = $w_word[$i-16];			
			${"ADD".($i-20)} = stripslashes($row['add'.($i-20)]);
		}
		
		if($jmode=='modify' && $i=='1') $tpl->parse("is_mjumin");
		else $tpl->parse($is_arr[$i]);		
    }
}
$tpl->parse("is_$jmode");
$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>

<SCRIPT LANGUAGE="JavaScript">
<!--
f = document.joinForm;
// -->
</SCRIPT>

<?
}  // end of if(회원 약관)
?>