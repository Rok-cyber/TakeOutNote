<?
ob_start();
$bo_path = ".";
include "{$bo_path}/init.php";

$access_ip	= $_SERVER['REMOTE_ADDR'];
$code		= isset($_GET['code'])		? $_GET['code']:'';
$no			= isset($_GET['no'])		? $_GET['no']:'';
$no2		= isset($_GET['no2'])		? $_GET['no2']:'';
$page		= isset($_GET['page'])		? $_GET['page'] : '';
$spage		= isset($_GET['spage'])		? $_GET['spage'] : '';
$slast_idx	= isset($_GET['slast_idx'])	? $_GET['slast_idx'] : '';
$field		= isset($_GET['field'])		? $_GET['field'] : '';
$word		= isset($_GET['word'])		? $_GET['word'] : '';
$seccate	= isset($_GET['seccate'])	? $_GET['seccate'] : '';
$Main		= isset($_GET['Main'])		? $_GET['Main'] : '';

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert($LANG_ERR_MSG[2],'back');

if($acc_level[11] == '!=' && $acc_level[5]!=$my_level) alert($LANG_ACC_MSG[5],'back');
if($acc_level[11] == '<' && $acc_level[5]>$my_level) alert($LANG_ACC_MSG[5],'back');

if(!$no || (!$no2 && $no2!=0) || !$code) alert($LANG_ERR_MSG[2],'back');

if($field && $word) $addstring = "&amp;field={$field}&amp;word={$word}";
if($page) $addstring .="&amp;page={$page}";
if($seccate) $addstring .="&amp;seccate={$seccate}";
if($spage && $slast_idx) $addstring .= "&amp;slast_idx={$slast_idx}&amp;spage={$spage}";

session_start();
$tmp=explode(',',$_SESSION['pboard_vote']);
if(!in_array("{$code}:{$no}",$tmp)){
	$sql = "SELECT file FROM pboard_{$code}_body WHERE no={$no}";
	$ck_ip = $mysql->get_one($sql);

	$ck_ip = explode("|",$ck_ip);
	if(in_array($access_ip,$ck_ip)) alert($LANG_CHK_MSG[4],"back");
	$ck_ip[] = $access_ip;
	$ck_ip = join("|",$ck_ip);
	$sql = "UPDATE pboard_{$code}_body SET file = '{$ck_ip}' WHERE no={$no}";
	$mysql->query($sql);	
	
	$sql = "UPDATE pboard_{$code}  SET reco=reco+1 WHERE no={$no}";
	$mysql->query($sql);
	$sql = "SELECT comment, m_link, s_link FROM pboard_{$code}_body WHERE no={$no}";
	$tmps = $mysql->one_row($sql);	
	
	$dates	= explode("~",$tmps['comment']);
	$scnt	= explode("||",$tmps['m_link']);	  
	$cnts	= explode("||",$tmps['s_link']);	  

	$today	= date("Y/m/d");
	if(trim($dates[0]) > $today || trim($dates[1]) < $today) {
		alert($LANG_CHK_MSG[6],'back');
	}

	if(!$scnt[$no2]) $scnt[$no2] = 1;
	else $scnt[$no2] += 1; 
              
	$tmps = $scnt[0];
	for($i=1,$cnt=count($cnts);$i<$cnt;$i++) {				
		if($cnts[$i]) $tmps .= "||".$scnt[$i];
	}	
	  
	$sql = "UPDATE pboard_{$code}_body SET m_link = '{$tmps}' WHERE no={$no}";	
	$mysql->query($sql);
	array_push($tmp, $code.":".$no);
	$pboard_vote = implode(',',$tmp);	  
    $_SESSION['pboard_vote'] = $pboard_vote;
	unset($tmp);
} else alert($LANG_CHK_MSG[4],"back");

include "{$bo_path}/close.php";  

$Main = str_replace("|","&",$Main);
alert($LANG_ETC_MSG[11],"{$Main}&amp;pmode=view&amp;no={$no}{$addstring}");     
?>
