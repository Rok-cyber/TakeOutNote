<?
include "sub_init.php";

if(!$my_id) alert("먼저 로그인을 하시기 바랍니다.","back");

$mode = $_GET['mode'];

switch($mode) {
	case "order" :
		$uid = $_GET['uid'];
		if(!$uid) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');
		
		$sql = "SELECT * FROM mall_cooperate WHERE uid='{$uid}' && id='{$my_id}'";
		if(!$row = $mysql->one_row($sql)) alert('신청된 내역이 존재하지 않습니다.','back');
		if($row['status']!='A') {
			if($row['status']=='B') alert("이미 구매가 완료 되었습니다.","back");
			else if($row['status']=='D') alert("신청취소가 되어 결제 하실 수 없습니다.","back");
		}

		$sql = "SELECT uid, cate, coop_sdate, coop_edate, coop_cnt, coop_price, reserve, coop_sale FROM mall_goods WHERE uid='{$row['guid']}'";
		if(!$data=$mysql->one_row($sql))  alert('상품이 삭제 되었거나 없는 상품입니다.','close5');

		//if($data['coop_sale']==0) alert("공구가 미성립되어 결제 하실 수 없습니다","back");

		$today	= date("Y-m-d H:i");	
		if($data['coop_edate']>$today) {
			$sql = "SELECT price FROM mall_goods_cooper WHERE guid='{$row['guid']}' ORDER BY qty DESC LIMIT 1";
			if($mysql->get_one($sql)!=$data['coop_price']) alert("공구가 마감되지 않았습니다","back");
		}

		$op_price = 0;
	    $p_option = $row['p_option'];
		if($p_option) {
			$p_option2 = explode("|",$p_option);
			for($i=0,$cnt=count($p_option2);$i<$cnt;$i++) {
				$sql = "SELECT option1, option2, price, qty FROM mall_goods_option WHERE uid='{$p_option2[$i]}'";
				$tmps = $mysql->one_row($sql);
				if(!$tmps['option1']) continue;				
				if($tmps['price']>0) $op_price += $tmps['price'];	
			}
		}	
		
		if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
			if($my_id) $tempid = $my_id;
			else $tempid = md5(uniqid(rand()));
			SetCookie("tempid",$tempid,0,"/");
		} 
		else $tempid = $_COOKIE['tempid'];

		$signdate = time();
		$sql = "DELETE FROM mall_cart WHERE tempid='{$tempid}' && SUBSTRING(p_cate,1,3)='999' && p_number='{$data['uid']}'";
		$mysql->query($sql);

		$sql = "UPDATE mall_cart SET p_direct='N' WHERE tempid='{$tempid}'";
		$mysql->query($sql);
				
		/************************* 적립금 관련 ***********************/
		$p_reserve = 0;
		$reserve = explode("|",$data['reserve']);
		if($reserve[0] =='2') { //쇼핑몰 정책일때			
			$sql = "SELECT code FROM mall_design WHERE mode='B'";
			$tmp_cash = $mysql->get_one($sql);
			$cash = explode("|*|",stripslashes($tmp_cash));
			if($cash[6] =='1') { 
				$p_reserve = (($data['coop_price']+$op_price) * $cash[8])/100;
			} 
		} 
		else if($reserve[0] =='3') { //별도 책정일때
			$p_reserve = (($data['coop_price']+$op_price) * $reserve[1])/100;
		}	
		/************************* 적립금 관련 ***********************/				      
		
		$sql = "INSERT INTO mall_cart (tempid,p_number,p_cate,p_qty,p_reserve,p_option,op_price,p_direct,date) VALUES ('{$tempid}','{$data['uid']}','{$data['cate']}','{$row['qty']}','{$p_reserve}','{$p_option}','{$op_price}','Y','{$signdate}')";
		$mysql->query($sql);

		movePage("../{$Main}?channel=order_form&amp;direct=Y");
		
	break;

	case "cancel" :
		$uid = $_GET['uid'];
		if(!$uid) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');
		
		$page	= $_GET['page'];
		$sdate1	= $_GET['sdate1'];
		$sdate2	= $_GET['sdate2'];

		if($page) $addstring = "&page={$page}";
		if($sdate1) $addstring .= "&sdate1={$sdate1}";
		if($sdate2) $addstring .= "&sdate2={$sdate2}";

		$sql = "SELECT guid, qty, status FROM mall_cooperate WHERE uid='{$uid}' && id='{$my_id}'";
		if(!$row = $mysql->one_row($sql)) alert('신청된 내역이 존재하지 않습니다.','back');
		if($row['status']!='A') {
			if($row['status']=='B') alert("구매완료된 내역은 취소 할 수 없습니다","back");
			movePage("../{$Main}?channel=cooperate_list{$addstring}");
		}
		$sql = "UPDATE mall_cooperate SET status='D' WHERE uid='{$uid}' && id='{$my_id}'";
		$mysql->query($sql);

		$sql = "SELECT price, s_qty, qty, coop_sdate, coop_edate, coop_cnt FROM mall_goods WHERE uid='{$row['guid']}'";
		if(!$data=$mysql->one_row($sql))  alert('상품이 삭제 되었거나 없는 상품입니다.','close5');
		
		$participation = $data['coop_cnt']-$row['qty'];
		$sql = "SELECT * FROM mall_goods_cooper WHERE guid='{$row['guid']}' ORDER BY qty ASC";
		$mysql->query($sql);

		$coop_arr = Array();
		while($data2=$mysql->fetch_array()) {
			if($data2['qty'] && $data2['price']) {
				$coop_arr[] = Array($data2['qty'],$data2['price']);
			}
		}

		$cnt = count($coop_arr);
		if($coop_arr[0][0]>$participation) {
			$coop_price = $data['price'];
		}
		else {	
			if($cnt==1) {
				$coop_price = $coop_arr[0][1];
			}
			else {
				if($coop_arr[$cnt-1][0]<$participation) {
					$coop_price = $coop_arr[$cnt-1][1];
				}
				else {
					for($i=0;$i<$cnt;$i++) {								
						if($coop_arr[$i][0]>=$participation) {
							$coop_price = $coop_arr[$i][1];						
							break;
						}	
					}
				}
			}	
		}
		$start_price = $data['price'];
		$coop_sale = 100 - round((100*$coop_price)/$start_price);

		$sql = "UPDATE mall_goods SET coop_cnt = coop_cnt-{$row['qty']}, coop_price='{$coop_price}', coop_sale='{$coop_sale}' WHERE uid='{$row['guid']}'";
		$mysql->query($sql);

		alert('해당 공동구매가 신청취소 되었습니다.',"../{$Main}?channel=cooperate_list{$addstring}");
	break;

	default :
		$gid	= $_POST['gid'];
		$qty	= $_POST['qty'];
		$p_option = $_POST['p_option'];
		$cell	= $_POST['phone11']."-".$_POST['phone12']."-".$_POST['phone13'];
		$email	= $_POST['email'];

		if(!$gid || !$qty || !$cell || !$email) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');

		$sql = "SELECT count(*) FROM mall_cooperate WHERE id='{$my_id}' && guid='{$gid}' && status!='D'";
		if($mysql->get_one($sql)>0) alert("이미 공동구매를 신청 하셨습니다. 신청수량을 변경 하실려면 마이페이지에서 취소 후 다시 신청 하시기 바랍니다.","close5");

		$sql = "SELECT price, s_qty, qty, coop_sdate, coop_edate, coop_cnt FROM mall_goods WHERE uid='{$gid}'";
		if(!$data=$mysql->one_row($sql))  alert('상품이 삭제 되었거나 없는 상품입니다.','close5');

		if($data['s_qty']==4) {
			if($data['qty']<=($data['coop_cnt'])) alert("공구가 마감 되었습니다. 신청 하실 수 없습니다.","close5");
			if($data['qty']<=($data['coop_cnt']+$qty)) {
				$ck_qty = $data['qty']-$date['coop_cnt'];
				alert("공동구매 총수량을 넘었습니다. 신청수량을 {{ck_qty}}개로 변경하셔서 다신 신청 하시기 바랍니다.","close5");
			}
		}

		$today	= date("Y-m-d H:i");	
		if($data['coop_sdate']>$today) alert("공구 준비중 입니다. 신청 하실 수 없습니다.","close5");
		else if($data['coop_edate']<$today) alert("공구가 마감 되었습니다. 신청 하실 수 없습니다.","close5");

		$participation = $data['coop_cnt'] + $qty;
		$sql = "SELECT * FROM mall_goods_cooper WHERE guid='{$gid}' ORDER BY qty ASC";
		$mysql->query($sql);

		$coop_arr = Array();
		while($data2=$mysql->fetch_array()) {
			if($data2['qty'] && $data2['price']) {
				$coop_arr[] = Array($data2['qty'],$data2['price']);
			}
		}

		$cnt = count($coop_arr);
		if($coop_arr[0][0]>$participation) {
			$coop_price = $data['price'];
		}
		else {	
			if($cnt==1) {
				$coop_price = $coop_arr[0][1];
			}
			else {
				if($coop_arr[$cnt-1][0]<$participation) {
					$coop_price = $coop_arr[$cnt-1][1];
				}
				else {
					for($i=0;$i<$cnt;$i++) {								
						if($coop_arr[$i][0]>=$participation) {
							$coop_price = $coop_arr[$i][1];						
							break;
						}	
					}
				}
			}	
		}
		$start_price = $data['price'];
		$coop_sale = 100 - round((100*$coop_price)/$start_price);

		$signdate = time();
		$sql = "INSERT INTO mall_cooperate (id,guid,qty,p_option,cell,email,signdate) VALUES ('{$my_id}','{$gid}','{$qty}','{$p_option}','{$cell}','{$email}','{$signdate}')";
		$mysql->query($sql);

		$sql = "UPDATE mall_goods SET coop_cnt = coop_cnt+{$qty}, coop_price='{$coop_price}', coop_sale='{$coop_sale}' WHERE uid='{$gid}'";
		$mysql->query($sql);

		if($data['s_qty']==4 && ($data['qty']==($data['coop_cnt']+$qty))){  // 판매수량 완료 자동 공구마감처리
			$sql = "UPDATE mall_goods SET coop_edate = '".date("Y-m-d H:i")."'  WHERE uid='{$gid}'";
			$mysql->query($sql);
		}

		alert('공동구매 신청이 접수 되었습니다. 마이페이지에서 내역을 확인 해 보시기 바랍니다.','close4');
	break;
}
?>
