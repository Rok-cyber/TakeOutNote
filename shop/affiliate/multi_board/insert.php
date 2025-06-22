<?
set_time_limit(0); 
######################## lib include
include "../ad_init.php";
include "./conf.php";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : '';
$word		= isset($_GET['word']) ? $_GET['word'] : '';
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : '';
$mode		= isset($_GET['mode']) ? $_GET['mode'] : '';
$limit		= isset($_GET['limit']) ? $_GET['limit'] : '';
$location	= isset($_GET['location']) ? $_GET['location'] : '';
$point		= isset($_GET['point']) ? $_GET['point'] : '';
$status		= isset($_GET['status']) ? $_GET['status'] : '';
$sdate1		= isset($_GET['sdate1']) ? $_GET['sdate1'] : '';
$sdate2		= isset($_GET['sdate2']) ? $_GET['sdate2'] : '';
$code		= isset($_GET['code'])? $_GET['code']:$_POST['code']; 
$uid		= isset($_GET['uid'])? $_GET['uid']:$_POST['uid']; 

##################### addstring ############################ 
if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($limit) $addstring .= "&limit={$limit}";
if($location) $addstring .= "&location={$location}";
if($status) $addstring .= "&status={$status}";
if($point) $addstring .= "&point={$point}";
if($seccate) $addstring = "&seccate={$seccate}";
if($page) $addstring .="&page={$page}";
if($sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}&dates={$dates}";	
	 
// DATE
$signdate = time();

if(!eregi("none",$_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name']) {									
	$_POST['file'] = upFile($_FILES['file']['tmp_name'],$_FILES['file']['name'],$dir);
}
else $_POST['file'] = '';

if(!eregi("none",$_FILES['file2']['tmp_name']) && $_FILES['file2']['tmp_name']) {									
	$_POST['file2'] = upFile($_FILES['file2']['tmp_name'],$_FILES['file2']['name'],$dir);
}
else $_POST['file2'] = '';


switch($mode) {
    case "write" : 
		$sql = "INSERT INTO mall_{$code} (";
	    for($i=1;$i<$MTins[0];$i++){  
            $sql .= $MTins[$i].", ";	
		}
		$sql .= $MTins[$i].") VALUES (";
        for($i=1;$i<$MTins[0];$i++){  
            $fd = $MTins[$i];
			$_POST[$fd] = addslashes($_POST[$fd]);
			$sql .= "'{$_POST[$fd]}', ";	
		}
		$sql .= "'{$signdate}')";
		$msg = "등록했습니다";
		$mysql->query($sql);

    break;
    case "modify" :  
	    $sql = "UPDATE mall_{$code} SET ";
		for($i=1;$i<=($MTmod[0]);$i++){  
            $fd = $MTmod[$i];
			$_POST[$fd] = addslashes($_POST[$fd]);
			if(eregi('file',$fd) || eregi('banner',$fd) || eregi('img',$fd)) {
				if($_POST[$fd]) {
					if($_POST[$fd])	$sql .= ", {$fd} =  '{$_POST[$fd]}' ";								
					$sql2 = "SELECT {$fd} FROM mall_{$code} WHERE uid='{$uid}'";
					$tmp_img = $mysql->get_one($sql2);
					if($tmp_img) @unlink("{$dir}/".$tmp_img); 
                }
            } 
			else if($fd=='passwd') {
				if($_POST[$fd]) $sql .= ", {$fd} =  '{$_POST[$fd]}'";		
			}
			else {
				if($i=='1') $sql .= $MTmod[$i]." =  '{$_POST[$fd]}'";		
			    else $sql .= ", {$MTmod[$i]} =  '{$_POST[$fd]}'";		
			}					
		}
					
		$sql .= " WHERE uid='{$uid}'";			 				 						
 		$msg = "수정했습니다";
		$mysql->query($sql);
	break;					
}  

alert($msg,$Main.$addstring);

//메모리제거
ignore_user_abort(true); 
register_shutdown_function('userAbortFunc');

?>