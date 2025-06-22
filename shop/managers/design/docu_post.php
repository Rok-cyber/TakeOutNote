<?
ob_start();
######################## lib include
include "../ad_init.php";

$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
$code = $_POST['code'];

if(!$mode) alert("정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.","back");

$code=ereg_replace("\n", "", $code);
$code=ereg_replace("\r", "", $code);
$code=ereg_replace("http://".$_SERVER["SERVER_NAME"]."/managers/design/document.html\?mode=".$mode."#", "#", $code);
$_SERVER["SERVER_NAME"] = ereg_replace("www.","",$_SERVER["SERVER_NAME"]);
$code=ereg_replace("http://".$_SERVER["SERVER_NAME"]."/managers/design/document.html\?mode=".$mode."#", "#", $code);

$code = addslashes($code);
 
$sql= "SELECT count(*) FROM mall_document WHERE mode='{$mode}'";
if($mysql->get_one($sql) <1) {
    $sql = "INSERT INTO mall_document values('','{$mode}','','{$code}')";
} else {
	if($file){
		$sql = "SELECT img FROM mall_document WHERE  mode='{$mode}'";
		@unlink($img_path.$mysql->get_one($sql));
    }
	$sql = "UPDATE mall_document SET code='{$code}' WHERE mode='{$mode}'";
}
$mysql->query($sql);

$title_arr = Array("A"=>"회사소개","B"=>"이용약관","C"=>"개인정보취급방침","D"=>"이용안내");
$msg = $title_arr[$mode]." 문서를 수정했습니다.";

alert($msg,"document.html?mode={$mode}");
?>
