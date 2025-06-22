<?
ob_start();
$skin_inc = "Y";
include "../html/top_inc.html"; // 상단 HTML 

######################## CONf
$skin = ".";
$code = "mall_goods";
$PGConf['page_record_num'] = '15';
$PGConf['page_link_num']='10';
$MTlist = Array(7,'uid','cate','name','price','s_qty','qty','signdate');

######################## lib include
require "{$lib_path}/lib.Shop.php";
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$field		= $_GET['field'];
$word		= $_GET['word'];
$smoney1	= $_GET['smoney1'];
$smoney2	= $_GET['smoney2'];
$sdate1		= $_GET['sdate1'];
$sdate2		= $_GET['sdate2'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$seccate	= $_GET['seccate'];
$brands		= $_GET['brands'];
$s_qty		= $_GET['s_qty'];
$mode		= $_GET['mode'];
$uid		= $_GET['uid'];

$up_dir		= "../../image/up_img/detail/";
$up_path	= "../../image/other_img/";
$icon_path	= '../../image/icon/';
$img_path	= '../../image/goods_img';
$tmp_uid	= '';

if($mode=='modify') {
	if(!$uid)  {
		$cate = isset($_POST['cate']) ? $_POST['cate'] : $_GET['cate'];
		$number = isset($_POST['number']) ? $_POST['number'] : $_GET['number'];

		if($cate && $number) {
			$sql = "SELECT uid FROM mall_goods WHERE cate='{$cate}' && number='{$number}'";
			$uid = $mysql->get_one($sql);			
		}
		if(!$uid) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
    }
	$sql = "SELECT cate FROM mall_goods WHERE uid='{$uid}'";
	if(substr($mysql->get_one($sql),0,3)=='999') movePage("coop_goods_write.php?mode=modify&uid={$uid}");

	if($uid>9999) $tmp_uid = floor($uid/10000);
}		
else {
	$sql = "SELECT MAX(uid) FROM mall_goods";
	$maxUid = $mysql->get_one($sql);
	if($maxUid>9999) $tmp_uid = floor($maxUid/10000);	
}

if($tmp_uid) {
	$up_dir .= $tmp_uid."/";
	if(!is_dir($up_dir)) mkdir($up_dir,0707);	
	$up_path .= $tmp_uid."/";
	if(!is_dir($up_path)) mkdir($up_path,0707);	
}

##################### addstring ############################
if($field && $word) $addstring = "&field=$field&word={$word}";
if($seccate) $addstring .= "&seccate={$seccate}";
if($brands) $addstring .="&brands={$brands}";
if($smoney1 && $smoney2) $addstring .= "&smoney1={$smoney1}&smoney2={$smoney2}";
if($sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($s_qty) $addstring .="&s_qty={$s_qty}";


// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./goods_write.html");
$tpl->scan_area("main");

######################## 분류 생성 ##############################
$tmps1	= "CATEname = [";
$tmps2	= "CATEnum	= [";
$cnts=0;
$sql = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = 1 && cate!='999000000000' ORDER BY number ASC";
$mysql->query($sql);
while($row=$mysql->fetch_array()){    
	$row['cate_name'] = addslashes($row['cate_name']);
	if($row['cate_sub']==1) {
		if($cnts==1) { 
			$tmps1.= ",['{$row[cate_name]}→'";		
			$tmps2.= ",['{$row[cate]}:O'";		
		}
		else { 
			$tmps1.= "['{$row[cate_name]}→'";		
			$tmps2.= "['{$row[cate]}:O'";		
		}
		$sql2 = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = '2' AND cate_parent = '{$row[cate]}' ORDER BY number ASC";
		$mysql->query2($sql2);
		while($row2=$mysql->fetch_array(2)){
			$row2['cate_name'] = addslashes($row2['cate_name']);
			if($row2['cate_sub']==1) {
				$tmps1.= ",['{$row2[cate_name]}→'";	
				$tmps2.= ",['{$row2[cate]}:O'";	
				$sql3 = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = '3' AND cate_parent = '{$row2[cate]}' ORDER BY number ASC";
				$mysql->query3($sql3);
				while($row3=$mysql->fetch_array(3)){
					$row3['cate_name'] = addslashes($row3['cate_name']);
					if($row3['cate_sub']==1) {
						$tmps1.= ",['{$row3[cate_name]}→'";	
						$tmps2.= ",['{$row3[cate]}:O'";	
						$sql4 = "SELECT cate,cate_name FROM mall_cate WHERE cate_dep = '4' AND cate_parent = '{$row3[cate]}' ORDER BY number ASC";
						$mysql->query4($sql4);						
						while($row4=$mysql->fetch_array(4)){							
							$row4['cate_name'] = addslashes($row4['cate_name']);
							$tmps1.= ",'{$row4[cate_name]}'";
							$tmps2.= ",'{$row4[cate]}'";
						}
						$tmps1.= "]";
						$tmps2.= "]";
					} 
					else {
						$tmps1.= ",['{$row3[cate_name]}']";		
						$tmps2.= ",['{$row3[cate]}:X']";		
					}					
				}
				$tmps1.= "]";
				$tmps2.= "]";
			} 
			else {
				$tmps1.= ",['{$row2[cate_name]}']";		
				$tmps2.= ",['{$row2[cate]}:X']";		
			}
		}
	} 
	else {
		if($cnts==1) {
			$tmps1.= ",['{$row[cate_name]}'";		
			$tmps2.= ",['{$row[cate]}:X'";		
		} 
		else {
			$tmps1.= "['{$row[cate_name]}'";		
			$tmps2.= "['{$row[cate]}:X'";		
		}
	
	}
	$tmps1.= "]";
	$tmps2.= "]";		
	$cnts=1;		
}
$tmps1.= "]";
$tmps2.= "]";	

$tmps3	= str_replace("CATEname = [","CATEname2 = [[' ==== 1차분류 ==== ',[' ==== 2차분류 ==== ',[' ==== 3차분류 ==== ',' ==== 4차분류 ==== ']]],",$tmps1);
$tmps4	= str_replace("CATEnum	= [","CATEnum2	= [['',['',['','']]],",$tmps2);	
$tmps4	= str_replace(array(":O",":X"),"",$tmps4);	
######################## 분류 생성 ##############################

if(!$mode) {	//상품등록
	$DSP21 = "checked";
	$DSP32 = "checked";
	$DSP42 = "checked";
	$DSP50 = "checked";
	$DSP60 = "checked";
	$DSP70 = "checked";
	$DISA1	= "disabled";
	$DISA2	= "disabled";
	$DISA3	= "disabled";
	$CKD1B	= "checked";
	$SECB11 = "selected";
	$SECB23 = "selected";
	$op_goods_cnt = isset($LIST_DEFINE['related']) ? $LIST_DEFINE['related'] : 5;
	$sequence = 3;
} 
else {	//상품수정

	$sql="SELECT * FROM mall_goods WHERE uid='{$uid}'";
	if(!$row = $mysql->one_row($sql)) alert('상품이 없거나 삭제 되었습니다. 다시한번 확인 해 보시기 바랍니다.','back');

	$CATE_LOC = getMLocation($row['cate'],1);
	
	$brand			= $row['brand'];
	$special		= $row['special'];
	$sequence		= $row['sequence'];
	$name			= stripslashes($row['name']);
	$name			= str_replace("\"","&#034;",$name);
	$name			= str_replace("'","&#039;",$name);
	$add_msg		= stripslashes($row['add_msg']);
	$model			= stripslashes($row['model']);
	$model			= str_replace("\"","&#034;",$model);
	$model			= str_replace("'","&#039;",$model);
	$comp			= stripslashes($row['comp']);
	$made			= stripslashes($row['made']);
	$price			= $row['price'];
	$consumer_price	= $row['consumer_price'];
	$price_ment		= stripslashes($row['price_ment']);
	$qty			= $row['qty'];
	$def_qty		= $row['def_qty'];    
	$GOODS_NUM		= $row['uid'];
	$LINKS			= "http://{$_SERVER["HTTP_HOST"]}/{$ShopPath}index.php?channel=view&amp;uid={$row['uid']}";
	$unit			= $row['unit'];    
	
	${"DSP2".$row['s_qty']} = "checked";
	if($row['s_qty']!='4') $DISA1 = "disabled";
	$reserve = explode("|",$row['reserve']);
	${"DSP3".$reserve[0]} = "checked";
	if($reserve[0] !='3') $DISA2 = "disabled";
	$reserve = $reserve[1];
	$carriage = explode("|",$row['carriage']);
	${"DSP4".$carriage[0]} = "checked";
	if($carriage[0] !='3') $DISA3 = "disabled";
	$carriage = $carriage[1];

	$tmps = explode("|",stripslashes($row['op_goods_type']));
	${"CKD1".$tmps[0]} = "checked";
	if($tmps[1]) {
		${"SECB1".$tmps[1]} = "selected";
		${"SECB2".$tmps[2]} = "selected";
		$op_goods_cnt = $tmps[3];
	}

	if($row['op_goods'])	$op_goods = stripslashes($row['op_goods']);

	$explan = stripslashes($row['explan']);
	$tag	= stripslashes($row['tag']);
	$tag	= str_replace("\"","&#034;",$tag);
	$tag	= str_replace("'","&#039;",$tag);
	$tag	= substr($tag,1,-1);

	$display = explode("|",$row['display']);
	${"DSP5".$display[0]} = "checked";
	${"DSP6".$display[1]} = "checked";
	${"DSP7".$display[2]} = "checked";

	$icon = $row['icon'];

	if($row['other_image']) $_COOKIE['tmp_gdir'] = previlEncode($row['other_image']);
	if(is_dir("{$up_dir}goods_{$row['uid']}/")) $_COOKIE['tmp2_dir'] = previlEncode("{$up_dir}goods_{$row['uid']}/");

	for($i=1;$i<6;$i++){
		$tmps = $row["image{$i}"];
		
		if($tmps) {
			$img_size = $IMG_DEFINE['img'.$i];
			if($img_size>130) $img_size = 130;			
			${"image".$i} = "<img src='{$img_path}{$tmps}' width='{$img_size}' height='{$img_size}' border=0>"; 
			$size=@GetImageSize($img_path.$tmps);
			${"img_size".$i} = "<font class=eng>{$size[0]}px * {$size[1]}px </font>";
			//if($i>4) ${"img_size".$i}.= "(<font class=small>삭제</font><input type=checkbox name=del_img{$i} value=1 onfocus='blur();' align=absmiddle>)"; 
		}
	}
	
	if($row['special']) {
		$tmps = substr($row['special'],1,-1);
		$tmps = explode(",",$tmps);
		for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
			if($tmps[$i]) {
				$SPECIAL = $tmps[$i];
				if($SPECIAL) $tpl->parse("loop_special");
			}
		}
	}
	######################## 전시분류 설정 ############################
	if($row['mcate']) {
		$tmps = substr($row['mcate'],1,-1);
		$tmps = explode(",",$tmps);
		for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
			$MCATE = $tmps[$i];

			$sql = "SELECT count(*) FROM mall_cate WHERE cate='{$MCATE}'";
			if($mysql->get_one($sql)==0) continue;

			if(substr($MCATE,3,9)=='000000000') {					
				$MCATE1 = $MCATE;
				$MCATE2 = " ==== 2차분류 ==== ";
				$MCATE3 = " ==== 3차분류 ==== ";
				$MCATE4 = " ==== 4차분류 ==== ";					
			}
			else if(substr($MCATE,6,6)=='000000') {
				$MCATE1 = substr($MCATE,0,3)."000000000";
				$MCATE2 = $MCATE;
				$MCATE3 = " ==== 3차분류 ==== ";	
				$MCATE4 = " ==== 4차분류 ==== ";	
			}
			else if(substr($MCATE,9,3)=='000') {
				$MCATE1 = substr($MCATE,0,3)."000000000";
				$MCATE2 = substr($MCATE,0,6)."000000";
				$MCATE3 = $MCATE;
				$MCATE4 = " ==== 4차분류 ==== ";	
			}
			else {
				$MCATE1 = substr($MCATE,0,3)."000000000";
				$MCATE2 = substr($MCATE,0,6)."000000";
				$MCATE3 = substr($MCATE,0,9)."000";
				$MCATE4 = $MCATE;
			}					
			$tpl->parse("loop_mcate");
		}
	}
	######################## 전시분류 설정 ############################

	######################## 필수정보 설정 ############################
	$sql = "SELECT * FROM mall_goods_info WHERE guid='{$uid}' ORDER BY o_num ASC";
	$mysql->query($sql);
	while($row2 = $mysql->fetch_array()){
		$opName1 = $row2['name1'];
		$opName1 = str_replace("\"","&#034;",$opName1);
		$opName1 = str_replace("'","&#039;",$opName1);
		$opContent1 = $row2['content1'];
		$opContent1 = str_replace("\"","&#034;",$opContent1);
		$opContent1 = str_replace("'","&#039;",$opContent1);
		$opName2 = $row2['name2'];
		$opName2 = str_replace("\"","&#034;",$opName2);
		$opName2 = str_replace("'","&#039;",$opName2);		
		$opContent2 = $row2['content2'];
		$opContent2 = str_replace("\"","&#034;",$opContent2);
		$opContent2 = str_replace("'","&#039;",$opContent2);
		$opUid2 = $row2['uid'];
		if($opName2=='x') $opTypes = 1;
		else $opTypes = 2;
		$tpl->parse("loop_info");		
	}
	$opTypes = $opName1 = $opContent1 = $opName2 = $opContent2 = $opUid2 = '';
	######################## 필수정보 설정 ############################
	
	######################## 옵션 설정 ############################
	$sql = "SELECT option1 FROM mall_goods_option WHERE guid='{$uid}' GROUP BY option1 ORDER BY o_num ASC";
	$mysql->query($sql);
	
	$option_arr = Array();
	while($row2 = $mysql->fetch_array()){
		$option_arr[] = $row2['option1'];
	}
		
	for($i=0,$cnt=count($option_arr);$i<$cnt;$i++) {
		$sql = "SELECT * FROM mall_goods_option WHERE guid='{$uid}' && option1='{$option_arr[$i]}' ORDER BY o_num ASC";
		$mysql->query($sql);
		while($row2 = $mysql->fetch_array()){
			$opType1 = $row2['option1'];
			$opType2 = $row2['option2'];
			$opType2 = str_replace("'","&#039;",$opType2);
			$opPrice = $row2['price'];
			$opDisplay = $row2['display'];
			$opQty = $row2['qty'];
			$opCode = $row2['code'];
			$opUid = $row2['uid'];
			$tpl->parse("loop_options");
		}
	}
	$opType1 = $opType2 = $opPrice = $opDisplay = $opQty = $opCode = $opUid = '';
	######################## 옵션 설정 ############################


	######################## 트랙백 설정 ############################
	$sql = "SELECT * FROM mall_tb_send WHERE gid='{$uid}' ORDER BY uid DESC";
	$mysql->query($sql);	

	while($data=$mysql->fetch_array()){
		$tb_uid = $data['uid'];
		$tb_olink = $data['link'];
		$tb_link = explode("/",$data['link']);
		$tb_link = $tb_link[2];
		$tb_title = stripslashes($data['title']);
		$tb_posts = $data['posts'];
		$tb_date = date("m-d H:i",$data['signdate']);
		$tpl->parse("loop_tb");
		$tb_disp = 'display:none';
	}

	if(!$tb_disp) $tb_disp = "display:block;";
	$tpl->parse("is_mod5");
	######################## 트랙백 설정 ############################

}	//end of mode

