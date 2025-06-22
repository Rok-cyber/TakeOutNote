<?
ob_start();
@set_time_limit(0);
$skin_inc = "Y";
include "../ad_init.php";

$size = filesize("../../include/zip.db");
$orig_data = getZip($size);

if($orig_data=='false') {
	alert("업데이트 서버 이상으로 실패 되었습니다","back");
}
else if($orig_data=='eq') {
	alert("최신 우편번호 파일 입니다. 아직 업데이트 파일이 나오지 않았습니다","back");
}
else {
	writeFile("../../include/zip.db",$orig_data);
	alert("성공적으로 우편번호 파일이 업데이트 되었습니다.","index.html");
}
?>