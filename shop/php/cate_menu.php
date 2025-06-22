<?
include "{$skin}/skin_define.php";

$tpl->define("main","{$skin}/cate_menu.html");
$tpl->scan_area("main");

$sql = "SELECT cate,cate_name,cate_sub,img2,img3 FROM mall_cate WHERE cate_dep ='1' && valid ='1' && cate != '999000000000' ORDER BY number ASC";
$mysql->query($sql);

$CNUM = 0;
$ck_top = $SKIN_DEFINE['menu_top'];

while($row_cate = $mysql->fetch_array()){	
	if($row_cate['cate_sub']==1) {	     		
		$sql = "SELECT * FROM mall_cate WHERE cate_parent = '{$row_cate[cate]}' && valid ='1' ORDER BY number ASC";
		$mysql->query2($sql);

		$ck_sub		= 1;
		while($sub_cate = $mysql->fetch_array('2')) {
			if($sub_cate['cate_sub']==0) $channel_type = 'list';
			else $channel_type = "main2";
			$channel_cate = $sub_cate['cate'];	
               
			if($sub_cate['img2']){
				$SCATE_NAME = "<img id='cs_{$ck_sub}' src='image/cate/{$sub_cate[img2]}' alt='".stripslashes($sub_cate['cate_name'])."' ";
				if($sub_cate['img3']) {
					$SCATE_NAME .= " onmouseover=\"this.src='image/cate/$sub_cate[img3]'\" onmouseout=\"this.src='image/cate/$sub_cate[img2]'\" />"; 
				} 		
				else $SCATE_NAME .= " />";
            } 
			else {
				$tpl->parse("is_text2","1");
				$SCATE_NAME = stripslashes($sub_cate['cate_name']);
			}			
			$tpl->parse("loop_scate");
			$ck_sub++;
		}
		
		if($ck_sub==1) $tpl->parse("no_scate","1");

		$ck_top2 = $ck_top + $SKIN_DEFINE['menu_arrow'];

		/***********************  BANNER  ********************************/
		$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '5' && status='1' && cate='{$row_cate[cate]}' ORDER BY rank ASC";
		$mysql->query2($sql);		
		while($row_ban = $mysql->fetch_array('2')){
			if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
			if($row_ban['link']) {
				$BLINK = str_replace("&","&amp;",$row_ban['link']);
				if($row_ban['target']=='2') $BTARGET = "target='_blank'";
				else $BTARGET = "";
			}
			else $BLINK = "#\" onclick=\"return false;";

			$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner4'],stripslashes($row_ban['name']));
			$tpl->parse("cate_banner");
		}			
		unset($BLINK, $BTARGET, $BLINK);
		/***********************  BANNER  ********************************/


		$tpl->parse("loop_smenu");
		$tpl->parse("loop_scate","2");
		$tpl->parse("no_scate","2");
		$tpl->parse("cate_banner","2");

		$LAYER = "onmouseover=\"openCM('{$CNUM}');\" onmouseout=\"swapImgRestore();closeCM('{$CNUM}','1');\"";	
	} 
	else $LAYER = "";

	$CATE_NAME = "<img "; 
	
	$channel_cate = $row_cate['cate'];	
	if($row_cate['cate_sub']==0) $channel_type= 'list';			
	else $channel_type = "main2";
	
	if($row_cate['img2']){ 
		$CATE_NAME .= "id='cm_{$CNUM}' src='image/cate/{$row_cate[img2]}' alt='".stripslashes($row_cate['cate_name'])."' />";		
		if($row_cate['img3']) {				
			$LAYER = "onmouseover=\"openCM('{$CNUM}','image/cate/{$row_cate[img2]}','image/cate/{$row_cate[img3]}');\" onmouseout=\"swapImgRestore();closeCM('{$CNUM}','1');\""; 
		} 
		
		$size = @GetImageSize("image/cate/{$row_cate[img2]}");        
		
		if($size[1]<$SKIN_DEFINE['menu_height']) $menu_height = $size[1]+(23-$size[1]);
		else $menu_height = $SKIN_DEFINE['menu_height'];

	} 
	else { 
		$CATE_NAME	 = stripslashes($row_cate['cate_name']); 				
		$menu_height = $SKIN_DEFINE['menu_height'];
		$tpl->parse("is_text","1");
	}
		
	$CNUM++;
	$tpl->parse("loop_cate");
	if($SKIN_DEFINE['cate_type']==2) $tpl->parse("loop_smenu","2");
	$ck_top += $menu_height;		
}

