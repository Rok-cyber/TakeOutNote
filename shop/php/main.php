<?
$sql = "SELECT code FROM mall_design WHERE mode='M'";
$main_dsp = explode("|*|",stripslashes($mysql->get_one($sql)));

/******************** 설정 설명 *************************
0->메인이미지,1->인기상품사용유무,2->추천상품사용유무,3->추천상품갯수,4->신상품사용유무,5->신상품갯수
6~13->삽입코드 유무,코드 , 14->이미지사용유무, 15->인기상품갯수, 16~19->사입코드2 유무,코드, 20->메인하단박스 사용유무, 21~24->삽입코드 유무,코드, 25->브랜드 사용유무, 26->메인이미지 링크,27->공동구매상품사용유무,28->공동구매상품갯수,29,30->공구상품아래삽입코드
******************** 설정 설명 *************************/

include "php/cate_menu.php";  // CATE MENU

$tpl->define("main","{$skin}/main.html");  //Template 
$tpl->scan_area("main");

/***************  메인 이미지 *******************/
if($main_dsp[0] && $main_dsp[14]==1) {	
    $ext = getExtension($main_dsp[0]); 
    if($ext=='swf') {   //플래쉬 파일일때
		$sizes = @GetImageSize("image/design/{$main_dsp[0]}");
		$M_IMG = "
			<script  type='text/javascript'> 
				setem = new setEmbed(); 
				setem.init('flash','image/design/{$main_dsp[0]}','{$sizes[0]}','{$sizes[1]}');				
				setem.parameter('wmode','transparent'); 
				setem.show(); 
			</script> 
			";
    } 
	else {
		$M_IMG = "<img src='image/design/{$main_dsp[0]}' border='0' alt='Main image' />";
		if($main_dsp[26]) {
			if(!eregi("http://",$main_dsp[26]) && !eregi("/index.php",$main_dsp[26])) $main_dsp[26]="http://".$main_dsp[26];
			$main_dsp[26] = str_replace("&","&amp;",$main_dsp[26]);
			$M_IMG = "<a href='{$main_dsp[26]}'>{$M_IMG}</a>";
		}		
	}
	$tpl->parse("is_m_img");
} 

/***********************  BANNER  ********************************/
for($i=1;$i<3;$i++) {
	$sql = "SELECT name, banner,link,target,edate FROM mall_banner WHERE location = '{$i}' && status='1' ORDER BY rank ASC";
	$mysql->query($sql);
	$j = 1;
	while($row_ban = $mysql->fetch_array()){
		if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
		if($row_ban['link']) {
			$BLINK = $FBLINK = str_replace("&","&amp;",$row_ban['link']);
			if($row_ban['target']=='2') {
				$BTARGET = "target='_blank'";
				$FBTARGET = "_blank";
			}
			else {
				$BTARGET = "";
				$FBTARGET = "_self";
			}
		}
		else {
			$BLINK = "#\" onclick=\"return false;";
			$FBLINK = "#";
		}

		$BANNER = imgSizeCh('image/banner/',$row_ban['banner'],'','',$IMG_DEFINE['banner'.$i],stripslashes($row_ban['name']));
		$FBANNER = "http://".$_SERVER['HTTP_HOST']."/{$ShopPath}image/banner/".$row_ban['banner'];
		$Fj = $j - 1;	
		
		if($j==1) $ON = "_on";
		else $ON = "";

		$tpl->parse("loop_banner{$i}");
		$tpl->parse("loop_banner_icon{$i}");
		$j++;
	}
	if($i==2) {
		$j--;
		${"BWEIGHT".$i} = $IMG_DEFINE['banner'.$i]*$j;	
		if($j>0) $tpl->parse("is_banner{$i}");
	}
}
unset($BLINK, $BTARGET, $BLINK, $j, $ON);
/***********************  BANNER  ********************************/

