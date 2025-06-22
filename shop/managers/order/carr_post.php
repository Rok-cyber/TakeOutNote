<?
######################## lib include
$skin_inc = 'Y';
include "../ad_init.php";
include "{$lib_path}/lib.Shop.php";

$mode = $_GET['mode'];
if($mode=="create"){	

	$file_name = "itsMallOrderCSV_".date("Ymd",time());
	header( "Content-type: application/vnd.ms-excel" ); 
	header( "Content-Disposition: attachment; filename={$file_name}.csv" ); 
	header( "Content-Description: Gamza Excel Data" ); 
	header( "Content-type: application/vnd.ms-excel;charset=KSC5601" ); 

	//변수 정의
	$sdate1	= $_POST['sdate1'];
	$sdate2	= $_POST['sdate2'];
	$sdate3	= $_POST['sdate3'];
	$sdate4	= $_POST['sdate4'];

	if($_POST['ck1']==1) {
		$where .= " && order_status ='C' ";
	}
	if($_POST['ck2']==1) {
		$where .= " && order_status ='D' ";
	}
	if($_POST['ck1']==1 && $_POST['ck2']==1) {
		$where = " && (order_status ='C' || order_status ='D') ";	
	}

	if($sdate1 && $sdate2) {	
		if($sdate1 > $sdate2) {$tmp = $sdate1; $sdate1 = $sdate2; $sdate2 = $tmp;}
		if($sdate1==$sdate2) $where .= "&& INSTR(signdate,'{$sdate1}') ";
		else $where .= "&& (signdate BETWEEN '{$sdate1}' AND '{$sdate2}' || INSTR(signdate,'{$sdate2}'))";		
	}  

	if($sdate3 && $sdate4) {	
		if($sdate3 > $sdate4) {$tmp = $sdate3; $sdate3 = $sdate4; $sdate4 = $tmp;}
		if($sdate3==$sdate4) $where .= "&& INSTR(signdate,'{$sdate3}') ";
		else $where .= "&& (status_date BETWEEN '{$sdate3}' AND '{$sdate4}' || INSTR(status_date,'{$sdate4}'))";		
	}  

	$sql = "SELECT order_num, name1, carr_info FROM mall_order_info	WHERE uid>0 {$where} ORDER BY uid ASC";
	$mysql->query($sql);
	
	while($data=$mysql->fetch_array()){
		$tmps = explode("|",$data['carr_info']);
		if($tmps[1]) continue;
		$data['name1'] = iconv("utf-8","euc-kr",$data['name1']);

		echo "{$data['order_num']},{$data['name1']},\n";
	}
}
else if($mode=='csv') {
	if(!eregi("none",$_FILES["csv"]['tmp_name']) && $_FILES["csv"]['tmp_name']) {
		$signdate = date("Y-m-d H:i:s");
		$carr = $_POST['carr'];
		$ext = getExtension($_FILES["csv"]['name']);
		if($ext!="csv") {			
			alert("CSV 파일이 아닙니다.","back");
		}

		############ 상품배송 메일 보내기전 정의 ############
		$URL = "http://".$_SERVER["SERVER_NAME"];		
		$sql = "SELECT code FROM mall_design WHERE mode = 'F'";
		$mail_img = explode("|*|",stripslashes($mysql->get_one($sql)));	
		if($mail_img[6]=='1') {			
			$skin = "../../skin/{$tmp_skin}/";
			$mail_type = "carr";
			$MAIL_IMG = "<a href='{$URL}' target='_blank'><img src='{$URL}/image/design/{$mail_img[0]}' border='0' alt='Mail Image' /></a>";	
			$MAIL_COMMENT = stripslashes($mail_img[7]).$MAIL_COMMENT;
			$MAIL_COMMENT =  str_replace("skin/", "{$URL}/skin/",$MAIL_COMMENT); 
			$MAIL_COMMENT =  str_replace("{$Main}", "{$URL}/{$Main}",$MAIL_COMMENT); 			
			$name = "{name}";		
			include "../../php/mail_form.php";   //메일 양식 인클루드
			$site_name = $basic[1];
		}
		############ 상품배송 메일 보내기전 정의 ############

		$tmps = readFiles($_FILES["csv"]['tmp_name']);
		$tmps = explode("\n",$tmps);
		for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
			$tmps[$i] = iconv("euc-kr","utf-8",$tmps[$i]);
			$tmps2 = explode(",",$tmps[$i]);
			
			if($tmps2[0] && $tmps2[2]){
				$carr_info = "{$carr}|{$tmps2[2]}";
				$sql = "SELECT id, name1, email, hphone1, pay_total, carr_info, order_status, escrow FROM mall_order_info WHERE order_num = '{$tmps2[0]}'";
				$info = $mysql->one_row($sql);

				if($info['order_status']=='C') {
					$sql = "UPDATE mall_order_info SET carr_info = '{$carr_info}', order_status='D', status_date='{$signdate}' WHERE order_num='{$tmps2[0]}'";
					$mysql->query($sql);

					$sql = "UPDATE mall_order_goods SET  order_status = 'D', signdate = '{$signdate}' WHERE order_num='{$tmps2[0]}'";
					$mysql->query($sql);

					############ 상품배송 메일 보내기 ############		
					if($mail_form) {
						$mail_form = str_replace("{shopName}",$site_name,$mail_form);
						$mail_form = str_replace("{name}",stripslashes($info['name1']),$mail_form);
						
						$tmpsc = explode("|",$carr_info);
						$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmpsc[0]}' && mode='Z'";
						$tmp = $mysql->one_row($sql);								
						$deli_comp = $tmp['name'];
						$deli_no = $tmpsc[1];

						$mail_form = str_replace("{deli_comp}",$deli_comp,$mail_form);
						$mail_form = str_replace("{deli_no}",$deli_no,$mail_form);
						pmallMailSend($info['email'], "{$site_name}에서 주문하신 상품이 배송 되었습니다.", $mail_form);					
					}
					############ 상품배송 메일 보내기 ############

					############ SMS 보내기 #############
					if($info['hphone1']) {
						$code_arr = Array();
						$code_arr['name'] = $info['name1'];
						$code_arr['number'] = $order_num;
						$code_arr['price'] = number_format($info['pay_total']);			
							
						$tmpsc = explode("|",$carr_info);
						$sql = "SELECT name, code FROM mall_design WHERE uid='{$tmpsc[0]}' && mode='Z'";
						$tmp = $mysql->one_row($sql);								
						$code_arr['deli_comp'] = $tmp['name'];
						$code_arr['deli_no'] = $tmpsc[1];
						
						pmallSmsAutoSend($info['hphone1'],"carriage",$code_arr);
					}
					############ SMS 보내기 #############

					if($info['escrow']=='Y') {
						$sql = "SELECT code FROM mall_design WHERE mode='B'";
						$tmp_cash = $mysql->get_one($sql);
						$cash = explode("|*|",stripslashes($tmp_cash));
						switch($cash[2]){
							case "KCP" : 
								$rtnVls = implode("", socketPost("http://".$_SERVER["SERVER_NAME"]."/card/kcp/deli_ok.php?order_num={$tmps2[0]}"));
							break;
						}	
					}
				}
				else if($info['order_status']=='D') {
					$sql = "UPDATE mall_order_info SET carr_info = '{$carr_info}' WHERE order_num='{$tmps2[0]}'";
					$mysql->query($sql);
				}
			}
		}
		alert("송장번호가 일괄 등록 되었습니다.","order_carr.html");
	}
	alert("파일을 등록하지 않으셨습니다","back");

}

?>