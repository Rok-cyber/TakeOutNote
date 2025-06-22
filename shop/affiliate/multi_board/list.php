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
	$order = "ORDER BY uid DESC";
}
if($seccate) $where = "&& cate = '{$seccate}' ";

if($field && $word) {
	if($code=='brand') $where .= "&& (INSTR(name,'{$word}') || INSTR(ename,'{$word}'))";
	else $where .= "&& INSTR({$field},'{$word}')";
}

if($status) {
	if($code=='affiliate_banner') $where = "&& type = '{$status}' ";		
}

if($code=='affiliate_account') $where .= " && affiliate='{$a_my_id}' ";

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

	if($code=='affiliate_account') {
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
			if($fd == 'cate') { 
				$sec = $row[$fd]; 
				$row[$fd] = $MTcate[$sec]; 
			}
			$row[$fd] = stripslashes($row[$fd]);
			if($i==2 && $code!='affiliate_account' && $code!='affiliate_banner') {
				${"LIST".$i} = "&nbsp;<a href='{$Main}&mode=modify&uid={$row[uid]}{$addstring}' onfocus='this.blur();'>{$row[$fd]}</a>";  
			}
			else ${"LIST".$i} = $row[$fd];      		 
		 }

		 $UID = $row['uid'];
	  
		switch($code){         
			case "affiliate_banner" :					
				if($LIST2=='I') {	
					$LIST2 = "이미지";
					$BANNER = imgSizeCh("{$dir}/",$row['banner'],'500');
					$size=@GetImageSize("{$dir}/{$row['banner']}");
					$SIZES  = "<font class=eng>{$size[0]}px * {$size[1]}px</font>";							
					$LINKS = "&lt;a href='{$affil_link}' target='_blank'>&lt;img src='{$aMain}/image/banner/{$row['banner']}' border=0 width='{$size[0]}' height='{$size[1]}' />&lt;/a>";
				}
				else {					
					$LIST2 = "텍스트";
					$BANNER = $row['title'];
					$SIZES = "N/A";					
					$LINKS = "&lt;a href='{$affil_link}' target='_blank'>{$BANNER}&lt;/a>";
				}
			break;

			case 'affiliate_account' :
				$LIST3 = number_format($LIST3);				
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