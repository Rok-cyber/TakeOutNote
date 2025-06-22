<?
$no		= $_GET['no'];
if(!$no) alert($LANG_ERR_MSG[1],"back");

switch($pmode) {
	case 'del' :
		############ 삭제될글의 정보를 가져온다####################
		$sql = "SELECT name,subject,id FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no=b.no && m.no={$no}";
		$data = $mysql->one_row($sql);
		$NAME = $data['name'];
		$SUBJECT = $data['subject'];
		if($my_level<9) {
		    if($data['id']!=$my_id && $data['id']) alert($LANG_ERR_MSG[2],'close'); 
		}				
		$ACTION = "{$bo_path}/insert.php?code={$code}&amp;pmode=del&amp;no={$no}{$addstring}";
	break;
	case 'mdel' : 
		$no2	= $_GET['no2'];		
		$addstring .="&pmode=view&no={$_GET['no']}";
		$ACTION = "{$bo_path}/me_insert.php?code={$code}&amp;pmode=del&amp;no={$no}&amp;no2={$no2}";		
	break;
	case 'secret' : 
		$ACTION = "{$Main}&amp;pmode=view&amp;no={$no}{$addstring}";
	break;
	case 'confirm' : 
		$ACTION = "{$Main}&amp;pmode=modify&amp;no={$no}{$addstring}";
	break;
	default  :  alert($LANG_ERR_MSG[2],'back');
}

############ 템플릿 ####################
$tpl = new classTemplate;
$tpl->define('main',"{$skin}/delete.html");
$tpl->scan_area('main');
$tpl->parse("is_{$pmode}");
if($pmode=='del') $tpl->parse('is_del2');
if(!$my_id) $tpl->parse('is_man');
$tpl->parse('main');

?>

<!-- ############ 자바스크립트 ####################-->
<script type="text/javascript">
<!--
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, ""); 
}

function checkIt(ck) {      
    form=document.signForm;
<? if(!$my_id) { ?>
    form.passwd.value=form.passwd.value.trim();
    if(!form.passwd.value) {
        alert('<?=$LANG_FORM_MSG[4]?>');
        form.passwd.focus();
        return false;
    }
<? } ?>

    if(ck==2) form.submit();
}
//-->
</script>

<?
$tpl->tprint('main');   //출력
?>
