<?
if(!$cate) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');

$tpl->define("main","{$skin}/main2.html");
$tpl->scan_area("main");

$sql = "SELECT * FROM mall_cate WHERE cate='{$cate}'";
$row = $mysql->one_row($sql);
$SCATENAME = stripslashes($row['cate_name']);

if(substr($cate,0,3)=='999') movePage("{$Main}?channel=cooperate");

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
unset($row_ban,$BANNER,$BLINK,$BTARGET);
/***********************  BANNER  ********************************/

$cgType = "list";
if($row['list_mode']==2) $cgType = "img";

$mtype = 2;

if($row['cate_dep']==1) {
	if($row['cate_sub']==1) {
		$sql = "SELECT count(*) FROM mall_cate WHERE cate_dep='3' && valid ='1' && SUBSTRING(cate,1,3)='".substr($cate,0,3)."'";	
		if($mysql->get_one($sql)>0) $mtype = "1";
	}
	$ajax_cate = substr($cate,0,3);
	$qstr = "o_num2";
}
else {
	if(substr($cate,6,3)=='000') $ck_len = 6;
	else $ck_len = 9;
	$ajax_cate = substr($cate,0,$ck_len);
	$qstr = "o_num3";
}

$main_dsp = explode("|*|",stripslashes($row['code']));

/**************************** SUB CATE **************************/
if($row['cate_sub']==1 && ($row['cate_dep']==2 || $row['cate_dep']==3) && $SKIN_DEFINE['cate_list_type']==2) {
	$sql = "SELECT cate_name,cate,cate_sub FROM mall_cate WHERE cate_dep='".($row['cate_dep']+1)."' && valid ='1' && cate_parent='{$cate}' ORDER BY number";
}
else {
	$sql = "SELECT cate_name,cate,cate_sub FROM mall_cate WHERE cate_dep='2' && valid ='1' && SUBSTRING(cate,1,3)='".substr($cate,0,3)."' ORDER BY number";
}
$mysql->query($sql);

$cks = 0;
while($row = $mysql->fetch_array()){		
	if($row['cate']==$cate) $COLOR1 = "cateColor2";
	else $COLOR1 = "cateColor1";
	$CNAME = $row['cate_name'];	
	
	if($row['cate_sub']==1) {
		$CLINK = "{$Main}?channel=main2&amp;cate={$row['cate']}";		
		$sql = "SELECT cate_name, cate FROM mall_cate WHERE cate_dep='3' && cate_parent='{$row[cate]}' ORDER BY number";
		$mysql->query2($sql);

		while($row2 = $mysql->fetch_array('2')){
			$COLOR2 = "cateColor3";
			$CNAME2 = $row2['cate_name'];
			$CLINK2 = "{$Main}?channel=list&amp;cate={$row2['cate']}";		
			$tpl->parse("loop_scate");					
		}
	}
	else $CLINK = "{$Main}?channel=list&amp;cate={$row['cate']}";		
	$tpl->parse("loop_cate");	
	$tpl->parse("loop_scate","2");	
	$cks = 1;
}
if($cks == 1) $tpl->parse("is_scate");
/**************************** SUB CATE **************************/	

$hit_cnt	= explode(",",$SKIN_DEFINE['sub_hit_cnt']);
$reco_cnt	= explode(",",$SKIN_DEFINE['sub_reco_cnt']);
$new_cnt	= explode(",",$SKIN_DEFINE['sub_new_cnt']);

if(!$main_dsp[0] && $main_dsp[0]!="0") $main_dsp[0] = 2;
if(!$main_dsp[2] && $main_dsp[2]!="0") $main_dsp[2] = 2;
if(!$main_dsp[1]) $main_dsp[1] = $hit_cnt[0];
if(!$main_dsp[3]) $main_dsp[3] = $reco_cnt[0];
if(!$main_dsp[5]) $main_dsp[5] = $new_cnt[0];

if($main_dsp[6]=='1')	{ $CATE_UP_CODE	= $main_dsp[7];		$tpl->parse("is_cate_up");	}
if($main_dsp[8]=='1')	{ $HIT_UP_CODE	= $main_dsp[9];		$tpl->parse("is_hit_up");	}

if($mtype==1) {
	if(!$main_dsp[4]) $main_dsp[4] = 1;	
	$where = "&& SUBSTRING(cate,1,3) = '".substr($cate,0,3)."'"; 
	$where2 = "&& SUBSTRING(cno,1,3) = '".substr($cate,0,3)."'";
	$where3 = "&& SUBSTRING(p_cate,1,3) = '".substr($cate,0,3)."'";
}
else {
	if(!$main_dsp[4]) $main_dsp[4] = 0;		
	if(substr($cate,3,3)=='000') $ck_len = 3;
	else if(substr($cate,6,3)=='000') $ck_len = 6;
	else $ck_len = 9;

	$where = "&& SUBSTRING(cate,1,{$ck_len}) = '".substr($cate,0,$ck_len)."'"; 
	$where2 = "&& SUBSTRING(cno,1,{$ck_len}) = '".substr($cate,0,$ck_len)."'";
	$where3 = "&& SUBSTRING(p_cate,1,{$ck_len}) = '".substr($cate,0,$ck_len)."'";
	$where4 = "&& (SUBSTRING(cate,1,{$ck_len}) = '".substr($cate,0,$ck_len)."'  || INSTR(mcate,',".substr($cate,0,$ck_len)."'))";	 
}