if($main_dsp[6]=='1')	{ $HIT_UP_CODE	= $main_dsp[7];		$tpl->parse("is_hit_up");	}
if($main_dsp[8]=='1')	{ $RECO_UP_CODE = $main_dsp[9];		$tpl->parse("is_reco_up");	}
if($main_dsp[10]=='1')	{ $NEW_UP_CODE	= $main_dsp[11];	$tpl->parse("is_new_up");	}
if($main_dsp[12]=='1')	{ $COPY_UP_CODE = $main_dsp[13];	$tpl->parse("is_copy_up");	}
if($main_dsp[16]=='1')	{ $CATE_DOWN_CODE = $main_dsp[17];	$tpl->parse("is_cate_down");	}
if($main_dsp[18]=='1')	{ $BANNER_DOWN_CODE = $main_dsp[19];	$tpl->parse("is_banner_down");	}
if($main_dsp[21]=='1')	{ $NEW_DOWN_CODE = $main_dsp[22];	$tpl->parse("is_new_down");	}
if($main_dsp[29]=='1')	{ $COOPER_DOWN_CODE = $main_dsp[30];	$tpl->parse("is_cooper_down");	}
if($main_dsp[23]=='1')	{ $BOX_UP_CODE = $main_dsp[24];	$tpl->parse("is_box_up");	}

/************************* MAIN GOODS DISPLAY ******************************/
$hit_cnt	= explode(",",$SKIN_DEFINE['main_hit_cnt']);
$reco_cnt	= explode(",",$SKIN_DEFINE['main_reco_cnt']);
$new_cnt	= explode(",",$SKIN_DEFINE['main_new_cnt']);
$cooper_cnt	= explode(",",$SKIN_DEFINE['main_cooper_cnt']);

$disp_arr = Array('',$main_dsp[1],$main_dsp[2],$main_dsp[4],$main_dsp[27]);
$vcnt_arr = Array('',$hit_cnt[0],$reco_cnt[0],$new_cnt[0],$cooper_cnt[0]);
$tcnt_arr = Array('',$main_dsp[15],$main_dsp[3],$main_dsp[5],$main_dsp[28]);

for($k=1;$k<5;$k++) {	
	if($disp_arr[$k] != 0){
		$i = 0;
		$vcnt = $vcnt_arr[$k];
		$ON = "_on";
		$DSTYLE = '';
		if(!$tcnt_arr[$k]) $tcnt_arr[$k] = $hit_cnt[0];

		if($k==4) $where = "&& SUBSTRING(cate,1,3)='999' && SUBSTRING(display,3,1)='1' ";
		else $where = " && SUBSTRING(display,1,1)='{$k}'";

		$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,comp,image5,icon,event,reserve,s_qty,qty,coop_price,coop_sale FROM mall_goods WHERE s_qty!='3' && type='A' {$where} ORDER BY o_num1 ASC LIMIT ".$tcnt_arr[$k];
		$mysql->query($sql);

		while($data = $mysql->fetch_array()){			
			$gData	= getDisplay($data,'image5');		// 디스플레이 정보 가공 후 가져오기
			$LINK	= $gData['link'];
			$IMAGE	= $gData['image'];
			$NAME	= $gData['name'];
			$COMP	= $gData['comp'];
			$PRICE	= $gData['price'];
			$CPRICE	= $gData['cprice'];
			$CP_PRICE	= $gData['cp_price'];
			$ICON	= $gData['icon'];
			$DRAGD	= $gData['dragd'];
			$QLINK	= $data['uid'];
			$CATE	= $data['cate'];
			
			if($CP_PRICE>0) {
				$tpl->parse("is_coupon");
				$tpl->parse("is_coupon{$k}");
				$PRICE = $CP_PRICE;
			}

			if($CPRICE) {
				$tpl->parse("is_cprice");
				$tpl->parse("is_cprice{$k}");
			}

			$PRICE2 = str_replace("원","",$PRICE);
			if($k==4) {
				if($data['coop_price']==0) $COOP_PRICE = $PRICE2;
				else $COOP_PRICE = number_format($data['coop_price'],$ckFloatCnt);
				$COOP_SALE = $data['coop_sale'];				
			}

			if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) $tpl->parse("is_soldout");
					
			$i++;	
			if($i>$vcnt) { 
				$ON = '';
				if($disp_arr[$k]==3) $DSTYLE = "style='display:none'";
			}

			if($disp_arr[$k]==2) $tpl->parse("loop_sicon{$k}");
			else if($disp_arr[$k]==3 && $i%$vcnt==1) $tpl->parse("loop_sicon{$k}");

			$tpl->parse("loop_goods{$k}");	
			$tpl->parse("is_soldout","2");
			$tpl->parse("is_coupon","2");
			$tpl->parse("is_cprice","2");
			$tpl->parse("is_soldout{$k}","2");
			$tpl->parse("is_coupon{$k}","2");
			$tpl->parse("is_cprice{$k}","2");
		}	
		
		if($i==0) {
			$tpl->parse("no_goods{$k}");		
		}
		else {
			switch($disp_arr[$k]) {
				case "1" : 
					$BCLASS = "boxList";
					$SWIDTH = ($IMG_DEFINE['img5'] + 30) * $vcnt;
				break;
				case "2" :
					$BCLASS = "boxScroll";
					$mcnt = $i - $vcnt;
					$SWIDTH = ($IMG_DEFINE['img5'] + 30) * $i;
					
					$tpl->parse("is_scoll{$k}1");
					$tpl->parse("is_scoll{$k}2");				
					$tpl->parse("is_scoll{$k}3");
				break;
				case "3" :
					$BCLASS = "boxMove";
					$SWIDTH = ($IMG_DEFINE['img5'] + 30) * $vcnt;
					$tpl->parse("is_scoll{$k}1");
					$tpl->parse("is_scoll{$k}2");
					$tpl->parse("is_move{$k}");

				break;
			}
		}
		$tpl->parse("is_main_goods{$k}");

	}
}
unset($hit_cnt,$reco_cnt,$new_cnt,$where,$COOP_PRICE,$COOP_SALE);

