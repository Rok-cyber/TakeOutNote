<?php
@error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
ob_start();
set_time_limit(0); 

$lib_path	= "../lib";
$inc_path	= "../include";

require "$lib_path/lib.Function.php";
include "$inc_path/dbconn.php";
require "$lib_path/class.Mysql.php";
require "$lib_path/checkLogin.php";
include "$lib_path/class.Thumb.php";

//if($my_level < 9 || !$my_id) Error("관리자 전용 페이지 입니다~");      //관리자 체크

$mysql = new  mysqlClass(); //디비 클래스


function upFiles($userfile,$userfile_name,$savedir,$max_size="",$img="",$save_name=""){
	global $o_size1, $o_size2;

	$userfile_name = urlencode($userfile_name);
   
	// 확장자 검사
    if(!eregi("\.jpg|\.jpeg|\.gif|\.pnp|\.swf|\.bmp",$userfile_name) && $img=='true') {
		header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload";
		exit(0);
    }

	if($img=='true' && !exif_imagetype($userfile)) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload";
		exit(0);
	}

	$lenStr= strlen($userfile_name);                         // 파일 길이 
	$dotPos = strrpos($userfile_name, ".");              // 맨 마지막 도트의 위치 
	$only_name = substr($userfile_name, 0, $dotPos);
	$ext = getExtension($userfile_name);

	if(eregi("\.php|\.inc|\.htm|\.phtm|\.shtm|\.ztx|\.dot|\.cgi|\.pl|\.asp|\.jsp",$userfile_name)) {
		$userfile_name = $only_name.".phps";      // 확장자와 점을 뺀 파일명		
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
		$userfile_name = $save_name.".{$ext}";
    }	

	//파일 업로드	
	if(!move_uploaded_file($userfile, $savedir."/".$userfile_name)){

		header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload";
		exit(0);
	} 
	
	$thum = createThumbnail($savedir."/".$userfile_name,$o_size1);
	rename($thum, $savedir."/".$only_name."_Pthum1.".$ext);
	$thum = createThumbnail($savedir."/".$userfile_name,$o_size2);				
	rename($thum, $savedir."/".$only_name."_Pthum2.".$ext);
	     
    return $userfile_name;
}


$tmp_dir = isset($_POST['tmp_dir']) ? $_POST['tmp_dir'] : $_GET['tmp_dir'];
$o_size1 = isset($_POST['o_size1']) ? $_POST['o_size1'] : $_GET['o_size1'];
$o_size2 = isset($_POST['o_size2']) ? $_POST['o_size2'] : $_GET['o_size2'];

if(!$tmp_dir) {
	header("HTTP/1.1 500 Internal Server Error");
	echo "invalid upload";
	exit(0);
}

$up_path = previlDecode($tmp_dir);
$up_path = str_replace("../../","../",$up_path);

if($_GET['mode']=='del') {	
	$file = urlencode($_GET['file']);
	$file = str_replace("|*|","+",$file);
	$size = @filesize("{$up_path}/{$file}");
	if(!$file || !$size) {
		$file = urldecode($_GET['file']);
		$file = str_replace("|*|","+",$file);
		$size = @filesize("{$up_path}/{$file}");
		if(!$file || !$size) {
			exit;
		}	
	}
	
	delFile("{$up_path}/{$file}");
	
	$lenStr= strlen($file);                         // 파일 길이 
	$dotPos = strrpos($file, ".");              // 맨 마지막 도트의 위치 
	$only_name = substr($file, 0, $dotPos);
	$ext = getExtension($file);

	delFile("{$up_path}/{$only_name}_Pthum1.{$ext}");
	delFile("{$up_path}/{$only_name}_Pthum2.{$ext}");

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
    $file = upFiles($_FILES['Filedata'][tmp_name],$_FILES['Filedata'][name],$up_path,"",'true',"");	
}     

if(!$file) {
	header("HTTP/1.1 500 Internal Server Error");
	echo "invalid upload";
	exit(0);
}

$fileSize	= getFilesize("{$up_path}/{$file}");
$ofileSize	= @filesize("{$up_path}/{$file}");
$ofile		= urlencode($file);
//$file		= iconv("euc-kr","utf-8",$file);

echo $ofile."|".$file."|".$fileSize."|".$ofileSize;
	
?>