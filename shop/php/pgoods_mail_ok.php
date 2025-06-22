<?
include "sub_init.php";
include "$skin/skin_define.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');


$CONTENT1 = stripslashes($_POST['gInfo']);
$CONTENT2 = $_POST['content'];
$NAME = $_POST['name1'];
$NAME2 = $_POST['name2'];
$EMAIL = $_POST['email1'];
$TITLE	= "{$NAME}님이 {$NAME2}님에게 보낸 메일 입니다.".$_POST['title'];

$sql = "SELECT code FROM mall_design WHERE mode='A'";
$tmp_basic = $mysql->get_one($sql);
$basic = explode("|*|",stripslashes($tmp_basic));

$URL = "http://".$_SERVER["SERVER_NAME"]."/{$ShopPath}";		
$CONTENT1 = str_replace("../skin/","{$URL}skin/",$CONTENT1);

$sql = "SELECT code FROM mall_design WHERE mode = 'F'";
$mail_img = explode("|*|",stripslashes($mysql->get_one($sql)));	

if(trim($mail_img[0])) $MAIL_IMG = "<a href='{$URL}' target='_blank'><img src='{$URL}image/design/{$mail_img[0]}' width='760' border='0' alt='Mail Image' /></a>";	
else $MAIL_IMG = '';
$MAIL_COMMENT =  "<br /><br />{$NAME}({$EMAIL})님이 {$basic[1]} 관심상품 정보를 보냈습니다.<br />&nbsp;<br />&nbsp;".$CONTENT1."<br />".$CONTENT2;

include "mail_form.php";   //메일 양식 인클루드 
pmallMailSend($_POST['email2'], $TITLE, $mail_form);	
 
?>

<SCRIPT LANGUAGE="JavaScript">

	alert('메일이 정상적으로 발송 되었습니다');
	parent.pLightBox.hide();

</SCRIPT>