<?
$skin_inc = "Y";
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

include "./conf.php";
require "{$lib_path}/class.Paging.php";
require "{$lib_path}/class.Template.php";

$path = ".";

?>

<div class="mainBox2"  id="mainContent">
	<div class="top">
		<div style="padding:9px 0 0 12px;">
			<div class='left'><img src="<?=$t_img?>" /></div>
			<div class='left small' style='padding:8px 0 0 10px;'><?=$t_msg?></div>
		</div>			
	</div>
	<div class="content">
		<div class='content_top'></div>			
		<div style="padding:0 6px 0 6px;" id='mainHeight'>
			<div style='height:10px;overflow:hidden'>&nbsp;</div>		

<table width=99% align=center cellpadding=0 cellspacing=0>
<TR height=5>
	<TD></TD>
</TR>
<TR>
	<TD align=left>
<!--##################  MULTI BOARD START ##########################-->  
<?

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : $_POST['field'];
$word		= isset($_GET['word']) ? $_GET['word'] : $_POST['word'];
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : $_POST['seccate'];
$mode		= isset($_GET['mode']) ? $_GET['mode'] : '';
$limit		= isset($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
$location	= isset($_GET['location']) ? $_GET['location'] : $_POST['location'];
$status		= isset($_GET['status']) ? $_GET['status'] : $_POST['status'];
$point		= isset($_GET['point']) ? $_GET['point'] : $_POST['point'];
$uid		= isset($_GET['uid'])? $_GET['uid']:$_POST['uid']; 

##################### addstring ############################ 
if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($limit) $addstring .= "&limit={$limit}";
if($location) $addstring .= "&location={$location}";
if($status) $addstring .= "&status={$status}";
if($point) $addstring .= "&point={$point}";
$addstring2= $addstring;
if($seccate) $addstring .= "&seccate={$seccate}";
$addstring3= $addstring;
if($page) $addstring .="&page={$page}";

switch($mode) {
	case "write": case "modify": 
		        include "{$path}/write_form.php";
	break;

	default: include "{$path}/list.php";
	break;
}

?>
<!--##################  @MULTI BOARD END ##########################-->  
	</TD>
</TR>
</TABLE>

			</div>
		</div>
		<div class="bottom"></div>
		</span>		
	</div>
</div>

<? include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/ ?>