<? 
$skin_inc = "Y";
include "../html/top_inc.html"; // 상단 HTML 

$skin = ".";
######################## lib include
require "{$lib_path}/class.Template.php";

###################### 변수 정의 ##########################
$mode		= isset($_GET['mode']) ? $_GET['mode'] : 'write';
$field		= $_GET['field'];
$word		= $_GET['word'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$status		= $_GET['status'];
$type		= $_GET['type'];
$stype		= $_GET['stype'];
$method		= $_GET['method'];
$uid		= $_GET['uid'];

##################### addstring ############################
if($field && $word) $addstring = "&field=$field&word={$word}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($status) $addstring .="&status={$status}";
if($type) $addstring .="&type={$type}";
if($stype) $addstring .="&stype={$stype}";
if($method) $addstring .="&method={$method}";


// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./attendance_write.html");
$tpl->scan_area("main");


if($mode=='write') {	//
	$TMODE = "등록";
	$CKD1A = "checked";
	$CKD2A = "checked";
	$CKD3S = "checked";
	$MSG1 = "이벤트에 참여해주셔서 감사합니다.";
	$MSG2 = "이미 이벤트에 참여하셨습니다. 다음에 다시 참여해주세요.";
	$MSG3 = "고객님께 이벤트 성공에 의한 적립금이 지급되었습니다. 이벤트에 참여해주셔서 감사합니다.";
	$MSG4 = "고객님께 이벤트 성공에 의한 적립금이 지급될예정입니다. 이벤트에 참여해주셔서 감사합니다.";
	$CKDN = "checked";

} 
else {	//상품수정	

	if(!$uid)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
	$sql = "SELECT * FROM mall_attendance WHERE uid='{$uid}'";
	if(!$row=$mysql->one_row($sql)) alert("등록되지않은 쿠폰이거나 삭제된 쿠폰 입니다.","back");

	$TITLE	= stripslashes($row['title']);
	$SDATE  = substr($row['s_date'],0,10);
	$EDATE  = substr($row['e_date'],0,10);
	if($row['type']=='A') $CONDI1  = stripslashes($row['condi']);
	else if($row['type']=='T') $CONDI2  = stripslashes($row['condi']);
	$POINT  = stripslashes($row['point']);
	$MSG1   = stripslashes($row['msg1']);
	$MSG2   = stripslashes($row['msg2']);
	$MSG3   = stripslashes($row['msg3']);
	$MSG4   = stripslashes($row['msg4']);
	$CODE   = stripslashes($row['code']);

	${"CKD1".$row['type']} = "checked";
	${"CKD2".$row['stype']} = "checked";
	${"CKD3".$row['method']} = "checked";	
	${"CKD".$row['code_use']} = "checked";
	
	if($row['img']){
		$IMG = $row['img'];
		$IMG2 = imgSizeCh("{$dir}/",$row['img'],'600');	 
		$tpl->parse("is_img");
	}

	$TMODE = "수정";
	
}	//end of mode

$up_dir = previlEncode("../../image/up_img/attendance/");		

$IMGSIZE = $SKIN_DEFINE['ctitle_img'];

$LIST = "attendance_list.php?{$addstring}";
$tpl->parse("main");
$tpl->tprint("main");

include "../html/bottom_inc.html"; // 하단 HTML
?>
