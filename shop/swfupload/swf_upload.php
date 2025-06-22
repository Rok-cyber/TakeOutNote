<?php
ob_start();
set_time_limit(0); 

$lib_path	= "../lib";

require "$lib_path/lib.Function.php";
include "$lib_path/dbconn.php";
require "$lib_path/class.Mysql.php";
require "$lib_path/checkLogin.php";

//if($my_level < 9 || !$my_id) Error("관리자 전용 페이지 입니다~");      //관리자 체크

$mysql = new  mysqlClass(); //디비 클래스


function upFiles($userfile,$userfile_name,$savedir,$max_size="",$img="",$save_name=""){
   
	// 확장자 검사
    if(!eregi("\.jpg|\.jpeg|\.gif|\.pnp|\.swf|\.bmp",$userfile_name) && $img=='true') {
		header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload";
		exit(0);
    }

	if(eregi("\.php|\.inc|\.htm|\.phtm|\.shtm|\.ztx|\.dot|\.cgi|\.pl|\.asp|\.jsp",$userfile_name)) {
		 
		// 먼저 들어온 파일명의 앞부분과 확장자 부분을 분리한다. 
		$lenStr= strlen($userfile_name);                         // 파일 길이 
		$dotPos = strrpos($userfile_name, ".");              // 맨 마지막 도트의 위치 
		$userfile_name = substr($userfile_name, 0, $dotPos).".phps";      // 확장자와 점을 뺀 파일명		
	}
    
    // 파일용량제한 
	$file_size = filesize($userfile);
	if(!$file_size){
		 header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload ";
		exit(0);
	}

	//$userfile_name = iconv("utf-8","euc-kr",$userfile_name);

	if(!$save_name) {		
		$i = 1;
		$ofile_name = $userfile_name;
		while (file_exists("{$savedir}/{$userfile_name}")) {
			$userfile_name = ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $ofile_name);
			$i++;			
		}		
		$userfile_name = str_replace(" ","_",$userfile_name);		
	}
	else {
		$userfile_name = $save_name.".".getExtension($userfile_name);
    }	

	//파일 업로드	
	if(!move_uploaded_file($userfile, $savedir."/".$userfile_name)){

		header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload";
		exit(0);
	} 
	     
    return $userfile_name;
}


$tmp_dir = isset($_POST['tmp_dir']) ? $_POST['tmp_dir'] : $_GET['tmp_dir'];


if(!$tmp_dir) {
	header("HTTP/1.1 500 Internal Server Error");
	echo "invalid upload";
	exit(0);
}

$up_path = previlDecode($tmp_dir);
$up_path = str_replace("../../","../",$up_path);


if($_GET['mode']=='del') {	
	$file = $_GET['file'];
	$size = @filesize("{$up_path}/{$file}");
	if(!$file || !$size) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "{$file} is not exists";
		exit(0);
	}
	
	delFile("{$up_path}/{$file}");		

	header("Content-type: text/xml; charset=utf-8"); 
	header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
	header("Cache-Control: no-store, no-cache, must-revalidate"); 
	header("Cache-Control: post-check=0, pre-check=0", false); 
	header("Pragma: no-cache"); 

	echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 
	echo "
	  <item>
		<id><![CDATA[{$file}]]></id>
		<size><![CDATA[{$size}]]></size>	
      </item>\n	
	 ";

	echo "</root>";
	exit;
}

if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
	header("HTTP/1.1 500 Internal Server Error");
	echo "invalid upload";
	exit(0);
}

if(!eregi('none',$_FILES['Filedata'][tmp_name]) && $_FILES['Filedata'][tmp_name]) {
    $file = upFiles($_FILES['Filedata'][tmp_name],$_FILES['Filedata'][name],$up_path,"","","");	
}     

if(!$file) {
	header("HTTP/1.1 500 Internal Server Error");
	echo "invalid upload";
	exit(0);
}

$ext = getExtension($file);

switch($ext) {
	case "bmp": case "exe": case "excel": case "gif" : case "hwp": case "jpg": case "ppt": case "swf": case "txt": case "word" : 
	case "zip":
		$icons = "/swfupload/icon/{$ext}.gif";
	break;
	default : $icons = "/swfupload/icon/etc.gif";
}

$fileSize	= getFilesize("{$up_path}/{$file}");
$ofileSize	= @filesize("{$up_path}/{$file}");
//$file		= iconv("euc-kr","utf-8",$file);

echo $icons."|".$file."|".$fileSize."|".$ofileSize;
	
?>