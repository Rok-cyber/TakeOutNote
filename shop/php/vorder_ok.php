<?
$num = isset($_GET['num']) ? $_GET['num'] : '';
echo "<script>parent.goodsForm{$num}.submit();</script>";
exit;
?>