if($main_dsp[20]=='1') {
	/*******************클릭 베스트 ****************************/
	$day1 = date("Ymd",strtotime('-1 WEEK', time()));	
	$cnt_limit	= $SKIN_DEFINE['main_click_cnt'] ? $SKIN_DEFINE['main_click_cnt'] : 3;

	$sql = "SELECT SUM(view) as sum, cno FROM mall_goods_view  WHERE date >= '{$day1}' GROUP BY cno ORDER BY sum DESC LIMIT {$cnt_limit}";
	$mysql->query($sql);
	
	$i = 1;

	while($data2 = $mysql->fetch_array()){		
		$tmp_cate = substr($data2['cno'],0,12);
		$tmp_number = substr($data2['cno'],12);
		
		$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,image4,image5,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && 
		type='A' && uid='{$tmp_number}'";

		if(!$data = $mysql->one_row($sql)) continue;
		
		$gData	= getDisplay($data,'image5');		// 디스플레이 정보 가공 후 가져오기
		$LINK	= $gData['link'];
		$IMAGE	= $gData['image'];
		$NAME	= $gData['name'];
		$COMP	= $gData['comp'];
		$PRICE	= $gData['price'];
		$CPRICE	= $gData['cprice'];
		$CP_PRICE	= $gData['cp_price'];
		$ICON	= $gData['icon'];
		$DRAGD	= $gData['dragd'];
		$QLINK	= $data['uid'];
		$CATE	= $data['cate'];
		
		if($CP_PRICE>0) {
			$tpl->parse("is_coupon");
			$PRICE = $CP_PRICE;
		}

		$PRICE2 = str_replace("원","",$PRICE);

		$tpl->parse("loop_goods5");	
		$tpl->parse("is_coupon","2");

		$i++;
	}
	/*******************클릭 베스트 ****************************/

	/*******************판매 베스트 ****************************/	
	$cnt_limit	= $SKIN_DEFINE['main_order_cnt'] ? $SKIN_DEFINE['main_order_cnt'] : 3;

	$sql = "SELECT COUNT(uid) as o_cnt, p_cate, p_number FROM mall_order_goods WHERE uid!='0' && signdate >= '{$day1}' GROUP BY p_number ORDER BY o_cnt DESC LIMIT {$cnt_limit}";
	$mysql->query($sql);

	$i = 1;
	while($data2 = $mysql->fetch_array()){			
		$tmp_cate = $data2['p_cate'];
		$tmp_number = $data2['p_number'];
		
		$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,image4,image5,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && 
		type='A' && uid='{$tmp_number}'";
		if(!$data = $mysql->one_row($sql)) continue;

		$gData	= getDisplay($data,'image5');		// 디스플레이 정보 가공 후 가져오기
		$LINK	= $gData['link'];
		$IMAGE	= $gData['image'];
		$NAME	= $gData['name'];
		$COMP	= $gData['comp'];
		$PRICE	= $gData['price'];
		$CPRICE	= $gData['cprice'];
		$CP_PRICE	= $gData['cp_price'];
		$ICON	= $gData['icon'];
		$DRAGD	= $gData['dragd'];
		$QLINK	= $data['uid'];
		$CATE	= $data['cate'];

		if($CP_PRICE>0) {
			$tpl->parse("is_coupon");
			$PRICE = $CP_PRICE;
		}

		$PRICE2 = str_replace("원","",$PRICE);

		$tpl->parse("loop_goods6");	
		$tpl->parse("is_coupon","2");					
		$i++;
	}
	/*******************판매 베스트 ****************************/	
	
	/*******************베스트 리뷰****************************/	
	$cnt_limit	= $SKIN_DEFINE['main_review_cnt'] ? $SKIN_DEFINE['main_review_cnt'] : 4;
	
	$sql = "SELECT a.uid,a.cate,a.number,a.name,a.price,a.consumer_price,a.price_ment,a.comp,a.image4,a.image5,a.icon,a.event,a.reserve,b.content FROM mall_goods a, mall_goods_point b WHERE a.s_qty!='3' && a.type='A' && a.uid=b.number && b.best='Y' ORDER BY rand() LIMIT {$cnt_limit}";

	$i = 0;
	$mysql->query($sql);
	while($data = $mysql->fetch_array()) { 

		$sql = "SELECT SUM(point)/COUNT(*) FROM mall_goods_point WHERE cate='{$data['cate']}' && number='{$data['uid']}'";
		$SUM_AFTER = round($mysql->get_one($sql)*2,1);
		$SUM_AFTER = ($SUM_AFTER*10);

		$gData	= getDisplay($data,'image5');		// 디스플레이 정보 가공 후 가져오기
		$LINK	= $gData['link'];
		$IMAGE	= $gData['image'];
		$NAME	= $gData['name'];
		$COMP	= $gData['comp'];
		$PRICE	= $gData['price'];
		$CPRICE	= $gData['cprice'];
		$CP_PRICE	= $gData['cp_price'];
		$ICON	= $gData['icon'];
		$DRAGD	= $gData['dragd'];
		$QLINK	= $data['uid'];
		$CATE	= $data['cate'];
				
		$CONTENT = str_replace("<BR>","<br/>",$data['content']);		
		$CONTENT = ieHackCheck(stripslashes($CONTENT));
		$i++;
		
		if($CP_PRICE>0) {
			$tpl->parse("is_coupon");
			$PRICE = $CP_PRICE;
		}

		$PRICE2 = str_replace("원","",$PRICE);

		if($i==2) $tpl->parse("loop_goods7");								
		else $tpl->parse("loop_goods8");								

		$tpl->parse("is_coupon","2");
	}
	
	if($i<5) {
		$sql = "SELECT a.uid,a.cate,a.number,a.name,a.price,a.consumer_price,a.price_ment,a.comp,a.image4,a.image5,a.icon,a.event,a.reserve,b.content FROM mall_goods a, mall_goods_point b WHERE a.s_qty!='3' && a.type='A' && a.uid=b.number && b.best='N' ORDER BY rand() LIMIT ".($cnt_limit-$i);
		
		$mysql->query($sql);
		while($data = $mysql->fetch_array()) { 

			$sql = "SELECT SUM(point)/COUNT(*) FROM mall_goods_point WHERE cate='{$data['cate']}' && number='{$data['uid']}'";
			$SUM_AFTER = round($mysql->get_one($sql)*2,1);
			$SUM_AFTER = ($SUM_AFTER*10);
				
			$gData	= getDisplay($data,'image5');		// 디스플레이 정보 가공 후 가져오기
			$LINK	= $gData['link'];
			$IMAGE	= $gData['image'];
			$NAME	= $gData['name'];
			$COMP	= $gData['comp'];
			$PRICE	= $gData['price'];
			$CPRICE	= $gData['cprice'];
			$CP_PRICE	= $gData['cp_price'];
			$ICON	= $gData['icon'];
			$DRAGD	= $gData['dragd'];
			$QLINK	= $data['uid'];
			$CATE	= $data['cate'];
			
			$CONTENT = str_replace("<BR>","<br/>",$data['content']);				
			$CONTENT = html2txt($CONTENT);

			$i++;
			
			if($CP_PRICE>0) {
				$tpl->parse("is_coupon");
				$PRICE = $CP_PRICE;
			}

			$PRICE2 = str_replace("원","",$PRICE);

			if($i==1) $tpl->parse("loop_goods7");								
			else $tpl->parse("loop_goods8");								

			$tpl->parse("is_coupon","2");
		}
	}
	/*******************베스트 리뷰****************************/	

	/************************* Notice & News ******************************/
	$cnt_limit	= $SKIN_DEFINE['main_notice_cnt'] ? $SKIN_DEFINE['main_notice_cnt'] : 5;
	$sql = "SELECT no, subject, signdate FROM pboard_notice WHERE idx < 999 && idx > 0 limit 0,{$cnt_limit}";
	$mysql->query($sql);

	while($data = $mysql->fetch_array()){
		$LINK  = "{$Main}?channel=board&amp;code=notice&amp;pmode=view&amp;no={$data['no']}";
		$BOARD = htmlspecialchars(stripslashes($data['subject']));
		$D_YYYY	= date("Y",$data['signdate']);
		$D_YY	= date("y",$data['signdate']);
		$D_MM	= date("m",$data['signdate']);
		$D_DD	= date("d",$data['signdate']);
		$tpl->parse("loop_notice");
	
	}
	/************************* Notice & News ******************************/

	$tpl->parse("is_mbox");
}

