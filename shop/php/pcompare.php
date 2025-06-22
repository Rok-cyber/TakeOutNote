<?
include "sub_init.php";
include "$skin/skin_define.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

$skin = "../skin/$tmp_skin";
$skin2 = $skin."/";
$ShopPath = "../";

$sql = "SELECT code FROM mall_design WHERE mode='B'";
$tmp_cash = $mysql->get_one($sql);
$cash = explode("|*|",stripslashes($tmp_cash));
//0:무통장,1:카드,2:대행사,3:아이디,4:카드최소액,5:계좌번호,6:적립금유무,7:회원,8:상품,9:최소사용액,10:배송비유무,11:적용금액,12:배송비

$compare = explode("|",$_GET['compare']);

if(!$compare) {
	echo "
		<script>
			alert('선택된 상품이 없습니다.');\n
			parent.pLightBox.hide();\n
		</script>
	";
	exit;
}

// 임시장바구니번호 존재확인
if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
	if($my_id) $tempid = $my_id;
	else $tempid = md5(uniqid(rand()));
	SetCookie("tempid",$tempid,0,"/");
} 
else $tempid = $_COOKIE['tempid'];

$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$tempid}'";
if($mysql->get_one($sql)>0) $ckCart = 'Y';
else $ckCart = 'N';

$tpl->define('main',"{$skin}/pcompare.html");
$tpl->scan_area('main');

