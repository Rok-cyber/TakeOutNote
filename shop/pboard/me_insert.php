<?
ob_start();
$bo_path = ".";
$memo_type='Y';

include "{$bo_path}/init.php";

$pmode		= isset($_GET['pmode'])		? $_GET['pmode']:'';

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER']) && $pmode!='del') Error($LANG_ERR_MSG[0]); 

######################### 변수정의 및 AddString 정의 ################################
$access_ip	= $_SERVER['REMOTE_ADDR'];
$ck_w		= isset($_POST['ck_w'])		? $_POST['ck_w'] : '';
$ck_w2		= isset($_POST['ck_w2'])	? $_POST['ck_w2'] : '';
$Main		= isset($_POST['Main'])	? "../".str_replace("../../","",addslashes($_POST['Main'])) : "../".str_replace("../../","",addslashes($_GET['Main']));

$no			= isset($_GET['no'])		? $_GET['no']:'';
$no2		= isset($_GET['no2'])		? $_GET['no2']:'';
$code		= isset($_GET['code'])		? $_GET['code']:'';
$page		= isset($_GET['page'])		? $_GET['page'] : '';
$spage		= isset($_GET['spage'])		? $_GET['spage'] : '';
$slast_idx	= isset($_GET['slast_idx'])	? $_GET['slast_idx'] : '';
$field		= isset($_GET['field'])		? $_GET['field'] : '';
$word		= isset($_GET['word'])		? $_GET['word'] : '';
$seccate	= isset($_GET['seccate'])	? $_GET['seccate'] : '';

$name		= !empty($my_name) ? $my_name : addslashes($_POST['mename']);

$signdate	= time();
$comment	= addslashes($_POST['mecomment']);
$mepasswd	= $_POST['mepasswd'];
$talk		= addslashes($_POST['talk']);

if($field && $word) $addstring = "&field={$field}&word={$word}";
if($page) $addstring .="&page={$page}";
if($seccate) $addstring .="&seccate={$seccate}";
if($spage && $slast_idx) $addstring .= "&slast_idx={$slast_idx}&spage={$spage}";

if(!$pmode || !$no || !$code || ($pmode=='mdel' && !$no2)) alert($LANG_ERR_MSG[1],'back');


switch($pmode) {
	case "write" :       
		
		$ck_w3 = md5($ck_w.$cook_rand);     //  암호화
		$ck_w4 = base64_decode($ck_w);
		$i=intval($signdate) - intval($ck_w4);
		
		if(!$ck_w || !$ck_w2 || ($ck_w2 !=$ck_w3) || $i<3 || $i>1800) Error($LANG_ERR_MSG[7]); 

		############ 글 등록 쿼리 ####################
        //메모는 no 30000000 이상을 사용한다
		chrtrim($name);					 
		chrtrim($comment);					
					
		$sql = "SELECT max(no) FROM pboard_{$code}_body WHERE no >= 30000000";
		if(!$insert_no = $mysql->get_one($sql)) $insert_no = "30000000";
		else $insert_no++;
                     
		$sql = "INSERT pboard_{$code}_body VALUES ('{$insert_no}','{$no}','1','".md5($mepasswd)."','{$name}','{$comment}','{$signdate}','{$talk}','','','{$my_icon}','{$my_id}','{$access_ip}')";
		$mysql->query($sql);

		if($talk && eregi('talk',$main_data['s_name'])) {  //talk
			if($talk=='Y') $plus_q = ", reco = reco+1 ";
			else if($talk=='N') $plus_q = ", down = down+1 ";
		}

		$sql = "UPDATE pboard_{$code} SET cnt_memo = cnt_memo+1 {$plus_q} WHERE no = {$no}";
		$mysql->query($sql);

		if($code=='cus_board' && $my_level==10) {
			$sql = "UPDATE pboard_{$code} SET down = 1 WHERE no = {$no}";
			$mysql->query($sql);			
		}

	break;
  
	case "del" :   
        ############ 비밀번호를 비교한다 ####################
		$passwd = isset($_POST['passwd']) ? $_POST['passwd']  : $_GET['passwd'] ; 
		$sql = "SELECT passwd,id,s_link FROM pboard_{$code}_body WHERE no={$no2}";
		$row = $mysql->one_row($sql);
		if((!$row['id'] || $row['id'] != $my_id) && $my_level <9) {
			$origin_pass = $row['passwd'];
			$user_pass = md5($passwd);
			if($origin_pass != $user_pass) alert($LANG_CHK_MSG[0],'back'); 
        }
		
		############### 글 삭제 쿼리 ##################
        $sql = "DELETE FROM pboard_{$code}_body WHERE no = {$no2}";
		$mysql->query($sql);
		
		if($row['s_link'] && eregi('talk',$main_data['s_name'])) {	//talk
			if($row['s_link']=='Y') $plus_q = ", reco = reco-1 ";
			else if($row['s_link']=='N') $plus_q = ", down = down-1 ";
		}
		$sql = "UPDATE pboard_{$code} SET cnt_memo = cnt_memo-1 {$plus_q} WHERE no = {$no}";
		$mysql->query($sql);

	break;

	default : 
		alert($LANG_ERR_MSG[2],'back');
	break;
}


####################### 답변메일 보내기 ##################################
if(($code=='counsel' || $code=='affil_counsel') && $pmode=='write'){
	$lib_path = "../lib";	
	$sql = "SELECT name, email FROM pboard_{$code} WHERE no='{$no}'";
	$row = $mysql->one_row($sql);
	
	if($row['email']) {
		include "{$lib_path}/lib.Shop.php";
		$URL = "http://".$_SERVER["SERVER_NAME"];		
		$sql = "SELECT code FROM mall_design WHERE mode = 'F'";
		$mail_img = explode("|*|",stripslashes($mysql->get_one($sql)));	
		$mail_type = "counsel";
		$MAIL_IMG = "<a href='{$URL}' target='_blank'><img src='{$URL}/{$shopPath}image/design/{$mail_img[0]}' width='760' border='0' alt='Mail Image' /></a>";
		$MAIL_COMMENT = nl2br($comment);
		$name = htmlspecialchars(stripslashes($row['name']));
		include "../php/mail_form.php";   //메일 양식 인클루드
		pmallMailSend($row['email'], "1:1문의에 대한 답변 입니다.", $mail_form);		
	}
}
####################### 답변메일 보내기 ##################################

$Main = str_replace("|","&",$Main);
movePage($Main."&pmode=view&no={$no}".$addstring);

include "{$bo_path}/close.php";
?>
