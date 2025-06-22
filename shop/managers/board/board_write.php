<? 
include "../html/top_inc.html";     /*** TOP INCLUDE ***/ 

$mode	= isset($_GET['mode']) ? $_GET['mode'] : 'write';
$uid	= isset($_GET['uid']) ? $_GET['uid'] : '';

$skin = ".";
require "{$lib_path}/class.Template.php";

// 템플릿
$tpl = new classTemplate;
$tpl->define('main','./board_write.html');
$tpl->scan_area('main');


if($mode=='modify') {
    if(!$uid) alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');
  
	############ 게시판 정보 불러오기 ####################
	$sql = "SELECT * FROM pboard_manager WHERE uid = '{$uid}'";
	if(!$row=$mysql->one_row($sql)) alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');

	$NAME		= stripslashes($row['name']);
	$TITLE		= stripslashes($row['title']);
	$BW_SIZE	= stripslashes($row['b_w_size']);
	$INPAGE		= stripslashes($row['inpage']);
	$PAGELINK	= stripslashes($row['pagelink']);
	$BGCOLOR	= stripslashes($row['bg_color']);
	$WORDLIMIT	= stripslashes($row['word_limit']);
	$IMGLIMIT	= stripslashes($row['img_limit']);
	$HEADER_URL	= stripslashes($row['header_url']);
	$HEADER		= stripslashes($row['header']);
	$FOOTER_URL	= stripslashes($row['footer_url']);
	$FOOTER		= stripslashes($row['footer']);  
	
	$options	= explode("||",$row['options']);
	
	for($i=0;$i<9;$i++) { 
		if($options[$i]=='Y') ${"CK".($i+1)} = 'checked';
		else ${"CK".($i+1)} = '';
	}
	
	$CK7 = "";
	if($options[6]=='Y') $CK7Y = "checked";
	else if($options[6]=='A') $CK7A = "checked";
	else $CK7N = "checked";
  
	$BOADMIN	= base64_decode(stripslashes($options[12]));
	$IMGCNT		= stripslashes($options[14]);
	$IMGSZ		= stripslashes($options[15]);
	$OP17		= stripslashes($options[17]);
	$OP18		= stripslashes($options[18]);

	
	if($options[13]=='Y') $CK13 = 'checked';
	if($options[16]=='Y') $CK16 = "checked";
	if($options[19]=='Y') $CK19 = "checked";
	if($options[20]=='Y') $CK20 = "checked";
	if($options[21]=='Y') $CK21 = "checked";

    $tpl->parse("is_modify1");
	$tpl->parse("is_modify2");


	/*   options[]        
	0:전체목록 출력| 1:카테고리 | 2:HTML | 3:공지사항 | 4:자료실 | 5:간단한 답글 | 6:비밀글 | 7:동영상 링크 | 8:관련사이트 링크 | 9:로그인 아이콘 | 10:업로드 파일수 | 11:링크 가능수 | 12 : 게시판 관리 아이디 | 13 : 답변메일 | 14 : 갤러리 이미지수 | 15 : 갤러리 이미지 사이즈 | 16:방명록형태 | 17:방명록형태시 내용글제한수 | 18: 새글표시 시간 | 19 : 웹에디터 사용 | 20 : 갤러리 사이즈고정 | 21 : 블로그형태 */ 
} 
else {
	$INPAGE	= $PAGELINK	= 10;
	$WORDLIMIT = $IMGLIMIT = 0;
	$CK1	= $CK3 = $CK6 = $CK20 = 'checked';
	$OP18	= '24';

	$tpl->parse("is_write1");
	$tpl->parse("is_write2");
}

if(!$BGCOLOR) $BGCOLOR = "#FFFFFF";


/*** skin 디렉토리에서 디렉토리를 구함 ***/
$defBoard = Array('notice','customer','faq','counsel','affil_counsel','sales','cooperation');  
$defSkin = Array('notice'=>'itsMall_bo','customer'=>'itsMall_bo','faq'=>'itsMall_faq','counsel'=>'itsMall_counsel','affil_counsel'=>'itsMall_counsel','sales'=>'itsMall_counsel2','cooperation'=>'itsMall_cooperation');  

if(in_array($NAME,$defBoard)) {	
	$SNAME = "<option value='{$defSkin[$NAME]}' selected>{$defSkin[$NAME]}</option>";
}
else {
	$handle=opendir("{$bo_path}/skin");
	while ($skin_info = readdir($handle)) {
		if(!eregi("\.",$skin_info)) {
			if($skin_info==$row['s_name']) $select="selected"; 
			else $select="";
			
			$SNAME .= "<option value='{$skin_info}' {$select}>{$skin_info}&nbsp;&nbsp;</option>";
		}
	}
	closedir($handle);
}

for($i=0;$i<16;$i++) {
	if($i == $options[10]) $select="selected"; 
	else $select="";
		
	$UPNUM .= "<option value='{$i}' {$select}>{$i}개</option>";
}

for($i=0;$i<6;$i++) {
	if($i == $options[11]) $select="selected"; 
	else $select="";
		
	$LINKNUM .= "<option value='{$i}' {$select}>{$i}개</option>";
}

$tpl->parse('main');
$tpl->tprint('main');
include "../html/bottom_inc.html";     /*** BOTTOM INCLUDE ***/ 
?>