if($CNUM==0) $tpl->parse('no_cate');


$sql	= "SELECT code FROM mall_design WHERE mode='W'";
$tmp	= $mysql->get_one($sql);
$ssl	= explode("|*|",$tmp);
$sMain = ".";
if($ssl[0]==1) {
	$tmp    = explode("|",$ssl[2]);	
	if(in_array(2,$tmp)) {
		if($ssl[1]) $sport = ":{$ssl[1]}";
		$sMain = "https://".$_SERVER["SERVER_NAME"]."{$sport}/{$ShopPath}";	
		unset($sport);
	}
}

if($my_id) $tpl->parse("is_clogout");
else $tpl->parse("is_clogin");

/***********************  BRAND  ********************************/
$sql = "SELECT code FROM mall_design WHERE mode='M'";
$tmp_dsp = explode("|*|",stripslashes($mysql->get_one($sql)));

if($tmp_dsp[25]=='1') {
	$sql = "SELECT uid,name,img1 FROM mall_brand ORDER BY name ASC";
	$mysql->query($sql);

	while($data=$mysql->fetch_array()){
		$NUM = $data['uid'];		
		$data['name'] = stripslashes($data[name]);
		
		if($data['img1'] && file_exists("image/brand/{$data['img1']}")) {
			$data['img1'] = urlencode($data['img1']);
			$BRAND = "<img src='image/brand/{$data['img1']}' border='0' height='28' alt='{$data['name']}' title='{$data['name']}' />";
		}
		else {
			$tpl->parse("is_brand_text","1");
			$BRAND = $data['name'];	
		}

		$tpl->parse("loop_brand");
		$tpl->parse("is_brand_text","2");
	}
	$tpl->parse("is_brand");
}
unset($tmp_dsp, $BRAND);
/***********************  BRAND  ********************************/


/***********************  BOARD LIST  ********************************/
if($tpl->getBlstring()) {
	$bl_string = explode(",",$tpl->getBlstring());
		
	for($ii=0,$cnt=count($bl_string);$ii<$cnt;$ii++) {
		
		$i = 0;
		$bl_string2 = explode("|",$bl_string[$ii]);
		$b_name = $bl_string2[0];
		$b_limit = !empty($bl_string2[1]) ? $bl_string2[1] : 5;

		$sql = "SELECT no,subject FROM pboard_{$b_name} WHERE idx < 999 && idx > 0 limit 0,{$b_limit}";
		$mysql->query($sql);

		while($data = $mysql->fetch_array()){
			$LINK  = "{$Main}?channel=board&amp;code={$b_name}&amp;pmode=view&amp;no={$data['no']}";
			$BOARD = htmlspecialchars(stripslashes($data['subject']));
			$tpl->parse("loop_board_{$b_name}");
		
		}
	}
}

if(!in_array($channel,array("main2","list","view"))){
	/***********************  BANNER  ********************************/
	$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '9' && status='1' ORDER BY rank ASC";
	$mysql->query($sql);
	while($row_ban = $mysql->fetch_array()){
		if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
		if($row_ban['link']) {
			$BLINK = str_replace("&","&amp;",$row_ban['link']);
			if($row_ban['target']=='2') $BTARGET = "target='_blank'";
			else $BTARGET = "";
		}
		else $BLINK = "#\" onclick=\"return false;";

		$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner3'],stripslashes($row_ban['name']));
		$tpl->parse("loop_banner");	
	}
	$tpl->parse("is_banner");	
	unset($row_ban,$BANNER,$BLINK,$BTARGET);
	/***********************  BANNER  ********************************/
}

if($my_id) $tpl->parse("is_logout");
else $tpl->parse("is_login");

if($channel) $tpl->parse("is_sub");
else $tpl->parse("is_main");

$tpl->parse('main');
$CATE_MENU = $tpl->tprint('main','1');
$tpl->close();

unset($CATE_NAME,$row_cate,$sub_cate,$channel_cate,$channel_type,$ck_top,$menu_height,$size,$bl_string2,$b_name,$b_limit,$LINK,$BOARD,$ssl,$sMain);
?>