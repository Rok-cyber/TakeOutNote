<?
$mysql = new  mysqlClass(); //디비 클래스

// 변수 지정
if(!$limit) {
	if($PGConf['page_record_num']) $limit = $PGConf['page_record_num'];
	else {
		$limit = 10;
		$PGConf['page_record_num'] = 10;
	}
}
else $PGConf['page_record_num'] = $limit;

$record_num = $PGConf['page_record_num'];
$page_num = $PGConf['page_link_num'];

if($code=='search') $order = "ORDER BY signdate DESC";
else {
	if(($code=='banner' || $code=='mobile_banner') && $location) $order = "ORDER BY rank ASC";
	else $order = "ORDER BY uid DESC";
}

$where = "";
if($seccate) $where .= "&& cate = '{$seccate}' ";
if($location) $where .= "&& location = '{$location}' ";
if($status) {
	if($code=='popup') {
		if($status==3) $where .= " && end_date < '".date("Y-m-d")."' && end_date !='0000-00-00'";
		else $where .= " && status='{$status}' && (end_date >= '".date("Y-m-d")."' || end_date ='0000-00-00') "; 
	}
	else if($code=='event') {
		if($status==1) $where .= "&& s_date > '".date("Y-m-d")."' && s_date !='0000-00-00'";
		else if($status==2) $where .= "&& s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."'";
		else $where .= "&& e_date <'".date("Y-m-d")."' && e_date !='0000-00-00'";			
	}
	else if($code=='sms_addr' || $code=='sms_sample') $where .= "&& groups = '{$status}' ";
	else if($code=='affiliate') $where .= "&& auth = '{$status}' ";	
	else if($code=='affiliate_banner') $where .= "&& type = '{$status}' ";	
	else if($code!='banner' && $code!='mobile_banner') $where .= "&& status = '{$status}' ";	
}

if($point) {
	if($code=='goods_point') $where .= "&& point = '{$point}' ";
	else if($code=='sms_addr') $where .= "&& sex = '{$point}' ";
	else {
		if($point=='Y') $where .= "&& answer != '' ";
		else $where .= "&& answer = '' ";
	}
}
if($field && $word) {	
	if($code=='reserve' && $field=='order_num') $where .= "&& (INSTR({$field},'{$word}') || INSTR(goods_num,'{$word}'))";
	else $where .= "&& INSTR({$field},'{$word}')";
}
	

if($code=='affiliate_account') {
	######################## 입점사 설정 ############################
	$sql = "SELECT * FROM mall_affiliate ORDER BY uid ASC";
	$mysql->query($sql);
		
	while($row=$mysql->fetch_array()){
		if($row['id'] == $status) $sec = 'selected';
		else $sec='';
		$AFFILIATE .= "<option value='{$row['id']}' {$sec}>{$row['id']}</option>\n";
	}	
}

$sql = "SELECT COUNT(uid) FROM mall_{$code} WHERE uid>0 {$where}";

$total_record = $mysql->get_one($sql);

/*********************************** LIMIT CONFIGURATION ***********************************/
$Pstart = $record_num*($page-1);
$total_page = ceil($total_record/$record_num);	
$v_num = $total_record - (($page-1) * $record_num);
$v_num2 = (($page-1) * $record_num)+1;

/*********************************** @LIMIT CONFIGURATION ***********************************/

?>
<!--  삭제용 스크립트 -->
<SCRIPT LANGUAGE="JavaScript">
<!--
function delSend(frm){
	for (i = cnt = 0; i < frm.elements.length; i++) {
		if(frm.elements[i].name == 'item[]' && frm.elements[i].checked == true) {
			cnt++;
		}
	}
	
	if ( cnt <= 0) {
		alert("삭제할 목록을 선택하세요!");
		return false;
	} 
	else {
		if (window.confirm("정말로 삭제 하시겠습니까?")) {
			frm.action = "./del_all.php?code=<?=$code.$addstring;?>";
			frm.submit();
			return;
		} 
		else return;
	}
}

function delSend2(frm){
	if (window.confirm("정말로 삭제 하시겠습니까?")) {
		frm.action = "./del_all.php?code=<?=$code.$addstring;?>&mode=all";
		frm.submit();		
	} 
	return false;	
}

