<?
$skin_inc='Y';
######################## lib include
include "../ad_init.php";
include "{$lib_path}/lib.Shop.php";

//변수 정의
$signdate = date("Y-m-d H:i:s",time());
$s_arr = array("X"=>"반품","Y"=>"교환","Z"=>"취소");

###################### 변수 정의 ##########################
$mode = $_GET['mode'];
$order_num	= isset($_POST['order_num']) ? $_POST['order_num'] : $_GET['order_num'];

if($mode=='restore' || $mode=='del' || $mode=='return') {
	$pop		= $_POST['pop'];
	$gs			= $_GET['gs'];
	$field		= $_GET['field'];
	$word		= $_GET['word'];
	$smoney1	= $_GET['smoney1'];
	$smoney2	= $_GET['smoney2'];
	$sdate1		= $_GET['sdate1'];
	$sdate2		= $_GET['sdate2'];
	$page		= $_GET['page'];
	$limit		= $_GET['limit'];
	$type		= $_GET['type'];
	$status		= $_GET['status'];
	$order		= $_GET['order'];
	$pop		= $_GET['pop'];
	$step		= !empty($_GET['step']) ? $_GET['step'] : $_POST['step'];

	if($gs) $addstring2 ="gs={$gs}";
	if($step) $addstring2 ="gs={$step}";		
	if($field && $word) $addstring .= "&field=$field&word={$word}";
	if($smoney1 && $smoney2) $addstring .= "&smoney1={$smoney1}&smoney2={$smoney2}";
	if($sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";
	if($page) $addstring .="&page={$page}";
	if($limit) $addstring .="&limit={$limit}";
	if($type) $addstring .="&type={$type}";
	if($status) $addstring .="&status={$status}";
	if($order) $addstring .="&order={$order}";
	if($pop) $addstring .="&pop={$pop}";
	$url = "./order_view.php?order_num={$order_num}{$addstring2}&{$addstring}";
}

switch($mode) {
	case "return" :
		$uid = $_GET['uid'];
		if(!$order_num || !$uid) alert("정보가 넘어오지 못했습니다.","back");
		$sql = "SELECT * FROM mall_order_change WHERE uid='{$uid}' && order_num='{$order_num}'";
		$row = $mysql->one_row($sql);
		if(!$row) alert("반품/교환내역이 존재 하지 않습니다","back");

		$sql = "UPDATE mall_order_change SET status2 = 'C', status_date = '{$signdate}' WHERE uid='{$uid}' && order_num='{$order_num}'";
		$mysql->query($sql);

		if($row['status']=='X') {
			$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$row['sgoods']})";
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

				$sql = "UPDATE mall_reserve SET status='D' WHERE order_num='{$order_num}' && goods_num='{$data['p_number']}|{$data['p_option']}'";
				$mysql->query2($sql);

				$sql = "UPDATE mall_order_goods SET order_status2='C', status_date='{$signdate}' WHERE uid='{$data['uid']}'";
				$mysql->query2($sql);
			}
		}		

		alert("반품/교환회수완료 처리가 되었습니다.",$url);

	break;

	case "send" :
		$uid = $_POST['uid'];
		$carr_info = $_POST['delivery']."|".addslashes($_POST['carr_num']);
		if(!$order_num || !$uid) alert("정보가 넘어오지 못했습니다.","back");
		
		$sql = "SELECT * FROM mall_order_change WHERE uid='{$uid}' && order_num='{$order_num}'";
		$row = $mysql->one_row($sql);
		if(!$row) alert("교환내역이 존재 하지 않습니다","back");

		$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$row['sgoods']})";
		$mysql->query($sql);
		
		while($data = $mysql->fetch_array()) {
			if($data['order_status']=='Y') {
				$sql = "UPDATE mall_order_goods SET order_status2 = 'D', status_date='{$signdate}', carr_info='{$carr_info}'  WHERE uid='{$data['uid']}'";
				$mysql->query2($sql);
			}
		}			

		$sql = "UPDATE mall_order_change SET status2 = 'D', status_date = '{$signdate}' WHERE uid='{$uid}' && order_num='{$order_num}'";
		$mysql->query($sql);

		alert("교환발송 처리가 되었습니다.","close4");

	break;

	case "restore" :
		$uid = $_GET['uid'];
		if(!$order_num || !$uid) alert("정보가 넘어오지 못했습니다.","back");
		
		$sql = "SELECT * FROM mall_order_change WHERE uid='{$uid}' && order_num='{$order_num}'";
		$row = $mysql->one_row($sql);
		if(!$row) alert("취소/반품/교환 내역이 존재 하지 않습니다","back");
		$sgoods		= $row['sgoods'];

		$sql = "SELECT order_status FROM mall_order_info WHERE order_num = '{$order_num}'";
		$def_status = $mysql->get_one($sql);

		if($def_status=='Z') alert("복원할수 없는 상태 입니다. 진행상황 처리에서 상태를 변경하시기 바랍니다","back");

		$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$sgoods})";
		$mysql->query($sql);

		$total = $tcarr = 0;
		while($data = $mysql->fetch_array()) {
			if($data['order_status']=='X' || $data['order_status']=='Y' || $data['order_status']=='Z') {
				$sql = "UPDATE mall_order_goods SET order_status = '{$def_status}', order_status2 = '', status_date='{$signdate}' WHERE uid='{$data['uid']}'";
				$mysql->query2($sql);

				if($row['status']!='Y') {
					$sql = "UPDATE mall_goods SET qty = qty - '{$data[p_qty]}' WHERE uid='{$data[p_number]}' && s_qty='4'";
					$mysql->query2($sql);

					$sql = "UPDATE mall_goods SET o_cnt = o_cnt + '{$data['p_qty']}' WHERE uid='{$data['p_number']}'";
					$mysql->query2($sql);

					########################## 옵션상품 재고수량 체크 ########################
					$sql = "SELECT s_qty FROM mall_goods WHERE uid='{$data['p_number']}'";
					if($mysql->get_one($sql)==4) {
						if($data['p_option']) {
							$stmps = explode("|*|",$data['p_option']);							
							for($i=0,$cnt=count($stmps);$i<$cnt;$i++) {
								$stmps2 = explode("|",$stmps[$i]);							
								$sql = "UPDATE mall_goods_option SET qty = qty - {$data['p_qty']} WHERE uid='{$stmps2[2]}'";
								$mysql->query2($sql);			
							}
						}						
					}
					########################## 옵션상품 재고수량 체크 ########################			

					$sql = "UPDATE mall_reserve SET status='A' WHERE order_num='{$order_num}' && status='D' && goods_num='{$data['p_number']}|{$data['p_option']}'";
					$mysql->query2($sql);
				}
			}
		}

		$sql = "UPDATE mall_order_change SET status2 = 'Z', status_date = '{$signdate}' WHERE uid='{$uid}' && order_num='{$order_num}'";
		$mysql->query($sql);
		
		if($status!='Y') {
			modifyOrder($order_num);
		}		
		alert("{$s_arr[$status]} 복원 처리가 되었습니다.",$url);
	break;

	case "del" :
		$uid = $_GET['uid'];
		if(!$order_num || !$uid) alert("정보가 넘어오지 못했습니다.","back");
		
		$sql = "SELECT * FROM mall_order_change WHERE uid='{$uid}' && order_num='{$order_num}'";
		$row = $mysql->one_row($sql);
		if(!$row) alert("취소/반품/교환 내역이 존재 하지 않습니다","back");
		if($row['status2']!='Z') alert("복원 되었을 경우에만 해당 내역을 삭제할 수 있습니다","back");
		
		$sql = "DELETE FROM mall_order_change WHERE uid='{$uid}' && order_num='{$order_num}'";
		$mysql->query($sql);
		
		alert("{$s_arr[$status]}복원된 내역이 삭제 처리 되었습니다.",$url);
	break;

	case "refund" :
		$name		= isset($_POST['name']) ? $_POST['name'] : $my_name;
		$refund		= $_POST['total'];
		$refund_g	= $_POST['g_total'];
		$refund_r	= $_POST['reserve'];
		$message	= addslashes($_POST['message']);
		$bank		= addslashes($_POST['bank']);
		$bank_num	= addslashes($_POST['bank_num']);
		$bank_name	= addslashes($_POST['bank_name']);
		$uid		= $_POST['uid'];
		if($bank && $bank_num && $bank_name) $bank_info = "{$bank} {$bank_num} {$bank_name}";
		if($_POST['card']=='Y') $refund_type='C';
		else $refund_type='B';

		if(!$order_num || !$uid) alert("정보가 넘어오지 못했습니다.","back");

		$sql = "SELECT order_status FROM mall_order_info WHERE order_num = '{$order_num}'";
		$def_status = $mysql->get_one($sql);
		if($def_status=='A') alert('입금대기중 상태일때는 환불할 수 없습니다',"back");
		$status2 = 'D';

		$sql = "SELECT sgoods FROM mall_order_change  WHERE order_num='{$order_num}' && uid='{$uid}'";
		$sgoods = $mysql->get_one($sql);
		
		$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$sgoods})";
		$mysql->query($sql);
		
		while($data = $mysql->fetch_array()) {
			if($data['order_status']=='X' || $data['order_status']=='Z') {				
				$sql = "UPDATE mall_order_goods SET order_status2 = '{$status2}', status_date='{$signdate}' WHERE uid='{$data['uid']}'";
				$mysql->query2($sql);
			}
		}
		
		$sql = "UPDATE mall_order_change SET refund='{$refund}', refund_g='{$refund_g}', refund_r='{$refund_r}', refund_type='{$refund_type}', name='{$name}', bank_info='{$bank_info}', message='{$message}', status2='{$status2}', status_date='{$signdate}' WHERE uid='{$uid}' && order_num='{$order_num}'";		
		$mysql->query($sql);

		if($refund_r>0) {
			$sql = "SELECT id FROM mall_order_info WHERE order_num='{$order_num}'";
			$id = $mysql->get_one($sql);
			if($id && $id!='guest') {
				$sql = "UPDATE pboard_member SET reserve = reserve + {$refund_r} WHERE id = '{$id}'";
				$mysql->query($sql);
				$subject = "취소/반품상품 환불에 따른 사용 적립금 환원";
				$sql = "INSERT INTO mall_reserve VALUES ('','{$id}','{$subject}','{$refund_r}','','{$order_num}','B','{$signdate}')";
				$mysql->query($sql);
			}
		}

		$sql = "SELECT count(*) FROM mall_order_goods WHERE ((order_status='X' && order_status2!='D') || (order_status='Z' && order_status2!='D') || order_status='A' || order_status='B' || order_status='C' || order_status='D' || order_status='E') && order_num='{$order_num}'";
		if($mysql->get_one($sql)==0) {  //모든 상품이 취소일 경우
			
			$sql = "UPDATE mall_order_info SET order_status = 'Z', status_date='{$signdate}' WHERE order_num = '{$order_num}'";	
			$mysql->query($sql);

			modifyOrder($order_num,1);

			$sql = "SELECT id, name1, email, hphone1, pay_total, carr_info, cash_info FROM mall_order_info WHERE order_num = '{$order_num}'";
			$info = $mysql->one_row($sql);
			$user_id = $info['id'];
			
			############ 현금영수증 발급취소 #############
			$sql = "SELECT uid, status FROM mall_order_cash WHERE order_num='{$order_num}'";
			if($cash_row = $mysql->one_row($sql)) {
				if($cash_row['status']=='B') {
					$uid = $cash_row['uid'];
					$sql = "SELECT code FROM mall_design WHERE mode='B'";
					$tmp_cash = $mysql->get_one($sql);
					$cash = explode("|*|",stripslashes($tmp_cash));

					switch($cash[2]){
						case "KCP" : default :										
							$rtnVls = implode("", socketPost("http://".$_SERVER["SERVER_NAME"]."/card/kcp/cash.php?uid={$uid}&mode=cancel"));					
							if(!eregi("true",$rtnVls)) alert("현금영수증이 취소되지 않았습니다. 에러 메세지를 확인 해보시기 바랍니다.","back");									
						break;
					}		
				}
			}
			############ 현금영수증 발급취소 #############
			
			############ SMS 보내기 #############
			if($info['hphone1']) {
				$code_arr = Array();
				$code_arr['name'] = $info['name1'];
				$code_arr['number'] = $order_num;
				$code_arr['price'] = number_format($info['pay_total']);			
				pmallSmsAutoSend($info['hphone1'],"cancel",$code_arr);
			}
			############ SMS 보내기 #############

			############ 사용적립금 환원 #############
			$sql = "SELECT reserve FROM mall_reserve WHERE order_num='{$order_num}' && status='C'";
			$tmp = $mysql->get_one($sql);
			
			if($tmp>0) {
				$sql = "UPDATE mall_reserve SET status='E' WHERE order_num='{$order_num}' && status='C'";
				$mysql->query($sql);
				$sql = "UPDATE pboard_member SET reserve = reserve + {$tmp} WHERE id = '{$user_id}'";
				$mysql->query($sql);			

				$sql = "SELECT count(*) FROM mall_reserve WHERE id='{$user_id}' && goods_num='{$order_num}' && status='B' && subject='취소/반품상품 환불에 따른 사용 적립금 환원'";
				if($mysql->get_one($sql)>0) {
					$sql = "DELETE FROM mall_reserve WHERE id='{$user_id}' && goods_num='{$order_num}' && status='B' && subject='취소/반품상품 환불에 따른 사용 적립금 환원'";
					$mysql->query($sql);

					$sql = "SELECT SUM(reserve) FROM  mall_reserve WHERE id='{$user_id}' && status='B'";
					$MONEY1= $mysql->get_one($sql);
					$sql = "SELECT SUM(reserve) FROM  mall_reserve WHERE id='{$user_id}' && status='C'";
					$MONEY2 = $mysql->get_one($sql);
					$MONEY3 = ($MONEY1 - $MONEY2);
					$sql = "UPDATE pboard_member SET reserve = '{$MONEY3}' WHERE id = '{$user_id}'"; 
					$mysql->query($sql);
				}
			}			
			############ 사용적립금 환원 #############
			
		}
		
		$sql = "SELECT count(*) FROM mall_order_goods WHERE order_num='{$order_num}' && uid NOT IN({$sgoods})";
		if($mysql->get_one($sql)>0) modifyOrder($order_num);

		alert("환불처리가 완료 되었습니다.","close4");
	break;

	default :
		$status		= $_POST['status'];
		$sgoods		= $_POST['sgoods'];
		$name		= isset($_POST['name']) ? $_POST['name'] : $my_name;
		$reason_code= $_POST['reason_code'];
		$message	= addslashes($_POST['message']);
		$bank		= addslashes($_POST['bank']);
		$bank_num	= addslashes($_POST['bank_num']);
		$bank_name	= addslashes($_POST['bank_name']);
		$uid		= $_POST['uid'];
		if($bank && $bank_num && $bank_name) $bank_info = "{$bank} {$bank_num} {$bank_name}";

		if(!$order_num || !$sgoods || !$status) alert("정보가 넘어오지 못했습니다.","back");

		$sql = "SELECT order_status FROM mall_order_info WHERE order_num = '{$order_num}'";
		$def_status = $mysql->get_one($sql);
		if($def_status=='A' && $status=='Z') {
			$status2 = 'D';
			$msg = "완료";
		}
		else {
			$status2 = 'B';
			$msg = "승인";
		}
		
		$sql = "SELECT * FROM mall_order_goods WHERE order_num='{$order_num}' && uid IN({$sgoods})";
		$mysql->query($sql);
		
		$total = $tcarr = 0;
		while($data = $mysql->fetch_array()) {
			if(($data['order_status']=='X' || $data['order_status']=='Y' || $data['order_status']=='Z') && $data['order_status2']=='A') $ck_status = 'Y';
			else $ck_status = '';
			if(($data['order_status']!='X' && $data['order_status']!='Y' && $data['order_status']!='Z') || $ck_status=='Y') {					
				$sql = "UPDATE mall_order_goods SET order_status = '{$status}', order_status2 = '{$status2}', status_date='{$signdate}' WHERE uid='{$data['uid']}'";
				$mysql->query2($sql);

				if($status!='Y') {
					
					if($status=='Z') {
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
				}
			}
			else {
				$sgoods = str_replace(",{$data['uid']}","",$sgoods);
			}
			
		}
		
		if($uid) {
			$sql = "UPDATE mall_order_change SET sgoods='{$sgoods}', name='{$name}', reason_code='{$reason_code}', bank_info='{$bank_info}', message='{$message}', status2='{$status2}', status_date='{$signdate}' WHERE uid='{$uid}' && order_num='{$order_num}'";
		}
		else {
			$sql = "INSERT INTO mall_order_change (uid,sgoods,order_num,name,reason_code,bank_info,message,status,status2,status_date,signdate) VALUES('','{$sgoods}','{$order_num}','{$name}','{$reason_code}','{$bank_info}','{$message}','{$status}','{$status2}','{$signdate}','{$signdate}')";
		}
		$mysql->query($sql);

		if($status!='Y') {
			
			$sql = "SELECT count(*) FROM mall_order_goods WHERE order_status!='X' && order_status!='Z' && order_status2!='D' && order_num='{$order_num}'";
			if($mysql->get_one($sql)==0) {
				
				$sql = "UPDATE mall_order_info SET order_status = 'Z', status_date='{$signdate}' WHERE order_num = '{$order_num}'";	
				$mysql->query($sql);

				modifyOrder($order_num,1);

				$sql = "SELECT id, name1, email, hphone1, pay_total, carr_info, cash_info FROM mall_order_info WHERE order_num = '{$order_num}'";
				$info = $mysql->one_row($sql);
				$user_id = $info['id'];
				
				############ 현금영수증 발급취소 #############
				$sql = "SELECT uid, status FROM mall_order_cash WHERE order_num='{$order_num}'";
				if($cash_row = $mysql->one_row($sql)) {
					if($cash_row['status']=='B') {
						$uid = $cash_row['uid'];
						$sql = "SELECT code FROM mall_design WHERE mode='B'";
						$tmp_cash = $mysql->get_one($sql);
						$cash = explode("|*|",stripslashes($tmp_cash));

						switch($cash[2]){
							case "KCP" : default :										
								$rtnVls = implode("", socketPost("http://".$_SERVER["SERVER_NAME"]."/card/kcp/cash.php?uid={$uid}&mode=cancel"));					
								if(!eregi("true",$rtnVls)) alert("현금영수증이 취소되지 않았습니다. 에러 메세지를 확인 해보시기 바랍니다.","back");									
							break;
						}		
					}
				}
				############ 현금영수증 발급취소 #############
				
				############ SMS 보내기 #############
				if($info['hphone1']) {
					$code_arr = Array();
					$code_arr['name'] = $info['name1'];
					$code_arr['number'] = $order_num;
					$code_arr['price'] = number_format($info['pay_total']);			
					pmallSmsAutoSend($info['hphone1'],"cancel",$code_arr);
				}
				############ SMS 보내기 #############

				############ 사용적립금 환원 #############
				$sql = "SELECT reserve FROM mall_reserve WHERE order_num='{$order_num}' && status='C'";
				$tmp = $mysql->get_one($sql);
				
				if($tmp>0) {
					$sql = "UPDATE mall_reserve SET status='E' WHERE order_num='{$order_num}' && status='C'";
					$mysql->query($sql);
					$sql = "UPDATE pboard_member SET reserve = reserve + {$tmp} WHERE id = '{$user_id}'";
					$mysql->query($sql);			
				}
				############ 사용적립금 환원 #############
				
			}
			else modifyOrder($order_num);		
		}
		alert("{$s_arr[$status]}{$msg}처리가 되었습니다.","close4");
	break;
}

?>