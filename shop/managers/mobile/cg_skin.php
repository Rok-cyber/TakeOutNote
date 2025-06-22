<?
ob_start();
include "../ad_init.php";

$skins = $_GET['skins'];
if(!$skins) exit;
$s_path = "../../skin";

include "{$s_path}/{$skins}/skin_define.php";
$NAME = $SKIN_DEFINE['skin_name'];
$DESC = $SKIN_DEFINE['skin_desc'];
$IMG = "{$s_path}/{$skins}/img/skin_thum.gif";
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	parent.document.getElementById('spath').innerHTML = "/skin/<?=$skins?>";
	parent.document.getElementById('sname').innerHTML = "<?=$NAME?>";
	parent.document.getElementById('sdesc').innerHTML = "<?=$DESC?>";
	parent.document.getElementById('simg').src = "<?=$IMG?>";
//-->
</SCRIPT>