$disp_arr = Array('',$main_dsp[0],$main_dsp[2],$main_dsp[4]);
$vcnt_arr = Array('',$hit_cnt[0],$reco_cnt[0],$new_cnt[0]);
$tcnt_arr = Array('',$main_dsp[1],$main_dsp[3],$main_dsp[5]);

for($k=1;$k<4;$k++) {	
	if($disp_arr[$k] != 0){
		$i = 0;
		$vcnt = $vcnt_arr[$k];
		$ON = "_on";
		$DSTYLE = '';
		if(!$tcnt_arr[$k]) $tcnt_arr[$k] = $hit_cnt[0];
		$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,comp,image5,icon,event,reserve,s_qty,qty FROM mall_goods WHERE SUBSTRING(display,3,1)='{$k}' && s_qty!='3' && type='A' {$where} ORDER BY {$qstr} ASC LIMIT ".$tcnt_arr[$k];
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
					
			$i++;	
			if($i>$vcnt) { 
				$ON = '';
				if($disp_arr[$k]==3) $DSTYLE = "style='display:none'";
			}
			
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

			if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) {
				$tpl->parse("is_soldout");
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
					$tpl->parse("is_move{$k}");
				break;
			}
		}

		if($k==1) {
			$day1 = date("Ymd",strtotime('-1 WEEK', time()));
			$cnt_limit	= $SKIN_DEFINE['sub_click_cnt'] ? $SKIN_DEFINE['sub_click_cnt'] : 3;

			$sql = "SELECT SUM(view) as sum, cno FROM mall_goods_view  WHERE date >= '{$day1}' {$where2} GROUP BY cno ORDER BY sum DESC LIMIT {$cnt_limit}";
			$mysql->query($sql);

			$i = 1;
			while($data2 = $mysql->fetch_array()){			
				$tmp_cate = substr($data2['cno'],0,12);
				$tmp_number = substr($data2['cno'],12);
				
				$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && type='A' && uid='{$tmp_number}'";
				if(!$data = $mysql->one_row($sql)) continue;

				$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
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

				$tpl->parse("loop_goods4");	
				$tpl->parse("is_coupon","2");
				$i++;
			}
			
			$cnt_limit	= $SKIN_DEFINE['sub_order_cnt'] ? $SKIN_DEFINE['sub_order_cnt'] : 3;
			$sql = "SELECT COUNT(uid) as o_cnt, p_cate, p_number FROM mall_order_goods WHERE uid!='0' && signdate >= '{$day1}' {$where3} GROUP BY p_number ORDER BY o_cnt DESC LIMIT {$cnt_limit}";
			$mysql->query($sql);

			$i = 1;
			while($data2 = $mysql->fetch_array()){		

				$tmp_cate = $data2['p_cate'];
				$tmp_number = $data2['p_number'];
				
				$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty FROM mall_goods WHERE s_qty !='3' && 
				type='A' && uid='{$tmp_number}'";
				if(!$data = $mysql->one_row($sql)) continue;

				$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
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
		}	
		else if($k==2) {
			$cnt_limit	= $SKIN_DEFINE['sub_review_cnt'] ? $SKIN_DEFINE['sub_review_cnt'] : 3;
			$sql = "SELECT SUM(point)/COUNT(*) as eve,cate, number FROM mall_goods_point WHERE uid>0 {$where} GROUP BY number ORDER BY eve  DESC LIMIT {$cnt_limit}";
			$mysql->query($sql);
			$i = 1;
			while($row = $mysql->fetch_array()) { 

				$SUM_AFTER = round($row['eve']*2,1);
				$SUM_AFTER = ($SUM_AFTER*10);
		
				$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,comp,image4,icon,event,reserve,qty,s_qty FROM mall_goods WHERE s_qty!='3' && type='A' && uid='{$row['number']}'";
				$data = $mysql->one_row($sql);

				if(!$data) continue;

				$gData	= getDisplay($data,'image4');		// 디스플레이 정보 가공 후 가져오기
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
				
				$sql = "SELECT content FROM mall_goods_point WHERE cate='{$row['cate']}' && number='{$row['number']}' ORDER BY point DESC, signdate DESC LIMIT 1";
				
				$CONTENT = str_replace("<BR>","<br/>",stripslashes($mysql->get_one($sql)));				
				$CONTENT = html2txt($CONTENT);

				$tpl->parse("loop_goods6");	
				$tpl->parse("is_coupon","2");	
				$i++;
			}
		}
		$tpl->parse("is_main_goods{$k}");
	}
}
unset($hit_cnt,$reco_cnt,$new_cnt,$cnt_limit);

