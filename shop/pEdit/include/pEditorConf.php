<?php 

include "../include/pEncodeDecode.php";

// 이미지 절대경로 및 상대경로
$WE_imageUrl = "http://{$_SERVER['SERVER_NAME']}/";
$WE_imagePath = "../../";

// 업로드 가능한 이미지확장자
$WE_validImages = array('gif', 'jpg', 'jpeg', 'png');

// 업로드 사용유무
$WE_uploadAllowed = true;

// 업로드 가능한 폴더 정의
$WE_uploadDir = array('image/up_img','pboard/data');
$WE_uploadDirSub = 'Y';

// 이미지 라이브러리 정의
$udir = isset($_GET['udir']) ? previlDecode($_GET['udir']) : '';

$WE_imageLibs = array(
	array('value'   => $udir,'text'    => 'img')  
);

?>
