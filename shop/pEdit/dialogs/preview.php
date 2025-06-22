<?
@error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
include "../include/pEncodeDecode.php";

function imgSizeCh($dir,$file,$size_factor=""){
	$dir = previlDecode($dir);
    if($size_factor=="") return "<img src='{$dir}".urlencode($file)."' border=0>"; 
    
	if(!$size=@GetImageSize($dir.$file)) return false; 
    if($size[0] == 0 ) $size[0]=1; 
    if($size[1] == 0 ) $size[1]=1; 
    
	if($size[0] > $size_factor || $size[1] > $size_factor){
		if($size[0]>$size[1]) { $per=$size_factor / $size[0]; }  
		else { $per=$size_factor / $size[1]; } 
	} else  $per=1;    
    
	$x_size=$size[0]*$per; 
    $y_size=$size[1]*$per; 
    
	echo "<img src='$dir".urlencode($file)."' width='{$x_size}' height='{$y_size}' border=0 align=absmiddle><font class=eng><br><br><b>Name</b> : {$file}&nbsp;&nbsp;&nbsp;<b>Size</b> : {$size[0]}px * {$size[1]}px</font>"; 	
}
?>
<HTML>
<HEAD>
<TITLE> IMAGE PREVIEW</TITLE>
<link rel="stylesheet" type="text/css" href="dialog.css">
<style>
BODY	{border-style:none;margin:0px;padding:10px;background-color:#EBEAE7}
</style>
</HEAD>
<BODY topmargin=0 leftmargin=0>
<TABLE width=100% height=100% border=0 cellspacing=0 cellpadding=0>
<TR><TD valign=top>

<? 
	if($_GET['dir'] && $_GET['image']) {
		imgSizeCh($_GET['dir'],$_GET['image'],180);
		$size=@GetImageSize(previlDecode($_GET['dir']).$_GET['image']);
	}
?>

</TD></TR>
</TABLE>
</BODY>
</HTML>
<SCRIPT LANGUAGE="JavaScript">
<!--
	parent.document.libbrowser.width.value = "<?=$size[0]?>";
	parent.document.libbrowser.height.value = "<?=$size[1]?>";	
//-->
</SCRIPT>
