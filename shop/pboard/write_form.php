<?
if($code=='counsel' || $code=='sales' || $code=='cooperation') {
	##################### 게시판 자동등록 방지(글쓰기폼연결용) ###########################
	$ck_w	= base64_encode(time());	// 현재 시간 정보를 생성 ...  글쓰기 버튼에서 받아옴
	$ck_w2	= md5($ck_w.$cook_rand);    // 암호화
}
else {
	$ck_w = $_GET['ck_w'];
	$ck_w2 = $_GET['ck_w2'];
}

if($pmode == 'modify' || $pmode == 'reply'){
	$no		= $_GET['no'];
	if(!$no) alert($LANG_ERR_MSG[1],"back");

	$sql = "SELECT m.depth as depth,m.idx as notice, m.name as name,m.email as email, m.cate as cate,m.secret as secret, m.file as ck_file, b.homepage as homepage,m.subject as subject, b.comment as comment, b.html_type as html_type,b.remail as remail, b.file as sfile, b.m_link as mlink, b.s_link as slink, b.passwd as passwd, b.id as id, m.reco as reco, m.down as down, m.signdate as signdate FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no={$no} && m.no=b.no && (b.memo=0 || b.memo='0')";
	$SQL_TIME[] = microtime();
	$data = $mysql->one_row($sql);
	$SQL_TIME[] = microtime();
    $is_no="&amp;no={$no}";
	if($pmode=='modify'){
        
		$passwd = isset($_POST['passwd']) ? $_POST['passwd'] : '';     
		############ 비밀번호를 비교한다 ####################
		if((!$data['id'] || $data['id'] != $my_id) && $my_level <9) {
			if(!$passwd) movePage($Main.$addstring."&amp;pmode=confirm&amp;no={$no}");
			$origin_pass = $data['passwd'];
			$user_pass = md5($passwd);
			if($origin_pass != $user_pass) alert($LANG_CHK_MSG[0],'back'); 
		}

		if($data['secret']==1 && $data['depth']>0) {
			if($my_level<9) alert($LANG_ACC_MSG[4],'back');
		}
	 
		$NAME = stripslashes($data['name']);
		$EMAIL = stripslashes($data['email']);
		$HOMEPAGE = stripslashes($data['homepage']);
		$SUBJECT = stripslashes($data['subject']);
  		$COMMENT = stripslashes($data['comment']);
		$CATE = $data[cate];
		$sfile = explode("||",stripslashes($data['sfile']));
		$mlink = explode("||",stripslashes($data['mlink']));
		$slink = explode("||",stripslashes($data['slink']));

		$ORI_MLINK = stripslashes($data['mlink']);
		$ORI_SLINK = stripslashes($data['slink']);

        if($data['html_type'] ==1) $OP_CK1 = 'checked';
		if($data['notice']==0) $OP_CK2 .= 'checked';
		if($data['secret']==1) $OP_CK3 = 'checked';
		if($data['remail']==1) $OP_CK4 = 'checked';	 
		$RECO = $data['reco'];
		if($options['19']=='Y') $_COOKIE['tmp_dir'] = previlEncode($data['sfile']);

		$tmps = explode("|",$data['reco']);
		if($tmps[0]==1) $OP_11 = "checked";
		if($tmps[1]==1) $OP_21 = "checked";
		if($tmps[2]==1) $OP_31 = "checked";

	} 
	else {
		if($data[secret]==1) $OP_CK3 = 'checked';
		$SUBJECT = preg_replace("/\[re\]/i", "", $data['subject']);
		$SUBJECT = "[re]" . $SUBJECT;
		$data['comment'] = stripslashes($data['comment']);
		$COMMENT = ">" . $data['comment'];
		$COMMENT = str_replace("\n", "\n>", $COMMENT);
		$COMMENT = $data['name'] .$LANG_ETC_MSG[0]."\n\n" . $COMMENT;
		$NAME = $my_name;
	}

	$SUBJECT = str_replace("\"","&#034;",$SUBJECT);
	$SUBJECT = str_replace("'","&#039;",$SUBJECT);
} 

