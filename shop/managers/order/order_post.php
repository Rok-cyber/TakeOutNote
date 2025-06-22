<?
$skin_inc='Y';
######################## lib include
include "../ad_init.php";
include "{$lib_path}/lib.Shop.php";

/***************** 진행상황 처리 함수 **********************/
function orderProcess($step,$order_num) {
	global $mysql, $signdate, $lib_path, $mail_form, $site_name;
	
	if(!$step || !$order_num) alert("정보가 제대로 넘어오지 못했습니다!","back");
			
    $sql = "SELECT order_status FROM mall_order_info WHERE order_num = '{$order_num}'";
	$prev_status = $mysql->get_one($sql);
	
	$sql = "SELECT uid FROM mall_order_goods WHERE order_num = '{$order_num}' && order_status='{$prev_status}'";	
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){		
		stepProcess($row['uid'],$step,$order_num);	
    } 
	
    $sql = "UPDATE mall_order_info SET order_status = '{$step}', status_date='{$signdate}' WHERE order_num = '{$order_num}'";	
	$mysql->query($sql);

	$sql = "SELECT id, name1, email, hphone1, pay_total, carr_info, cash_info FROM mall_order_info WHERE order_num = '{$order_num}'";
	$info = $mysql->one_row($sql);
	$user_id = $info['id'];

	if($user_id && $user_id != 'guest'){  
		
		if($step=="E") {  //주문 완료일때 적립금 적용
			$sql = "SELECT sum(reserve) FROM mall_reserve WHERE status = 'A' && order_num = '{$order_num}'";
			$tmp_r = $mysql->get_one($sql);
			if(!$tmp_r && $tmp_r==0) $tmp_r=0;
			
			$sql = "UPDATE mall_reserve SET status = 'B' WHERE status = 'A' && order_num = '{$order_num}'";
			$mysql->query($sql);
				 
			$sql = "UPDATE pboard_member SET reserve = reserve + {$tmp_r} WHERE id = '{$user_id}'";
			$mysql->query($sql);
			
		} 
		else if($step=="Z") {            
			$sql = "UPDATE mall_reserve SET status='D' WHERE order_num='{$order_num}' && status='A'";
			$mysql->query($sql);

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
	}

	if($step=='B' && $prev_status!='B') {

		############ 현금영수증 발급 #############
		if($info['cash_info']) {
			$sql = "SELECT count(*) FROM mall_order_cash WHERE order_num='{$order_num}'";
			if($mysql->get_one($sql)==0) {
				$sql = "SELECT code FROM mall_design WHERE mode='B'";
				$tmp_cash = $mysql->get_one($sql);
				$cash = explode("|*|",stripslashes($tmp_cash));
				$ckCP = $cash[2];

				if(substr($info['cash_info'],0,1)==1) $cash_type = 'A';
				else $cash_type = 'B';

				$auth_number = substr($info['cash_info'],2);
				
				$sql = "SELECT count(*) as cnt, p_name FROM mall_order_goods WHERE order_num='{$order_num}'";
				$tmp = $mysql->one_row($sql);
				if($tmp['cnt']==1) $goods_name	= $tmp['p_name'];
				else $goods_name = $tmp['p_name']."외 ".($tmp['cnt']-1)."건";

				$sql = "SELECT code FROM mall_design WHERE mode='O'";
				$code = $mysql->get_one($sql);
				if($code[3]==2) $tax_type = 'B';
				else $tax_type = 'A';

				$sql = "INSERT INTO mall_order_cash (cp_name,order_num,name,cell,email,price,goods_name,tax_type,cash_type,auth_number,status,status_date,signdate) VALUES ('{$ckCP}','{$order_num}','{$info['name1']}','{$info['hphone1']}','{$info['email']}','{$info['pay_total']}','{$goods_name}','{$tax_type}','{$cash_type}','{$auth_number}','A','{$signdate}','{$signdate}')";			
				$mysql->query($sql);
			}

			$sql = "SELECT uid FROM mall_order_cash WHERE order_num='{$order_num}'";
			$uid = $mysql->get_one($sql);

			$sql = "SELECT code FROM mall_design WHERE mode='B'";
			$tmp_cash = $mysql->get_one($sql);
			$cash = explode("|*|",stripslashes($tmp_cash));

			switch($cash[2]){
				case "KCP" : 								
					$rtnVls = implode("", socketPost("http://".$_SERVER["SERVER_NAME"]."/card/kcp/cash.php?uid={$uid}"));					
					if(!eregi("true",$rtnVls)) alert("현금영수증이 발급되지 않았습니다. 에러 메세지를 확인 해보시기 바랍니다.","back");									
				break;
			}		
		}
		############ 현금영수증 발급 #############

		############ SMS 보내기 #############
		if($info['hphone1']) {
			$code_arr = Array();
			$code_arr['name'] = $info['name1'];
			$code_arr['number'] = $order_num;
			$code_arr['price'] = number_format($info['pay_total']);			
			pmallSmsAutoSend($info['hphone1'],"pay_ok",$code_arr);
		}
		############ SMS 보내기 #############
	}
	else if($step=='D' && $prev_status!='D') {
		############ 상품배송 메일 보내기 ############		
		if($mail_form) {
			$mail_form2 = str_replace("{shopName}",$site_name,$mail_form);
			$mail_form2 = str_replace("{name}",stripslashes($info['name1']),$mail_form2);
			if($info['carr_info']) {
				$tmps = explode("|",$info['carr_info']);
				$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
				$tmp = $mysql->one_row($sql);								
				$deli_comp = $tmp['name'];
				$deli_no = $tmps[1];
			}
			$mail_form2 = str_replace("{deli_comp}",$deli_comp,$mail_form2);
			$mail_form2 = str_replace("{deli_no}",$deli_no,$mail_form2);
			pmallMailSend($info['email'], "{$site_name}에서 주문하신 상품이 배송 되었습니다.", $mail_form2);					
		}
		############ 상품배송 메일 보내기 ############

		############ SMS 보내기 #############
		if($info['hphone1']) {
			$code_arr = Array();
			$code_arr['name'] = $info['name1'];
			$code_arr['number'] = $order_num;
			$code_arr['price'] = number_format($info['pay_total']);			
			
			if($info['carr_info']) {
				$tmps = explode("|",$info['carr_info']);
				$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmps[0]}' && mode='Z'";
				$tmp = $mysql->one_row($sql);								
				$code_arr['deli_comp'] = $tmp['name'];
				$code_arr['deli_no'] = $tmps[1];
			}
			pmallSmsAutoSend($info['hphone1'],"carriage",$code_arr);
		}
		############ SMS 보내기 #############
	}
	else if($step=='Z' && $prev_status!='Z') {
		############ 현금영수증 발급취소 #############
		$sql = "SELECT uid, status FROM mall_order_cash WHERE order_num='{$order_num}'";
		if($cash_row = $mysql->one_row($sql)) {
			if($cash_row['status']=='B') {
				$uid = $cash_row['uid'];
				$sql = "SELECT code FROM mall_design WHERE mode='B'";
				$tmp_cash = $mysql->get_one($sql);
				$cash = explode("|*|",stripslashes($tmp_cash));

				switch($cash[2]){
					case "KCP" : 							
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
	}
}

   
function stepProcess($t_num,$gs,$order_num2=""){	
    global $mysql, $order_num,$signdate;

	if(!$order_num2) $order_num2=$order_num;
	 
	if($gs=='Z') $p_status2 = ", order_status2 = 'D'";
	else $p_status2 = '';

	$sql = "UPDATE mall_order_goods SET  order_status = '{$gs}', status_date = '{$signdate}' {$p_status2} WHERE order_num='{$order_num2}' && uid = '{$t_num}'";
	$mysql->query2($sql);
	  
	if($gs=='E' || $gs=='Z'){
		if($gs=='Z') {
			$sql = "SELECT order_status FROM mall_order_info WHERE order_num='{$order_num2}'";
			$prev_status = $mysql->get_one($sql);

			if($prev_status !='E' &&  $prev_status !='Z') {

				$sql = "SELECT p_cate,p_number,p_qty, p_option FROM mall_order_goods WHERE uid = '{$t_num}'";
				$tmp = $mysql->one_row($sql);

				if(substr($tmp['p_cate'],0,3)!='999') {
					$sql = "UPDATE mall_goods SET qty = qty + '{$tmp[p_qty]}' WHERE uid='{$tmp[p_number]}' && s_qty='4'";
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
		} 

	} //end of if(d||z)
}
/***************** 진행상황 처리 함수 **********************/

//변수 정의
$signdate = date("Y-m-d H:i:s",time());

###################### 변수 정의 ##########################
$order_num	= isset($_POST['order_num']) ? $_POST['order_num'] : $_GET['order_num'];
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
$mobile		= $_GET['mobile'];
$step		= !empty($_GET['step']) ? $_GET['step'] : $_POST['step'];

##################### addstring ############################
if($gs) $addstring2 ="gs={$gs}";
if($step) $addstring2 ="gs=$step";
if($step[0]) {
	if($step[0]=='DEL') $addstring2 ="gs=Z";
	else $addstring2 ="gs={$step[0]}";
}
if($field && $word) $addstring .= "&field=$field&word={$word}";
if($smoney1 && $smoney2) $addstring .= "&smoney1={$smoney1}&smoney2={$smoney2}";
if($sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";
if($page) $addstring .="&page={$page}";
if($limit) $addstring .="&limit={$limit}";
if($type) $addstring .="&type={$type}";
if($status) $addstring .="&status={$status}";
if($order) $addstring .="&order={$order}";
if($mobile) $addstring .="&mobile={$mobile}";

if($_POST['mode']=='coop_cancel') {
	$item = $_POST['item'];		
	if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
	
	$gid = $_GET['gid'];
	if($gid) $addstring .= "&gid={$gid}";
	$step = 'Z';
	for($i=0,$z=0,$cnt=count($item);$i<$cnt;$i++) {
		$sql = "SELECT order_num FROM mall_cooperate WHERE uid='{$item[$i]}'";
		$order_num = $mysql->get_one($sql);
		if($order_num){
			orderProcess($step,$order_num); 	
			$sql = "UPDATE mall_cooperate SET status='C' WHERE uid='{$item[$i]}'";
			$mysql->query($sql);
			$z++;
		}		
	} 
	alert("주문내역이 있는 ".$z."건의 상품을 취소처리를 했습니다!","../shopping/coop_participation_list.php?{$addstring}");	
}

if(!$order_num && $_GET['mode']!='secStep')  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하세요!','back');

$url = "./order_list.php?{$addstring2}&{$addstring}";

############ 상품배송 메일 보내기전 정의 ############
if($step=='D' || $step[0]=='D') {
	$URL = "http://".$_SERVER["SERVER_NAME"];		
	$sql = "SELECT code FROM mall_design WHERE mode = 'F'";
	$mail_img = explode("|*|",stripslashes($mysql->get_one($sql)));	
	if($mail_img[6]=='1') {			
		$skin = "../../skin/{$tmp_skin}/";
		$mail_type = "carr";
		if($mail_img[0]) $MAIL_IMG = "<a href='{$URL}' target='_blank'><img src='{$URL}/image/design/{$mail_img[0]}' width='760' border='0' alt='Mail Image' /></a>";	
		$MAIL_COMMENT = stripslashes($mail_img[7]).$MAIL_COMMENT;
		$MAIL_COMMENT =  str_replace("skin/", "{$URL}/skin/",$MAIL_COMMENT); 
		$MAIL_COMMENT =  str_replace("{$Main}", "{$URL}/{$Main}",$MAIL_COMMENT); 			
		$name = "{name}";		
		include "../../php/mail_form.php";   //메일 양식 인클루드
		$site_name = $basic[1];
	}
}
############ 상품배송 메일 보내기전 정의 ############

if($_GET['mode']=='secStep') {
	$item = $_POST['item'];		
	if(!$item || !$step)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

	for($i=0,$cnt=count($item);$i<$cnt;$i++) {
		orderProcess($step,$item[$i]); 			
	} 
	alert($i."건의 상품을 ".$status_arr[$step]."처리를 했습니다!",$url);	
}

$sql = "SELECT escrow, carr_info FROM mall_order_info WHERE order_num = '{$order_num}'";
$es_tmp = $mysql->one_row($sql);

$carr_info = $_POST['delivery']."|".addslashes($_POST['carr_num']);
$admess = addslashes($_POST['admess']);

$name2		= addslashes($_POST['name2']);
$tel1		= addslashes($_POST['tel11']);
$tel2		= addslashes($_POST['tel12']);
$tel3		= addslashes($_POST['tel13']);
$phone1		= addslashes($_POST['phone11']);
$phone2		= addslashes($_POST['phone12']);
$phone3		= addslashes($_POST['phone13']);
$zip1		= addslashes($_POST['zip11']);
$zip2		= addslashes($_POST['zip12']);
$addr1		= addslashes($_POST['addr1']);
if($tel1 && $tel2 && $tel3) $tel = "{$tel1} - {$tel2} - {$tel3}";
else $tel = "";
if($phone1 && $phone2 && $phone3) $hphone = "{$phone1} - {$phone2} - {$phone3}";
else $hphone = "";
if($zip1 && $zip2) $zipcode = "{$zip1} - {$zip2}";
else $zipcode = "";
if($name2) $pname = ", name2 = '{$name2}'";
else $pname = "";
if($addr1) $paddr = ", address = '{$addr1}'";
else $paddr = "";

$sql = "UPDATE mall_order_info SET carr_info= '{$carr_info}'  ,admess = '{$admess}', tel2 = '{$tel}', hphone2 = '{$hphone}', zipcode = '{$zipcode}' {$pname} {$paddr} WHERE order_num = '{$order_num}'";
$mysql->query($sql);

if($es_tmp['escrow']=='Y') {
	$tmps = explode("|",$es_tmp['carr_info']);
	if(!$tmps[1] && $_POST['carr_num']) {  //에스크로 결제일 경우 배송정보 등록시 자료전송
		$sql = "SELECT code FROM mall_design WHERE mode='B'";
		$tmp_cash = $mysql->get_one($sql);
		$cash = explode("|*|",stripslashes($tmp_cash));
		switch($cash[2]){
			case "KCP" : 				
				$rtnVls = implode("", socketPost("http://".$_SERVER["SERVER_NAME"]."/{$ShopPath}card/kcp/deli_ok.php?order_num={$order_num}"));
				if(eregi('False',$rtnVls)) alert("배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.","back");		
			break;
			case "LGDACOM" : 		
				$rtnVls = implode("", socketPost("http://".$_SERVER["SERVER_NAME"]."/{$ShopPath}card/lgdacom/deli_ok.php?order_num={$order_num}"));
				if(eregi('False',$rtnVls)) alert("배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.","back");			
			break;
		}
	}
}
$msg = "처리 되었습니다.";

if($step[0]) {
    if($step[0]=='DEL') {		
		$sql = "SELECT order_status FROM mall_order_info WHERE order_num = '{$order_num}'";
		if($mysql->get_one($sql)!='Z') alert('주문취소 상태건만 삭제가 가능합니다','back');

		$sql = "DELETE FROM mall_order_goods WHERE order_num = '{$order_num}'";
        $mysql->query2($sql);        
		$sql = "DELETE FROM mall_order_info WHERE order_num = '{$order_num}'";
		$mysql->query2($sql);        
		$sql = "DELETE FROM mall_reserve WHERE order_num = '{$order_num}'";
		$mysql->query2($sql);
		$sql = "DELETE FROM mall_order_change WHERE order_num = '{$order_num}'";
		$mysql->query2($sql);
	}
	else {
		orderProcess($step[0],$order_num); 	
	}
	$msg = "주문상품을 ".$status_arr[$step[0]]."처리 하였습니다.";
}

if($pop==1) {
	echo "<script>alert('{$msg}'); parent.location.reload();</script>";	
}

alert($msg,$url);

?>