######################## 브랜드 설정 ############################
$sql = "SELECT uid, name FROM mall_brand ORDER BY name ASC";
$mysql->query($sql);
$BRAND_SELECT = "<option value=''> ==== 선택 ==== </option>";
while($row=$mysql->fetch_array()){
	if($row['uid'] == $brand) $sec = 'selected';
	else $sec='';
	$row['name'] = stripslashes($row['name']);
	$row['name'] = str_replace("\"","&#034;",$row['name']);
	$row['name'] = str_replace("'","&#039;",$row['name']);
	$BRAND_SELECT.= "<option value='{$row[uid]}' {$sec}>{$row['name']}</option>\n";
}	

######################## 기획전 설정 ############################
$sql = "SELECT uid, name FROM mall_special ORDER BY uid DESC";
$mysql->query($sql);
$SPECIAL_SELECT = "<option value=''> ==== 선택 ==== </option>";
while($row=$mysql->fetch_array()){	
	$row['name'] = stripslashes($row['name']);
	$row['name'] = str_replace("\"","&#034;",$row['name']);
	$row['name'] = str_replace("'","&#039;",$row['name']);
	$SPECIAL_SELECT.= "<option value='{$row[uid]}'>{$row['name']}</option>\n";
}

######################## 옵션 설정 ############################
$sql = "SELECT name FROM mall_goods_conf WHERE mode='O'";
$mysql->query($sql);
$OPTION_SELECT = "<option value=''>직접입력</option>";
while($row=$mysql->fetch_array()){
	$OPTION_SELECT.= "<option value='{$row[name]}' {$sec}>".stripslashes($row['name'])."</option>\n";
}	