if($options['19']=='Y') {	
	################ 이전 임시폴더 삭제 ################
	if(is_dir($bo_path."/data/{$code}/".date("Ym"))) {
		$handle = opendir("{$bo_path}/data/{$code}/".date("Ym"));
		while ($tmps = readdir($handle)) {	
			if(!eregi("\.",$tmps)) {
				if(substr($tmps,0,8) < date("Ymd",time()-(3600*24))) {
					delTree("{$bo_path}/data/{$code}/".date("Ym")."/".$tmps); 
				}
			}
		}
		closedir($handle);
	}

	################ 생성된 폴더 확인 및 생성 ################
	if(!is_dir($bo_path."/data/{$code}/".date("Ym"))) mkdir($bo_path."/data/{$code}/".date("Ym"),0707);	
	
	if($pmode=='modify' && is_dir("{$bo_path}/data/{$code}/".date("Ym",$data['signdate'])."/article_{$no}")) {
		$tmp_dir = 	"data/{$code}/".date("Ym",$data['signdate'])."/article_{$no}";
		$tmp_dir2 = previlEncode($tmp_dir);		 
		$ckk = 1;
	}
	
	if($ckk!=1) {
		if($_COOKIE['tmp_dir']) {		
			$tmp_dir2 = $_COOKIE['tmp_dir'];		
			$tmp_dir = previlDecode($tmp_dir2);		
			
			if(eregi($code,$tmp_dir)) {
				if(!is_dir($bo_path."/".$tmp_dir)) mkdir($bo_path."/".$tmp_dir,0707);	
			}
			else {
				$tmp_dir = "data/{$code}/".date("Ym")."/".date("Ymd_his").getCode(4);	
				if(!is_dir($bo_path."/".$tmp_dir)) mkdir($bo_path."/".$tmp_dir,0707);	
				$tmp_dir2 = previlEncode($tmp_dir);
				SetCookie("tmp_dir",$tmp_dir2,0,"/");		
			}
		} 
		else {		
			$tmp_dir = "data/{$code}/".date("Ym")."/".date("Ymd_his").getCode(4);	
			if(!is_dir($bo_path."/".$tmp_dir)) mkdir($bo_path."/".$tmp_dir,0707);	
			$tmp_dir2 = previlEncode($tmp_dir);
			SetCookie("tmp_dir",$tmp_dir2,0,"/");	
		}
	}
	
	$up_dir = previlEncode("../../pboard/".$tmp_dir."/");
	if($admin_board == 'Y') $we_width = "720";
	else $we_width = !empty($main_data['img_limit']) ? $main_data['img_limit'] : 700;
}

$tpl = new classTemplate;
$tpl->define('main',"{$skin}/write.html");
$tpl->scan_area('main');    

###################### 옵션 추가 ######################
$tpl->parse('is_'.$pmode);
$tpl->parse('is_'.$pmode."2");
$ACTION = "{$bo_path}/insert.php?code={$code}&amp;pmode={$pmode}{$is_no}{$addstring}";
if($options[2] =='Y') $tpl->parse('is_html');
if($options[3] =='Y') {
	if(($acc_level[10] == '!=' && ($acc_level[4]==$my_level || $my_level>8)) || ($acc_level[10] == '<' && $acc_level[4]<=$my_level)) { $tpl->parse('is_notice'); }
}

if($options[6]=='A') $tpl->parse('is_serect2');
else if($options[6]=='Y') $tpl->parse('is_serect');

if($options[13] =='Y') $tpl->parse('is_remail');

if(($options[2] =='Y' && $options[19] !='Y') || ($options[3] =='Y' && (($acc_level[10] == '!=' && ($acc_level[4]==$my_level || $my_level>8)) || ($acc_level[10] == '<' && $acc_level[4]<=$my_level))) || $options[6] =='Y' || $options[13] =='Y'){
	$tpl->parse("is_option");
}

if(!$my_name) { 
	$tpl->parse('is_info1'); 
	if($pmode=='modify') {
		$IMPASS = previlEncode($passwd);
		$tpl->parse("is_info3");
    } 
	else { 
	    if($data[secret]!=1) $tpl->parse('is_info2'); 
	}
}

###################### 카데고리 ######################
if($options[1] =='Y'){
	if(!$data['cate']) $data['cate'] = $seccate;
	$OPTION = "<option value=''>선택</option>\n";
	for($i=1;$i<=$category[0];$i++) {
	  if($data['cate'] ==$i) $OPTION .="<option value='{$i}' selected>{$category[$i]}</option>";
	  else $OPTION .="<option value='{$i}'>{$category[$i]}</option>";
	}
	$tpl->parse('is_cate');
}
else if($seccate || $CATE) { 
	$CATE = $seccate;
	$tpl->parse('is_hcate'); 
}