function chCheck(frm){
	for (j = 0;  j< frm.elements.length; j++) {
		if(frm.elements[j].name == 'item[]' && frm.elements[j].checked == true) { 
			frm.elements[j].checked = false;
		} 
		else {
			frm.elements[j].checked = true;
		}
	}
	return false;
}
//-->
</SCRIPT>
<!--  삭제용 스크립트 -->


<?
$TOTAL_PRICE = 0;

$tpl = new classTemplate; // 템플릿
$tpl->define('main',"{$skin}/list.html");
$tpl->scan_area('main');
$tpl->parse('is_man1');
if($total_record > 0) {

	if($code=='sms_list') {
		include_once("{$lib_path}/lib.Shop.php");
		$sql = "SELECT uid,status,err_msg,result,status FROM mall_sms_list WHERE uid!=0 {$where} {$order} LIMIT {$Pstart},{$record_num}";
		$mysql->query($sql);
		while($row = $mysql->fetch_array()){
			if($row['result']==1 && $row['stauts']!=2) {				
				if(($row['status']==3 && $row['err_msg']>date("Y-m-d H:i:s")) || $row['status']==1) {
					pmallSmsResult($row['uid']);
				}
			}
		}
	}
	else if($code=='sms_addr' || $code=="sms_sample") {
		$sql = "SELECT groups FROM mall_{$code} WHERE groups!='' GROUP BY groups ORDER BY groups DESC";
		$mysql->query($sql);
		$SECGROUP = "";
		while($row2 = $mysql->fetch_array()){
			if($row2['groups']==$status) $sec = "selected";
			else $sec ="";
			$SECGROUP .= "<option value='{$row2['groups']}' {$sec}>{$row2['groups']}</option>";
		}
	}
	else if($code=='affiliate_account') {
		$sql = "SELECT SUM(a_price) FROM mall_{$code} WHERE uid!=0 {$where}";
		$TOTAL_PRICE = number_format($mysql->get_one($sql));
	}

	
	/*********************************** QUERY ***********************************/
    $query = "SELECT ";
	for($i=1;$i<$MTlist[0];$i++){  
        $query .= $MTlist[$i].", ";	
	}
	$query .= $MTlist[$i]." FROM  mall_{$code} WHERE uid>0 {$where} {$order} LIMIT {$Pstart},{$record_num}";
    $mysql->query($query);	
	/*********************************** QUERY ***********************************/

	/*********************************** LOOP ***********************************/
	while ($row=$mysql->fetch_array()){
        $NUM = $v_num;
	    $NUM2 = $v_num2;
	    if($v_num%2 ==0) $BGCOLOR = '#efefef';
	    else $BGCOLOR = '#ffffff';
		$DEL = "<input type='checkbox' value='{$row[uid]}' name='item[]' onfocus=blur();>";
	    for($i=2;$i<=$MTlist[0];$i++){  
			$fd = $MTlist[$i];
			if($fd == 'cate' && $code!='goods_point' && $code!='goods_qna' && $code!='banner' && $code!='mobile_banner') { 
				$sec = $row[$fd]; 
				$row[$fd] = $MTcate[$sec]; 
			}

			$row[$fd] = stripslashes($row[$fd]);
			if($i==2 && $code!='banner' && $code!='mobile_banner' && $code!='affiliate_banner' && $code!='count_refer' && $code!='search' && $code!='brand') {
				${"LIST".$i} = "&nbsp;<a href='{$Main}&mode=modify&uid={$row[uid]}{$addstring}' onfocus='this.blur();'>{$row[$fd]}</a>";  
			}
			else ${"LIST".$i} = $row[$fd];      
		 
		 }
	  
		switch($code){         
			case 'add_page' :
				$SMain = str_replace("../../","",$SMain);
				$LIST3 = "http://{$_SERVER[SERVER_NAME]}/{$SMain}?channel=plus&plus={$row[uid]}";
				$LIST3 = "<a href='{$LIST3}' onfocus='this.blur();' target=_blank>{$LIST3}</a>";
			break;

			case 'banner' : case "mobile_banner" :
				if($LIST2) $size=@GetImageSize("{$dir}/{$LIST2}");
				if($size[1]<50 && $size[0]>300) $IMGS = imgSizeCh("{$dir}/",$LIST2,'250');
				else $IMGS = imgSizeCh("{$dir}/",$LIST2,'','50');
				$LIST2 = "<a href='{$Main}&mode=modify&uid={$row[uid]}{$addstring}' onfocus='this.blur();'>{$IMGS}</a>";
				
				if(substr($LIST5,0,4)=='0000') $LIST5 = '';
				else $LIST5 = substr($LIST5,0,10);
				$LIST6 = $banner_arr[$LIST6];

				$SIZE  = "<font class=eng>{$size[0]}px * {$size[1]}px</font>";				
				if($LIST8) {					
					$sql = "SELECT cate_name FROM mall_cate WHERE cate='{$LIST8}'";
					$LIST8 = "<br />(".$mysql->get_one($sql).")";
				}
				else $LIST8 = "";
			break;

			case 'brand' :					
				$LIST2 = "<a href='{$Main}&mode=modify&uid={$row[uid]}{$addstring}' onfocus='this.blur();'>".imgSizeCh("{$dir}/",$LIST2,'','50')."</a>";				
				$LIST3 = "<a href='{$Main}&mode=modify&uid={$row[uid]}{$addstring}' onfocus='this.blur();'>{$LIST3}</a>";
				$sql = "SELECT count(*) FROM mall_goods WHERE uid>0 && brand='{$row['uid']}'";
				$LIST5 = number_format($mysql->get_one($sql));
				if($LIST5!=0) $LIST5= "<a href='../shopping/goods_brand.php?brand={$row['uid']}' title='해당브랜드상품보기'>{$LIST5}</a>";
			break;

			case 'special' :					
				$sql = "SELECT count(*) FROM mall_goods WHERE uid>0 && INSTR(special,',{$row['uid']},')";
				$LIST4 = number_format($mysql->get_one($sql));
				if($LIST4!=0) $LIST4 = "<a href='../shopping/goods_special.php?special={$row['uid']}' title='해당기획전상품보기'>{$LIST4}</a>";
			break;

			case 'popup' :
			    if($LIST3 =='1') $LIST3 = "사용중";
				else $LIST3 = "일시정지";

				if($LIST4=='2') $LIST4 = "항상";
				else $LIST4 = "하루에한번";

				if($LIST5=='2') $LIST5 = "레이어";
				else $LIST5 = "일반팝업창";

				if(substr($row['end_date'],0,10) < date("Y-m-d",time()) && substr($row['end_date'],0,4)!='0000') $LIST3 = "종료";
				 
			break;

			case 'reserve' :
				$sql = "SELECT name FROM pboard_member WHERE id='{$LIST3}'";
				$NAME = stripslashes($mysql->get_one($sql));
			    $LIST4 = number_format($LIST4,$ckFloatCnt);
				$LIST5 = $reserve_arr[$LIST5];
			    $LIST6 = substr($LIST6,0,16);
			break;

			case "goods_point" :			     
				$LIST2 .= " ";
				$POINT='';
							
				for($i=0;$i<$row['point'];$i++){
		            $POINT .= "★";
	            }

				$LIST3 = "<a href='{$SMain}?channel=view&uid={$LIST9}&cate={$LIST8}' target='_blank' title='쇼핑몰 상세설명 보기'>{$LIST3}</a>";
			
				if($LIST6==1) $LIST2 .= "<img src='{$skin}/img/icon_buy.gif' align='absmiddle' />";
				if($LIST7=='Y') $LIST2 .= "<img src='{$skin}/img/icon_best.gif' align='absmiddle' />";
			break;

			case "goods_qna" :			     
				$LIST2 .= " ";				
				$LIST3 = "<a href='{$SMain}?channel=view&uid={$LIST7}&cate={$LIST6}' target='_blank' title='쇼핑몰 상세설명 보기'>{$LIST3}</a>";			
				if($LIST5) $LIST5 = "<font class=blue>답변완료</font>";
				else $LIST5 = "<font class=orange>미답변</font>";
			break;

			case "event" :
				$LIST5 = substr($LIST5,0,10);
				$LIST6 = substr($LIST6,0,10);

				if($LIST5<=date("Y-m-d")) {
					if($LIST6>=date("Y-m-d")) {
						$STATUS = "<a href='{$SMain}?channel=event&plus={$row[uid]}' target='_blank'><font class='blue'>진행중</font></a>";
						$DEL = "<input type='checkbox' value='{$row[uid]}' name='item[]' onfocus=blur(); disabled>";
					}
					else $STATUS = "종료";
				}
				else $STATUS = "준비중";
				
			break;	      

			case "search" :
				if($LIST4=='1') $LIST4 = "태그검색";
				else if($LIST4=='2') $LIST4 = "브랜드검색";
				else $LIST4 = '';
			break;

			case "sms_addr" :
				if($LIST5=='M') $LIST5 = "남자";
				else if($LIST5=='F') $LIST5 = "여자";
			break;

			case "sms_list" :
				$LIST2 = "<a href='{$Main}&mode=modify&uid={$row[uid]}{$addstring}' onfocus='this.blur();'>".date("Y-m-d H:i",$row['signdate'])."</a>";
				
				$LIST8 = number_format($LIST7-$LIST6);
				$LIST6 = number_format($LIST6);
				$LIST7 = number_format($LIST7);
				
				switch($LIST5) {
					case "1" : 
						if($row['result']==2) $LIST5 = "발송완료"; 
						else { 
							$LIST5 = "발송중"; 					
							$LIST8 = 0;
						}
					break;
					case "2" : $LIST5 = "발송실패"; break;
					case "3" : $LIST5 = "예약발송"; break;
				}

			break;

			case "affiliate" :
				if($LIST7=='Y') $LIST7 = "<font class='small bold orange'>승인</font>";
				else $LIST7 = "<font class='small bold'>보류</font>";
			break;

			case "affiliate_banner" :				
				if($LIST2=='I') {	
					$LIST2 = "이미지";
					$BANNER = "<a href='{$Main}&mode=modify&uid={$row[uid]}{$addstring}' onfocus='this.blur();'>".imgSizeCh("{$dir}/",$row['banner'],'500')."</a>";
					$size=@GetImageSize("{$dir}/{$row['banner']}");
					$SIZES  = "<font class=eng>{$size[0]}px * {$size[1]}px</font>";							
				}
				else {					
					$LIST2 = "텍스트";
					$BANNER = $row['title'];
					$BANNER = "<a href='{$Main}&mode=modify&uid={$row[uid]}{$addstring}' onfocus='this.blur();'>{$BANNER}</a>";
					$SIZES = "N/A";					
				}
			break;

			case 'affiliate_account' :
				$LIST5 = number_format($LIST5);				
			break;
		}
	 
	  
		$DATE = date("Y-m-d",$row['signdate']);
		$DATE2 = date("Y-m-d H:i",$row['signdate']);
          
		$tpl->parse('is_man2','1');
		$tpl->parse('loop');
		$v_num--;
		$v_num2++;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?code={$code}{$addstring3}"); 
	$PAGING = $pg->print_page();  //페이징 
	$tpl->parse('is_man3');
	if($location) $tpl->parse("is_move");
	if($field) $tpl->parse("is_field");

} 
else $tpl->parse('is_loop');
/*********************************** LOOP ***********************************/

$TOTAL = $total_record;      //토탈수 
if($MTcate){
	$CATE = "<option value=''>전체</option>\n";
	for($i=1;$i<=$MTcate[0];$i++) {
	    if($seccate ==$i) $CATE .="<option value='{$i}' selected>{$MTcate[$i]}</option>";
	    else $CATE .="<option value='{$i}'>{$MTcate[$i]}</option>";
	}
}
$C_ACTION = $Main.$addstring2;
$PAGE = "{$page}/{$total_page}";
$LINK1 = $Main.$addstring;    // 목록보기 링크 
$LINK2 = $Main.$addstring."&mode=write"; //글쓰기 링크 

$ACTION = $Main;   //검색 경로
$CANCEL = $Main;

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>