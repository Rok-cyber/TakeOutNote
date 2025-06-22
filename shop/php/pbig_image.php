<?
include "sub_init.php";
include "{$skin}/skin_define.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

$uid = $_GET['uid'];

$skin = "../skin/$tmp_skin";
$skin2 = $skin."/";
$ShopPath = "../";

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비


$sql = "SELECT * FROM mall_goods WHERE uid='{$uid}'";
if(!$data = $mysql->one_row($sql)) {
	echo "
		<script>
			alert('상품이 삭제 되었거나 없는 상품입니다.');\n
			parent.pLightBox.hide();\n
		</script>
	";
	exit;
}

$cate = $data['cate'];
$number = $data['number'];

$gData	= getDisplay($data,'image1');		// 디스플레이 정보 가공 후 가져오기
$GOODS_IMG	= $gData['image'];
$GOODS_IMG_WIDTH = $IMG_DEFINE['img1'];
$GOODS_OIMG = $gData['image'];
$GOODS_NAME	= $gData['name'];		
$GOODS_PRICE= $gData['price'];
$GOODS_ICON	= $gData['icon'];

$tpl->define('main',"{$skin}/pbig_image.html");
$tpl->scan_area('main');

if($data['consumer_price'] && $data['consumer_price'] >0){
	$GOODS_C_PRICE = number_format($data['consumer_price'])."원";
    $tpl->parse('is_c_price'); 
}

/**************************** OTHER IMAGE **************************/
$tmp_dir = str_replace("../../","../",$data['other_image']);
$tmp_dir2 = str_replace("../","/",$tmp_dir);
$OCNT = 0;
if(is_dir($tmp_dir)) { 	
	$handle	= @opendir($tmp_dir);
	$ot_img1 = array();
	$ot_img2 = array();	
	while ($file = @readdir($handle)) {
		if($file != '.' && $file != '..' && is_file("{$tmp_dir}/{$file}") && !eregi("_Pthum",$file)) {
			
			$lenStr= strlen($file);                         // 파일 길이 
			$dotPos = strrpos($file, ".");              // 맨 마지막 도트의 위치 
			$only_name = substr($file, 0, $dotPos);
			$ext = getExtension($file);

			if(is_file("{$tmp_dir}/{$only_name}_Pthum1.{$ext}")) $ot_img1[] = "{$tmp_dir}/".urlencode($only_name)."_Pthum1.{$ext}";
			else $ot_img1[] = "{$tmp_dir}/".urlencode($file);		
			$ot_img2[] = "{$tmp_dir}/".urlencode($file);			
		}		
	}
	@closedir($handle);		

	sort($ot_img1);
	sort($ot_img2);
	for($i=0,$cnt=count($ot_img1);$i<$cnt;$i++) {
		$OT_IMG1 = $ot_img1[$i];
		$OT_IMG2 = $ot_img2[$i];
		$tpl->parse("loop_ot_img");		
		$OCNT++;
	}

	if($OCNT>$SKIN_DEFINE['other_vcnt']) $OWIDTH = ($IMG_DEFINE['other_s'] + 10) * $OCNT;
	else $OWIDTH = ($IMG_DEFINE['other_s'] + 6) * $i;

	for($i=$OCNT;$i<$SKIN_DEFINE['other_vcnt'];$i++) $tpl->parse("no_ot_img");	
     if($OCNT>0)  $tpl->parse("is_ot_img");
}	
/**************************** OTHER IMAGE **************************/

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>