######################## 제조사 설정 ############################
$sql = "SELECT name FROM mall_goods_conf WHERE mode='C'";
$mysql->query($sql);
$COMP_SELECT = "<option value=''>선택</option>";
while($row=$mysql->fetch_array()){
	$COMP_SELECT.= "<option value='{$row[name]}' {$sec}>".stripslashes($row['name'])."</option>\n";
}	

######################## 원산지 설정 ############################
$sql = "SELECT name FROM mall_goods_conf WHERE mode='M'";
$mysql->query($sql);
$MADE_SELECT = "<option value=''>선택</option>";
while($row=$mysql->fetch_array()){
	$MADE_SELECT.= "<option value='{$row[name]}' {$sec}>".stripslashes($row['name'])."</option>\n";
}	

######################## 판매단위 설정 ############################
$sql = "SELECT name FROM mall_goods_conf WHERE mode='U'";
$mysql->query($sql);
$UNIT_SELECT = "";
$i=0;
while($row=$mysql->fetch_array()){
	if($row['name'] == $unit) { $sec = 'selected'; $UNIT = $row['name']; }
	else $sec='';
	if(!$unit && $i==0) $UNIT = $row['name'];
	$UNIT_SELECT.= "<option value='{$row[name]}' {$sec}>".stripslashes($row['name'])."</option>\n";
	$i=1;
}	