if($my_id) $CKLOGIN = "Y";
else $CKLOGIN = "N";
for($k=0,$cnt=count($compare)-1;$k<$cnt;$k++) {
	$sql = "SELECT * FROM mall_goods WHERE uid='{$compare[$k]}'";
	if($data = $mysql->one_row($sql)) {
		$num = $k + 1;

		$gData	= getDisplay($data,'image3');		// 디스플레이 정보 가공 후 가져오기
		$GOODS_IMG	= $gData['image'];
		$GOODS_IMG_WIDTH = $IMG_DEFINE['img3'];
		$GOODS_OIMG = $gData['image'];
		$GOODS_NAME	= $gData['name'];		
		$GOODS_PRICE= $gData['price'];
		$GOODS_ORIG_PRICE= number_format($data['price'],$ckFloatCnt)."원";
		$GOODS_ICON	= $gData['icon'];
		$GOODS_COMP = $gData['comp'];
		$GOODS_RESERVE  = $gData['reserve'];
		$GOODS_ORESERVE = str_replace(",","",$GOODS_RESERVE);

		$DEF_QTY		= stripslashes($data['def_qty']);
		$GOODS_MADE		= stripslashes($data['made']);
		$GOODS_EXPL		= stripslashes($data['explan']);
		$GOODS_UNIT		= stripslashes($data['unit']);
		$P_PRICE		= $data['price'];
		$P_PRICE2		= str_replace(array("원",","),"",$GOODS_PRICE);
		$P_CATE			= $data['cate'];
		$P_UID			= $data['uid'];
		if($data['s_qty']==4) $S_QTY = $data['qty'];
		else $S_QTY = 0;


		if($gData['my_sale']>0) {
			$TTL = $gData['my_type1'];
			$MY_SALE = $gData['my_sale'];
			$tpl->parse("is_my_sale");
		}

		if($cash[6]==1) {
			if($gData['my_point']>0) {
				$TTL = $gData['my_type2'];
				$MY_POINT = $gData['my_point'];
				$tpl->parse("is_my_point");
			}
			if($GOODS_ORESERVE>0) $tpl->parse("is_reserve");
		}

		if($data['consumer_price'] && $data['consumer_price'] >0){
			$GOODS_C_PRICE = number_format($data['consumer_price'],$ckFloatCnt)."원";
			$tpl->parse('is_c_price',"1"); 
		}

		if($data['brand']>0) {
			$sql = "SELECT name,uid FROM mall_brand WHERE uid='{$data['brand']}'";
			$tmps = $mysql->one_row($sql);
			$GOODS_BRAND = stripslashes($tmps['name']);
			$PLUS	= $tmps['uid'];
			$tpl->parse("is_brand","1");
		}

		if($data['event']>0) {
			$sql = "SELECT name FROM mall_event WHERE uid='{$data['event']}'";
			$GOODS_EVENT = stripslashes($mysql->get_one($sql));
			$PLUS = $data['event'];
			$tpl->parse("is_event","1");
		}

		if($gData['cp_price']) {
			$sql = "SELECT * FROM mall_cupon_manager WHERE type='3' && INSTR(sgoods,'|{$P_UID}|') ORDER BY uid DESC LIMIT 1";
			if($cupon = $mysql->one_row($sql)){
				$CUID = $cupon['uid'];
				$CP_TYPE = $cupon['use_type'];
				$CP_SALE = $cupon['sale'];
				$CP_SALE_TYPE = $cupon['stype'];
				$GOODS_CPRICE = $gData['cp_price'];
				$P_PRICE3 = str_replace(array("원",","),"",$GOODS_CPRICE);
				$tpl->parse("is_cupon");
			}
		}
		else $P_PRICE3 = '';

		if($GOODS_COMP) $tpl->parse("is_comp","1");
		if($GOODS_MADE) $tpl->parse("is_made","1");

		/************************* 배송비 관련 ***********************/
		$P_CARR = 0;
		$carriage = explode("|",$data['carriage']);
		if($carriage[0]==1) {
			$tpl->parse("is_carr_free");
		}
		else if($carriage[0]==3) { //별도 책정일때
			$GOODS_CARR = number_format($carriage[1],$ckFloatCnt)."원";	
			$P_CARR = $carriage[1];
			$tpl->parse("is_carr");
		}	
		/************************* 배송비 관련 ***********************/

		/**************************** GOODS OPTIONS **************************/
		$tmps = $OP_TITLE = $OP_VALUES = "";
		$op_img_arr = Array('','색상','사이즈','용량','옵션','선택사항','추가구매');

		$sql = "SELECT option1 FROM mall_goods_option WHERE guid='{$uid}' GROUP BY option1 ORDER BY o_num ASC";
		$mysql->query($sql);
			
		$option_arr = Array();
		while($row2 = $mysql->fetch_array()){
			$option_arr[] = $row2['option1'];
		}
				
		for($j=$i=0,$cnt2=count($option_arr);$j<$cnt2;$j++) {

			$sql = "SELECT * FROM mall_goods_option WHERE guid='{$uid}' && option1='{$option_arr[$j]}' ORDER BY o_num ASC";
			$mysql->query($sql);
			
			$OP_TITLE = $option_arr[$j];
			if(in_array($OP_TITLE,$op_img_arr)) {
				for($o=1;$o<7;$o++) {
					if($OP_TITLE==$op_img_arr[$o]) break;
				}
				if(is_file("{$skin}/img/shop/ttl_goods_option{$o}.gif")) $tpl->parse("is_op_img");
				else $tpl->parse("is_op_text");
			}

			$OP_VALUES = "\n<option value=''>선택</option>\n";		
			$i = $j+1;
			while($tmp_op=$mysql->fetch_array()){	
				if($tmp_op['display']=='N') continue;
						
				if($tmp_op['price']>0) {
					$tmp_op['price'] = $tmp_op['price'] - round(($tmp_op['price'] * $MY_SALE)/100,$ckFloatCnt);
					$tmps2 = " (+".number_format($tmp_op['price'],$ckFloatCnt)."원)";
				}
				else $tmps2 = "";

				if($tmp_op['qty']==0) $OP_VALUES .= "<option value='{$tmp_op['uid']}' class='disabled'>{$tmp_op['option2']}{$tmps} [품절]</option>\n";
				else $OP_VALUES .= "<option value='{$tmp_op['uid']}'>{$tmp_op['option2']}{$tmps2}</option>\n";		
			}
			$tpl->parse("is_option");
			$tpl->parse("is_op_img","2");
			$tpl->parse("is_op_text","2");	
		}
		$op_cnt = $i;
		/**************************** GOODS OPTIONS **************************/

		/**************************** 품절 관련 **************************/
		if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) {
			$tpl->parse('is_limit',"1");
			$tpl->parse('is_limit3',"1");
		} 
		else {
			$tpl->parse('no_limit',"1");
			$tpl->parse('is_limit2',"1");
		}
		/**************************** 품절 관련 **************************/

		/*********************** 옵션선택 및 수량변경시 가격적용 이용 **************************/
		if($P_PRICE3>0) $tmp_total = $P_PRICE3;
		else if($P_PRICE2>0) $tmp_total = $P_PRICE2;
		else $tmp_total = $P_PRICE;

		$P_TOTAL = $tmp_total + $P_CARR;
		$GOODS_TOTAL = number_format($P_TOTAL);

		if($P_PRICE2>0) $tmp_total = $P_PRICE2;
		else $tmp_total = $P_PRICE;
		$P_TOTAL2 = $tmp_total + $P_CARR;

		$P_RESERVE = 0;
		if($GOODS_ORESERVE>0) {		
			$reserve = explode("|",$data['reserve']);
			if($reserve[0] =='2') { //쇼핑몰 정책일때
				if($cash[6] =='1') $P_RESERVE = $cash[8];
			} 
			else if($reserve[0] =='3') { //별도 책정일때
				$P_RESERVE = $reserve[1];
			}
		}
		$P_RESERVE += $MY_SALE;
		/*********************** 옵션선택 및 수량변경시 가격적용 이용 **************************/
		
		$tpl->parse("loop_goods");
		$tpl->parse("is_option","2");
		$tpl->parse('is_limit',"2");
		$tpl->parse('is_limit2',"2");
		$tpl->parse('is_limit3',"2");
		$tpl->parse('is_comp',"2");
		$tpl->parse('is_made',"2");
		$tpl->parse('is_cupon',"2");
		$tpl->parse('is_oprice',"2");
		$tpl->parse('is_my_sale',"2");
		$tpl->parse('is_reserve',"2");
		$tpl->parse('is_brand',"2");
		$tpl->parse('is_model',"2");
		$tpl->parse('is_event',"2");
		$tpl->parse('is_carr',"2");
		$tpl->parse('is_carr_free',"2");
		$tpl->parse('is_option',"2");

	}	
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>