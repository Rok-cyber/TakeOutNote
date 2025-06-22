<? 
$PGConf['page_record_num'] = "10";
$PGConf['page_link_num'] = "10";

$code = isset($_GET['code'])? $_GET['code']:$_POST['code']; 

$Main = "./board.php?code={$code}";

switch($code) {
	case "add_page" :	        
         	$skin = './skin/add_page';           
			$t_img = "{$skin}/img/tm_add_page.gif";         
			$t_msg = "페이지를 원하시는 디자인으로 추가 할 수 있습니다. 추가된 페이지는 페이지 주소를 확인 하셔서 사용 하시면 됩니다.";
			$MTlist = Array(3,'uid','location','signdate');
			$MTform = Array(4,'location','leftmenu','board','html');
			$MTins = Array(5,'location','leftmenu','board','html','signdate');
			$MTmod = Array(4,'location','leftmenu','board','html');
			
	break;

	case "banner" :	        
         	$skin = "./skin/banner";
			$t_img = "$skin/img/tm_banner.gif";         
            $dir ="../../image/banner";
			$t_msg = "배너를 등록, 수정, 삭제 하거나 순서를 변경 할 수 있습니다. 배너는 메인페이지, 고객센터, 로그인 페이지에 나타 납니다.";
			$MTlist = Array(8,'uid','banner','name','rank','edate','location','signdate','cate');
			$MTform = Array(9,'rank','banner','link','location','status','target','name','edate','cate');
			$MTins = Array(10,'name','rank','status','target','banner','link','location','edate','cate','signdate');
			$MTmod = Array(8,'name','status','target','banner','link','location','edate','cate');
			$banner_arr = Array('','메인1','메인2','고객센터','로그인','분류박스','퀵메뉴','마이페이지','상단','서브');
			
	break;

	case "brand" :
	        $skin = "./skin/brand";    
            $t_img = "$skin/img/tm_brand.gif";    
			$dir ="../../image/brand";
			$t_msg = "브랜드를 등록하거나 수정 하실 수 있습니다.";
			$MTlist = Array(5,'uid','img1','name','tag','signdate');
			$MTform = Array(6,'name','tag','img1','code_use','code','uid');
			$MTins = Array(6,'name','tag','img1','code_use','code','signdate');
			$MTmod = Array(5,'name','tag','img1','code_use','code');			
	break;

	case "popup" :	        
         	$skin = "./skin/popup";
			$t_img = "$skin/img/tm_popup.gif";
			$t_msg = "팝업을 등록하거나 수정 할 수 있습니다.";
            $MTlist = Array(7,'uid','subject','status','days','type','signdate','end_date');
			$MTform = Array(7,'subject','status','days','type','end_date','info','comment');
			$MTins = Array(8,'subject','status','days','type','end_date','info','comment','signdate');
			$MTmod = Array(7,'subject','status','days','type','end_date','info','comment');
			
	break;	

	case "reserve" :
			$skin = "./skin/reserve";     	        
			$t_img = "$skin/img/tm_reserve.gif";         	      
			$t_msg = "적립금을 등록하거나 수정 할 수 있습니다. 이벤트등으로 회원에게 적립금을 주실때는 적립상태를 적립완료로 하시면 됩니다.";
			$MTlist = Array(6,'uid','subject','id','reserve','status','signdate');
			$MTform = Array(4,'id','subject','reserve','status');
			$MTins = Array(5,'id','subject','reserve','status','signdate');
			$MTmod = Array(4,'id','subject','reserve','status');

			$reserve_arr = Array("A"=>"<font class=green>적립대기</font>","B"=>"<font class=orange>적립완료</font>","C"=>"<font class=blue>적립사용</font>","D"=>"적립취소","E"=>"사용취소");			
	break;

	case "search" :
	        $PGConf['page_record_num'] = "20";
	        $skin = "./skin/search";
			$t_img = "$skin/img/tm_search.gif";         	
			$t_msg = "고객님께서 검색한 검색어를 관리할 수 있습니다. 검색어는 실시간 검색 순위에 이용 됩니다.";
            $MTlist = Array(5,'uid','word','ip','tag','signdate');			
	break;

	case "auto_search" :
	        $PGConf['page_record_num'] = "20";
	        $skin = "./skin/auto_search";
			$t_img = "$skin/img/tm_auto_search.gif";    
			$t_msg = "검색창 검색시 자동완성에 나타날 검색어를 관리할 수 있습니다.";
            $MTlist = Array(4,'uid','word','ord','signdate');			
			$MTform = Array(2,'word','ord');			
			$MTins = Array(4,'word','split_word','ord','signdate');
			$MTmod = Array(3,'word','split_word','ord');
	break;

	case "goods_point" :
	        $skin = "./skin/point";    
            $t_img = "$skin/img/tm_point.gif";    
			$t_msg = "고객이 평가한 상품평을 보실 수 있습니다. 우수상품평을 채택하여 적립금을 부여 할 수도 있습니다.";
			$MTlist = Array(10,'uid','title','goods_name','point','name','buy','best','cate','number','signdate');
			$MTform = Array(10,'goods_name','title','point','name','id','content','best','reserve','cate','number');			
			$MTmod = Array(2,'best','reserve');
			
	break;

	case "goods_qna" :
	        $skin = "./skin/qna";    
            $t_img = "$skin/img/tm_qna.gif";    
			$t_msg = "고객님이 문의 하신 상품에 관한 질문을 확인 하시고 답변 하시면 됩니다.";
			$MTlist = Array(8,'uid','title','goods_name','name','answer','cate','number','signdate');
			$MTform = Array(8,'goods_name','title','name','id','content','answer','cate','number');			
			$MTmod = Array(1,'answer');			
	break;

	case "member_quit" :
			$skin = "./skin/member_quit";     	    
			$t_img = "$skin/img/tm_quit.gif";
			$t_msg = "탈퇴 회원의 탈퇴 사유및 의견을 볼 수 있습니다.";
			$MTlist = Array(5,'uid','name','cate','ocnt','signdate');
			$MTform = Array(5,'name','cate','ocnt','message','signdate');
			$MTcate = Array(5,"상품품질불만","배송지연","교환/환불/반품불만","개인정보유출방지","기타");
	break;

	case "event" :	        
         	$skin = "./skin/event";
			$t_img = "$skin/img/tm_event.gif";
			$dir= "../../image/event";
			$t_msg = "이벤트를 등록하거나 수정 하실 수 있습니다. 이벤트별로 할인률 및 추가적립금을 설정 할 수 있습니다.";
            $MTlist = Array(7,'uid','name','sale','point','s_date','e_date','signdate');
			$MTform = Array(11,'name','s_date','e_date','sale','point','code','scate','sgoods','sbrand','code_use','img1');
			$MTins = Array(12,'name','s_date','e_date','sale','point','code','scate','sgoods','sbrand','code_use','img1','signdate');
			$MTmod = Array(11,'name','s_date','e_date','sale','point','code','scate','sgoods','sbrand','code_use','img1');			
	break;	

	case "special" :
	        $skin = "./skin/special";    
            $t_img = "$skin/img/tm_special.gif";    
			$dir ="../../image/special";
			$t_msg = "기획전을 등록하거나 수정 하실 수 있습니다. 기획전은 행사상품, 계절상품 처럼 분류와 상관없이 상품을 한페이지에 진열할때 사용하시면 됩니다.";
			$MTlist = Array(3,'uid','name','signdate');
			$MTform = Array(5,'name','img1','code_use','code','uid');
			$MTins = Array(5,'name','img1','code_use','code','signdate');
			$MTmod = Array(4,'name','img1','code_use','code');			
	break;

	case "sms_list" :
	        $PGConf['page_record_num'] = "20";
	        $skin = "./skin/sms_list";
			$t_img = "$skin/img/tm_sms_list.gif";         	
			$t_msg = "SMS 발송에 대한 상세내역을 확인 할 수 있습니다. SMS 발송 결과가 자동으로 업데이트 되어 다소의 시간이 소요될 수 있습니다. ";
            $MTlist = Array(8,'uid','signdate','num','message','status','succ_cnt','total_cnt','result');		
			$MTform = Array(9,'signdate','num','succ_cnt','total_cnt','message','err_msg','status','LMS','result');
	break;
	
	case "sms_addr" :
	        $skin = "./skin/sms_addr";    
            $t_img = "$skin/img/tm_sms_addr.gif";    
			$t_msg = "현재 내 쇼핑몰의 SMS 주소록을 파악하고 SMS를 보낼 수 있습니다.";
			$MTlist = Array(7,'uid','name','groups','cell','sex','memo','signdate');
			$MTform = Array(5,'groups','name','cell','sex','memo');
			$MTins = Array(6,'groups','name','cell','sex','memo','signdate');
			$MTmod = Array(5,'groups','name','cell','sex','memo');
			
	break;

	case "sms_sample" :
	        $skin = "./skin/sms_sample";    
            $t_img = "$skin/img/tm_sms_sample.gif";    
			$t_msg = "SMS 발송시 사용될 문구를 미리 등록하고 관리 할 수 있습니다.";
			$MTlist = Array(4,'uid','title','groups','message');
			$MTform = Array(3,'groups','title','message');
			$MTins = Array(4,'groups','title','message','signdate');
			$MTmod = Array(3,'groups','title','message');
			
	break;

	case "affiliate" :	        
         	$skin = "./skin/affiliate";
			$t_img = "$skin/img/tm_affiliate.gif";         
			$t_msg = "제휴마케팅 업체(Affiliate)를 등록하고 관리할 수 있습니다. Affiliate 관리 페이지 : <a href='http://".$_SERVER['HTTP_HOST']."/{$ShopPath}affiliate' target='_blank'>http://".$_SERVER['HTTP_HOST']."/{$ShopPath}affiliate</a>";
			$MTlist = Array(8,'uid','id','name','cell','commission','bank_day','auth','signdate');
			$MTform = Array(11,'auth','id','name','cell','email','commission','bank_name','bank_num','bank_owner','bank_day','memo');
			$MTins = Array(13,'auth','id','passwd','name','cell','email','commission','bank_name','bank_num','bank_owner','bank_day','memo','signdate');
			$MTmod = Array(11,'auth','passwd','name','cell','email','commission','bank_name','bank_num','bank_owner','bank_day','memo');
	break;

	case "affiliate_banner" :	        
         	$skin = "./skin/affiliate_banner";
			$t_img = "$skin/img/tm_affiliate_banner.gif";
			$dir ="../../image/banner";
			$t_msg = "제휴마케팅 업체에게 제공될 배너를 등록하고 관리할 수 있습니다";
			$MTlist = Array(5,'uid','type','title','banner','signdate');
			$MTform = Array(3,'type','title','banner');
			$MTins = Array(4,'type','title','banner','signdate');
			$MTmod = Array(3,'type','title','banner');
	break;

	case "affiliate_account" :	        
         	$skin = "./skin/affiliate_account";
			$t_img = "$skin/img/tm_affiliate_account.gif";
			$t_msg = "Affiliate 정산 송금내역을 확인 할 수 있습니다.";
            $MTlist = Array(7,'uid','a_month','affiliate','name','a_price','bank_info','dates');
			$MTform = Array(6,'affiliate','a_month','a_price','bank_info','dates','memo');
			$MTins = Array(8,'affiliate','name','a_month','a_price','bank_info','dates','memo','signdate');
			$MTmod = Array(7,'affiliate','name','a_month','a_price','bank_info','dates','memo');			
	break;	

	case "mobile_banner" :	        
         	$skin = "./skin/mobile_banner";
			$t_img = "$skin/img/tm_banner.gif";         
            $dir ="../../image/banner";
			$t_msg = "모바일샵에 사용되는 배너를 등록 하실 수 잇습니다.";
			$MTlist = Array(8,'uid','banner','name','rank','edate','location','signdate','cate');
			$MTform = Array(9,'rank','banner','link','location','status','target','name','edate','cate');
			$MTins = Array(10,'name','rank','status','target','banner','link','location','edate','cate','signdate');
			$MTmod = Array(8,'name','status','target','banner','link','location','edate','cate');
			$banner_arr = Array('','메인1','메인2');			
	break;

}
?>