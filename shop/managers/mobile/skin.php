<?
include "../html/top_inc.html"; // 상단 HTML 

/******************** 스킨 설정 *************************/
$sql = "SELECT code FROM mall_mobile WHERE mode = 'S'";
$tmp_skin = $mysql->get_one($sql);
if(!$tmp_skin) $tmp_skin = "default";
include "../../m/skin/{$tmp_skin}/skin_define.php";

?>

<SCRIPT language="JavaScript">
<!--

//-->
</SCRIPT>

<div class="mainBox2"  id="mainContent">
	<div class="top">
		<div style="padding:9px 0 0 12px;">
			<div class='left'><img src="img/tm_skin.gif" alt="스킨선택" /></div>
			<div class='left small' style='padding:8px 0 0 10px;'>모바일샵 스킨을 선택 하실 수 있습니다. 스킨에 따라 모바일샵의 디자인이 전체적으로 변경됩니다.</div>
		</div>			
	</div>
	<div class="content">
		<div class='content_top'></div>			
		<div style="padding:0 6px 0 6px;" id='mainHeight'>
			<div style='height:10px;overflow:hidden'>&nbsp;</div>	

<table border=0 cellpadding="0" cellspacing="0" width="99%" align=center>
<!---------------------------- 스킨 관리 ----------------------------------->
<TR><TD>&nbsp;<img src='./img/ttl_skin01.gif'></TD></TR>
<TR><TD align=center>
	<TABLE border=0 cellpadding=4 cellspacing=0 bgcolor=#EEEEEE width="100%">
	<TR>
		<TD>
			<TABLE border=0 cellpadding=0 cellspacing=0 bgcolor=#999999 width="100%">
			<TR>
				<TD>
					<TABLE border=0 width="100%" cellpadding=0 cellspacing=1>
					<TR height=30 align=left> 
						<td class=gr_bg width="120"><font class="small bTitle">&nbsp;&nbsp;*&nbsp;스킨명</font></td>
						<td bgcolor="#FFFFFF" colspan=3 >&nbsp;<?=$SKIN_DEFINE['skin_name']?> (<?=$tmp_skin?>)</td>
					</tr>
					</FORM>
					</TABLE>		
				</TD>
			</TR>
			</TABLE>
		</TD>
	</TR>
	</TABLE>
</TD></TR>
<TR><TD height=20></TD></TR>
<TR><TD>&nbsp;<img src='./img/ttl_skin02.gif'></TD></TR>
<TR><TD align=center>
	<TABLE border=0 cellpadding=4 cellspacing=0 bgcolor=#EEEEEE width="100%">
	<TR>
		<TD>
			<TABLE border=0 cellpadding=0 cellspacing=0 bgcolor=#999999 width="100%">
			<TR>
				<TD>
					<TABLE border=0 width="100%" cellpadding=0 cellspacing=1>
					<FORM name='skinForm' method='post' action='mobile_post.php?mode=skin' onsubmit='return false;'>
					<input type=hidden name=b_mode>
					<input type=hidden name=b_num>
					<TR height=30>
						<TD width=35% class=gr_bg align=center><font class="small bTitle">*&nbsp;스킨 선택</font></TD>
						<TD width=65% class=gr_bg align=center><font class="small bTitle">*&nbsp;스킨 정보</font></TD>
					</TR>
					<TR>
						<TD bgcolor=#EFEFEF align=center>
						<SELECT name=skin size=12 style='width:100%;background:#FFFFFF;' onchange="cgSkin(this.value)">
							<?
/*** skin 디렉토리에서 디렉토리를 구함 ***/
$s_path = "../../m/skin";
$SKIN = $NAME = $DESC = $IMG = '';

$handle=opendir($s_path);
while ($skin_info = readdir($handle)) {
	if(!eregi("\.",$skin_info)) {
		if($skin_info==$tmp_skin) {
			$SKIN = $skin_info;
			include "{$s_path}/{$SKIN}/skin_define.php";
			$NAME = $SKIN_DEFINE['skin_name'];
			$DESC = $SKIN_DEFINE['skin_desc'];
			$IMG = "{$s_path}/{$SKIN}/img/skin_thum.gif";
			$select="selected"; 
		}
		else $select="";
		
		echo "<option value='{$skin_info}' {$select}>{$skin_info}&nbsp;&nbsp;</option>";
	}
}
closedir($handle);
?>
						</SELECT>
						</TD>
						<TD height=30 bgcolor='#EFEFEF'>
							<div style="width:202px;padding:6px;float:left">
								<img src="<?=$IMG?>" border="0" width=100 height=156 id="simg" />
							</div>

							<div style="width:258px;float:left;margin:10px 0 0 10px;text-align:left;" class="small">
								<div class="bold">스킨폴더</div>
								<div style="margin-top:8px;" id="spath">/skin/<?=$SKIN?></div>

								<div style="margin-top:16px;" class="bold">스킨명</div>
								<div style="margin-top:8px;" id="sname"><?=$NAME?></div>

								<div style="margin-top:16px;" class="bold">스킨 간략설명</div>
								<div style="margin-top:8px;" id="sdesc"><?=$DESC?></div>
							</div>
						</TD>					
					</TR>		
					<tr height=30 align=left>
					    <td colspan=2 bgcolor='#FFFFFF' style='padding-left:6px;'>
						    <div style="float:left;padding-top:6px;" class='small'>								
							</div>
							<div id="sBtn1" style="float:right;padding-right:6px;">
								<a href="#" onclick="return ckForm();"><span class="blue">선택스킨적용</span></a>
							</div>		
						</td>
					</tr>
					</FORM>
					</TABLE>		
				</TD>
			</TR>
			</TABLE>
		</TD>
	</TR>
	</TABLE>
</TD></TR>
<TR><TD height=10></TD></TR>
<TR>	
	<TD class=small>
		&nbsp;&nbsp;&nbsp;* 스킨은 기본스킨(무료)과 유료 스킨이 있으며 계속 추가 될 예정 입니다.<br />
		&nbsp;&nbsp;&nbsp;* 다운(구매)받은 스킨을 해당폴더(/skin)에 올리시고 스킨관리에서 선택하셔서 사용 하시면 됩니다.<br />		
	</TD>
</TR>

<!---------------------------- @스킨 관리 끝 ----------------------------------->
<TR><TD height=20></TD></TR>
</TABLE>

			</div>
		</div>
		<div class="bottom"></div>
		</span>		
	</div>
</div>


<SCRIPT LANGUAGE="JavaScript">
<!--
	function cgSkin(vls){
		if(!vls) return false;		
		document.HFrm.location.href = "cg_skin.php?skins="+vls;
	}

	function ckForm(){
		f = document.skinForm;
		if(!f.skin.value) {
			alert("스킨을 선택하기시 바랍니다.");
			return false;
		}
		f.submit();
	}
//-->
</SCRIPT>

<iframe name="HFrm" style="display:none;"></iframe>

<? include "../html/bottom_inc.html"; // 하단 HTML?>