/***********************  DEFINE BOARD LIST  ********************************/
if($tpl->getBlstring()) {
	$bl_string = explode(",",$tpl->getBlstring());
		
	for($ii=0,$cnt=count($bl_string);$ii<$cnt;$ii++) {
		
		$i = 0;
		$bl_string2 = explode("|",$bl_string[$ii]);
		$b_name = $bl_string2[0];
		$b_limit = !empty($bl_string2[1]) ? $bl_string2[1] : 5;

		$sql = "SELECT no, subject, signdate FROM pboard_{$b_name} WHERE idx < 999 && idx > 0 limit 0,{$b_limit}";
		$mysql->query($sql);

		while($data = $mysql->fetch_array()){
			$LINK  = "{$Main}?channel=board&amp;code={$b_name}&amp;pmode=view&amp;no={$data['no']}";
			$BOARD = htmlspecialchars(stripslashes($data['subject']));
			$D_YYYY	= date("Y",$data['signdate']);
			$D_YY	= date("y",$data['signdate']);
			$D_MM	= date("m",$data['signdate']);
			$D_DD	= date("d",$data['signdate']);
			$tpl->parse("loop_board_{$b_name}");
		
		}
	}
}
/***********************  DEFINE BOARD LIST  ********************************/

/***********************  CATEGORY GOODS LIST  ********************************/
if($tpl->getClstring()) {
	$cl_string = explode(",",$tpl->getClstring());
	$gtype_arr = Array("hit"=>1,"reco"=>2,"new"=>3);
		
	for($ii=0,$cnt=count($cl_string);$ii<$cnt;$ii++) {
		$i = 0;
		$cl_string2 = explode("|",$cl_string[$ii]);
		$c_cate = $cl_string2[0];
		$cate_len = strlen($c_cate);
		$c_type = $gtype_arr[$cl_string2[1]];
		$c_limit = !empty($cl_string2[2]) ? $cl_string2[2] : $tcnt_arr[$k];

		$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,comp,image5,icon,event,reserve,s_qty,qty FROM mall_goods WHERE SUBSTRING(display,3,1)='{$c_type}' && SUBSTRING(cate,1,{$cate_len}) = '{$c_cate}' && s_qty!='3' && type='A' ORDER BY o_num2 ASC LIMIT {$c_limit}";
		$mysql->query($sql);

		while($data = $mysql->fetch_array()){			
			$gData	= getDisplay($data,'image5');		// 디스플레이 정보 가공 후 가져오기
			$LINK	= $gData['link'];
			$IMAGE	= $gData['image'];
			$NAME	= $gData['name'];
			$COMP	= $gData['comp'];
			$PRICE	= $gData['price'];
			$CPRICE	= $gData['cprice'];
			$CP_PRICE	= $gData['cp_price'];
			$ICON	= $gData['icon'];
			$DRAGD	= $gData['dragd'];
			$QLINK	= $data['uid'];
			$CATE	= $data['cate'];

			if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) {
				$tpl->parse("is_soldout");
			} 
						
			$i++;	

			if($CP_PRICE>0) {
				$tpl->parse("is_coupon");
				$PRICE = $CP_PRICE;
			}

			$PRICE2 = str_replace("원","",$PRICE);
	
			$tpl->parse("loop_catelist_{$c_cate}");	
			$tpl->parse("is_soldout","2");
			$tpl->parse("is_coupon","2");
		}	
			
		if($i==0) $tpl->parse("no_catelist_{$c_cate}");	
	}
} 
/***********************  CATEGORY GOODS LIST  ********************************/

if($my_id) $tpl->parse("is_logout");
else $tpl->parse("is_login");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>