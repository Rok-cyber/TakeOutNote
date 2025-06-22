<? 
$PGConf['page_record_num'] = "10";
$PGConf['page_link_num'] = "10";

$code = isset($_GET['code'])? $_GET['code']:$_POST['code']; 

$Main = "./board.php?code={$code}";

switch($code) {
	case "affiliate_account" :	        
         	$skin = "./skin/affiliate_account";
			$t_img = "{$skin}/img/tm_account.gif";
			$t_msg = "정산 입금내역을 확인 할 수 있습니다.";
            $MTlist = Array(5,'uid','a_month','a_price','bank_info','dates');			
	break;	

	case "affiliate_banner" :	        
         	$skin = "./skin/affiliate_banner";
			$t_img = "{$skin}/img/tm_banner.gif";
			$dir ="../../image/banner";
			$t_msg = "원하는 배너를 가져가서 사이트에 링크를 걸 수 있습니다";
			$MTlist = Array(5,'uid','type','title','banner','signdate');			
	break;
}
?>