######################## 아이콘 설정 ############################
$sql = "SELECT uid,name FROM mall_goods_conf WHERE mode='I' ORDER BY uid ASC";
$mysql->query($sql);

while($row=$mysql->fetch_array()){	
	if(eregi($row[name],$icon)) $sec = 'checked';
	else $sec='';	
	$ICON_LIST.= "<div style='float:left;'><div style='float:left'><input type=checkbox name=icon_arr[] onfocus='blur();' value='{$row[name]}' {$sec}/></div><div style='float:left;padding-top:2px;padding-right:12px;'>".imgSizeCh($icon_path,$row['name'],50)."</div>";
}
if(!$ICON_LIST) $ICON_LIST = '등록된 아이콘이 없습니다.';

unset($sec);

$ISIZE1 = $SKIN_DEFINE['img1'];
$ISIZE2 = $SKIN_DEFINE['img2'];
$ISIZE3 = $SKIN_DEFINE['img3'];
$ISIZE4 = $SKIN_DEFINE['img4'];
$ISIZE5 = $SKIN_DEFINE['img5'];
$ISIZEOT = $SKIN_DEFINE['other'];
$o_size1 = $IMG_DEFINE['other_ms'];
$o_size2 = $IMG_DEFINE['other_s'];

################ 생성된 폴더 확인 및 생성 ################
if(!$_COOKIE['tmp_gdir']) {
	$tmp_gdir = $up_path.date("Ymd_his").getCode(4);
	if(!is_dir($tmp_gdir)) mkdir($tmp_gdir,0707);	
	$tmp_gdir2 = previlEncode($tmp_gdir);
	SetCookie("tmp_gdir",$tmp_gdir2,0,"/");
} 
else {		
	$tmp_gdir2 = $_COOKIE['tmp_gdir'];
	$tmp_gdir = previlDecode($tmp_gdir2);

	if(!is_dir($tmp_gdir)) mkdir($tmp_gdir,0707);
	
	$handle	= @opendir($tmp_gdir);
	$cnts = 0;
	while ($file = @readdir($handle)) {
		if($file != '.' && $file != '..' && is_file("{$tmp_gdir}/{$file}") && !eregi("_Pthum",$file)) {
			$fileSize = getFilesize("{$tmp_gdir}/{$file}");
			$ofileSize = @filesize("{$tmp_gdir}/{$file}");

			$TMP_DIR = urlencode($file)."|".$file."|".$fileSize."|".$ofileSize;
			$tpl->parse("is_tmp_gdir");				
			$cnts++;
		}		
	}
	@closedir($handle);	
}

