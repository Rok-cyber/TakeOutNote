<?
include "sub_init.php";

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');

$pop		= $_POST['pop'];
$type		= isset($_POST['type']) ? $_POST['type'] : $_GET['type'];
$p_number	= isset($_POST['p_number']) ? $_POST['p_number'] : $_GET['p_number'];
$p_cate		= isset($_POST['p_cate']) ? $_POST['p_cate'] : $_GET['p_cate'];
$p_qty		= isset($_POST['p_qty']) ? $_POST['p_qty'] : $_GET['p_qty'];
$p_uid		= isset($_POST['p_uid']) ? $_POST['p_uid'] : $_GET['p_uid'];
$page		= $_POST['page'];
$p_direct	= $_POST['direct']=='Y' ? 'Y' : 'N';
if($p_direct=='Y') $addstring = "&amp;direct=Y";

if($_POST['coop_pay']=='Y') $type = "cooper";

if(!$type && (!$p_number || !$p_cate || !$p_qty)) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');
if(($type=='mod' || $type=='del') && !$p_uid) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');

// 임시장바구니번호 존재확인
if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
	if($my_id) $tempid = $my_id;
	else $tempid = md5(uniqid(rand()));
	SetCookie("tempid",$tempid,0,"/");
} 
else $tempid = $_COOKIE['tempid'];

$signdate = time();

