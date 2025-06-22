<?
include "../ad_init.php";

############ 파라미터(값) 검사 ####################
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if($_SERVER['REQUEST_METHOD']=='GET') alert('정상적으로 등록하세요!','back');

$options = "26";
for($i=1;$i<27;$i++){
	$options .= "|".$_POST['opt'.$i][0];
}

$m_form = $_POST['edu_in']."|".$_POST['hobby_in']."|".$_POST['job_in']."|".$_POST['use_in']."|".$_POST['useid_in']."|".$_POST['add1']."|".$_POST['add2']."|".$_POST['add3']."|".$_POST['add4']."|".$_POST['add5']."|".$_POST['sname'];

$m_form = addslashes($m_form);

$sql = "UPDATE pboard_member SET address = '{$options}', info='{$m_form}' WHERE uid=1";
$mysql->query($sql);

alert("회원가입 양식을 수정했습니다!","./mem_form.html");
?>