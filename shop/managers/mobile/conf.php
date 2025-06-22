<?php
include "../html/top_inc.html"; // 상단 HTML 

$dir = "../../image/mobile/";

$sql = "SELECT code FROM mall_mobile WHERE mode='C'";
$tmps = $mysql->get_one($sql);
$basic = explode("|*|",stripslashes($tmps));

${"CKD1".$basic[0]} = "checked";
${"CKD2".$basic[1]} = "checked";

if($basic[2]) {
	$LOGO_IMG = imgSizeCh($dir,$basic[2],'200');
	$size = @GetImageSize($dir.$basic[2]);	
	$LOGO_SIZE = " <span class='eng' style='display:inline-block; padding-left:20px;'>({$size[0]}px * {$size[1]}px)</span>";
}

if($basic[3]) {
	$ICON_IMG = imgSizeCh($dir,$basic[3],'200');
	$size = @GetImageSize($dir.$basic[3]);	
	$ICON_SIZE = " <span class='eng' style='display:inline-block; padding-left:20px;'>({$size[0]}px * {$size[1]}px)</span>";
}
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
			<div class='left'><img src="img/tm_conf.gif" alt="모바일샵기본설정" /></div>
			<div class='left small' style='padding:8px 0 0 10px;'>모바일 페이지 기본설정을 하실 수 있습니다.</div>
		</div>			
	</div>
	<div class="content">
		<div class='content_top'></div>			
		<div style="padding:0 6px 0 6px;" id='mainHeight'>
			<div style='height:10px;overflow:hidden'>&nbsp;</div>	

<table border=0 cellpadding="0" cellspacing="0" width="99%" align=center>
<tr><td>&nbsp;<img src='./img/t_conf.gif'></td></tr>
<tr><td align=center>
	<table border=0 cellpadding=4 cellspacing=0 bgcolor=#EEEEEE width="100%">
	<tr>
		<td>
			<table border=0 cellpadding=0 cellspacing=0 bgcolor=#999999 width="100%">
			<tr>
				<td>
					<table border=0 width="100%" cellpadding=0 cellspacing=1>
					<FORM name='basicForm' method='post' enctype='multipart/form-data' action='mobile_post.php?mode=conf'>
					<tr height=30 align=left> 
						<td class=gr_bg width=140><font class="small bTitle">&nbsp;&nbsp;*&nbsp;모바일샵 주소</font></td>
						<td bgcolor="#EEEEEE" style="padding-left:6px" class="eng">
							http://<?=$_SERVER["HTTP_HOST"]."/".$ShopPath?>m
						</td>
					</tr>	
					<tr height=30 align=left> 
						<td class=gr_bg width=140><font class="small bTitle">&nbsp;&nbsp;*&nbsp;모바일샵 사용여부</font></td>
						<td bgcolor="#EEEEEE" style="padding-left:6px">
							<input type='radio' name='use' id='use1' value='1' <?=$CKD11?>>&nbsp;<label for='use1'>사용함</label>&nbsp;
							<input type='radio' name='use' id='use2' value='0' <?=$CKD10?>>&nbsp;<label for='use2'>사용안함</label>
						</td>
					</tr>	
					<tr height=30 align=left> 
						<td class=gr_bg width=140><font class="small bTitle">&nbsp;&nbsp;*&nbsp;모바일샵 자동연결</font></td>
						<td bgcolor="#EEEEEE" style="padding-left:6px">
							<input type='radio' name='pc_use' id='pc_use1' value='1' <?=$CKD21?>>&nbsp;<label for='pc_use1'>사용함</label>&nbsp;
							<input type='radio' name='pc_use' id='pc_use2' value='0' <?=$CKD20?>>&nbsp;<label for='pc_use2'>사용안함</label>
							&nbsp;&nbsp;<font class=small>모바일 기기에서 PC버전으로 접속시 자동으로 모바일샵으로 연결 됩니다.</font>
						</td>
					</tr>
					<tr height=30 align=left> 
						<td class=gr_bg><font class="small bTitle">&nbsp;&nbsp;*&nbsp;로고</font></td>
						<td bgcolor="#EFEFEF">&nbsp;<input type='file' class='ta' name='img1' size='50'>&nbsp;&nbsp;<font class=small>최적 사이즈 100px * 30px</font></td>
					</tr>
<?php if($LOGO_IMG) { ?>
					<tr>
						<td colspan=2 align=middle bgcolor='ffffff' style="padding:6px;"><?=$LOGO_IMG?> <?=$LOGO_SIZE?></td>
					</tr>
<?php } ?>
					<tr height=30 align=left> 
						<td class=gr_bg><font class="small bTitle">&nbsp;&nbsp;*&nbsp;아이콘</font></td>
						<td bgcolor="#EFEFEF">&nbsp;<input type='file' class='ta' name='img2' size='50'>&nbsp;&nbsp;<font class=small>최적 사이즈 72px * 72px</font></td>
					</tr>
<?php if($ICON_IMG) { ?>
					<tr>
						<td colspan=2 align=middle bgcolor='ffffff' style="padding:6px;"><?=$ICON_IMG?> <?=$ICON_SIZE?></td>
					</tr>
<?php } ?>					
					<tr height=50 align=left>
					    <td colspan=2 bgcolor='#FFFFFF' style='padding-left:6px;'>
						    <div style="float:left;" class='small'>
								! 모바일샵 사용안함일 경우 모바일샵으로 접속하시면 PC버전으로 자동으로 이동 됩니다.<br />
								! 모바일샵 자동연결은 모바일샵이 사용함으로 설정된 경우에만 적용이 됩니다.
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