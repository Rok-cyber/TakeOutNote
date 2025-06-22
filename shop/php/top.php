<?
$img_path = "./image/design/";
$sql = "SELECT code FROM mall_design WHERE mode='C'";
$common = explode("|",$mysql->get_one($sql));
//0->logo,1->copy,2->copy_type,3->menu_type,4->flash, 5->menu

#################### 로고 ########################
if($common[0]) {
	$ext = getExtension($common[0]); 
    if($ext =='swf') {   //플래쉬 파일일때
		$T_LOGO = imgSizeCh($img_path,$common[0]);			
    } 
	else {
        $T_LOGO ="<a href='{$Main}' onfocus='this.blur();'>".imgSizeCh($img_path,$common[0],'','','','logo')."</a>";
    }	      
     	  
}

$B_COPY = $img_path.$common[1];

########################### 상단 메뉴 #################
if($common[3]==1 && $common[4]) {
	$T_MENU = imgSizeCh($img_path,$common[4]);		
} 
else {
	$menu_img = explode(",",$common[5]);

	for($i=0;$i<10;$i++){
		$tmp_i = $i*3;
		if($menu_img[$tmp_i]) {
			if($menu_img[$tmp_i+2]) {
				$M_LINK1 = "<a href='".$menu_img[$tmp_i+2]."' onfocus='this.blur();'>";
				$M_LINK2 = "</a>";
			}
			if($menu_img[$tmp_i+1]) $M_OVER = "onmouseover=\"menu_top{$i}.src='{$img_path}".$menu_img[$tmp_i+1]."'\" onmouseout=\"menu_top{$i}.src='{$img_path}".$menu_img[$tmp_i]."'\"";
			else $M_OVER = "";
			
			$T_MENU .= "</li><li>{$M_LINK1}<img src='{$img_path}".$menu_img[$tmp_i]."' border='0' name='menu_top{$i}' $M_OVER alt='Top Menu{$i}' style='vertical-align:top;' />{$M_LINK2}";
		}
		unset($M_LINK1,$M_LINK2);
	}
}

if($common[6]) $SEARCHBG = $img_path."/".$common[6];
else $SEARCHBG = '';

$tpl->define("main","{$skin}/top.html");
$tpl->scan_area("main");

####################################### 실시간 검색어 랭킹 #########################################

$sday1 = time() - (86400*2);
$sday2 = time() - 86400;

$sql = "SELECT word,  count(*) as cnt FROM mall_search WHERE signdate between {$sday1} AND {$sday2} GROUP BY word ORDER BY cnt DESC, word limit 10";
$mysql->query($sql);
for ($i=1; $row=$mysql->fetch_array(); $i++) {    
    $oword = stripslashes($row['word']);
	$orank[$oword] = $i;	
}

$sql = "SELECT word,  count(*) as cnt FROM mall_search WHERE signdate > {$sday2} GROUP BY word ORDER BY cnt DESC, word limit 10";
$mysql->query($sql);

$i = 1;
while($row = $mysql->fetch_array()){
	$NAME = stripslashes($row['word']);		
	if($orank[$NAME]) {
		$rank = $i - $orank[$NAME];    
		if($rank==0) {
			$NUM = 0;
			$CHANGE = "mid";
		} else if($rank<0) { 
			$NUM = -$rank;
			$CHANGE = "up";
		} 
		else {
			$NUM = $rank; 
			$CHANGE = "down";
		}
	} 
	else {
		$NUM = '';
		$CHANGE = "new";;
	}		

	if($i>5) $DISP = "display:none;";
	$tpl->parse("loop_rank1");
	$tpl->parse("loop_rank2");	
	$i++;
	$oname[] = $NAME;
}

if($i<11) {	
	$tmps = explode(",",$basic[13]);
	$NUM = '';
	$CHANGE = '';		
		
	for($k=0;$i<11;$i++) {		
		$NAME = $tmps[$k];		
		$k++;		
		if(@in_array($NAME,$oname)) {
			$i--;
			continue;		
		}
		if($i>5) $DISP = "display:none;";
		$tpl->parse("loop_rank1");
		$tpl->parse("loop_rank2");		
	}
}

/***********************  BANNER  ********************************/
$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '8' && status='1' ORDER BY rank ASC";
$mysql->query($sql);
while($row_ban = $mysql->fetch_array()){
	if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
	if($row_ban['link']) {
		$BLINK = str_replace("&","&amp;",$row_ban['link']);
		if($row_ban['target']=='2') $BTARGET = "target='_blank'";
		else $BTARGET = "";
	}
	else $BLINK = "#\" onclick=\"return false;";

	$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner8'],stripslashes($row_ban['name']));
	$tpl->parse("loop_banner");		
}
unset($BLINK, $BTARGET, $BLINK);
/***********************  BANNER  ********************************/

if($my_id) $tpl->parse("is_logout");
else $tpl->parse("is_login");

if(!$channel) $tpl->parse("is_main");
else $tpl->parse("is_sub");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

unset($data,$tmps1,$tmps2,$T_TITLE,$CATE_TABLE,$sday1,$sday2,$rank,$orank,$DISP,$oname,$NAME);


?>