if($mtype==2) {
	
	include "lib/class.Paging.php";

	$limit	= isset($_POST['limit']) ? $_POST['limit'] : $LIST_DEFINE['limit'];
	$page	= isset($_GET['page']) ? $_GET['page'] : 1;
	$order	= isset($_POST['order']) ? $_POST['order'] : "uid";
	$mo1	= isset($_POST['mo1']) ? $_POST['mo1'] : $_GET['mo1'];
	$mo2	= isset($_POST['mo2']) ? $_POST['mo2'] : $_GET['mo2'];
	$field	= isset($_POST['field']) ? $_POST['field'] : $_GET['field'];
	$search	= isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
	
	$ajaxstring = "&cate={$ajax_cate}";
	$cstring = "&cate={$cate}";

	$where = $where4;

	$sql = "SELECT access_level, cate FROM mall_cate WHERE SUBSTRING(cate,1,3) = '".substr($cate,0,3)."'";
	$mysql->query($sql);
	while($tmps=$mysql->fetch_array()) {
		if($tmps['access_level'] && $my_level<9) {
			$access_level = explode("|",$tmps['access_level']);
			if(($access_level[1]=='!=' && $access_level[0]!=$my_level) || ($access_level[1]=='<' && $access_level[0]>$my_level)) {
				$where .= " && cate != '{$tmps['cate']})' ";					
			}
		}
	}
	unset($access_level);

	if($search) {
		$fields_arr = Array("tag"=>"태그","name"=>"상품명","brand"=>"브랜드");
		$ajaxstring .= "&search=".urlencode($search)."&field={$field}";
		$sstring .= "&search=".urlencode($search)."&field={$field}";
		
		if($field=='tag') $where .= "  && INSTR({$field},',{$search},')";
		else if($field=='brand') {
			$sql = "SELECT uid FROM mall_brand WHERE INSTR(name,',{$search},') || INSTR(tag,'{$search}')";
			$mysql->query($sql);
			$tmps = array();
			while($row = $mysql->fetch_array()){
				$tmps[] = $row['uid'];
			}
			if($tmps[0]) {
				$brand = join(",",$tmps);
				$where .= "  && brand IN ({$brand})";
			}
		}
		else if($field=="name") $where .= "  && (INSTR(name,'{$search}') || INSTR(search_name,'{$search}'))";
		else $where .= "  && INSTR({$field},'{$search}')";

		$fields = $fields_arr[$field];

		$tpl->parse("is_search");
	}

	if(($mo1 || $mo1==0) && $mo2) {
		$where .= " && price BETWEEN '{$mo1}' AND '{$mo2}' ";
		$mstring = "&amp;mo1={$mo1}&amp;mo2={$mo2}";
		$SEC_MON = number_format($mo1)."원 ~ ".number_format($mo2)."원";   
		$ajaxstring .= "&mo1={$mo1}&mo2={$mo2}";
		$tpl->parse("is_sec_mon");
		SetCookie("search_yn","Y",0,"/"); 
	}
	else SetCookie("search_yn","",-999,"/");

	$pstring = "&amp;page={$page}";
	$sql = "SELECT COUNT(uid) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";

	$TOTAL = $mysql->get_one($sql);

	if($TOTAL>0) {
		$sql = "SELECT MAX(price) FROM mall_goods WHERE s_qty !='3' && type='A' {$where}";
		$tmps = $mysql->get_one($sql);
		if(substr($tmps,0,1)==9) $MaxMoney = "10";
		else $MaxMoney = substr($tmps,0,1)+1;

		for($i=1,$cnt=strlen($tmps);$i<$cnt;$i++){
			$MaxMoney .="0";
		}
	}
	else $MaxMoney = 0;


	$record_num = $limit; 
	$limit_arr = isset($LIST_DEFINE['limit_arr']) ? $LIST_DEFINE['limit_arr'] : "10,20,30,50,100";
	$tmps = explode(',',$limit_arr);
	$LIMIT_OPTION = $LIMIT_OPTION2 = "";
	for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
		${"SEC".$tmps[$i]} = "";
		if($tmps[$i]==$limit) $LIMIT_OPTION .= "<option value='{$tmps[$i]}' selected>{$tmps[$i]}개씩 보기</option>";
		else $LIMIT_OPTION .= "<option value='{$tmps[$i]}'>{$tmps[$i]}개씩 보기</option>";
		
		$LIMIT_OPTION2 .= "limitBoxs.addItem('{$tmps[$i]}개씩 보기','{$tmps[$i]}');";
		if($limit==$tmps[$i]) $SEC_LIMIT = $i+1;
	}
	${"SEC".$limit} = "selected";

	/*********************************** LIMIT CONFIGURATION ***********************************/
	$Pstart = $record_num*($page-1);
	$TOTAL_PAGE = ceil($TOTAL/$record_num);	
	if($TOTAL <= ($page * $record_num)) $TONUM = $TOTAL;
	else $TONUM = $record_num; 
	$PAGE = $page;
	/*********************************** @LIMIT CONFIGURATION ***********************************/

	if($TOTAL==0) $tpl->parse("no_loop");

	$tpl->parse("is_cate_goods");

	if($page) $tpl->parse("is_page");
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>