<?php 
@error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
$tmps = $_SERVER['HTTP_HOST'];

include ("../include/pEditorConf.php");
$value_found = false;

function alert($msg) {
    echo "<script>alert('\\n $msg \\n');self.close();</script>";
	exit;
}

function is_array_value($value, $key, $_imgLib) {
    global $value_found;
    if (is_array($value)) array_walk($value, 'is_array_value',$_imgLib);
    if ($value == $_imgLib) $value_found=true;
}

if ( ! function_exists( 'exif_imagetype' ) ) {
    function exif_imagetype ( $filename ) {
        if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
            return $type;
        }
    return false;
    }
} 

array_walk($WE_imageLibs, 'is_array_value',$imgLib);

if (!$value_found || empty($imgLib))  $imgLib = $WE_imageLibs[0]['value'];
$lib_options = liboptions($WE_imageLibs,'',$imgLib);

$imageUrl = $WE_imageUrl.str_replace($WE_imagePath,"",$imgLib);

if($WE_uploadDirSub=='Y') {
	$ck=0;
	for($i=0,$cnt=count($WE_uploadDir);$i<$cnt;$i++){
	    if(eregi($WE_uploadDir[$i],$imgLib)) { $ck='1'; break;}
    }
	if($ck==0) alert('업로드 가능한 폴더가 아닙니다');
	unset($i);
	unset($ck);
	unset($cnt);
} 
else {
	if (!in_array($imgLib,$WE_uploadDir)) alert('업로드 가능한 폴더가 아닙니다');
}

$img = $_POST['imglist'];
$preview = '';
$errors = array();

if($_POST['delc']==1 && $WE_uploadAllowed) {
    @unlink($imgLib.urldecode($_POST['imglist'])); 
}
if ($_FILES['img_file']['size']>0) {
    if ($img = uploadImg('img_file')) $preview = "1";  
}

$encode_dir = previlEncode($imgLib);

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>이미지 삽입</title>
<link rel=StyleSheet HREF='dialog.css' type='text/css' title='CSS'>

<script>
try {	
	if(typeof(window.opener.dialogArguments)=='undefined') {
		self.close();
	}

	tmps1 = window.opener.location.href.split("/");
	tmps2 = window.location.href.split("/");
	if(tmps1[2]!=tmps2[2] || tmps1[2]!='<?=$tmps?>') {
		self.close();
	}
} catch(e) {
	self.close();
}
</script>

<script src="dialogComm.js"></script>