if($options[4] =='Y') {       //자료실 파일 업로드    
	for($i=1;$i<=$options[10];$i++){		    
		$FNUM=$i;
		$tpl->parse('is_pds');		
    }
}

if($data['sfile'] && $options[4] =='Y') {
    $SAVEDFILE="";
	for($i=0;$i<count($sfile);$i++){
		$SAVEDFILE .= $sfile[$i]." <input type='checkbox' name='delfile[]' value='{$sfile[$i]}' class='input_check' style='border:0px;' /> ";
    }
 	$tpl->parse('is_file');
}

if($options[7]=='Y') {   //동영상 링크
	$MLINK	= $mlink[0];
	$WIDTH	= $mlink[1];
	$HEIGHT	= $mlink[2];
	$tpl->parse('is_mlink');
}

if($options[8] =='Y') {       //관련사이트 링크
   for($i=1;$i<=$options[11];$i++){	
	 $SLINK = $slink[$i-1];
	 ${"SLINK".$i} = $SLINK;
	 $tpl->parse("is_slink");
   }
}

if($options['19']=='Y') {
	$tpl->parse("is_editer1");
	$tpl->parse("is_editer2");
	$tpl->parse("is_editer3");
}
else $tpl->parse("is_default");

if($code=='affil_counsel') $tpl->parse("is_affil");

$tpl->parse('main');

?>

<script type="text/javascript">
<!--

String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, ""); 
}

function checkIt(a) { 
  
    form=document.signForm;
  
    if(form.ck_bt.value=='1')  { alert('<?=$LANG_FORM_MSG[9]?>'); return false; }

    function ck_value(name,msg,ck) {
        var ch=0;
	    eval("if(!form."+name+".value) { ch = 1 }")
        if(ch==1) { 	        
			alert(msg);
            if(ck) comment.focus();
			else eval("form."+name+".focus()")
		    return 1;
	    } 
    } 
<?

if($code=='sales') {	
	$LANG_FORM_MSG[3] = "기타문의내용을 입력하세요";
}
	
if(!$my_name) { 
	echo "
		form.name.value = form.name.value.trim();\n
        if(ck_value('name','{$LANG_FORM_MSG[0]}')==1) return false;\n
	";
}

if($code=='sales' || $code=='cooperation' || $code=='counsel') {
	echo "
		if(typeof(form.email)!='undefined') {\n
			form.email.value = form.email.value.trim();\n
			if(ck_value('email','이메일을 입력 하세요.')==1) return false;\n
		}\n
	";
}

echo "
	form.subject.value = form.subject.value.trim();\n
	if(ck_value('subject','{$LANG_FORM_MSG[1]}')==1) return false;\n
";

if($options[1]=='Y') {
	echo "
		if(ck_value('cate','{$LANG_FORM_MSG[2]}')==1) return false;\n
	";
}

if($options[19]=='Y') {
	echo "
		form.comment.value = comment.getHtml(); //대체한 textarea에 작성한HTML값 전달	
		form.comment.value = form.comment.value.trim();	
		if(ck_value('comment','{$LANG_FORM_MSG[3]}','Y')==1) return false;\n
	";
} 
else {
	echo "
		form.comment.value = form.comment.value.trim();	
		if(ck_value('comment','{$LANG_FORM_MSG[3]}')==1) return false;\n
	";
}

if(!$my_name  && !($data[secret] =='1' || $pmode=='modify')) { 
	echo "
		if(ck_value('passwd','{$LANG_FORM_MSG[4]}')==1) return false;\n 
	";
} 
  
##################### 게시판 자동등록 방지(글쓰기폼용) #####################
$ck_w3 = md5($ck_w.$cook_rand);	//  암호화
if($ck_w2==$ck_w3) { 
	echo "
		form.ck_w.value		= '{$ck_w}';\n
		form.ck_w2.value	= '{$ck_w2}';\n
	";
}

?>
    
	return checkIt2();	
}

//-->
</script>

<? $tpl->tprint("main"); ?>