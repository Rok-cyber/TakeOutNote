<?
$acc_ip = $_SERVER['REMOTE_ADDR'];  //접속 ip 

if(!$referer=urldecode($_GET['referer']))  $referer = urldecode($_SERVER['HTTP_REFERER']);  //접속 경로
if(!$referer) { 
	$referer="Typing or Bookmark Moving On This Site";     //접속 경로 값이 없으면...
    $sites = "Typing or Bookmark";
} else {	
	$tmp = str_replace("http://","",$referer);
	$tmp = explode("/",$tmp);
    $sites = str_replace("www.","",$tmp[0]);
	unset($tmp);	
}

$browser = ckBrowser();  //브라우저 정보
$os = ckOs(); //OS 정보

$YEAR	= date('Y');     
$MONTH	= date('m');
$DAY	= date('d');
$TIME	= date('H');
$ck_date= date('Ymd');

$sql = "SELECT ck_date FROM pcount_check ORDER BY uid DESC LIMIT 1";
$row_ct = $mysql->one_row($sql);

if((!$row_ct['ck_date']) || $row_ct['ck_date'] < $ck_date) { //날이 바뀌면
    $sql = "DELETE FROM pcount_check";
	$mysql->query($sql);	
	
	############## 주문완료 처리 (배송후 7일 후) ##################
	$sql	= "SELECT code FROM mall_design WHERE mode='T'";
	$tmp	= $mysql->get_one($sql);
	$etc_info = explode("|",$tmp);
	$EDATE = isset($etc_info[3]) ? $etc_info[3] : 7;
	unset($etc_info);

	$DAY2 = date("Y-m-d", strtotime("-{$EDATE} DAY", time()));
	$sql ="SELECT order_num, id FROM mall_order_info WHERE order_status = 'D' && status_date < '{$DAY2}'";
	$mysql->query($sql);
	$signdate = date("Y-m-d H:i:s",time());
	
	while($row = $mysql->fetch_array()){		
		$sql = "UPDATE mall_order_info SET order_status = 'E', status_date = '{$signdate}' WHERE order_num = '{$row['order_num']}'";	
		$mysql->query2($sql);

		$sql = "UPDATE mall_order_goods SET order_status = 'E' , status_date = '{$signdate}' WHERE order_num = '{$row['order_num']}' && order_status='D'";	
		$mysql->query2($sql);
		
		if($row['id'] && $row['id']!='guest') {		
			$sql = "SELECT sum(reserve) FROM mall_reserve WHERE status = 'A' && order_num = '{$row['order_num']}'";
			$tmp_r = $mysql->get_one($sql);
			if(!$tmp_r && $tmp_r==0) $tmp_r=0;
				
			$sql = "UPDATE mall_reserve SET status = 'B' WHERE status = 'A' && order_num = '{$row['order_num']}'";
			$mysql->query2($sql);
					 
			$sql = "UPDATE pboard_member SET reserve = reserve + {$tmp_r} WHERE id = '{$row['id']}'";
			$mysql->query2($sql);
		}
	}

	############## 장바구니 기록 삭제(2일 지난자료, 비회원) ###############
	$DAY2 = strtotime('-2 DAY', time());
	$sql = "DELETE FROM mall_cart WHERE LENGTH(tempid)=32 && date < '{$DAY2}'";
	$mysql->query($sql);

	############## 장바구니 기록 삭제(두달 지난자료) ###############
	$DAY2 = strtotime('-2 MONTH', time());
	$sql = "DELETE FROM mall_cart WHERE date < '{$DAY2}'";
	$mysql->query($sql);

	############## 상품클릭 기록 삭제(두달 지난자료) ###############	
	$DAY2 = date("Ymd", strtotime('-2 MONTH', time()));
	$sql = "DELETE FROM mall_goods_view WHERE date < '{$DAY2}'";
	$mysql->query($sql);

	############### 이벤트 상품 체크 ###############################
	$sql = "SELECT uid,scate,sgoods,sbrand FROM mall_event WHERE s_check='0' && s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."'";
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){
		if(!$row['scate'] && !$row['sgoods'] && !$row['sbrand']) {
			$sql = "UPDATE mall_goods SET event = '{$row['uid']}' WHERE SUBSTRING(cate,1,3)!='999'";
			$mysql->query2($sql);
		}
		else {
			if($row['scate']) {
				$tmps = explode("|",$row['scate']);
				for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
					if(substr($tmps[$i],3,6)=='000000') {
						$sql = "UPDATE mall_goods SET event = '{$row['uid']}' WHERE SUBSTRING(cate,1,3)='".substr($tmps[$i],0,3)."'";
					}
					else if(substr($tmps[$i],6,3)=='000') {
						$sql = "UPDATE mall_goods SET event = '{$row['uid']}' WHERE SUBSTRING(cate,1,6)='".substr($tmps[$i],0,6)."'";
					}
					else {
						$sql = "UPDATE mall_goods SET event = '{$row['uid']}' WHERE cate='{$tmps[$i]}'";
					}
					$mysql->query2($sql);
				}
			}

			if($row['sgoods']) {
				$tmps = explode("|",$sgoods);
				for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
					$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE uid='{$tmps[$i]}'";
					$mysql->query2($sql);
				}				
			}

			if($row['sbrand']) {
				$tmps = explode("|",$row['sbrand']);
				for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
					$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE brand='{$tmps[$i]}'";
					$mysql->query2($sql);
				}
			}
		}
		$sql ="UPDATE mall_event SET s_check = '1' WHERE uid='{$row['uid']}'";
		$mysql->query2($sql);
	}

	$sql = "SELECT uid FROM mall_event WHERE s_check='1' && e_date <'".date("Y-m-d")."'";
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){
		$sql = "UPDATE mall_goods SET event='0' WHERE event='{$row['uid']}'";
		$mysql->query2($sql);
		$sql = "UPDATE mall_event SET s_check ='2' WHERE uid='{$row['uid']}'";
		$mysql->query2($sql);
	}

	################# 쿠폰 유효성 체크 ####################
	$sql = "SELECT a.uid, a.id, a.status, a.signdate, b.sdate, b.edate, b.days FROM mall_cupon a, mall_cupon_manager b WHERE a.pid=b.uid && a.status ='A'";
	$mysql->query($sql);

	while($row = $mysql->fetch_array()){			
		$ck_cupon = 0;

		if($row['sdate'] && $row['edate'] && !$row['days']) {
			if(date("Y-m-d")>$row['edate']) $ck_cupon = 1;
		}
		else {
			$tmps = date("Y-m-d", strtotime("+{$row['days']} DAY", $row['signdate']));		
			if(date("Y-m-d")>$tmps) $ck_cupon = 1;	
		}	
		if($ck_cupon==1) {
			$sql = "UPDATE mall_cupon SET status='C' WHERE id='{$row['id']}' && uid = '{$row['uid']}'";
			$mysql->query2($sql);
		}
	}

	################ 상품등록 임시폴더 삭제 ################		

	$sql = "SELECT MAX(uid) FROM mall_goods";
	$maxUid = $mysql->get_one($sql);
	if($maxUid>9999) $tmp_uid = floor($maxUid/10000);	

	$up_dir		= "image/up_img/detail/";
	$up_path	= "image/other_img/";
	
	if($tmp_uid) {
		$up_dir .= $tmp_uid."/";
		$up_path .= $tmp_uid."/";
	}	

	$handle = @opendir($up_path);
	while ($tmps = readdir($handle)) {	
		if(!eregi("\.",$tmps)) {
			if(substr($tmps,0,8) < date("Ymd",time()-(3600*24))) {
				delTree($up_path.$tmps);
			}
		}
	}
	@closedir($handle);

	$handle = @opendir($up_dir);
	while ($tmps = readdir($handle)) {	
		if(!eregi("\.",$tmps)) {
			if(substr($tmps,0,8) < date("Ymd",time()-(3600*24))) {
				delTree($up_dir.$tmps);
			}
		}
	}
	@closedir($handle);
	unset($up_dir,$up_path,$handle,$ck_cupon);
	
	################ SMS 발송결과 확인 ################		
	$sql = "SELECT uid,status,err_msg FROM mall_sms_list WHERE result='1' && status!='2' ORDER BY uid DESC LIMIT 5";
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){
		if(($row['status']==3 && $row['err_msg']>date("Y-m-d H:i:s")) || $row['status']==1) {
			pmallSmsResult($row['uid']);
		}
	}	
}	

