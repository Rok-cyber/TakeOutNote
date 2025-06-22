<?
include "sub_init.php";

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');

$signdate = date("Y-m-d H:i:s",time());
$s_arr = array("X"=>"반품","Y"=>"교환","Z"=>"취소");

$order_num	= $_POST['order_num'];
$status		= $_POST['status'];
$sgoods		= $_POST['sgoods'];
$reason_code= $_POST['reason_code'];
$message	= addslashes($_POST['message']);
$bank		= addslashes($_POST['bank']);
$bank_num	= addslashes($_POST['bank_num']);
$bank_name	= addslashes($_POST['bank_name']);
if($bank && $bank_num && $bank_name) $bank_info = "{$bank} {$bank_num} {$bank_name}";

if(!$order_num || !$sgoods || !$status) alert("정보가 넘어오지 못했습니다.","back");

$sql = "SELECT order_status FROM mall_order_info WHERE order_num = '{$order_num}'";
$def_status = $mysql->get_one($sql);

if(!$def_status) alert("해당 주문이 삭제 되었거나 존재 하지않습니다.","back");

$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$sgoods})";
$mysql->query($sql);

$total = $tcarr = 0;
while($data = $mysql->fetch_array()) {
	if($data['order_status']!='X' && $data['order_status']!='Y' && $data['order_status']!='Z') {				
		$sql = "UPDATE mall_order_goods SET order_status = '{$status}', order_status2 = 'A', status_date='{$signdate}' WHERE uid='{$data['uid']}'";
		$mysql->query2($sql);		
	}
	else {
		$sgoods = str_replace(",{$data['uid']}","",$sgoods);
	}	
}

$status2 = 'A';

if($status=='Z' && $def_status=='A') {   // 미입금상태 주문취소시 바로 취소처리 
	$status2 = 'D';

	$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$order_num}' && order_status!='Z'";
	if($mysql->get_one($sql)==0) {  //모든상품 주문취소시
		
		$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}'";
		$mysql->query($sql);
		
		while($data = $mysql->fetch_array()) {
			$sql = "UPDATE mall_goods SET qty = qty + '{$data[p_qty]}' WHERE uid='{$data[p_number]}' && s_qty='4'";
			$mysql->query2($sql);

			$sql = "UPDATE mall_goods SET o_cnt = o_cnt - '{$data['p_qty']}' WHERE uid='{$data['p_number']}' && o_cnt>='{$data['p_qty']}'";
			$mysql->query2($sql);

			########################## 옵션상품 재고수량 체크 ########################
			$sql = "SELECT s_qty FROM mall_goods WHERE uid='{$data['p_number']}'";
			if($mysql->get_one($sql)==4) {
				if($data['p_option']) {
					$stmps = explode("|*|",$data['p_option']);							
					for($i=0,$cnt=count($stmps);$i<$cnt;$i++) {
						$stmps2 = explode("|",$stmps[$i]);							
						$sql = "UPDATE mall_goods_option SET qty = qty + {$data['p_qty']} WHERE uid='{$stmps2[2]}'";
						$mysql->query2($sql);			
					}
				}						
			}
			########################## 옵션상품 재고수량 체크 ########################

			$sql = "UPDATE mall_reserve SET status='D' WHERE order_num='{$order_num}' && status='A' && goods_num='{$data['p_number']}|{$data['p_option']}'";
			$mysql->query2($sql);			
		}
		
		$sql = "UPDATE mall_order_goods SET order_status2 = 'D', status_date='{$signdate}' WHERE order_num='{$order_num}' && order_status='Z'";
		$mysql->query2($sql);			

		$sql = "UPDATE mall_order_info SET order_status = 'Z', status_date='{$signdate}' WHERE order_num='{$order_num}'";
		$mysql->query2($sql);			

		$sql = "SELECT id, name1, email, hphone1, pay_total, carr_info, cash_info FROM mall_order_info WHERE order_num = '{$order_num}' && id='{$my_id}'";
		$info = $mysql->one_row($sql);

		############ SMS 보내기 #############
		if($info['hphone1']) {
			$code_arr = Array();
			$code_arr['name'] = $info['name1'];
			$code_arr['number'] = $order_num;
			$code_arr['price'] = number_format($info['pay_total']);			
			pmallSmsAutoSend($info['hphone1'],"cancel",$code_arr);
		}
		############ SMS 보내기 #############
		
		if($info['id']) {
			############ 사용적립금 환원 #############
			$sql = "SELECT reserve FROM mall_reserve WHERE order_num='{$order_num}' && status='C'";
			$tmp = $mysql->get_one($sql);
			
			if($tmp>0) {
				$sql = "UPDATE mall_reserve SET status='E' WHERE order_num='{$order_num}' && status='C'";
				$mysql->query($sql);
				$sql = "UPDATE pboard_member SET reserve = reserve + {$tmp} WHERE id = '{$info['id']}'";
				$mysql->query($sql);			
			}
			############ 사용적립금 환원 #############
		}
	}	
	else {  //선택 상품문 취소시
		
		$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$sgoods})";
		$mysql->query($sql);
		
		while($data = $mysql->fetch_array()) {
			$ck_status = 'Y';
			$sql = "UPDATE mall_order_goods SET order_status = '{$status}', order_status2 = 'D', status_date='{$signdate}' WHERE uid='{$data['uid']}'";
			$mysql->query2($sql);

			$sql = "UPDATE mall_goods SET qty = qty + '{$data[p_qty]}' WHERE uid='{$data[p_number]}' && s_qty='4'";
			$mysql->query2($sql);

			$sql = "UPDATE mall_goods SET o_cnt = o_cnt - '{$data['p_qty']}' WHERE uid='{$data['p_number']}' && o_cnt>='{$data['p_qty']}'";
			$mysql->query2($sql);

			########################## 옵션상품 재고수량 체크 ########################
			$sql = "SELECT s_qty FROM mall_goods WHERE uid='{$data['p_number']}'";
			if($mysql->get_one($sql)==4) {
				if($data['p_option']) {
					$stmps = explode("|*|",$data['p_option']);							
					for($i=0,$cnt=count($stmps);$i<$cnt;$i++) {
						$stmps2 = explode("|",$stmps[$i]);							
						$sql = "UPDATE mall_goods_option SET qty = qty + {$data['p_qty']} WHERE uid='{$stmps2[2]}'";
						$mysql->query2($sql);			
					}
				}						
			}
			########################## 옵션상품 재고수량 체크 ########################

			$sql = "UPDATE mall_reserve SET status='D' WHERE order_num='{$order_num}' && status='A' && goods_num='{$data['p_number']}|{$data['p_option']}'";
			$mysql->query2($sql);
		}

		modifyOrder($order_num);
	}
}

$sql = "INSERT INTO mall_order_change VALUES('','{$sgoods}','{$order_num}','','{$reason_code}','{$bank_info}','{$message}','0','0','0','','{$status}','{$status2}','{$signdate}','{$signdate}')";
$mysql->query($sql);

alert("{$s_arr[$status]}신청이 완료 되었습니다.","close4");
?>