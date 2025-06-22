<?
@set_time_limit(0);
include "../ad_init.php";
require "{$lib_path}/lib.Shop.php";

############ 파라미터(값) 검사 ####################
$signdate	= time();
$send_type	= $_POST['send_type'];
$uid		= $_POST['uid'];
$m_to		= $_POST['m_to'];
$subject	= $_POST['subject'];
$content	= $_POST['content'];

if((!$send_type && !$m_to) || ((!$subject || !$content) && !$uid)) alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');

if($send_type=='2') {
	$mlevel		= isset($_GET['mlevel']) ? $_GET['mlevel'] : $_POST['mlevel'];
	$mail_ok	= isset($_GET['mail_ok']) ? $_GET['mail_ok'] : $_POST['mail_ok'];
	
	if($mlevel) {
		$addstring .= "&mlevel={$mlevel}";
		$where .= " && level='{$mlevel}'";
	}

	if($mail_ok=='Y') {
		$addstring .= "&mail_ok={$mail_ok}";
		$where .= " && mailling='{$mail_ok}'";
	}
}
else if($send_type=='4') {
	$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
	$word		= isset($_GET['word']) ? urldecode($_GET['word']) : urldecode($_POST['word']);
	$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : $_POST['sdate1'];
	$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : $_POST['sdate2'];
	$dates		= isset($_GET['dates']) ? $_GET['dates'] : $_POST['dates'];
	$auth		= isset($_GET['auth']) ? $_GET['auth'] : $_POST['auth'];
	$sex		= isset($_GET['sex']) ? $_GET['sex'] : $_POST['sex'];
	$level		= isset($_GET['level']) ? $_GET['level'] : $_POST['level'];
	$mailling	= isset($_GET['mailling']) ? $_GET['mailling'] : $_POST['mailling'];

	if($word) {
		$addstring .= "&field={$field}&word={$word}";
		$where .= "&& INSTR({$field},'{$word}')";
	}

	if($sdate1 && $sdate2) {		
		if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
		$addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}&dates={$dates}";	
		if($sdate1==$sdate2) $where .= "&& INSTR({$dates},'{$sdate1}') ";
		else $where .= "&& ({$dates} BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR({$dates},'{$sdate2}'))";		
	}  

	if($auth) {
		$addstring .= "&auth={$auth}";
		$where .= " && auth='{$auth}'";
	}

	if($sex) {
		$addstring .= "&sex={$sex}";
		$where .= " && sex='{$sex}'";
	}

	if($level) {
		$addstring .= "&level={$level}";
		$where .= " && level='{$level}'";
	}

	if($mailling) {
		$addstring .= "&mailling={$mailling}";
		$where .= " && mailling='{$mailling}'";
	}
}

$mlimit = 5;    // 한번에 보낼 메일 수
$mcnt =1;

if($uid) {
	$sql = "SELECT * FROM pboard_maillog WHERE uid='{$uid}'";
	$data = $mysql->one_row($sql);
	$subject = stripslashes($data['subject']);
	$content = stripslashes($data['content']);			
	$data['err_log'] = stripslashes($data['err_log']);
	$mcnt = $data['m_cnt'] + 1;		
}

############ 메일 보내기전 정의 ############
$URL = "http://".$_SERVER["SERVER_NAME"];		
$sql = "SELECT code FROM mall_design WHERE mode = 'F'";
$mail_img = explode("|*|",stripslashes($mysql->get_one($sql)));	
$skin_path = "../../";
if($mail_img[0]) $MAIL_IMG = "<a href='{$URL}' target='_blank'><img src='{$URL}/image/design/{$mail_img[0]}' width='760' border='0' alt='Mail Image' /></a>";	
$MAIL_COMMENT = stripslashes($content);
include "../../php/mail_form.php";   //메일 양식 인클루드
############ 상품발송 메일 보내기전 정의 ############

$error_log = "";

switch($send_type) {
	case "2" : case "3" : case "4" : 
		if($send_type=='2') {
			if($_POST['mail_ok']=='Y') $where .= " && mailling='Y' ";
			$sql = "SELECT count(*) FROM pboard_member WHERE uid > 1 {$where}";
			$mtotal = $mysql->get_one($sql);
			$sql = "SELECT code FROM mall_design WHERE mode='L' && name='{$mlevel}'";
			$level_name = $mysql->get_one($sql);
			$level_name = explode("|",$level_name);
			$m_to = "[{$level_name[0]} 등급] Mailling";
		}
		else if($send_type=='3') {
			$where .= " && mailling='Y' ";
			$sql = "SELECT count(*) FROM pboard_member WHERE uid > 1 && mailling='Y'";
			$mtotal = $mysql->get_one($sql);
			$m_to = "Mailling";
		}
		else {
			$where .= " && mailling='Y' ";
			$sql = "SELECT count(*) FROM pboard_member WHERE uid > 1 {$where}";
			$mtotal = $mysql->get_one($sql);
			$m_to = "Search Mailling";
		}
		$m_total = round($mtotal/$mlimit)+1;
		$mstart = $mlimit * ($mcnt-1);

		$sql = "SELECT email FROM pboard_member WHERE uid > 1 {$where} ORDER BY mail_server asc LIMIT $mstart,$mlimit";
		$mysql->query($sql);
		$cnt1 = $cnt2 = 0;		
		while($row = $mysql->fetch_array()){
			if(pmallMailSend($row['email'], $subject, $mail_form)) $cnt2++;
			$cnt1++;
		}
		
	break;

	default : 
		$m_email = explode(",",$m_to);				
		for($cnt1 = $cnt2 = 0, $cnt = count($m_email);$cnt1<$cnt;$cnt1++) {			
			if(pmallMailSend($m_email[$cnt1], $subject, $mail_form)) $cnt2++;
		}
	break;
}