$sql = "SELECT ck_ip,ck_date FROM pcount_check WHERE ck_ip = '{$acc_ip}'";
$row_ct = $mysql->one_row($sql);

if(!$row_ct['ck_ip']) { //아이피 체크 후 처음 접속이면 ... 카운팅
   
    $sql = "INSERT INTO pcount_check VALUES('','{$acc_ip}','{$ck_date}')";  //아이피 저장
    $mysql->query($sql);

    /*********** 카운팅 ************/
    $sql = "SELECT count(*) FROM pcount_list WHERE year = '$YEAR' && month = '$MONTH' && day = '$DAY'";  // 카운팅...
   
    if($mysql->get_one($sql) == '1') {	   
	    $sql = "UPDATE pcount_list SET h_{$TIME} = h_{$TIME} + 1, total = total + 1 WHERE year = '{$YEAR}' && month = '{$MONTH}' && day = '{$DAY}'";	   
    } 
	else {
	    $sql = "INSERT INTO pcount_list (uid,year,month,day,h_{$TIME},total) VALUES('','{$YEAR}','{$MONTH}','{$DAY}','1','1')";
    }
    $mysql->query($sql);

    /*********** 브라우저 카운팅 ************/
    $sql = "SELECT count(*) FROM pcount_agent WHERE type = 'B' && content = '{$browser}'";  

    if($mysql->get_one($sql) == '0') {	   
	    $sql = "INSERT INTO pcount_agent (type,content,cnts) VALUES('B','{$browser}','1')";
    } 
	else {
	    $sql = "UPDATE pcount_agent SET cnts = cnts + 1 WHERE type = 'B' && content = '{$browser}'";	   	   
    }
    $mysql->query($sql);

    /*********** OS 카운팅 ************/
    $sql = "SELECT count(*) FROM pcount_agent WHERE type = 'O' && content = '{$os}'";  

    if($mysql->get_one($sql) == '0') {	   
	    $sql = "INSERT INTO pcount_agent (type,content,cnts) VALUES('O','{$os}','1')";
    } 
	else {
	    $sql = "UPDATE pcount_agent SET cnts = cnts + 1 WHERE type = 'O' && content = '{$os}'";	   	   
    }
    $mysql->query($sql);  

	/*********** 사이트 카운팅 ************/
    $sql = "SELECT count(*) FROM pcount_agent WHERE type = 'S' && content = '{$sites}'";  

    if($mysql->get_one($sql) == '0') {	   
	    $sql = "INSERT INTO pcount_agent (type,content,cnts) VALUES('S','{$sites}','1')";
    } 
	else {
	    $sql = "UPDATE pcount_agent SET cnts = cnts + 1 WHERE type = 'S' && content = '{$sites}'";	   	   
    }
    $mysql->query($sql);  

	/********** 검색어 카운팅 ****************/
	if(eregi('query=',$referer)) {
		$tmps = explode("query=",$referer);
		$tmps = explode("&",$tmps[1]);
		$keyword = $tmps[0];
	}
	else if(eregi('q=',$referer)) {
		$tmps = explode("q=",$referer);
		$tmps = explode("&",$tmps[1]);
		$keyword = $tmps[0];
	}
	else if(eregi('p=',$referer)) {
		$tmps = explode("p=",$referer);
		$tmps = explode("&",$tmps[1]);
		$keyword = $tmps[0];
	}
	else if(eregi('Query=',$referer)) {
		$tmps = explode("Query=",$referer);
		$tmps = explode("&",$tmps[1]);
		$keyword = $tmps[0];
	}

	if($keyword) {
		$sql = "SELECT count(*) FROM pcount_agent WHERE type = 'K' && content = '{$keyword}'";  

		if($mysql->get_one($sql)==0) {	   
			$sql = "INSERT INTO pcount_agent (type,content,cnts) VALUES('K','{$keyword}','1')";
		} 
		else {
			$sql = "UPDATE pcount_agent SET cnts = cnts + 1 WHERE type = 'K' && content = '{$keyword}'";	   	   
		}
		$mysql->query($sql);  
	}
	
	/*********** 접속경로저장 ************/
	$sql = "SELECT count(*) FROM pcount_refer WHERE referer = '{$referer}'";
	if($mysql->get_one($sql) =='1') {	   
		$sql = "UPDATE pcount_refer SET hit = hit +1 WHERE referer = '{$referer}'";
	} 
	else {
	   $sql = "INSERT INTO pcount_refer  VALUES('','{$referer}','1')";
	}
	$mysql->query($sql);  
}

//페이지뷰

/*********** 카운팅 ************/
$sql = "UPDATE pcount_list SET h2_{$TIME} = h2_{$TIME} + 1, total2 = total2 + 1 WHERE year = '{$YEAR}' && month = '{$MONTH}' && day = '{$DAY}'";	   
$mysql->query($sql);

unset($acc_ip,$YEAR,$MONTH,$DAY,$TIME,$ck_date,$sql,$row_ct,$data);
?>