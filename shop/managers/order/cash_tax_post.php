<?
######################## lib include
include "../ad_init.php";
require "{$lib_path}/lib.Shop.php";

$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

$field		= $_GET['field'];
$word		= $_GET['word'];
$sdate1		= $_GET['sdate1'];
$sdate2		= $_GET['sdate2'];
$page		= $_GET['page'];
$limit		= $_GET['limit'];
$status		= $_GET['status'];
$order		= $_GET['order'];

if($field && $word) $addstring .= "&field=$field&word={$word}";
if($sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";
if($page) $addstring .="&page={$page}";
if($limit) $addstring .="&limit={$limit}";
if($status) $addstring .="&status={$status}";
if($order) $addstring .="&order={$order}";

switch($mode) {
	
	case "conf" :
		$ckCP	= $_POST['ckCP'];
		$use	= $_POST['use'];
		$type	= $_POST['type'];
		$ctype	= $_POST['ctype'];
		$days	= $_POST['days'];

		if($ckCP!='Y' || !$days) alert("정보가 제대로 넘어오지 못했습니다!","back");
		
		$code = "{$use}|{$type}|{$days}|{$ctype}";
		$sql = "SELECT count(*) FROM mall_design WHERE mode='O'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_design SET mode='O', code='{$code}'";
		}
		else {
			$sql = "UPDATE mall_design SET code='{$code}' WHERE mode='O'";
		}
		$mysql->query($sql);

		alert("현금영수증 기본정보를 설정 했습니다","cash_tax_conf.html");
	break;	

	case "write" :
		$signdate = date("Y-m-d H:i:s",time());
		$ckCP	= $_POST['ckCP'];
		$name	= addslashes($_POST['name']);
		$cell	= addslashes($_POST['cell']);
		$email	= addslashes($_POST['email']);
		$goods_name	= addslashes($_POST['goods_name']);
		$price	= preg_replace('/[^0-9\-]/', '', $_POST['price']);
		if($_POST['cash_type']=='A') {
			if($_POST['pay_type']=='A') {
				$auth_number = $_POST['cell1'].$_POST['cell2'].$_POST['cell3'];
			}
			else if($_POST['pay_type']=='B') {
				$auth_number = $_POST['jumin1'].$_POST['jumin2'];
			}
		}
		else {
			$auth_number = $_POST['cnum1'].$_POST['cnum2'].$_POST['cnum3'];
		}
		if(!$ckCP || !$name || !$goods_name || !$auth_number) alert("정보가 제대로 넘어오지 못했습니다!","back");
		
		$sql = "SELECT code FROM mall_design WHERE mode='O'";
		$code = $mysql->get_one($sql);
		if($code[3]==2) $tax_type = 'B';
		else $tax_type = 'A';

		$sql = "INSERT INTO mall_order_cash (cp_name,name,cell,email,price,goods_name,tax_type,cash_type,auth_number,status,status_date,signdate) VALUES ('{$ckCP}','{$name}','{$cell}','{$email}','{$price}','{$goods_name}','{$tax_type}','{$cash_type}','{$auth_number}','A','{$signdate}','{$signdate}')";
		$mysql->query($sql);

		alert("현금연수증 개별발급요청이 정상적으로 처리 되었습니다. 현금연수증발급/조회에서 발급하시기 바랍니다.","cash_tax_write.html");
	break;

	case "apply" :
		$uid = $_GET['uid'];
		
		$sql = "SELECT code FROM mall_design WHERE mode='O'";
		$code = $mysql->get_one($sql);
		if(!$code)  alert("현금영수증설정을 하지 않았습니다.","back");
		
		$sql = "SELECT * FROM mall_order_cash WHERE uid='{$uid}'";
		$row = $mysql->one_row($sql);

		if($row['status']!='A') alert("발급요청상태일 때만 발급이 가능 합니다.","back");
		if($row['order_num']) {
			$sql = "SELECT count(*) FROM mall_order_cash WHERE order_num='{$row['order_num']}' && status='B'";
			if($mysql->get_one($sql)>0) alert("해당 주문건으로 이미 현금영수증이 발급되었습니다. 중복 발급은 되지 않습니다","back");		
		}
		
		$sql = "SELECT code FROM mall_design WHERE mode='B'";
		$tmp_cash = $mysql->get_one($sql);
		$cash = explode("|*|",stripslashes($tmp_cash));

		switch($cash[2]){
			case "KCP" : default :				
				$rtnVls = implode("", socketPost("http://".$_SERVER["SERVER_NAME"]."/card/kcp/cash.php?uid={$uid}"));
				if(!eregi("true",$rtnVls)) alert("현금영수증이 발급되지 않았습니다. 에러 메세지를 확인 해보시기 바랍니다.","back");									
			break;
		}		
		alert("해당 현금영수증이 정상적으로 발급되었습니다.","cash_tax_list.php?{$addstring}");
	break;

	case "cancel" :
		$uid = $_GET['uid'];
		
		$sql = "SELECT * FROM mall_order_cash WHERE uid='{$uid}'";
		$row = $mysql->one_row($sql);

		if($row['status']!='B') alert("발급완료상태일 때만 발급취소가 가능 합니다.","back");
		
		$sql = "SELECT code FROM mall_design WHERE mode='B'";
		$tmp_cash = $mysql->get_one($sql);
		$cash = explode("|*|",stripslashes($tmp_cash));

		switch($cash[2]){
			case "KCP" : default :				
				$rtnVls = implode("", socketPost("http://".$_SERVER["SERVER_NAME"]."/card/kcp/cash.php?uid={$uid}&mode=cancel"));			
				if(!eregi("true",$rtnVls)) alert("현금영수증 발급취소가 되지 않았습니다. 에러 메세지를 확인 해보시기 바랍니다.","back");									
			break;
		}		
		alert("해당 현금영수증이 정상적으로 발급취소 되었습니다.","cash_tax_list.php?{$addstring}");
	break;

	case "del" :
		$uid = $_GET['uid'];
		
		$sql = "SELECT * FROM mall_order_cash WHERE uid='{$uid}'";
		$row = $mysql->one_row($sql);

		if($row['status']=='C' || ($row['status']=='A' && !$row['order_num'])) {
			$sql = "DELETE FROM mall_order_cash WHERE uid='{$uid}'";
			$mysql->query($sql);
			movePage("cash_tax_list.php?{$addstring}");
		}
		else alert("해당 현금영수증을 삭제 할 수 없습니다.","back");

	break;
}
?>