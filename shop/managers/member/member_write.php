<?
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

require "{$lib_path}/class.Template.php";

$code = "pboard_member";
$skin = ".";
$field	= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word	= isset($_GET['word']) ? $_GET['word'] : $_POST['word'];
$page	= isset($_GET['page']) ? $_GET['page']:1;
$uid	= $_GET['uid'];

##################### addstring ############################ 
if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($page) $addstring .="&page={$page}";

$ACTION = "./member_ok.php?mode=new{$addstring}";
$LIST = "member_list.php?{$addstring}";

$sql = "SELECT address,info FROM pboard_member WHERE uid=1";
$data = $mysql->one_row($sql);

$options = explode("|",stripslashes($data['address']));
$w_word = explode("|",stripslashes($data['info']));

$is_arr = Array('','is_jumin','is_tel','is_phone','is_addr','','is_homepage','is_mess','is_bir','is_sex','is_marr','is_edu','is_hobby','is_job','is_jobname','is_info','is_mailling','','','','','is_add1','is_add2','is_add3','is_add4','is_add5','is_sms');
$is_arr2 = Array('','jumin1','tel11','phone11','zip11','','homepage','mess','bir1','','','edu','hobby','job','jobname','info','mailling','','','','','add1','add2','add3','add4','add5');
$is_arr3 = Array('','주민번호앞자리를 입력하세요!','전화번호 앞자리를 입력하세요!','이동전화번호 앞자리를 입력하세요!','우편번호 앞자리를 입력하세요!','','홈페이지를 입력하세요!','메신저를 입력하세요!','생년월일을 입력하세요!','','','최종학력을 선택하세요!','취미를 선택하세요!','직업을 선택하세요!','직장명을 입력하세요!','남기는 말씀을  입력하세요!','','','','','',$w_word[5].'을(를) 입력하세요!',$w_word[6].'을(를) 입력하세요!',$w_word[7].'을(를) 입력하세요!',$w_word[8].'을(를) 입력하세요!',$w_word[9].'을(를) 입력하세요!');


$tpl = new classTemplate;
$tpl->define("main","member_write.html");
$tpl->scan_area("main");

for($i=1;$i<27;$i++){
	if($i>16 && $i<21) continue;
	if($options[$i] == '1' || $options[$i] == '2') {
		if($options[$i] == '2') $REQUIRE = "rq";
		else $REQUIRE = "";

		if($i>20) {
			${"TADD".($i-20)} = $w_word[$i-16];			
			${"ADD".($i-20)} = stripslashes($row['add'.($i-20)]);
		}
		
		$tpl->parse($is_arr[$i]);		
    }
}

$sql = "SELECT name, code FROM mall_design WHERE mode='L' && name!='10' ORDER BY name ASC";
$mysql->query($sql);

for($i=2;$i<9;$i++) {
	$row = $mysql->fetch_array();
	while($row['name']!=$i) {
		$LEVEL .= "<option value='{$i}'>LV{$i}</option>";
		if($i==8) break;
		$i++;
	}
	if($row['name']==$i) {
		$tmps = explode("|",$row['code']);
		$LEVEL .= "<option value='{$i}'>".stripslashes($tmps[0])."</option>";		
	}
}

$tpl->parse("main");
$tpl->tprint("main");
?>
<SCRIPT LANGUAGE="JavaScript">
<!--

/***************** 입력 체크 스크립트 *********************/
function checkIt() {      	       	
<?

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
	}\n		
";

for($i=1;$i<26;$i++){
	if($i==9 || $i==10 || ($i>15 && $i<21)) continue;

	if($options[$i] == '2')  {
		echo "if(ck_value('$is_arr2[$i]','$is_arr3[$i]')==1) return false;\n";
		if($i==1) echo "if(ck_value('jumin2','주민번호 뒷자리를 입력하세요!')==1) return false;\n";
		else if($i==2) echo "if(ck_value('tel12','전화번호 가운데자리를 입력하세요!')==1) return false;\n if(ck_value('tel13','전화번호 뒷자리를 입력하세요!')==1) return false;\n";
		else if($i==3) echo "if(ck_value('phone12','이동전화번호 가운데자리를 입력하세요!')==1) return false;\nif(ck_value('phone13','이동전화번호 뒷자리를 입력하세요!')==1) return false;\n";
		else if($i==4) echo "if(ck_value('zip12','우편번호 뒷자리를 입력하세요!')==1) return false;\nif(ck_value('addr1','주소를 입력하세요!')==1) return false;\n";
		else if($i==8) echo "if(ck_value('bir2','생년월일을 입력하세요!')==1) return false;\nif(ck_value('bir3','생년월일을 입력하세요!')==1) return false;\n";
	}
}		
?>
	f.submit();
return false;
}

// -->
</SCRIPT>
<?
include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/  
?>