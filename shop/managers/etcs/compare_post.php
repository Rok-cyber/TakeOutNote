<?
######################## lib include
include "../ad_init.php";

$code = $_POST['ck1']."|".stripslashes($_POST['ex1']);
for($i=2;$i<10;$i++) {
	$code .= "|".$_POST['ck'.$i]."|".stripslashes($_POST['ex'.$i]);
}

$sql = "SELECT count(*) FROM mall_design WHERE mode='X'";
if($mysql->get_one($sql)==0) {
	$sql = "INSERT INTO mall_design VALUES('','가격비교','{$code}','X')";
}
else {
	$sql = "UPDATE mall_design SET code='{$code}' WHERE mode='X'";
}
$mysql->query($sql);
$msg = "가격비교 엔진 페이지 정보를 수정 저장 했습니다.";

alert($msg,"compare.html");
?>