switch($type) {	
	case "cdel" :
		$item = isset($_POST['item'])? $_POST['item']:'';
		if(!$item) alert("선택한 항목이 없습니다.","back");
		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			$sql = "DELETE FROM mall_cart WHERE tempid = '{$tempid}' && uid='{$item[$i]}'";
			$mysql->query($sql);
		}
		movePage("../{$Main}?channel=cart");
	break;

	case "corder" :
		$item = isset($_POST['item'])? $_POST['item']:'';
		if(!$item) alert("선택한 항목이 없습니다.","back");
		
		$sql = "UPDATE mall_cart SET p_direct='N' WHERE tempid='{$_COOKIE['tempid']}'";
		$mysql->query($sql);

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			$sql = "UPDATE mall_cart SET p_direct='Y' WHERE tempid = '{$tempid}' && uid='{$item[$i]}'";
			$mysql->query($sql);
		}
		movePage("../{$Main}?channel=order_form&direct=Y");
	break;

	case "qorder" :
		$uid = $_POST['uid'];
		$item = explode(",",$uid);		
		if(!$item) alert("선택한 항목이 없습니다.","back");
		
		$sql = "UPDATE mall_cart SET p_direct='N' WHERE tempid='{$_COOKIE['tempid']}'";
		$mysql->query($sql);

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			$sql = "UPDATE mall_cart SET p_direct='Y' WHERE tempid = '{$tempid}' && uid='{$item[$i]}'";
			$mysql->query($sql);
		}
		movePage("../{$Main}?channel=order_form&direct=Y");
	break;

	case "mod": // 장바구니 수정모드
		$sql = "SELECT p_cate,p_number FROM mall_cart WHERE tempid = '{$tempid}' && uid='{$p_uid}'";
		$tmps = $mysql-> one_row($sql);

		$sql = "SELECT price, reserve, s_qty, qty, def_qty, unit FROM mall_goods WHERE uid='{$tmps[p_number]}'";
        $row = $mysql->one_row($sql);					  
		
		if($p_qty < $row['def_qty']) alert("해당상품 구매수량은 최소 {$row['def_qty']}{$row['unit']}이상 구매하셔야 됩니다.","back");
		if($row['s_qty']==2 || ($row['s_qty']==4 && $row['qty']<$p_qty)) alert("물품재고량(현재:{$row['qty']}개)을 초과 했습니다!","back");
        
		$op_price = 0;
	    $p_option = $_POST['p_option'];
		if($p_option) {
			$p_option2 = explode("|",$p_option);
			for($i=0,$cnt=count($p_option2);$i<$cnt;$i++) {
				$sql = "SELECT option1, option2, price, qty FROM mall_goods_option WHERE uid='{$p_option2[$i]}'";
				$tmps = $mysql->one_row($sql);
				if(!$tmps['option1']) continue;
				if($tmps['qty']<$p_qty && $row['s_qty']==4) {
					alert("죄송합니다. 옵션상품({$tmps['option1']}:{$tmps['option2']}) 물품재고량(현재:{$tmps['qty']}개)을 초과 했습니다!","back"); 
				}
				if($tmps['price']>0) $op_price += $tmps['price'];	
			}
		}

		/************************* 적립금 관련 ***********************/
		if($op_price>0) {
			$p_reserve = 0;
			$reserve = explode("|",$row['reserve']);
			if($reserve[0] =='2') { //쇼핑몰 정책일때
				
				$sql = "SELECT code FROM mall_design WHERE mode='B'";
				$tmp_cash = $mysql->get_one($sql);
				$cash = explode("|*|",stripslashes($tmp_cash));

				if($cash[6] =='1') { 
					$p_reserve = (($row['price']+$op_price) * $cash[8])/100;
				} 
			} 
			else if($reserve[0] =='3') { //별도 책정일때
				$p_reserve = (($row['price']+$op_price) * $reserve[1])/100;
			}	

			$p_rese = ", p_reserve = '{$p_reserve}'";
		}
		else $p_rese = "";
		/************************* 적립금 관련 ***********************/	

		$sql = "UPDATE mall_cart SET p_qty ='{$p_qty}', p_option = '{$p_option}', op_price = '{$op_price}' {$p_rese} WHERE tempid = '{$tempid}' && uid='{$p_uid}'";

	break;
		
	case "del": // 삭제모드
		$sql = "DELETE FROM mall_cart WHERE tempid = '{$tempid}' && uid='{$p_uid}'";
	break;

	case "cooper" : // 공동구매 선결제(소셜커머스)
		$sql = "SELECT uid, cate, coop_edate, coop_sale, coop_pay, reserve, s_qty, qty FROM mall_goods WHERE uid='{$p_number}'";
		if(!$row=$mysql->one_row($sql))  alert('상품이 삭제 되었거나 없는 상품입니다.','back');

		$today	= date("Y-m-d H:i");	
		if($row['coop_pay']!='Y') alert("공동구매 신청 후 주문 하실 수 있습니다.","back");
		if($row['coop_sdate']>$today) alert("공구 준비중 입니다. 신청 하실 수 없습니다.","back");
		if($row['coop_edate']<$today) alert("공동구매가 마감되어 주문 하실 수 없습니다","back");

		$sql = "SELECT count(*) FROM mall_cooperate WHERE id='{$my_id}' && guid='{$p_number}' && status!='D'";
		if($mysql->get_one($sql)>0) alert("이미 공동구매를 신청 하셨습니다. 공동구매는 아이디당 한번만 신청 하실 수 있습니다.","back");
		
		$op_price = 0;
	    $p_option = $_POST['p_option'];
		if($p_option) {
			$p_option2 = explode("|",$p_option);
			for($i=0,$cnt=count($p_option2);$i<$cnt;$i++) {
				$sql = "SELECT option1, option2, price, qty FROM mall_goods_option WHERE uid='{$p_option2[$i]}'";
				$tmps = $mysql->one_row($sql);
				if(!$tmps['option1']) continue;
				if($tmps['qty']<$p_qty && $row['s_qty']==4) {
					alert("죄송합니다. 옵션상품({$tmps['option1']}:{$tmps['option2']}) 물품재고량(현재:{$tmps['qty']}개)을 초과 했습니다!",$backs); 
				}
				if($tmps['price']>0) $op_price += $tmps['price'];	
			}
		}

		if($row['s_qty']==2 || ($row['s_qty']==4 && $row['qty']<$p_qty)) alert("물품재고량(현재:{$row['qty']}개)을 초과 했습니다!","back");
	
		$sql = "DELETE FROM mall_cart WHERE tempid='{$tempid}' && SUBSTRING(p_cate,1,3)='999' && p_number='{$p_number}'";
		$mysql->query($sql);

		$sql = "UPDATE mall_cart SET p_direct='N' WHERE tempid='{$tempid}'";
		$mysql->query($sql);

		$sql = "SELECT price FROM mall_goods_cooper WHERE guid='{$p_number}' ORDER BY qty ASC LIMIT 1";
		$price = $mysql->get_one($sql);

		/************************* 적립금 관련 ***********************/
		$p_reserve = 0;
		$reserve = explode("|",$row['reserve']);
		if($reserve[0] =='2') { //쇼핑몰 정책일때			
			$sql = "SELECT code FROM mall_design WHERE mode='B'";
			$tmp_cash = $mysql->get_one($sql);
			$cash = explode("|*|",stripslashes($tmp_cash));

			if($cash[6] =='1') { 
				$p_reserve = (($price+$op_price) * $cash[8])/100;
			} 
		} 
		else if($reserve[0] =='3') { //별도 책정일때
			$p_reserve = (($price+$op_price) * $reserve[1])/100;
		}	
		/************************* 적립금 관련 ***********************/				      
		
		$sql = "INSERT INTO mall_cart (tempid,p_number,p_cate,p_qty,p_reserve,p_option,op_price,p_direct,date) VALUES ('{$tempid}','{$row['uid']}','{$row['cate']}','{$p_qty}','{$p_reserve}','{$p_option}','{$op_price}','Y','{$signdate}')";
		$mysql->query($sql);

		movePage("../{$Main}?channel=order_form{$addstring}");
	break;
		  
	default : // 장바구니에 상품담기	
		if(substr($p_cate,0,3)=='999') alert("공동구매 상품은 장바구니에 담을 수 없습니다","back");
		$sql= "SELECT access_level FROM mall_cate WHERE cate='{$p_cate}'";
		$tmps = $mysql->get_one($sql);
		if($tmps && $my_level<9) {
			$access_level = explode("|",$tmps);
			if(($access_level[1]=='!=' && $access_level[0]!=$my_level) || ($access_level[1]=='<' && $access_level[0]>$my_level)) {
				alert("상품 접근권한이 없어 장바구니에 담을 수 없습니다","back");
			}
		}
		
		$sql = "SELECT price, reserve, s_qty, qty FROM mall_goods WHERE uid='{$p_number}'";
		$row = $mysql->one_row($sql);

		$op_price = 0;
	    $p_option = $_POST['p_option'];
		if($p_option) {
			$p_option2 = explode("|",$p_option);
			for($i=0,$cnt=count($p_option2);$i<$cnt;$i++) {
				$sql = "SELECT option1, option2, price, qty FROM mall_goods_option WHERE uid='{$p_option2[$i]}'";
				$tmps = $mysql->one_row($sql);
				if(!$tmps['option1']) continue;
				if($tmps['qty']<$p_qty && $row['s_qty']==4) {
					alert("죄송합니다. 옵션상품({$tmps['option1']}:{$tmps['option2']}) 물품재고량(현재:{$tmps['qty']}개)을 초과 했습니다!",$backs); 
				}
				if($tmps['price']>0) $op_price += $tmps['price'];	
			}
		}

		if($row['s_qty']==2) alert("죄송합니다. 상품이 품절되어 장바구니에 담을 수 없습니다.","back");
		if($row['s_qty']==2 || ($row['s_qty']==4 && $row['qty']<$p_qty)) alert("물품재고량(현재:{$row['qty']}개)을 초과 했습니다!","back");
	
		$sql = "SELECT p_qty FROM mall_cart WHERE tempid='{$tempid}' && p_number='{$p_number}' && p_cate='{$p_cate}'  && p_option='{$p_option}'";
				
		if($ck_qty = $mysql->get_one($sql)){ // 장바구니에 완전동일상품이 존재할경우 수량만 업데이트함
			if($p_direct=='Y') $qty = $p_qty;
			else $qty = $ck_qty + $p_qty;					  					  
			$sql = "UPDATE mall_cart SET p_qty = '{$qty}', p_direct='{$p_direct}'  WHERE tempid='{$tempid}' && p_number='{$p_number}' && p_cate='{$p_cate}' && p_option='{$p_option}'";
		} 
		else { // 장바구니에 상품추가	
			
			/************************* 적립금 관련 ***********************/
			$p_reserve = 0;
			$reserve = explode("|",$row['reserve']);
			if($reserve[0] =='2') { //쇼핑몰 정책일때
				
				$sql = "SELECT code FROM mall_design WHERE mode='B'";
				$tmp_cash = $mysql->get_one($sql);
				$cash = explode("|*|",stripslashes($tmp_cash));

				if($cash[6] =='1') { 
					$p_reserve = (($row['price']+$op_price) * $cash[8])/100;
				} 
			} 
			else if($reserve[0] =='3') { //별도 책정일때
				$p_reserve = (($row['price']+$op_price) * $reserve[1])/100;
			}	
			/************************* 적립금 관련 ***********************/				      
			
			$sql = "INSERT INTO mall_cart (tempid,p_number,p_cate,p_qty,p_reserve,p_option,op_price,p_direct,date) VALUES ('{$tempid}','{$p_number}','{$p_cate}','{$p_qty}','{$p_reserve}','{$p_option}','{$op_price}','{$p_direct}','{$signdate}')";
		}
	break;
}

$mysql->query($sql);

if($type=='order') {
	if($pop==1) movePage2("../{$Main}?channel=order_form{$addstring}");
	else movePage("../{$Main}?channel=order_form{$addstring}");
}
else {
	if($page) $pstring = "&pcate={$p_cate}&page={$page}";
	if($pop==1) movePage2("../{$Main}?channel=cart");
	else movePage("../{$Main}?channel=cart{$pstring}");
}
?>