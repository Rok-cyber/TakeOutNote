<?
######################## lib include
$skin_inc = "Y";
include "../ad_init.php";

$cate = $_GET['cate'];
if(!$cate) exit;

?>
<script>
f = parent.document;

<?
$sql = "SELECT code, cate_dep, cate_sub FROM mall_cate WHERE cate='{$cate}'";
$row = $mysql->one_row($sql);

$code = explode("|*|",stripslashes($row['code']));

if($row['cate_sub']) {
	if(!$code[0]) {
		if($code[0]!="0") $code[0] = 1;
	} else $code[0] = $code[0] - 1;

	if(!$code[2]) {
		if($code[2]!="0") $code[2] = 1;
	} else $code[2] = $code[2] - 1;

	if(!$code[4] && $code[4]!="0") {
		if($row['cate_dep']==1) $code[4] = 1;
		else $code[4] = 0;
	}
	echo "
		f.goodsForm.hit_use[{$code[0]}].checked=true;
		f.goodsForm.reco_use[{$code[2]}].checked=true;
		f.goodsForm.new_use[{$code[4]}].checked=true;
		f.goodsForm.cate.value='{$cate}';
		";

	if($code[1]) echo "f.goodsForm.hit_num.value = {$code[1]};";
	if($code[3]) echo "f.goodsForm.reco_num.value = {$code[3]};";
	if($code[5]) echo "f.goodsForm.new_num.value = {$code[5]};";
}

if(!$code[6]) $code[6] = 1;
else $code[6] = $code[6] - 1;
if(!$code[8]) $code[8] = 1;
else $code[8] = $code[8] - 1;

echo "
	f.codeForm.cate_up_use[{$code[6]}].checked=true;
	f.codeForm.hit_up_use[{$code[8]}].checked=true;
	f.codeForm.cate.value='{$cate}';
	";
?>
</script>
<form name="codes">
<textarea name="code1"><?=$code[7]?></textarea>
<textarea name="code2"><?=$code[9]?></textarea>
</form>
<script>

<?
echo "parent.cate_insert._doc.body.innerHTML = document.codes.code1.value;";
echo "parent.hit_insert._doc.body.innerHTML = document.codes.code2.value;";
?>

</script>
