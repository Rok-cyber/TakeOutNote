<?
$tpl->define("main","{$skin}/location.html");
$tpl->scan_area("main");

################################### 쇼핑카테고리 전체보기 ######################
if(substr($cate,0,3)=='999') $coop_where = " && cate = '999000000000'";
else $coop_where = " && cate != '999000000000'";

$sql = "SELECT * FROM mall_cate WHERE cate_dep ='1' && valid ='1' {$coop_where} ORDER BY number ASC";
$mysql->query($sql);
unset($coop_where);

$i = 1;
while($data=$mysql->fetch_array()){
	if($cate && ($channel=='view' || $channel=="main2" || $channel=="list")) {
		$CNAME = addslashes($data['cate_name']);
		$CCATE = $data['cate'];
				
		if($CCATE==substr($cate,0,3)."000000000") $CSEC1 = $i;
		$i++;
		$tpl->parse("loop_seccate1");
	}
	else {
		$tcate = $data[cate];
		if($SKIN_DEFINE['cate_all_type']==1) {
			if($data['img2']){
				if($data['img3']) {				
					$CATE_NAME = "<img src='image/cate/{$data[img2]}' class='hand' onmouseover=\"this.src='image/cate/{$data[img3]}'\" onmouseout=\"this.src='image/cate/{$data[img2]}'\" alt='{$data['cate']}' />"; 
				} 
				else {
					$CATE_NAME = "<img src='image/cate/{$data[img2]}' onfocus='this.blur();' border='0' alt='{$data['cate']}' />";
				}			
			} 
			else {		
				$tpl->parse("is_text","1");
				$CATE_NAME = $data[cate_name];
			}
		}
		else {
			$tpl->parse("is_text","1");
			$CATE_NAME = $data[cate_name];
		}
		$tpl->parse("loop_tcate");				   
	}
}

