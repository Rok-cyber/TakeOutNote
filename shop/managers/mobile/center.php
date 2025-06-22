<?php
include "../html/top_inc.html"; // 상단 HTML 

$dir = "../../image/mobile/";

$sql = "SELECT code FROM mall_mobile WHERE mode='T'";
$tmps = $mysql->get_one($sql);
$basic = explode("|*|",stripslashes($tmps));

$tel = $basic[0];
$fax = $basic[1];
$email = $basic[2];
$time1 = $basic[3];
$time2 = $basic[4];
$time3 = $basic[5];
$time4 = $basic[6];

?>

<SCRIPT LANGUAGE="JavaScript">
<!--

function ckForm(){
	f = document.basicForm;
	f.submit();
}
//-->
</SCRIPT>

<div class="mainBox2"  id="mainContent">
	<div class="top">
		<div style="padding:9px 0 0 12px;">
			<div class='left'><img src="img/tm_center.gif" alt="고객센터 정보 설정" /></div>
			<div class='left small' style='padding:8px 0 0 10px;'>모바일샵 고객센터 페이지에 출력되는 정보를 설정 하실 수 있습니다.</div>
		</div>			
	</div>
	<div class="content">
		<div class='content_top'></div>			
		<div style="padding:0 6px 0 6px;" id='mainHeight'>
			<div style='height:10px;overflow:hidden'>&nbsp;</div>	

<table border=0 cellpadding="0" cellspacing="0" width="99%" align=center>
<tr><td>&nbsp;<img src='./img/t_center.gif'></td></tr>
<tr><td align=center>
	<table border=0 cellpadding=4 cellspacing=0 bgcolor=#EEEEEE width="100%">
	<tr>
		<td>
			<table border=0 cellpadding=0 cellspacing=0 bgcolor=#999999 width="100%">
			<tr>
				<td>
					<table border=0 width="100%" cellpadding=0 cellspacing=1>
					<FORM name='basicForm' method='post' enctype='multipart/form-data' action='mobile_post.php?mode=center'>
					<tr height=30 align="left"> 
						<td class=gr_bg width="120"><font class="small bTitle">&nbsp;&nbsp;*&nbsp;전화번호</font></td>
						<td bgcolor="#EFEFEF" colspan=3>&nbsp;<input type=text class=ta name=tel value='<?=$tel?>' size=30></td>
					</tr>
					<tr height=30 align="left"> 
						<td class=gr_bg width="120"><font class="small bTitle">&nbsp;&nbsp;*&nbsp;팩스번호</font></td>
						<td bgcolor="#EFEFEF" colspan=3>&nbsp;<input type=text class=ta name=fax value='<?=$fax?>' size=30></td>
					</tr>
					<tr height=30 align="left"> 
						<td class=gr_bg width="120"><font class="small bTitle">&nbsp;&nbsp;*&nbsp;이메일</font></td>
						<td bgcolor="#EFEFEF" colspan=3>&nbsp;<input type=text class=ta name=email value='<?=$email?>' size=50></td>
					</tr>
					<tr height=98 align="left"> 
						<td class=gr_bg width="120"><font class="small bTitle">&nbsp;&nbsp;*&nbsp;상담시간</font></td>
						<td bgcolor="#EFEFEF" colspan=3>
							<div>
								<span style="display:inline-block; width:80px;">&nbsp;평일&nbsp;&nbsp;</span> : 
								<input type=text class=ta name=time1 value='<?=$time1?>' size=50>&nbsp;&nbsp;<font class="small">미등록시 : 09:00 ~ 18:00</font>
							</div>
							<div style="padding-top:4px;">
								<span style="display:inline-block; width:80px;">&nbsp;토요일</span> : 
								<input type=text class=ta name=time2 value='<?=$time2?>' size=50>&nbsp;&nbsp;<font class="small">미등록시 : 09:00 ~ 13:00</font>
							</div>
							<div style="padding-top:4px;">
								<span style="display:inline-block; width:80px;">&nbsp;휴일</span> : 
								<input type=text class=ta name=time3 value='<?=$time3?>' size=50>&nbsp;&nbsp;<font class="small">미등록시 : 휴무</font>
							</div>
							<div style="padding-top:4px;">
								<span style="display:inline-block; width:80px;">&nbsp;점심시간</span> : 
								<input type=text class=ta name=time4 value='<?=$time4?>' size=50>&nbsp;&nbsp;<font class="small">미등록시 : 12:00 ~ 13:00</font>
							</div>
						</td>
					</tr>		
					<tr height=30 align=left>
					    <td colspan=2 bgcolor='#FFFFFF' style='padding-left:6px;'>
						    <div style="float:left;" class='small'>
								! 전화번호, 팩스번호, 이메일 주소는 미등록시 쇼핑몰환경설정에 등록된 정보가 출력 됩니다.
							</div>
							<div id="sBtn1" style="float:right;padding-right:6px;">
								<a href="#" onclick="return ckForm();return false;"><span class="blue">설정저장</span></a>
							</div>		
						</td>
					</tr>
					</FORM>
					</table>		
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
</td></tr>
<tr><td height=20></td></tr>
</table>
			</div>
		</div>
		<div class="bottom"></div>
		</span>		
	</div>
</div>

<?php include "../html/bottom_inc.html"; // 하단 HTML?>