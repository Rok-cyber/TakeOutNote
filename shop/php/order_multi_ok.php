<?
include "sub_init.php";

$order_num  = isset($_GET['order_num']) ? $_GET['order_num'] : $_POST['order_num'];
$name		= isset($_GET['name']) ? $_GET['name'] : $_POST['name'];
$mode		= $_GET['mode'];
//if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if(!$my_id && $mode=='delete2') alert('먼저 로그인을 하시기 바랍니다','back');

$signdate = date("Y-m-d H:i:s",time());
if(!$order_num) alert("정보가 제대로 넘어오지 못했습니다!","back");

if($mode=='cancel2' || $mode=='paycg2'  || $mode=='cash2' || $mode=='carrok2') {
	if(!$name) alert("정보가 제대로 넘어오지 못했습니다!","back");
	$sql = "SELECT order_status, admess , name1, hphone1, email, pay_total, signdate FROM mall_order_info WHERE name1 = '{$name}' && order_num = '{$order_num}'";
	if(!$row = $mysql->one_row($sql)) alert('주문내역이 존재 하지 않습니다.  고객센터에 문의 하시기 바랍니다.','back');

	$name = urlencode($name);
}
else {
	$page	= $_GET['page'];
	$sdate1	= $_GET['sdate1'];
	$sdate2	= $_GET['sdate2'];
	$status	= $_GET['status'];

	$addstring = "&page={$page}";
	if($status) {
		$addstring .= "&status={$status}";
	}
	if($sdate1 && $sdate2) {	
		if($sdate1 > $sdate2) {$sdate1 = $tmp; $sdate1 = $sdate2; $sdate2 = $tmp;}
		$addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";	
	}  

	$sql = "SELECT order_status, admess, name1, hphone1, email, pay_total, signdate FROM mall_order_info WHERE id='{$my_id}' && order_num='{$order_num}'";
	if(!$row = $mysql->one_row($sql)) alert("주문내역이 존재 하지 않습니다. 고객센터에 문의 하시기 바랍니다.","back");
}

