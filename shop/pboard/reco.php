<?
ob_start();
$bo_path = ".";
include "{$bo_path}/init.php";

$code		= isset($_GET['code'])		? $_GET['code']:'';
$no			= isset($_GET['no'])		 ? $_GET['no']:'';
$page		= isset($_GET['page'])		? $_GET['page'] : '';
$spage		= isset($_GET['spage'])		? $_GET['spage'] : '';
$slast_idx	= isset($_GET['slast_idx'])	? $_GET['slast_idx'] : '';
$field		= isset($_GET['field'])		? $_GET['field'] : '';
$word		= isset($_GET['word'])		? $_GET['word'] : '';
$seccate	= isset($_GET['seccate'])	? $_GET['seccate'] : '';
$Main		= isset($_GET['Main'])		? $_GET['Main'] : '';

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert($LANG_ERR_MSG[2],'back');

if($acc_level[11] == '!=' && $acc_level[5]!=$my_level) alert($LANG_ACC_MSG[4],'back');
if($acc_level[11] == '<' && $acc_level[5]>$my_level) alert($LANG_ACC_MSG[4],'back');

if(!$no || !$code) alert($LANG_ERR_MSG[2],'back');

if($field && $word) $addstring = "&amp;field={$field}&amp;word={$word}";
if($page) $addstring .="&amp;page={$page}";
if($seccate) $addstring .="&amp;seccate={$seccate}";
if($spage && $slast_idx) $addstring .= "&amp;slast_idx={$slast_idx}&amp;spage={$spage}";

session_start();
$tmp=explode(',',$_SESSION['pboard_reco']);
if(!in_array("{$code}:{$no}",$tmp)){
	  $sql = "UPDATE pboard_{$code}  SET reco = reco+1 WHERE no={$no}";
	  $mysql->query($sql);
	  array_push($tmp, $code.":".$no);
	  $pboard_reco = implode(',',$tmp);	  
      $_SESSION['pboard_reco'] = $pboard_reco;
	  unset($tmp);
} else alert($LANG_CHK_MSG[1],'back');

include "{$bo_path}/close.php";  

$Main = str_replace("|","&",$Main);
movePage("{$Main}&amp;pmode=view&amp;no={$no}{$addstring}");   
?>