if($cate && ($channel=='view' || $channel=="main2" || $channel=="list")) {
	$sql = "SELECT * FROM mall_cate WHERE cate='{$cate}'";
	$tmps = $mysql->one_row($sql);

	if($tmps['access_level'] && $my_level<9) {
		$access_level = explode("|",$tmps['access_level']);
		if($access_level[0]>1) {
			if($channel=='view') $tmps = '해당 상품의';
			else $tmps = '해당 분류의';
			if($access_level[1]=='!=' && $access_level[0]!=$my_level) alert("{$tmps} 접근권한이 없습니다.",'back');
			if($access_level[1]=='<' && $access_level[0]>$my_level) alert("{$tmps} 접근권한이 없습니다.",'back');
		}
	}	
	

	$p_cate = substr($cate,0,3)."000000000";
	$sql = "SELECT cate_name,img1,cate_dep FROM mall_cate WHERE cate = '{$p_cate}'";
	$row = $mysql->one_row($sql);

	if($row['img1']) $SHOP_CATE = "<img src='image/cate/{$row[img1]}' border='0' alt='{$row[cate_name]}' />";
	else $SHOP_CATE = "<div class='nameText'>{$row[cate_name]}</div>";	
		
	if($SKIN_DEFINE['location_type']==1) {
		$tpl->parse("is_seccate1");

		if(substr($cate,3,3)!='000') {
			$sql = "SELECT cate,cate_name FROM mall_cate WHERE cate_dep ='2' && valid ='1' && cate_parent='{$p_cate}' ORDER BY number ASC";
			$mysql->query($sql);
				
			$i = 1;
			while($row = $mysql->fetch_array()){
				$CNAME = addslashes($row['cate_name']);
				$CCATE = $row['cate'];
					
				if($CCATE==substr($cate,0,6)."000000") $CSEC2 = $i;
				$i++;

				$tpl->parse("loop_seccate2");
			}
			$tpl->parse("is_seccate2");
		}

		if(substr($cate,6,3)!='000') {
			$p_cate = substr($cate,0,6)."000000";
			$sql = "SELECT cate,cate_name FROM mall_cate WHERE cate_dep ='3' && valid ='1' && cate_parent='{$p_cate}' ORDER BY number ASC";
			$mysql->query($sql);
				
			$i = 1;
			while($row = $mysql->fetch_array()){
				$CNAME = addslashes($row['cate_name']);
				$CCATE = $row['cate'];
					
				if($CCATE==substr($cate,0,9)."000") $CSEC3 = $i;
				$i++;

				$tpl->parse("loop_seccate3");
			}
			$tpl->parse("is_seccate3");
		}	

		if(substr($cate,9,3)!='000') {
			$p_cate = substr($cate,0,9)."000";
			$sql = "SELECT cate,cate_name FROM mall_cate WHERE cate_dep ='4' && valid ='1' && cate_parent='{$p_cate}' ORDER BY number ASC";
			$mysql->query($sql);
				
			$i = 1;
			while($row = $mysql->fetch_array()){
				$CNAME = addslashes($row['cate_name']);
				$CCATE = $row['cate'];
					
				if($CCATE==$cate) $CSEC4 = $i;
				$i++;

				$tpl->parse("loop_seccate4");
			}
			$tpl->parse("is_seccate4");
		}	
	}
	else {
		$lt_string = explode(",",$tpl->getLtstring());
		$LOCATION = getLocation($cate,1,$lt_string[0],$lt_string[1]);
		$tpl->parse("define_location");
	}
}
else { 
	$lt_string = explode(",",$tpl->getLtstring());
	$sepa = ($lt_string[0]) ? $lt_string[0] : ">";
	$css_class = ($lt_string[1]) ? " class='{$lt_string[1]}' " : "";

	if($channel=='brand' || $channel=='special') {	
	
		$uid = $_GET['uid'];
		if(!$uid) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');
		
		$sql = "SELECT * FROM mall_{$channel} WHERE uid='{$uid}'";
		if(!$row = $mysql->one_row($sql)) alert('페이지가 존재하지 않습니다.\\n\\n다시 확인해 주시기 바랍니다.','back');
		
		$code_use	= $row['code_use'];
		$code		= stripslashes($row['code']);

		if($row['img1'] && file_exists("image/{$channel}/{$row['img1']}")) {
			$row['img1'] = urlencode($row['img1']);
			$SHOP_CATE = "<img src='image/{$channel}/{$row['img1']}' border='0' alt='{$row[name]}' />";
		}
		else $SHOP_CATE = "<div class='nameText'>{$row[name]}</div>";	

		if($channel=='brand') {
			$LOCATION .= "&nbsp;{$sepa}&nbsp;브랜드샵&nbsp;{$sepa}&nbsp;";	
			$sql = "SELECT name, uid FROM mall_brand ORDER BY name ASC";
			$NUMS = 2;
		}
		else {
			$LOCATION .= "&nbsp;{$sepa}&nbsp;기획전&nbsp;{$sepa}&nbsp;";	
			$sql = "SELECT name, uid FROM mall_special ORDER BY uid DESC";
			$NUMS = 6;
		}
		$mysql->query($sql);
				
		$i = 1;
		while($row = $mysql->fetch_array()){
			$row['name'] = stripslashes($row['name']);
			$row['name'] = str_replace("\"","&#034;",$row['name']);
			$row['name'] = str_replace("'","&#039;",$row['name']);
			$CNAME = $row['name'];
			$CUID = $row['uid'];
					
			if($uid==$CUID) {
				$CSEC1 = $i;
				if($SKIN_DEFINE['location_type']!=1) $LOCATION .= $CNAME;
			}
			$i++;
			$tpl->parse("loop_sbe_list");
		}	
		$tpl->parse("is_sbe_page");		
	}
	else if($channel=='event') {	
		$uid = $_GET['uid'];
		if(!$uid) {
			$sql = "SELECT uid FROM mall_event WHERE s_check='1' && s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."' LIMIT 1";
			$uid = $mysql->get_one($sql);
		}
		
		if($uid) {
			$sql = "SELECT * FROM mall_event WHERE uid='{$uid}' && s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."'";
			if(!$row = $mysql->one_row($sql)) alert('페이지가 존재하지 않거나 종료된 이벤트 입니다.',"{$Main}?channel=event");
			
			$code_use	= $row['code_use'];
			$code		= stripslashes($row['code']);

			if($row['img1'] && file_exists("image/event/{$row['img1']}")) {
				$row['img1'] = urlencode($row['img1']);
				$SHOP_CATE = "<img src='image/event/{$row['img1']}' border='0' alt='{$row[name]}' />";
			}
			else $SHOP_CATE = "<div class='nameText'>{$row[name]}</div>";	
				
			$LOCATION .= "&nbsp;{$sepa}&nbsp;이벤트샵&nbsp;{$sepa}&nbsp;";	

			$sql = "SELECT name, uid FROM mall_event WHERE s_date <= '".date("Y-m-d")."' && e_date >='".date("Y-m-d")."' ORDER BY s_date ASC";
			$mysql->query($sql);
					
			$i = 1;
			while($row = $mysql->fetch_array()){
				$row['name'] = stripslashes($row['name']);
				$row['name'] = str_replace("\"","&#034;",$row['name']);
				$row['name'] = str_replace("'","&#039;",$row['name']);
				$CNAME = $row['name'];
				$CUID = $row['uid'];
						
				if($uid==$CUID) {
					$CSEC1 = $i;
					if($SKIN_DEFINE['location_type']!=1) $LOCATION .= $CNAME;
				}
				$i++;
				$tpl->parse("loop_sbe_list");
			}
			$NUMS = 3;
			$tpl->parse("is_sbe_page");
		}
		else {
			$SHOP_CATE = "<div class='nameText'>이벤트 준비중</div>";	
			$LOCATION .= "&nbsp;{$sepa}&nbsp;이벤트샵&nbsp;{$sepa}&nbsp;";			
		}		
	}
	else {
		$tpl->parse("is_tcate");

		$title_arr = Array('cart'=>'장바구니','order_form'=>'주문/결제','order_end'=>'주문완료','search'=>'상품검색결과','customer'=>'고객센터','login'=>'로그인','mypage'=>'마이페이지','today'=>'오늘본상품','new'=>'신상품','reco'=>'추천상품','best'=>'베스트상품','tag'=>'Tag Top 100','rss'=>'RSS Service','event'=>'이벤트','card_pay'=>"주문/결제&nbsp;{$sepa}&nbsp;카드결제중",'cooperate'=>'공동구매','attendance'=>'출석체크');
		
		$location_customer = "&nbsp;{$sepa}&nbsp;<a href='{$Main}?channel=customer' {$css_class}>고객센터</a>";
		$location_mypage = "&nbsp;{$sepa}&nbsp;<a href='{$Main}?channel=mypage' {$css_class}>마이페이지</a>";

		switch($channel) {
			case "regist" : $LOCATION = "&nbsp;{$sepa}&nbsp;회원가입";
				if(!$_POST['jmode']) $LOCATION .= "&nbsp;{$sepa}&nbsp;약관동의";
				else $LOCATION .= "&nbsp;{$sepa}&nbsp;정보입력";
			break;
			case "regist2" : $LOCATION = "&nbsp;{$sepa}&nbsp;회원가입&nbsp;{$sepa}&nbsp;가입완료";
			break;
			case "board" :
				$LOCATION = $location_customer;
				$code = isset($_GET['code']) ? $_GET['code'] : 'notice';
				
				switch($code) {
					case "notice" : $LOCATION .= "&nbsp;{$sepa}&nbsp;공지사항";
					break;
					case "faq" : $LOCATION .= "&nbsp;{$sepa}&nbsp;FAQ";
					break;
					case "customer" : $LOCATION .= "&nbsp;{$sepa}&nbsp;고객게시판";
					break;
					case "counsel" : $LOCATION .= "&nbsp;{$sepa}&nbsp;1:1고객문의";
					break;	
					case "sales" : $LOCATION .= "&nbsp;{$sepa}&nbsp;대량구매문의";
					break;	
					case "cooperation" : $LOCATION .= "&nbsp;{$sepa}&nbsp;제휴/광고문의";
					break;	
					case "gallery" : $LOCATION .= "&nbsp;{$sepa}&nbsp;갤러리";
					break;
				}
			break;

			case "after2" : $LOCATION .= "&nbsp;{$sepa}&nbsp;<a href='{$Main}?channel=customer' {$css_class}>고객센터</a>&nbsp;{$sepa}&nbsp;이용후기";
			break;
			case "qna2" : $LOCATION .= "&nbsp;{$sepa}&nbsp;<a href='{$Main}?channel=customer' {$css_class}>고객센터</a>&nbsp;{$sepa}&nbsp;상품Q&A";
			break;

			case "docu" :
				$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
				
				switch($mode) {
					case "A" : $LOCATION .= "&nbsp;{$sepa}&nbsp;회사소개";
					break;
					case "B" : $LOCATION .= "&nbsp;{$sepa}&nbsp;이용약관";
					break;
					case "C" : $LOCATION .= "&nbsp;{$sepa}&nbsp;개인정보취급방침";
					break;
					case "D" : $LOCATION .= "&nbsp;{$sepa}&nbsp;이용안내";
					break;
				}
			break;

			case "osearch" : $LOCATION = "{$location_customer}&nbsp;{$sepa}&nbsp;비회원 주문/배송조회";
			break;						
			case "idpwsearch" : $LOCATION = "{$location_customer}&nbsp;{$sepa}&nbsp;아이디/비밀번호 찾기";
			break;		

			case "modify" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;회원정보수정";
			break;
			case "passwd" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;비빌번호변경";
			break;
			case "quit" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;회원탈퇴신청";
			break;
			case "counsel" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;1:1고객문의";
			break;
			case "wish" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;관심상품 목록";
			break;
			case "reserve" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;적립금 내역";
			break;
			case "cupon" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;발급 쿠폰 내역";
			break;
			case "order" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;주문/배송조회";
			break;
			case "order_cancel" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;취소/반품/교환내역";
			break;
			case "order_detail" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;주문상세내역";
			break;
			case "cooperate_list" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;공동구매 신청내역";
			break;
			case "after" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;나의 이용후기";
			break;
			case "qna" : $LOCATION = "{$location_mypage}&nbsp;{$sepa}&nbsp;나의 상품Q&A";
			break;	
			case "plus" :
				$plus = $_GET['plus'];
				if(!$plus) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');
				
				$sql = "SELECT * FROM mall_add_page WHERE uid='{$plus}'";
				if(!$plus_info = $mysql->one_row($sql)) alert('페이지가 존재하지 않습니다.\\n\\n다시 확인해 주시기 바랍니다.','back');
				$plus_info['location'] = addslashes($plus_info['location']);
				$LOCATION = "&nbsp;{$sepa}&nbsp;{$plus_info['location']}";
			break;

			default :
				$LOCATION = "&nbsp;{$sepa}&nbsp;{$title_arr[$channel]}";
			break;	
		}	
		unset($title_arr);
	}
	$tpl->parse("define_location");
	unset($sepa, $css_class);
}

$tpl->parse("main");
if($SKIN_DEFINE['location_view']==2) $LOCATION_VALUE = $tpl->tprint("main","1");
else $tpl->tprint("main");
$tpl->close();

if($p_cate) {
	unset($location_customer,$location_mypage,$p_cate,$i,$CSEC1,$CSEC2,$CSEC3,$CNAME,$CCATE);
}
?>		           