############ 메일링 로그 기록 ####################
$e_time = time();
$error_log = $data['err_log'].$error_log;
$error_log = addslashes($error_log);

if($send_type!=1){
	if(!$uid) {
		$sql = "INSERT INTO pboard_maillog VALUES ('','{$m_to}','{$subject}','{$content}','{$cnt2}','{$cnt1}','{$m_total}','{$mcnt}','{$addstring}','{$error_log}','{$signdate}','{$e_time}','{$signdate}')";
	}
	else {
		$sql = "UPDATE pboard_maillog SET m_true = m_true + {$cnt2} ,m_false = m_false + {$cnt1}, m_cnt = {$mcnt} ,err_log = '{$error_log}', e_time = '{$e_time}' WHERE uid = '{$data[uid]}'";
	}
	
	$mysql->query($sql);
	$m_go = "";
	if($mtotal < ($mlimit*$mcnt)) alert("메일을 성공적으로 보냈습니다.\\n 메일발송내역을 확인해 보세요",'maillog_list.php');
	else $m_go = "1";
	
	if(!$uid) {
		$sql = "SELECT MAX(uid) FROM pboard_maillog";
		$uid = $mysql->get_one($sql);
	}

	$mcnt++;
	$bgsize = intval((300*($mlimit*$mcnt))/$mtotal);
} else {
	$sql = "INSERT INTO pboard_maillog VALUES ('','{$m_to}','{$subject}','{$content}','{$cnt2}','{$cnt1}','1','1','','{$error_log}','{$signdate}','{$e_time}','{$signdate}')";
	$mysql->query($sql);
	alert("{$cnt1}건의 메일을 성공적으로 보냈습니다.\\n 메일발송내역을 확인해 보세요","maillog_list.php");
}

?>
<HTML>
<HEAD>
<title>회원 메일링...</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<link rel=StyleSheet HREF='../html/style.css' type=text/css title=style>
</HEAD>

<body bgcolor="#ffffff" onload="loading();">

<SCRIPT LANGUAGE=javascript>
<!--
function loading()
{
	document.all.preview.style.display = "none";
	document.all.show.style.display = "";
}
//-->
</SCRIPT>

<div id="preview">
<table width=400 border=0  cellpadding=0 cellspacing=0  align=center>
<tr><td height=150></td></tr>
<tr>
	<td valign=middle>
		<table border=0 cellpadding=0 cellspacing=4 width=100% bgcolor=#EEEEEE>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#999999>
				<tr>
					<td>
						<table border="0" cellpadding="0" cellspacing="1" width="400">
						<tr class="gr_bg"><td align=center height=40 class="eng bold" >MEMBER MAILLING...</td></tr>
						<tr>
							<td>
								<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#FFFFFF>
								<tr><td height=30></td></tr>
								<tr>
									<td align=center bgcolor="#FFFFFF">
									전체 메일건수 : <font class=eng><?=$mtotal?></font>&nbsp;&nbsp;[ 현재 <font class=eng><? echo ($mlimit*$mcnt);?></font>건 전송중... ]
									</td>
								</tr>
								<tr><td height=30></td></tr>
								<tr>
									<td align=center valign=top>
										<table border=0 cellspacing=0 cellpadding=0 width=300 height=8 class=all>
										<tr>
											<td> 
												<table border=0 cellspacing=0 cellpadding=0 width='<?=$bgsize?>' height='8' background='img/grp_bg.gif'>
												<tr><td width=100%></td></tr>
												</table>
											</td>
										</tr>
										</table>
									</td>
								</tr>
								<tr><td height=20></td></tr>
								<tr>
									<td align=center height=30>
										<div id="sBtn1" style="padding-left:170px;">
											<a href="mail_form.html?type=<?=$type?>"><span class="blue">메일링 취소</span></a>
										</div>
									</td>
								</tr>
								<tr><td height=20></td></tr>
								</table>
							</td>
						</tr>		
					   </table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</div>


<div id="show" style="display:none">
	<? if($send_type!='1' && $m_go=='1'){?>
	<form name=mail method=post action="mail_ok.php?<?=$addstring?>">	
	<input type=hidden name=send_type value="<?=$send_type?>">
	<input type=hidden name=uid value="<?=$uid?>">
	</form>
	<script language="JavaScript">
	<!--
	document.mail.submit();
	-->
	</script>
	<? } ?>
</div>

</body>

<?
############ 메모리제거 ####################
@$smtp->smtp_close();
?>