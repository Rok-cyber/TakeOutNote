<?
include "sub_init.php";

$name = addslashes($_POST['name']);
$email = addslashes($_POST['email']);

if(!$name || !$email) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');

$sql = "SELECT id FROM pboard_member WHERE name = '{$name}' && email='{$email}'";
if($id = $mysql->get_one($sql)) {
?>
<script>
parent.document.getElementById("viewId1").style.display='none';
parent.document.getElementById("viewId2").style.display='block';
parent.document.getElementById("memId").innerHTML = "<?=$id?>";
</script>
<? } else { ?>
<script>
parent.document.getElementById("viewId2").style.display='none';
parent.document.getElementById("viewId1").style.display='block';
</script>
<? 
}
?>