switch($mode) {
	case "cancel" : case "cancel2" : 
		if($row['order_status']!='A') alert("입금확인중일 경우에만 주문 취소가 가능 합니다. 주문취소를 원하시면 고객센터에 문의 하시기 바랍니다.","back");

		$admess = $row['admess']."\n사용자 주문취소";
		$sql = "UPDATE mall_order_info SET order_status='Z', admess='{$admess}', status_date='{$signdate}' WHERE order_num='{$order_num}'";
		$mysql->query($sql);

		$sql = "SELECT uid FROM mall_order_goods WHERE order_num = '{$order_num}'";	
		$mysql->query($sql);
		while($row = $mysql->fetch_array()){
			$sql = "UPDATE mall_order_goods SET  order_status = 'Z', status_date = '{$signdate}' WHERE order_num='{$order_num}' && uid = '{$row['uid']}'";
			$mysql->query2($sql);
				  
			$sql = "SELECT p_cate,p_number,p_qty,p_option FROM mall_order_goods WHERE order_num='{$order_num}' && uid = '{$row['uid']}'";
			$tmp = $mysql->one_row($sql);
			
			if(substr($tmp['p_cate'],0,3)!='999') {
				$sql = "UPDATE mall_goods SET qty = qty + '{$tmp['p_qty']}' WHERE uid='{$tmp['p_number']}' && s_qty='4'";
				$mysql->query2($sql);

				########################## 옵션상품 재고수량 체크 ########################
				$sql = "SELECT s_qty FROM mall_goods WHERE uid='{$tmp['p_number']}'";
				if($mysql->get_one($sql)==4) {
					if($tmp['p_option']) {
						$stmps = explode("|*|",$tmp['p_option']);							
						for($i=0,$cnt=count($stmps);$i<$cnt;$i++) {
							$stmps2 = explode("|",$stmps[$i]);							
							$sql = "UPDATE mall_goods_option SET qty = qty + {$tmp['p_qty']} WHERE uid='{$stmps2[2]}'";
							$mysql->query2($sql);			
						}
					}						
				}
				########################## 옵션상품 재고수량 체크 ########################
			}

			$sql = "UPDATE mall_goods SET o_cnt = o_cnt - '{$tmp['p_qty']}' WHERE uid='{$tmp['p_number']}' && o_cnt>='{$tmp['p_qty']}'";
			$mysql->query2($sql);			
		} 
		
		$sql = "SELECT name1, hphone1, pay_total FROM mall_order_info WHERE order_num = '{$order_num}'";
		$info = $mysql->one_row($sql);

		$sql = "UPDATE mall_reserve SET status='D' WHERE order_num='{$order_num}' && status='A'";
		$mysql->query($sql);

		$sql = "SELECT reserve FROM mall_reserve WHERE order_num='{$order_num}' && status='C'";
		$tmp = $mysql->get_one($sql);
			
		if($tmp>0) {
			$sql = "UPDATE mall_reserve SET status='E' WHERE order_num='{$order_num}' && status='C'";
			$mysql->query($sql);
			$sql = "UPDATE pboard_member SET reserve = reserve + {$tmp} WHERE id = '{$my_id}'";
			$mysql->query($sql);			
		}

		if($info['hphone1']) {
			$code_arr = Array();
			$code_arr['name'] = $info['name1'];
			$code_arr['number'] = $order_num;
			$code_arr['price'] = number_format($info['pay_total']);
			pmallSmsAutoSend($info['hphone1'],"cancel",$code_arr);
		}

		############ 쿠폰 사용가능하게 수정 #############
		$sql = "SELECT cupon FROM mall_order_info WHERE order_num='{$order_num}'";
		$ck_cupon = $mysql->get_one($sql);

		if($ck_cupon) {
			$ck_cupon = explode(",",$ck_cupon);
			for($c=0;$c<count($ck_cupon);$c++) {
				$sql = "UPDATE mall_cupon SET status='A', usedate='0' WHERE uid='{$ck_cupon[$c]}'";
				$mysql->query($sql);
			}		
		}
		############ 쿠폰 사용가능하게 수정 #############

		if($mode=='cancel2') alert("주문이 정상적으로 취소 되었습니다","../{$Main}?channel=osearch&order_num={$order_num}&name={$name}");
		else alert("주문이 정상적으로 취소 되었습니다","../{$Main}?channel=order_detail&order_num={$order_num}{$addstring}");
	break;

	case "carrok" :
		$sql = "SELECT order_status FROM mall_order_info WHERE id='{$my_id}' && order_num='{$order_num}'";
		$row = $mysql->get_one($sql);
		if($row!='D') alert("죄송합니다. 아직 상품이 발송되지 않았거나 이미 수령확인 처리가 되었습니다.","back");
	
		$sql = "UPDATE mall_order_info SET order_status = 'E', status_date = '{$signdate}' WHERE id='{$my_id}' && order_num='{$order_num}'";
		$mysql->query($sql);

		$sql = "SELECT uid FROM mall_order_goods WHERE order_num = '{$order_num}' && order_status = 'D'";	
		$mysql->query($sql);
		while($row = $mysql->fetch_array()){
			$sql = "UPDATE mall_order_goods SET  order_status = 'E', status_date = '{$signdate}' WHERE order_num='{$order_num}' && uid = '{$row['uid']}'";
			$mysql->query2($sql);		
		} 

		$sql = "SELECT sum(reserve) FROM mall_reserve WHERE status = 'A' && order_num = '{$order_num}'";
		$tmp_r = $mysql->get_one($sql);
		if(!$tmp_r && $tmp_r==0) $tmp_r=0;
				
		$sql = "UPDATE mall_reserve SET status = 'B' WHERE status = 'A' && order_num = '{$order_num}'";
		$mysql->query($sql);

					 
		$sql = "UPDATE pboard_member SET reserve = reserve + {$tmp_r} WHERE id = '{$my_id}'";
		$mysql->query($sql);

		alert("수령확인 처리가 되었습니다","../{$Main}?channel=order&order_num={$order_num}{$addstring}");
	break;

	case "carrok2" :
		$sql = "SELECT order_status FROM mall_order_info WHERE order_num='{$order_num}'";
		$row = $mysql->get_one($sql);
		if($row!='D') alert("죄송합니다. 아직 상품이 발송되지 않았거나 이미 수령확인 처리가 되었습니다.","back");
	
		$sql = "UPDATE mall_order_info SET order_status = 'E', status_date = '{$signdate}' WHERE order_num='{$order_num}'";
		$mysql->query($sql);

		$sql = "SELECT uid FROM mall_order_goods WHERE order_num = '{$order_num}' && order_status = 'D'";	
		$mysql->query($sql);
		while($row = $mysql->fetch_array()){
			$sql = "UPDATE mall_order_goods SET  order_status = 'E', status_date = '{$signdate}' WHERE order_num='{$order_num}' && uid = '{$row['uid']}'";
			$mysql->query2($sql);		
		} 
		
		alert("수령확인 처리가 되었습니다","../{$Main}?channel=osearch&order_num={$order_num}&name={$name}");
	break;

	case "paycg" : case "paycg2" :
		$cash_type = $_POST['cash_type'];
		if(!$cash_type) alert("정보가 제대로 넘어오지 못했습니다!","back");

		$sql = "SELECT * FROM mall_order_info WHERE order_num='{$order_num}'";
		if(!$row = $mysql->one_row($sql)) alert("정보가 제대로 넘어오지 못했습니다!","back");

		if($row['cash_sale'] && $cash_type=='C') {
			$pay_total = $_POST['cash_total'];
			if(!$pay_total) alert("정보가 제대로 넘어오지 못했습니다!","back");
			$sql = "UPDATE mall_order_info SET pay_total='{$pay_total}', cash_sale=0 WHERE order_num='{$order_num}'";
			$mysql->query($sql);	
		}
		
		$sql = "UPDATE mall_order_info SET pay_type='{$cash_type}', pay_status='A' WHERE order_num='{$order_num}'";
		$mysql->query($sql);

		movePage("../{$Main}?channel=card_pay&cash_type={$cash_type}&order_num={$order_num}"); 

	break;

	case "cash" : case "cash2" :
		$cash_info = '';
		if($_POST['cash_ctype']=='A') {
			if($_POST['pay_type']=='A') {
				$auth_number = $_POST['cell1'].$_POST['cell2'].$_POST['cell3'];
				if(strlen($auth_number)==10 || strlen($auth_number)==11) {
					$cash_info = "1|{$auth_number}";
				}
			}
			else if($_POST['pay_type']=='B') {
				$auth_number = $_POST['jumin1'].$_POST['jumin2'];
				if(strlen($auth_number)==13) {
					$cash_info = "1|{$auth_number}";
				}
			}
		}
		else {
			$auth_number = $_POST['cnum1'].$_POST['cnum2'].$_POST['cnum3'];
			if(strlen($auth_number)==10) {
				$cash_info = "2|{$auth_number}";
			}
		}

		############ 현금영수증 발급 #############
		$sql = "SELECT code FROM mall_design WHERE mode='O'";
		$code = $mysql->get_one($sql);
		if($code) {
			$code = explode("|",stripslashes($code));
			if($code[0]==1 && $code[1]==2) {
				if($row['signdate'] < date("Y-m-d H:i:s",time()-(86400*$code[2]))) {
					alert("현금영수증 발급 기간이 지나 신청 할 수 없습니다","back");
				}
			}
		}
		else alert("현금영수증 발급 할 수 없습니다","back");
			
		$sql = "SELECT count(*) FROM mall_order_cash WHERE order_num='{$order_num}'";
		if($mysql->get_one($sql)==0) {
			$sql = "SELECT code FROM mall_design WHERE mode='B'";
			$tmp_cash = $mysql->get_one($sql);
			$cash = explode("|*|",stripslashes($tmp_cash));
			$ckCP = $cash[2];

			if(substr($cash_info,0,1)==1) $cash_type = 'A';
			else $cash_type = 'B';

			$auth_number = substr($cash_info,2);
			
			$sql = "SELECT count(*) as cnt, p_name FROM mall_order_goods WHERE order_num='{$order_num}'";
			$tmp = $mysql->one_row($sql);
			if($tmp['cnt']==1) $goods_name	= $tmp['p_name'];
			else $goods_name = $tmp['p_name']."외 ".($tmp['cnt']-1)."건";

			$sql = "SELECT code FROM mall_design WHERE mode='O'";
			$code = $mysql->get_one($sql);
			if($code[3]==2) $tax_type = 'B';
			else $tax_type = 'A';

			$sql = "INSERT INTO mall_order_cash (cp_name,order_num,name,cell,email,price,goods_name,tax_type,cash_type,auth_number,status,status_date,signdate) VALUES ('{$ckCP}','{$order_num}','{$row['name1']}','{$row['hphone1']}','{$row['email']}','{$row['pay_total']}','{$goods_name}','{$tax_type}','{$cash_type}','{$auth_number}','A','{$signdate}','{$signdate}')";			
			$mysql->query($sql);
		}
		if($mode=='cash') alert("현금영수증 발급신청이 정상적으로 처리 되었습니다.","../{$Main}?channel=order_detail&order_num={$order_num}{$addstring}");
		else alert("현금영수증 발급신청이 정상적으로 처리 되었습니다.","../{$Main}?channel=osearch&order_num={$order_num}{$addstring}&name={$name}");
	break;
}
?>