<style>
BODY	{border-style:none;margin:0px;padding:10px;background-color:#EBEAE7}
</style>


<script>

window.name			= 'imgLibrary';
var g_sUrl			= "";
var g_sAlign		= "newline";
var g_sBorderColor	= "#8c8c8c";
var Args			= window.opener.dialogArguments;


function checkInsertImage() {
	var oForm = document.libbrowser;		
	var nBorder = document.getElementById("nBorder").value;

	if(!oForm.imglist.value) {
	    alert('먼저 이미지를 선택 하시기 바랍니다.');
		return false;
    }

	g_sUrl = '<?=$imageUrl?>' + oForm.imglist.options[oForm.imglist.selectedIndex].value;

	if(nBorder>0) g_border = 'style="border:'+nBorder+'px solid '+g_sBorderColor+';width:'+document.libbrowser.width.value+';height:'+document.libbrowser.height.value+';"';
	else g_border = 'border="0" style="width:'+document.libbrowser.width.value+';height:'+document.libbrowser.height.value+';"';
	
	if (g_sAlign=="newline") var sHTML = "<img "+g_border+" src='"+g_sUrl+"'><br>";
	else var sHTML = "<img "+g_border+" src='"+g_sUrl+"' align="+g_sAlign+">";
	
	if(Args.callback) eval('window.opener.'+Args.callback + '(Args.Editor, sHTML)');
    self.close();
}

function setImgAlignType(sAlign,obj) {
		
	document.all.imgAlign1.className = "imgAlign";
	document.all.imgAlign2.className = "imgAlign";
	document.all.imgAlign3.className = "imgAlign";
    
	obj.className = "imgAlignOn";
	g_sAlign = sAlign;
}

function selectImg(frm) {
    if (frm.selectedIndex>=0) {
		imgpreview.location.href = './preview.php?dir=<?=$encode_dir;?>&image=' + frm.options[frm.selectedIndex].value;
	}
	document.libbrowser.btndel.style.display='inline';
}

function delClick() {
    if(!document.libbrowser.imglist.value) {
	    alert('먼저 이미지를 선택 하시기 바랍니다.');
		return false;
    }
	   
    document.libbrowser.delc.value=1;	   
	document.libbrowser.submit();	         
}

</script>


<body scroll=no onload="setColorPicker();">


<fieldset style="width:100%;padding:8px">
<legend> 이미지 첨부 </legend>	
   
    <form name="libbrowser" method="post" action="insertImage.php?udir=<?=$encode_dir?>" enctype="multipart/form-data" target="imgLibrary">
    <input type=hidden name=delc>
	<input type=hidden name="width">
	<input type=hidden name="height">

	<table border="0" cellpadding="2" cellspacing="0" width=100% class=fixed>
      <tr height=4>
	    <td valign="top" align="left" width=52%></td>
	    <td valign="top" align="left" width=2%>&nbsp;</td>
	    <td valign="top" align="left" width=46%></td>
	  </tr>
	  <tr>        
        <td valign="top" align="left">
		  <select name="imglist" size="15" class="input" style="width: 100%;" onchange="selectImg(this);" ondblclick="checkInsertImage()">
<?php 

if ($d = dir($imgLib)) {
    while (false !== ($entry = $d->read())) { 
		$ck[] = $entry;		
    }
    $d->close();
     
	sort($ck); 
	for($i=0;$i<count($ck);$i++){
        if (is_file($imgLib.$ck[$i])) {
			echo "<option value='".urlencode($ck[$i])."'";
			echo ($ck[$i] == $img)?'selected':'';
			echo ">{$ck[$i]}</option>";
        }
    }

} else {
	$errors[] = 'error_no_dir';
}
?>


          </select>
        </td>         
        <td valign="top" align="left">&nbsp;</td>
        <td valign="top" align="left">
           <iframe name="imgpreview" src="preview.php" style="width: 270; height: 225;" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"></iframe>
        </td>
      </tr>
	  <? if($WE_uploadAllowed) { ?>
      <tr>
        <td valign="top" align="left" colspan=3>
           <input type="file" name="img_file" size=29 class="small"><input type="submit" name="btnupload" class="small" value="올리기">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="btndel" value="선택이미지 삭제" class="small" onclick="return delClick();" style="display:none">
        </td>
      </tr>
	  <? } ?>
	</table>
	</form>
</fieldset>


<fieldset style="width:100%;padding:8px">
<legend> 레이아웃 </legend>
	<center>
	<table border=0 cellpadding=5 cellspacing=0 width=100% class=fixed>
	<tr align=center>
		<td width=20%></td>
		<td><img id=imgAlign1 class='imgAlignOn' onclick="setImgAlignType('newline',this)" src=../img/imgInsertImageAlignType_newline.png border=0></td>
		<td><img id=imgAlign2 class='imgAlign' onclick="setImgAlignType('left',this)" src=../img/imgInsertImageAlignType_left.png border=0></td>
		<td><img id=imgAlign3 class='imgAlign' onclick="setImgAlignType('right',this)" src=../img/imgInsertImageAlignType_right.png border=0></td>
		<td width=20%></td>
	</tr>
	<tr align=center>
	    <td></td>
		<td>줄바꿈</td>
		<td>왼쪽</td>
		<td>오른쪽</td>
		<td></td>
	</tr>
	</table>
	</center>
</fieldset>

<fieldset style="width:100%;padding:8px">
<legend> 테두리 </legend>
	<center>
	<table border=0 cellpadding=0 cellspacing=0 width=100% class=fixed>
	<tr>
	    <td width=20%></td>
		<td width=40>두께</td>
		<td width=100>
			<select id=nBorder>
			<option value=0>0 px
			<option value=1>1 px
			<option value=2>2 px
			<option value=3>3 px
			</select>
		</td>
		<td width=40>색상</td>
		<td width=100><button onclick="cpShow(this)" style="border:0px;width:50px;background-color:#8c8c8c" onfocus="this.blur();" id=ccolor>&nbsp;&nbsp;&nbsp;</button></td>
		<td width=20%></td>
	</tr>
	</table>
	</center>
</fieldset>




<center>
<table border=0 cellpadding=0 cellspacing=0 width=100% class=fixed>
<tr>
	<td height=40 align=right style="padding-right:5px"><button onclick="checkInsertImage()" class='tah11px bold' style="width:80px">OK</button></td>
	<td style="padding-left:5px"><button style="width:80px" onclick="self.close()" class='tah11px'>CANCEL</button></td>
</tr>
</table>
</center>

<?php 

if ( ! function_exists( 'exif_imagetype' ) ) {
    function exif_imagetype ( $filename ) {
        if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
            return $type;
        }
    return false;
    }
} 

function liboptions($arr, $prefix = '', $sel = '') {
    $buf = '';
    foreach($arr as $lib) {
        $buf .= '<option value="'.$lib['value'].'"'.(($lib['value'] == $sel)?' selected':'').'>'.$prefix.$lib['text'].'</option>'."\n";
    }
    return $buf;
}

function uploadImg($img) {

    global $_FILES, $_SERVER, $WE_validImages, $imgLib, $errors, $WE_uploadAllowed;  
    if (!$WE_uploadAllowed) return false;
    if ($_FILES[$img]['size']>0) {
        $data['type'] = $_FILES[$img]['type'];
		$data['name'] = $_FILES[$img]['name'];
		$data['size'] = $_FILES[$img]['size'];
		$data['tmp_name'] = $_FILES[$img]['tmp_name'];
		
		if(!exif_imagetype($data['tmp_name'])) return false;

		// get file extension
		$ext = strtolower(substr(strrchr($data['name'],'.'), 1));
		if (in_array($ext,$WE_validImages)) {
			$dir_name = $imgLib;
		    $img_name = $data['name'];
            $i = 1;
			while (file_exists($dir_name.$img_name)) {
				$img_name = ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $data['name']);
				$i++;
			}
			$img_name = str_replace(" ","_",$img_name);
			if (!move_uploaded_file($data['tmp_name'], $dir_name.$img_name)) {
			    $errors[] = '이미지 업로드중 에러가 발생했습니다!';
			    return false;
		    }
			return $img_name;
        } else {
			$errors[] = '이미지만 업로드 가능합니다!';
		}
    }
    return false;
}


if($preview=="1") {
	echo "<script>
			document.imgpreview.location.href='preview.php?dir={$encode_dir}&image={$img}';
			document.libbrowser.btndel.style.display='inline';	
		  </script>
		  ";
}

?>
</html>