<?
include "sub_init.php";

// 임시장바구니번호 존재확인
if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
	if($my_id) $tempid = $my_id;
	else $tempid = md5(uniqid(rand()));
	SetCookie("tempid",$tempid,0,"/");
} 
else $tempid = $_COOKIE['tempid'];

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

switch($_GET['mode']) {
	case 'del' :
		$uid = $_GET['uid'];
		$uid2 = explode(",",$uid);
		for($i=0,$cnt=count($uid2);$i<$cnt;$i++) {
			if($uid2[$i]) {
				$sql = "DELETE FROM mall_cart WHERE uid = '{$uid2[$i]}' && tempid='{$tempid}'";
				$mysql->query($sql);
			}
		}

		echo "<item>true</item>\n"; 
		echo "<type>Cart</type>\n"; 
		echo "<uid>{$uid}</uid>\n"; 
		echo "</root>";
		exit;
	break; 

	case 'option' :
		
		$uid = $_GET['uid'];
		$sql = "SELECT p_qty, p_number FROM mall_cart WHERE uid='{$uid}'";
		$row = $mysql->one_row($sql);
		
		$p_qty = isset($_GET['p_qty']) ? $_GET['p_qty'] : $row['p_qty'];
		$number = $row['p_number'];

		$sql = "SELECT def_qty, s_qty, reserve, price FROM mall_goods WHERE uid='{$number}'";
		$row = $mysql->one_row($sql);
		$s_qty = $row['s_qty'];

		$op_price = 0;
		$p_option = $_GET['p_option'];
		if($p_option) {
			$p_option2 = explode("|",$p_option);
			for($i=0,$cnt=count($p_option2);$i<$cnt;$i++) {
				$sql = "SELECT option1, option2, price, qty FROM mall_goods_option WHERE uid='{$p_option2[$i]}'";
				$tmps = $mysql->one_row($sql);
				if(!$tmps['option1']) continue;
				if($tmps['qty']<$p_qty && $s_qty==4) {
					echo "<item>죄송합니다. 옵션상품({$tmps['option1']}:{$tmps['option2']}) 물품재고량(현재:{$tmps['qty']}개)을 초과 했습니다!</item>\n</root>"; 
					exit;		
				}
				if($tmps['price']>0) $op_price += $tmps['price'];	
			}
		}	

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
		$price = $row['price']+$op_price;
		
		$sql = "UPDATE mall_cart SET p_qty='{$p_qty}', p_option='{$p_option}', p_reserve='{$p_reserve}', op_price='{$op_price}' WHERE uid='{$uid}'";
		$mysql->query($sql);

		echo "		<item>true</item>\n
					<uid><![CDATA[{$uid}]]></uid>\n
					<price><![CDATA[{$price}]]></price>\n
				</root>";
		exit;
	break;

	default : 
		$cate	= $_GET['cate'];
		$number = intval($_GET['number']);

		if(!$cate || !$number) { 
			echo "<item>false</item>"; 
			echo "</root>";
			exit;
		}

		if(substr($cate,0,3)=='999') {
			echo "<item>공동구매 상품은 장바구니에 담을 수 없습니다.</item>\n</root>"; 
			exit;			
		}

		$sql = "SELECT def_qty, s_qty FROM mall_goods WHERE uid='{$number}'";
		$row = $mysql->one_row($sql);
		$p_qty = isset($_GET['p_qty']) ? intval($_GET['p_qty']) : $row['def_qty'];
		$s_qty = $row['s_qty'];
		$signdate	= time();

		if($_GET['view']=='Y') $view = 'Y';
		else $view = 'N';

		$sql= "SELECT access_level FROM mall_cate WHERE cate='{$cate}'";
		$tmps = $mysql->get_one($sql);
		if($tmps && $my_level<9) {
			$access_level = explode("|",$tmps);
			if(($access_level[1]=='!=' && $access_level[0]!=$my_level) || ($access_level[1]=='<' && $access_level[0]>$my_level)) {
				echo "<item>상품 접근권한이 없어 장바구니에 담을 수 없습니다.</item>\n</root>"; 
				exit;			
			}
		}

		$op_price = 0;
		$p_option = $_GET['p_option'];
		if($p_option) {
			$p_option2 = explode("|",$p_option);
			for($i=0,$cnt=count($p_option2);$i<$cnt;$i++) {
				$sql = "SELECT option1, option2, price, qty FROM mall_goods_option WHERE uid='{$p_option2[$i]}'";
				$tmps = $mysql->one_row($sql);
				if(!$tmps['option1']) continue;
				if($tmps['qty']<$p_qty && $s_qty==4) {
					echo "<item>죄송합니다. 옵션상품({$tmps['option1']}:{$tmps['option2']}) 물품재고량(현재:{$tmps['qty']}개)을 초과 했습니다!</item>\n</root>"; 
					exit;
				}
				if($tmps['price']>0) $op_price += $tmps['price'];	
			}
		}

		$sql = "SELECT p_qty FROM mall_cart WHERE tempid='{$tempid}' && p_number='{$number}' && p_cate='{$cate}' && p_option='{$p_option}'";

		if($ck_qty = $mysql->get_one($sql)){ // 장바구니에 완전동일상품이 존재할경우 수량만 업데이트함
			$qty = $ck_qty + $p_qty;					  
			$sql = "SELECT s_qty,qty FROM mall_goods WHERE uid='{$number}'";
			$tmps = $mysql->one_row($sql);					  
			if($tmps['s_qty']==4 && $tmps['qty']<$qty) {
				echo "<item>물품재고량을 초과 했습니다!</item>\n</root>"; 
				exit;
			}					  
			$sql = "UPDATE mall_cart SET p_qty = '{$qty}' WHERE tempid='{$tempid}' && p_number='{$number}' && p_cate='{$cate}' && p_option='{$p_option}'";
			$mysql->query($sql);

			$sql = "SELECT uid FROM mall_cart WHERE tempid='{$tempid}' && p_number='{$number}' && p_cate='{$cate}' && p_option='{$p_option}'";
			$uid = $mysql->get_one($sql);
			
			echo "<item>true</item>\n<view><![CDATA[{$view}]]></view>\n<uid><![CDATA[{$uid}]]></uid>\n<qty><![CDATA[{$qty}]]></qty>\n</root>"; 
			exit;
		} 
		else { // 장바구니에 상품추가	
			
			$sql = "SELECT s_qty, qty, price, reserve, name, image4, price_ment FROM mall_goods WHERE uid='{$number}'";
			$row = $mysql->one_row($sql);					  
			
			if($row['s_qty']==2 || ($row['s_qty']==4 && $row['qty']<$p_qty)) {
				echo "<item>죄송합니다. 상품이 품절되어 장바구니에 담을 수 없습니다.</item>\n</root>"; 
				exit;
			}		

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

								  
			$sql = "INSERT INTO mall_cart (tempid,p_number,p_cate,p_qty,p_reserve,p_option,op_price,date) VALUES ('{$tempid}','{$number}','{$cate}','{$p_qty}','{$p_reserve}','{$p_option}','{$op_price}','{$signdate}')";
			$mysql->query($sql);

			$sql = "SELECT MAX(uid) FROM mall_cart WHERE tempid='{$tempid}'";
			$uid = $mysql->get_one($sql);

			$MY_SALE = $my_sale;
			$MY_POINT = $my_point;
			$data = Array();
			$data['p_cate'] = $cate;
			$data['p_number'] = $number;
			$gData	= getDisplayOrder($data);
			$price = $gData['p_price'];
			$price2 = str_replace("원","",$price);
			$oprice = $gData['oprice'];	

			$sql = "SELECT count(*) FROM mall_goods_option WHERE guid='{$number}'";
			if($mysql->get_one($sql)==0) $option = 'Y';
			else $option = 'N';

			echo "
					<item>true</item>\n
					<uid><![CDATA[{$uid}]]></uid>\n
					<link><![CDATA[?channel=view&amp;uid={$number}&amp;cate={$cate}]]></link>\n
					<name><![CDATA[{$row[name]}]]></name>\n
					<image><![CDATA[{$gData['simage']}]]></image>\n
					<price><![CDATA[{$price}]]></price>\n
					<price2><![CDATA[{$price2}]]></price2>\n
					<oprice><![CDATA[{$oprice}]]></oprice>\n
					<qty><![CDATA[{$p_qty}]]></qty>\n
					<view><![CDATA[{$view}]]></view>\n
					<option><![CDATA[{$option}]]></option>\n
				</root>";
		}
	break;
}

?>