if(!$_COOKIE['tmp2_dir']) {
	$tmp2_dir = $up_dir.date("Ymd_his").getCode(4)."/";
	if(!is_dir($tmp2_dir)) mkdir($tmp2_dir,0707);	
	$tmp2_dir2 = previlEncode($tmp2_dir);
	SetCookie("tmp2_dir",$tmp2_dir2,0,"/");
} 
else {		
	$tmp2_dir2 = $_COOKIE['tmp2_dir'];
	$tmp2_dir = previlDecode($tmp2_dir2);

	if(!is_dir($tmp2_dir)) mkdir($tmp2_dir,0707);
}
################ 생성된 폴더 확인 및 생성 ################


$back = isset($_GET['back']) ? $_GET['back'] : "goods_list";

if(!$mode) {
    $tpl->parse("is_write1");	
	$tpl->parse("is_write2");
	$tpl->parse("is_write3");
	$tpl->parse("is_write4");
	$tpl->parse("is_write5");
	$tpl->parse("is_write6");
	$tpl->parse("is_jwrite1");
	$tpl->parse("is_jwrite2");
} 
else {
	$tpl->parse("is_mod1");
	$tpl->parse("is_mod2");	
	$tpl->parse("is_mod3");
	$tpl->parse("is_mod41");
	$tpl->parse("is_mod42");
	$tpl->parse("is_mod6");
}
$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html"; // 하단 HTML
?>
