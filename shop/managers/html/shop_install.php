<? 
ob_start();

$ad_id = "admin";
$ad_pw = "1213";
$signdate = time();

$bo_path	= "../../pboard";      
$lib_path	= "../../lib";
$inc_path	= "../../include";

require "$lib_path/lib.Function.php";
include "$inc_path/dbconn.php";
require "$lib_path/class.Mysql.php";

$mysql = new  mysqlClass(); //디비 클래스

/*
$prev_table = $mysql->table_list('objetmall1');
for($i=0;$i<count($prev_table);$i++) {
	$sql = "DROP TABLE {$prev_table[$i]}";
	$mysql->query($sql);
}
*/

if(!$mysql->table_list("","mall_add_page")) {
	$sql =  "
	CREATE TABLE mall_add_page (
	  uid int(11) unsigned NOT NULL auto_increment,
	  location varchar(80) binary NOT NULL default '',
	  leftmenu enum('0','1','2','3','4') not null default '0',
	  html text NOT NULL default '',
	  board varchar(50) binary NOT NULL default '',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_auto_search")) {
	$sql =  "
	CREATE TABLE mall_auto_search (
	  uid int(11) unsigned NOT NULL auto_increment,
	  word varchar(200) binary NOT NULL default '',
	  split_word varchar(200) binary NOT NULL default '',
	  ord int(8) unsigned NOT NULL default '0',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_banner")) {
	$sql =  "
	CREATE TABLE mall_banner (
	  uid int(11) unsigned NOT NULL auto_increment,
	  rank int(8) unsigned NOT NULL default '0',
	  location int(4) unsigned NOT NULL default '0',
	  cate bigint(12) unsigned NOT NULL default '0',
	  name varchar(100) binary NOT NULL default '',
	  banner varchar(50) binary NOT NULL default '',
	  link varchar(80) binary NOT NULL default '',
	  status enum('1','2') NOT NULL default '1',
	  target enum('1','2') NOT NULL default '1',
	  edate  datetime default NULL,
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY (uid),
	  KEY rank (rank)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_brand")) {
	$sql =  "
	CREATE TABLE mall_brand (
	  uid int(11) unsigned NOT NULL auto_increment,	  
	  name varchar(100) NOT NULL default '',
	  tag varchar(250) NOT NULL default '',
	  img1 varchar(50) binary NOT NULL default '',
	  img2 varchar(50) binary NOT NULL default '',
	  display varchar(10) binary NOT NULL default '',
	  code_use enum('Y','N') NOT NULL default 'Y',
	  code mediumtext NOT NULL default '',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY name (name),
	  KEY tag (tag)	  
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_cart")) {
	$sql =  "
	CREATE TABLE mall_cart (
	  uid int(11) unsigned NOT NULL auto_increment,
	  tempid varchar(32) NOT NULL default '',
	  p_number int(11) unsigned NOT NULL default '0',
	  p_cate bigint(12) unsigned NOT NULL default '0',
	  p_qty int(8) unsigned NOT NULL default '0',
	  p_reserve int(11) unsigned NOT NULL default '0',
	  p_option mediumtext NOT NULL default '',
	  op_price varchar(100) binary NOT NULL default '',
	  p_direct enum('N','Y') NOT NULL default 'N',
	  date varchar(10) binary NOT NULL default '',
	  PRIMARY KEY  (uid,tempid)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_cate")) {
	$sql =  "
	CREATE TABLE mall_cate (
	  num int(10) unsigned NOT NULL auto_increment,
	  cate bigint(12) unsigned NOT NULL default '0',
	  cate_name varchar(60) binary NOT NULL default '',
	  cate_dep tinyint(1) unsigned NOT NULL default '0',
	  cate_parent bigint(12) unsigned NOT NULL default '0',
	  cate_sub enum('1','0') NOT NULL default '1',
	  img1 varchar(50) binary NOT NULL default '',
	  img2 varchar(50) binary NOT NULL default '',
	  img3 varchar(50) binary NOT NULL default '',
	  list_mode enum('1','2') NOT NULL default '1',
	  valid enum('0','1') NOT NULL default '1',
	  soldout enum('0','1') NOT NULL default '0',
	  number int(4) unsigned NOT NULL default '0',
	  code mediumtext NOT NULL default '',
	  martching varchar(100) binary NOT NULL default '',
	  access_level varchar(10) binary NOT NULL default '',
	  PRIMARY KEY  (num),
	  KEY cate (cate)
	)
	";
	$mysql->query($sql);

	$sql = "INSERT INTO mall_cate (cate,cate_name,cate_dep,cate_sub,number,valid,code) VALUES ('999000000000','공동구매','1,'0','5','0','N|*|')";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_design")) {
	$sql =  "
	CREATE TABLE mall_design (
	  uid int(11) unsigned NOT NULL auto_increment,
	  name varchar(255) binary NOT NULL default '',
	  code text NOT NULL default '',
	  mode char(1) binary NOT NULL default '',
	  PRIMARY KEY  (uid),
	  KEY mode (mode)
	)
	";
	$mysql->query($sql);

	$sql = "INSERT INTO `mall_design` VALUES ('','','|*|itsMall|*|itsMall|*||*|000-00-0000|*|제0-000호|*||*|000-0000-0000|*|000-0000-0000|*||*||*|럭셔리 쇼핑몰 솔류션 itsMall ^^|*|쇼핑몰 숄류션|*|itsMall,01','A'),(2,'','|*||*|1|*|안녕하십니까? <BR>저희 쇼핑몰에 회원가입해 주셔서 감사드립니다. <BR>가입하신 아이디로 로그인하신 후부터는 모든 서비스를 자유롭게 이용이 가능합니다. <BR>저희 쇼핑몰은 고객님을 위해 언제나 최선을 다하겠습니다. |*|1|*|저희 쇼핑몰에서 상품을 구매해 주셔서 감사드립니다. <BR>상품에 대해 문의 사항이 있으시면 고객센터 1:1 고객문의나 고객 게시판을 이용해 주시기 바랍니다. <BR>저희 쇼핑몰은 고객님을 위해 언제나 최선을 다하겠습니다. |*|1|*|주문하신 상품이 발송 되었습니다.<BR>배송기간은&nbsp; 1 ~ 3일&nbsp;(공휴일제외)&nbsp; 소요되면&nbsp;&nbsp;산간지방 및 섬 지역은&nbsp; 3 ~ 7일&nbsp;(공휴일제외)&nbsp; 소요됩니다.<BR>주문의 폭주, 천재지변, 배송 과정에 차질이 발생한 경우 등에는 배송이 지연될 수 있음을 양지해 주시기 바랍니다.<BR>배송에 대해 문의 사항이 있으시면 고객센터 1:1 고객문의나 고객 게시판을 이용해 주시기 바랍니다. <BR>저희 쇼핑몰은 고객님을 위해 언제나 최선을 다하겠습니다. <BR><BR>','F'),('','','default','G'),('','','logo.jpg||1|2||menu1_off.gif,menu1_on.gif,/index.php?channel=best,menu2_off.gif,menu2_on.gif,/index.php?channel=new,menu3_off.gif,menu3_on.gif,/index.php?channel=reco,menu4_off.gif,menu4_on.gif,/index.php?channel=event,menu5_off.gif,menu5_on.gif,/index.php?channel=after2,,,|search_bg.gif','C'),('','','|*|2|*|3|*|5|*|1|*|5|*|1|*||*|1|*||*|2|*||*|2|*||*|1|*|5|*|2|*||*|2|*||*|1|*|2|*||*|2|*||*|1|*||*|0|*|5|*|1|*||*|','M'),('','','<DIV style=\"PADDING-RIGHT: 4px; PADDING-LEFT: 4px; PADDING-BOTTOM: 4px; WIDTH: 100%; PADDING-TOP: 4px\">\r\n<DIV><IMG src=\"../../image/up_img/etc/ttt_info1.gif\" border=0></DIV>\r\n<DIV style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 8px; FONT-SIZE: 8pt; PADDING-BOTTOM: 0px; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma\"><B>배송지역</B> : 전국<BR><BR><B>배송비</B> :&nbsp; 30,000원 미만 상품 구매시에는 배송료 2,500원이 고객부담이며, 30,000원 이상 상품 구매시에는 배송료가 무료입니다.<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (구매합계액기준, 단! 제주도 및 기타 도서지역은 별도로 항공료가 추가됩니다.)<BR><BR><B>배송기간</B> : &nbsp;(공휴일제외) : 1 ~ 3일 소요 / &nbsp;산간지방 및 섬 지역(공휴일 제외) : 3 ~ 7일 소요&nbsp;<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 대금지급방법이 무통장 입금일 경우에는 입금 확인된 날부터 계산합니다.&nbsp;<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 주문의 폭주, 천재지변, 배송 과정에 차질이 발생한 경우 등에는 배송이 지연될 수 있음을 양지해 주시기 바랍니다.<BR><BR><B>배송조회</B> : 주문한 상품의 현재 배송상황을 확인할 수 있습니다. </DIV>\r\n<DIV style=\"MARGIN-TOP: 20px\"><IMG src=\"../../image/up_img/etc/ttt_info2.gif\" border=0></DIV>\r\n<DIV style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 8px; FONT-SIZE: 8pt; PADDING-BOTTOM: 0px; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma\">공정거래위원회가 인증한 표준약관에 의거, <B><FONT style=\"COLOR: #fa0026\">상품 인도 후 7일 이내</FONT></B>에 다음의 사유에 의한 교환, 반품 및 환불을 보장하고 있습니다. 단, 고객의 단순한 변심으로 교환, 반품 및 환불을 요구할 때 수반되는 배송비는 고객님께서 부담하셔야 합니다. <B>또한, 상품을 개봉했거나 설치한 후에는 상품의 재판매가 불가능하므로 고객님의 변심에 의한 교환, 반품이 불가능함을 양지해 주시기 바랍니다.</B> </DIV>\r\n<DIV style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 8px; FONT-SIZE: 8pt; PADDING-BOTTOM: 0px; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma\">교환 및 반품이 가능한 경우</DIV>\r\n<DIV style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 16px; FONT-SIZE: 8pt; PADDING-BOTTOM: 0px; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma\">- 배송된 상품이 주문내용과 상이하거나 본비비 에서 제공한 정보와 상이할 경우. <BR>- 배송된 상품 자체의 이상 및 결함이 있을 경우. <BR>- 배송된 상품이 파손, 손상되었거나 오염되었을 경우. </DIV>\r\n<DIV style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 8px; FONT-SIZE: 8pt; PADDING-BOTTOM: 0px; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma\">교환 및 반품이 불가능한 경우</DIV>\r\n<DIV style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 16px; FONT-SIZE: 8pt; PADDING-BOTTOM: 0px; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma\">- 고객님의 책임 있는 사유로 상품 등이 멸실 또는 훼손된 경우. <BR>- 고객님의 사용 또는 일부 소비에 의하여 상품의 가치가 현저히 감소한 경우. <BR>- 재판매가 곤란할 정도로 상품 등의 가치가 현저히 감소한 경우. </DIV>\r\n<DIV style=\"MARGIN-TOP: 20px\"><IMG src=\"../../image/up_img/etc/ttt_info3.gif\" border=0></DIV>\r\n<DIV style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 8px; FONT-SIZE: 8pt; PADDING-BOTTOM: 0px; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma\">소비자 보호규정에 의거하여 주문의 취소일 혹은 재화 등을 반환받은 날로부터 <B><FONT style=\"COLOR: #fa0026\">영업일 3일 이내에 결제 금액을 환불</FONT></B>해 드립니다. <B>신용카드로 결제하신 경우</B>에는 신용카드 승인을 취소하여 결제 대금이 청구되지 않게 합니다. 단, 신용카드 결제일자에 맞추어 대금이 청구될 경우, 익월 신용카드 대금 청구시 카드사에서 환급 처리됩니다. 무통장 입금의 경우에는 주문의 취소 혹은 제품 회수 후 입금계좌가 확인되면 <B>3일 이내에 환불해 드립니다.</B> (토요일, 일요일 및 공휴일 제외) </DIV></DIV>','D'),('','','<TABLE cellSpacing=0 cellPadding=0 width=\"96%\" align=center border=0>\r\n<TBODY>\r\n<TR class=word_break>\r\n<TD>● 주문취소 : 무통장 입금의 경우, 상품주문후 일주일 이내에 송금을 하지 않으면 자동주문취소가 되며 마이페이지에서 주문취소를 하실 수 있습니다.<BR>카드로 결제하신 경우, 승인취소가 가능하면 취소를 해드리지만 승인 취소가 불가능한 경우 해당 금액을 송금해 드립니다.<BR><BR>● 교환 : 상품의 상태에 따라 교환이 가능하니 고객센터(000-0000)로 문의해주시기 바랍니다.<BR><BR>● 환불 : 제품의 하자가 아닌 경우에 제품이 개봉되었거나, 훼손되었을 경우 환불/반품이 불가능합니다.<BR><BR>● 반품 : 상품 인도후 7일 이내에 반품을 보장합니다. 고객센터로 문의주시기 바랍니다. \r\n<TD></TD></TR></TBODY></TABLE>','E'),('','','1|*||*||*||*|1000|*|농협,1234-5678-90,홍길동||*|1|*|1000|*|1|*|5000|*|1|*|30000|*|2500|*||*||*|test|*|1|*||*||*||*|6|*|0|*||*|1','B'),('','한진택배','http://www.hanjinexpress.hanjin.net/customer/plsql/hddcw07.result?wbl_num=','Z'),('','로젠택배','http://d2d.ilogen.com/d2d/delivery/invoice_tracesearch_link_view.jsp?slipno=','Z'),('','','1|1|2|7|0','T'),('','2','일반회원|0|0|0|Y','L'),('','9','부관리자|3|0|0|','L'),('','2','1|||||','P'),('','1','||1|||','P'),('','3','1||1|1|1|','P'),(18,'10','관리자|4|0|0|','L'),('', '우체국택배', 'http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=', 'Z'),('', '현대택배', 'http://www.hlc.co.kr/hydex/jsp/tracking/trackingViewCus.jsp?InvNo=', 'Z'),('', 'KG옐로우캡택배', 'http://www.yellowcap.co.kr/custom/inquiry_result.asp?invoice_no=', 'Z'),('', 'KGB택배', 'http://www.kgbls.co.kr/sub3/sub3_4_1.asp?f_slipno=', 'Z'),('', '동부익스프레스', 'http://www.dongbuexpress.co.kr/Html/Delivery/DeliveryCheck.jsp?search_item_no=', 'Z'),('','','0_2|0_3|0_9|1_4|3_1|today','H'),('','','0|*||*|0','Y')";
	
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_document")) {
	$sql =  "
	CREATE TABLE mall_document (
	  uid int(11) unsigned NOT NULL auto_increment,
	  mode enum('A','B','C','D') default NULL,
	  img varchar(80) binary NOT NULL default '',
	  code text NOT NULL default '',
	  PRIMARY KEY  (uid),
	  KEY mode (mode)
	)
	";
	$mysql->query($sql);

	$sql = "INSERT INTO `mall_document` VALUES (1,'A','docu_title_3.jpg','<DIV style=\"BORDER-RIGHT: #eee 1px solid; PADDING-RIGHT: 10px; BORDER-TOP: #eee 1px solid; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; BORDER-LEFT: #eee 1px solid; WIDTH: 698px; LINE-HEIGHT: 200%; PADDING-TOP: 10px; BORDER-BOTTOM: #eee 1px solid; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; BACKGROUND-COLOR: #fafafa; TEXT-ALIGN: left; TEXT-DECORATION: none\">안녕하세요. \'{shopName}\'입니다.<BR><BR><BR>21세기 디지털 시대에는 \'지식과 정보\'를 사용할줄 아는 자만이 시대를 선도해 나갈수 있습니다.<BR>\'{shopName}\'은 단순히 상품만 판매하는 쇼핑몰을 탈피하여 생활에 유익한 다양한 정보나,살아있는 새소식 알찬정보를 고객 여러분께 전해 드릴것이며, 따스한 교감을 나눌수있는 행복한 인터넷 세상을 열어 나갈것입니다.<BR><BR>\'{shopName}\'은 최저가격,최상의 품질,신뢰할수있는 사후관리에 사훈을 걸고,고객만족을 위해 최선을 다하고 있습니다.<BR><BR>\'{shopName}\' 임직원 일동은 항상 최선을 다하는 자세로 고객분들을 맞이할것입니다.<BR><BR></DIV>'),(2,'B','docu_title_3_1.jpg','<DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>01</FONT>조 (목적)</DIV><DIV style=\"MARGIN-TOP: 4px\">이 약관은&nbsp; {shopName}(이하 \"몰\"이라 칭함)가&nbsp; 제공하는 인터넷 관련 서비스(이하 \"서비스\"라 한다.)를 이용함에 있어 이용자와 사이버 몰의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>02</FONT>조&nbsp; (이용약관의 효력 및 변경)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 이 약관은 “몰”에서 온라인으로 공시함으로써 효력을 발생하며 합리적인 사유가 발생할 경우 관련 법령에 위배되지 않는 범위 안에서 개정될 수 있습니다. 개정된 약관은 온라인에서 공지함으로써 효력을 발휘하며 이용자의 권리 또는 의무 등 중요한 규정의 개정은 사전에 공지합니다. <BR>(2) “몰”은 합리적인 사유가 발생될 경우에는 이 약관을 변경할 수 있으며 약관을 변경할 경우에는 지체 없이 이를 사전에 공시합니다. <BR>(3) 이 약관에 동의하는 것은 정기적으로 웹을 방문하여 약관의 변경사항을 확인하는 것에 동의함을 의미합니다. 변경된 약관에 대한 정보를 알지 못해 발생하는 이용자의 피해는 “몰”에서 책임지지 않습니다. <BR>(4) 회원은 변경된 약관에 동의하지 않을 경우 회원탈퇴(해지)를 요청할 수 있으며 변경된 약관의 효력 발생일로부터 7일 이후에도 거부의사를 표시하지 아니하고 서비스를 계속 사용할 경우 약관의 변경사항에 동의한 것으로 간주됩니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>03</FONT>조 (약관 외 준칙)</DIV><DIV style=\"MARGIN-TOP: 4px\">이 약관에 명시되지 않은 사항에 대해서는 전기통신기본법, 전기통신사업법 등 관계법령 및 “몰”이 정한 서비스의 세부이용지침 등의 규정에 의합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>04</FONT>조 (용어의 정의)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 이 약관에서 사용하는 용어의 정의는 다음과 같습니다. <BR>1. 회원 : “몰”에 접속하여 이 약관에 동의하고 ID(고유번호)와 Password(비밀번호)를 발급 받은 자 <BR>2. 이용자 : “몰”의 회원 및 회원이 아니면서 서비스를 이용하는 자 <BR>3. ID(고유번호) : 회원 식별과 회원의 서비스 이용을 위하여 회원이 선정하고 회사가 승인하는 영문자와 숫자의 조합으로, 하나의 주민등록번호에 하나의 ID만 발급, 이용 가능 <BR>4. Password(비밀번호) : 회원의 정보 보호를 위해 회원 자신이 설정한 문자와 숫자의 조합 <BR>5. 관리자 : 서비스의 전반적인 관리와 원활한 운영을 위하여 회사가 선정한 자 <BR>6. 서비스 중지 : 정상 이용 중 회사가 정한 일정한 요건에 따라 일정기간 동안 서비스의 제공을 중지하는 것 <BR>7.&nbsp;적립금 : “몰”에서 별도로 명칭을 부여한 것으로서 회원이 “몰”의 상품구매 또는 이벤트 참여 등에 의해 받게 되는 가상의 화폐 입니다.<BR>(2) 이 약관에서 사용하는 용어의 정의는 제1항에서 정하는 것을 제외하고는 관계법령 및 서비스별 안내에서 정하는 바에 의합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>05</FONT>조 (이용 계약의 성립)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) \"위의 이용약관에 동의하십니까?\" 라는 이용 신청시의 물음에 \"동의함\" 단추를 누르면 약관에 동의하는 것으로 간주됩니다. <BR>(2) 이용 계약은 이용자의 본 이용약관 내용에 대한 동의와 이용신청에 대하여 \"몰\" 이용승낙으로 성립합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>06</FONT>조 (서비스 이용 신청)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 회원으로 가입하여 본 서비스를 이용하고자 하는 이용자는 “몰”에서 요청하는 제반정보(이름, 주민등록번호, 연락처 등)을 제공하여야 합니다. <BR>(2)회원가입은 반드시 실명으로만 가입할 수 있으며 “몰”은 실명확인조치를 할 수 있습니다. <BR>(3) 타인의 명의(이름 및 주민등록번호)를 도용하여 이용신청을 한 회원의 모든 ID는 삭제되며 관계법령에 따라 처벌을 받을 수 있습니다. <BR>(5) “몰”은 본 서비스를 이용하는 회원에 대하여 등급별로 구분하여 이용시간, 이용횟수, 서비스 메뉴 등을 세분하여 이용에 차등을 둘 수 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>07</FONT>조 (개인정보의 보호 및 사용)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰” 이용 신청 시 회원이 제공하는 정보, 커뮤니티 활동, 각종 이벤트 참가를 위하여 회원이 제공하는 정보 등을 통하여 회원에 관한 정보를 수집하며, 회원의 개인정보는 본 이용계약의 이행과 본 이용계약상의 서비스 제공을 위한 목적으로 이용합니다. <BR>(2) “몰”의 서비스 제공과 관련하여 취득한 회원의 신상정보를 본인의 승낙 없이 제3자에게 누설 또는 배포할 수 없으며 상업적 목적으로 사용할 수 없습니다. <BR>다만, 다음의 각 호의 경우에는 그러하지 아니합니다.<BR>1. 관계 법령에 의하여 수사상의 목적으로 관계기관으로부터 요구가 있는 경우<BR>2. 정보통신윤리위원회의 요청이 있는 경우<BR>3. 기타 관계법령에서 정한 절차에 따른 요청이 있는 경우<BR>4. 정보통신서비스의 제공에 따른 요금정산을 위하여 필요한 경우<BR>5. 통계작성, 학술연구 또는 시장조사를 위하여 필요한 경우로서 특정 개인을 알아볼 수 없는 형태로 가공하여 제공하는 경우 <BR>(3) 회원이 “몰” 및 “몰”과 제휴한 서비스들을 편리하게 이용할 수 있도록 하기 위해 회원정보는 “몰”과 제휴한 업체에 제공될 수 있습니다. 단, “몰”은 회원정보 제공 이전에 제휴업체, 제공 목적, 제공할 회원 정보의 내용 등을 사전에 공지하고 회원의 동의를 얻어야 합니다. <BR>(4) “몰”은 위 3항의 범위 내에서 “몰”의 업무와 관련하여 회원 전체 또는 일부의 개인정보에 관한 집합적인 통계 자료를 작성하여 이를 사용할 수 있고 서비스를 통하여 회원의 컴퓨터에 쿠키를 전송할 수 있습니다. 이 경우 회원은 쿠키의 수신을 거부하거나 수신에 대하여 경고하도록 사용하는 컴퓨터의 브라우저 설정을 변경할 수 있습니다. <BR>(5) “몰”은 다음의 각 호와 같은 경우에는 이용자의 동의 하에 개인정보를 제3자에게 제공할 수 있습니다. 이러한 경우에도 개인정보의 제3자 제공은 이용자의 동의 하에서만 이루어지며 개인정보가 제공되는 것을 원하지 않는 경우에는 특정 서비스를 이용하지 않거나 특정한 형태의 판촉이나 이벤트에 참여하지 않으면 됩니다.<BR>1. “몰” 내에서 물품구매, 유료 컨텐츠 이용 등 서비스 제공을 위해 이용자의 이름, 주소, 전화번호 등이 해당 쇼핑몰 업체, 유료 컨텐츠 제공자, 배송업자에게 제공될 수 있습니다.<BR>2. “몰” 내에서 벌어지는 각종 이벤트 행사에 참여한 회원의 개인정보가 해당 이벤트의 주최자에게 제공될 수 있습니다. <BR>(6) 회원이 이용신청서에 회원정보를 기재하고, “몰”의 본 약관에 따라 이용신청을 하는 것은 “몰”이본 약관에 따라 이용신청서에 기재된 회원정보를 수집, 이용 및 제공하는 것에 동의하는 것으로 간주됩니다. <BR>(7) “몰”에서의 개인정보보호와 관련된 보다 자세한 사항은 “몰” 내에 게시된 개인정보보호정책을 참조하시기 바랍니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>08</FONT>조 (목적)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰”은 제6조의 규정에 의한 이용신청 고객에 대하여 업무 수행상 또는 기술상 지장이 없는 경우에 원칙적으로 접수순서에 따라 서비스 이용을 승낙합니다. <BR>(2) “몰”은 아래사항에 해당하는 경우에 대해서 승낙하지 아니합니다.<BR>1. 실명이 아니거나 타인의 명의를 이용하여 신청한 경우<BR>2. 이용계약 신청서의 내용을 허위로 기재한 경우<BR>3. 사회의 안녕과 질서, 미풍양속을 저해할 목적으로 신청한 경우<BR>4. 부정한 용도로 본 서비스를 이용하고자 하는 경우 <BR>5. 영리를 추구할 목적으로 본 서비스를 이용하고자 하는 경우 <BR>6. 기타 규정한 제반사항을 위반하며 신청하는 경우 <BR>7. 본 서비스와 경쟁관계에 있는 이용자가 신청하는 경우 <BR>(3) “몰”의 서비스 이용신청이 다음 각 호에 해당하는 경우에는 그 신청에 대하여 승낙 제한사유가 해소될 때까지 승낙을 유보할 수 있습니다. <BR>1. “몰”이 설비의 여유가 없는 경우 <BR>2. “몰”에 기술상 지장이 있는 경우 <BR>3. 기타 “몰”의 귀책사유로 이용승낙이 곤란한 경우 <BR>(4) “몰”은 이용신청고객이 관계법령에서 규정하는 미성년자일 경우에 서비스별 안내에서 정하는 바에 따라 승낙을 보류할 수 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>09</FONT>조 (계약 사항의 변경)</DIV><DIV style=\"MARGIN-TOP: 4px\">회원은 회원정보관리를 통해 언제든지 개인정보를 열람하고 수정할 수 있습니다. 회원은 이용신청 시 기재한 사항이 변경되었을 경우에는 온라인으로 수정을 해야 하고 미변경으로 인하여 발생되는 문제의 책임은 회원에게 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>10</FONT>조 (“몰”의 의무)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰”은 이용자가 희망한 서비스 제공 개시일에 특별한 사정이 없는 한 서비스를 이용할 수 있도록 하여야 합니다.<BR>(2) “몰”은 계속적이고 안정적인 서비스의 제공을 위하여 설비에 장애가 생기거나 멸실된 때에는 부득이한 사유가 없는 한 지체 없이 이를 수리 또는 복구합니다. <BR>(3) “몰”은 개인정보 보호를 위해 보안시스템을 구축하며 개인정보 보호정책을 공시하고 준수 합니다. <BR>(4) “몰”은 이용고객으로부터 제기되는 의견이나 불만이 정당하다고 객관적으로 인정될 경우에는 적절한 절차를 거쳐 즉시 처리하여야 합니다. 다만, 즉시 처리가 곤란한 경우는 이용자에게 그 사유와 처리일정을 통보하여야 합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>11</FONT>조 (이용자의 의무)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 이용자는 회원가입 신청 또는 회원정보 변경 시 실명으로 모든 사항을 사실에 근거하여 작성하여야 하며 허위 또는 타인의 정보를 등록할 경우 일체의 권리를 주장할 수 없습니다. <BR>(2) 회원은 본 약관에서 규정하는 사항과 기타 “몰”이 정한 제반 규정, 공지사항 등 “몰”이 공지하는 사항 및 관계법령을 준수하여야 하며 기타 “몰”의 업무에 방해가 되는 행위, “몰”의 명예를 손상시키는 행위를 해서는 안됩니다. <BR>(3) 회원은 주소, 연락처, 전자우편 주소 등 이용계약 사항이 변경된 경우에 해당 절차를 거쳐 이를 “몰”에 즉시 알려야 합니다.<BR>(4) “몰”의 관계법령 및 \'개인정보 보호 정책\'에 의거하여 그 책임을 지는 경우를 제외하고 회원에게 부여된 ID의 비밀번호 관리소홀, 부정사용에 의하여 발생하는 모든 결과에 대한 책임은 회원에게 있습니다. <BR>(5) 회원은 “몰”의 사전 승낙 없이 서비스를 이용하여 영업활동을 할 수 없으며 그 영업활동의 결과에 대해 “몰”은 책임을 지지 않습니다. 또한 회원은 이와 같은 영업활동으로 “몰”이 손해를 입은 경우, 회원은 “몰”에 대해 손해배상의무를 지며 “몰”은 해당 회원에 대해 서비스 이용제한 및 적법한 절차를 거쳐 손해배상 등을 청구할 수 있습니다. <BR>(6) 회원은 “몰”의 명시적 동의가 없는 한 서비스의 이용권한, 기타 이용계약상의 지위를 타인에게 양도, 증여할 수 없으며 이를 담보로 제공할 수 없습니다. <BR>(7) 회원은 “몰” 및 제3자의 지적 재산권을 침해해서는 안됩니다. <BR>(8) 회원은 다음 각 호에 해당하는 행위를 하여서는 안되며 해당 행위를 하는 경우에 “몰”은 회원의 서비스 이용제한 및 적법 조치를 포함한 제재를 가할 수 있습니다.<BR>1. 회원가입 신청 또는 회원정보 변경 시 허위내용을 등록하는 행위<BR>2. 다른 이용자의 ID, 비밀번호, 주민등록번호를 도용하는 행위<BR>3. 이용자 ID를 타인과 거래하는 행위<BR>4. “몰” 운영진, “몰”의 직원 또는 관계자를 사칭하는 행위<BR>5. “몰”로부터 특별한 권리를 부여 받지 않고 “몰”의 클라이언트 프로그램을 변경하거나 “몰”의 서버를 해킹하거나 “몰” 또는 “몰”에 게시된 정보의 일부분 또는 전체를 임의로 변경하는 행위<BR>6. 서비스에 위해를 가하거나 고의로 방해하는 행위<BR>7. “몰”을 통해 얻은 정보를 “몰”의 사전 승낙 없이 “몰”의 서비스 이용 외의 목적으로 복제하거나 이를 출판 및 방송 등에 사용하거나 제 3자에게 제공하는 행위<BR>8. 공공질서 및 미풍양속에 위반되는 저속, 음란한 내용의 정보, 문장, 도형, 음향, 동영상을 전송, 게시, 전자우편 또는 기타의 방법으로 타인에게 유포하는 행위<BR>9. 모욕적이거나 개인신상에 대한 내용이어서 타인의 명예나 프라이버시를 침해할 수 있는 내용을 전송, 게시, 전자우편 또는 기타의 방법으로 타인에게 유포하는 행위<BR>10. 다른 이용자를 희롱 또는 위협하거나 특정 이용자에게 지속적으로 고통 또는 불편을 주는 행위<BR>11. “몰”의 승인을 받지 않고 다른 사용자의 개인정보를 수집 또는 저장하는 행위<BR>12. 범죄와 결부된다고 객관적으로 판단되는 행위<BR>13. 본 약관을 포함하여 기타 “몰”이 정한 제반 규정 또는 이용 조건을 위반하는 행위<BR>14. 기타 관계법령에 위배되는 행위 </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>12</FONT>조 (회원 ID(고유번호)와 Password(비밀번호) 관리에 대한 의무와 책임)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰” 내에서 일부 서비스 신청 시 이용요금을 부과할 수 있으므로 회원은 ID(고유번호) 및 Password(비밀번호) 관리를 철저히 해야 합니다.<BR>(2) 회원 ID(고유번호) 및 Password(비밀번호)의 관리 소홀, 부정 사용에 의하여 발생하는 모든 결과에 대한 책임은 회원 본인에게 있으며 “몰”의 시스템 고장 등 “몰”에 책임이 있는 사유로 발생하는 문제에 “몰”이 책임을 집니다. <BR>(3) 회원은 본인의 ID(고유번호) 및 Password(비밀번호)를 제3자에게 이용하게 해서는 안되며 회원 본인의 ID(고유번호) 및 Password(비밀번호)를 도난 당하거나 제3자가 사용하고 있음을 인지하는 경우에는 바로 “몰”에 홍보하고 “몰”의 안내가 있는 경우 그에 따라야 합니다. <BR>(4) 회원의 ID(고유번호)는 “몰”의 동의 없이 변경할 수 없습니다. <BR>(3) “몰”은 개인정보 보호를 위해 보안시스템을 구축하며 개인정보 보호정책을 공시하고 준수 합니다. <BR>(4) “몰”은 이용고객으로부터 제기되는 의견이나 불만이 정당하다고 객관적으로 인정될 경우에는 적절한 절차를 거쳐 즉시 처리하여야 합니다. 다만, 즉시 처리가 곤란한 경우는 이용자에게 그 사유와 처리일정을 통보하여야 합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>13</FONT>조 (회원에 대한 통지)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 회원에 대한 통지를 하는 경우 회원이 등록한 e-mail 주소로 할 수 있습니다. <BR>(2) “몰”의 불특정 다수 회원에 대한 통지의 경우 서비스 게시판 등에 게시함으로써 개별 통지에 갈음할 수 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>14</FONT>조 (서비스 이용 시간)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰”은 회원의 이용신청을 승낙한 때부터 즉시 서비스를 개시합니다. 단 “몰”의 업무상 또는 기술상의 장애로 인하여 서비스를 개시하지 못하는 경우 서비스에 공지하거나 회원에게 즉시 이를 통지합니다. <BR>(2) 서비스의 이용은 연중무휴 1일 24시간을 원칙으로 합니다. 다만 “몰”의 업무상 또는 기술상의 이유로 서비스의 전부 또는 일부가 일시 중지 되거나 운영상의 목적으로 “몰”이 정한 기간에는 서비스의 전부 또는 일부가 일시 중지 될 수 있습니다. 이러한 경우 “몰”은 사전 또는 사후 이를 공지합니다. <BR>(3) “몰”은 서비스별로 이용 가능한 시간을 별도로 정할 수 있으며 이 경우 그 내용을 사전에 공지합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>15</FONT>조 (서비스 내용 및 유의사항)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰”은 회원에게 아래와 같은 서비스를 제공합니다. <BR>1. “몰”과 관련 메일 서비스 <BR>2. “몰”회원을 위한 고객센터 서비스 <BR>(2) “몰”에서 제공하는 서비스 중 외부필자에 의한 정보는 “몰”의 의견과는 다를 수 있습니다. <BR>(3) “몰”에서 제공하는 각종 유료 서비스를 이용하거나 디지털 컨텐츠를 사용하는 경우 해당 서비스나 디지털 컨텐츠가 회원의 특정한 목적이나 필요를 충족시킨다는 보장이 없습니다. 따라서 이용에 있어서 품질, 성능, 정확도 또는 노력의 면에서 회원의 불만족에 대해서는 책임을 지지 않습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>16</FONT>조 (서비스 변경 및 중지)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰”은 변경될 서비스의 내용 및 제공일자를 제13조에서 정한 방법으로 회원에게 통지하고 서비스를 변경하여 제공할 수 있습니다. <BR>(2) “몰”은 다음 각 호에 해당하는 경우 서비스의 전부 또는 일부를 제한하거나 중지할 수 있으며 제13조에서 정한 방법으로 회원에게 통지하고 서비스를 변경하여 제공할 수 있습니다. 다만, “몰”이 통제할 수 없는 사유로 인한 서비스의 중단(운영자의 고의, 과실이 없는 디스크장애, 시스템 다운 등)으로 인하여 사전 통지가 불가능한 경우에는 그러하지 아니합니다.<BR>1. 서비스용 설비의 보수 등 공사로 인한 부득이한 경우<BR>2. 회원이 “몰”의 영업활동을 방해하는 경우<BR>3. 정전, 제반 설비의 장애 또는 이용량의 폭주 등으로 정상적인 서비스 이용에 지장이 있는 경우<BR>4. 서비스 제공업자와의 계약 종료 등과 같은 “몰”의 제반 사정으로 서비스를 유지할 수 없는 경우<BR>5. 기타 천재지변, 국가비상사태 등 불가항력적 사유가 있는 경우 <BR>(3) “몰”은 서비스의 변경, 중지로 발생하는 문제에 대해서는 어떠한 책임도 지지 않습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>17</FONT>조 (게시물의 관리)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰”은 회원이 게시하거나 전달하는 서비스 내의 모든 내용물(회원간 전달 포함)이 다음 각 호의 경우에 해당한다고 판단되는 경우 사전 통지 없이 삭제할 수 있으며 이에 대해 “몰”은 어떠한 책임도 지지 않습니다.<BR>1. 다른 회원 또는 제3자를 비방하거나 중상모략으로 명예를 손상시키는 내용인 경우<BR>2. 공공질서 및 미풍양속에 위반되는 내용의 정보, 문장, 도형 등의 유포에 해당하는 경우<BR>3. 범죄적 행위에 결부된다고 인정되는 내용인 경우<BR>4. “몰”의 저작권, 제3자의 저작권 등 기타 권리를 침해하는 내용인 경우<BR>5. 제2항 소정의 세부이용지침을 통하여 “몰”에서 규정한 게시기간을 초과한 경우<BR>6. “몰”에서 제공하는 서비스와 관련 없는 내용인 경우<BR>7. 불필요하거나 승인되지 않은 광고, 판촉물을 게재하는 경우<BR>8. 타인의 ID(고유번호), 성명 등을 무단으로 도용하여 작성한 내용이거나 타인이 입력한 정보를 무단으로 위조, 변조한 내용인 경우<BR>9. 동일한 내용을 중복하여 다수 게시하는 등 게시의 목적에 어긋나는 경우<BR>10. 기타 관계 법령 및 “몰”의 지침 등에 위반된다고 판단되는 경우 <BR>(2) “몰”은 게시물에 관련된 세부 이용지침을 별도로 정하여 시행할 수 있으며 회원은 그 지침에 따라 각종 게시물(회원간 전달 포함)을 등록하거나 삭제하여야 합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>18</FONT>조 (게시물 세부 이용지침)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰”에서 제공하는 게시판은 회원의 참여방법과 게시물에 대한 평가방법에 의해 아래와 같은 게시판 유형이 있습니다.<BR>1. 코멘트형 게시판 : 회원 혹은 “몰”이 글을 게시하고 이에 대한 다른 회원이 의견을 게시함으로 회원간의 커뮤니케이션을 위한 게시판으로 스타일진의 브랜드스토리와 같은 유형입니다.<BR>2. 상품평 게시판 : “몰”이 제공하는 서비스 및 상품에 대한 평가를 게시하는 게시판으로 “몰”에서 쇼핑스쿱의 쇼핑백토크, 상품후기와 같은 유형입니다.<BR>4. 기타 게시판 : “몰” 이용자, 관리자가 “몰”에 의견을 게시하는 일반적인 게시판입니다. <BR>(2) 게시판 유형에 따라 게시물 유지 및 삭제 기준은 각 게시판 운영페이지에 공지되어 있으며 이에 따라 게시물이 유지 및 삭제 됩니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>19</FONT>조 (게시물의 저작권)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 회원이 서비스 내에 게시판 게시물(회원간 전달 포함)의 저작권은 회원이 소유하며 회사는 서비스 내에 이를 게시할 수 있는 권리를 갖습니다. <BR>(2) “몰”은 게시한 회원의 동의 없이 게시물을 다른 목적으로 사용할 수 없습니다. 단, “몰”의 합병, 영업양도, “몰”을 운영하는 사이트간의 통합 등의 사유로 원래의 게시물의 내용을 변경하지 않고 게시물의 게시 위치를 변경할 수는 있습니다. <BR>(3) “몰”의 회원이 서비스 내에 게시한 게시물이 타인의 저작권, 프로그램 저작권 등을 침해하더라도 이에 대한 민, 형사상의 책임을 부담하지 않습니다. 만일 회원이 타인의 저작권, 프로그램 저작권 등을 침해하였음을 이유로 “몰”이 타인으로부터 손해배상청구 등 이의 제기를 받은 경우 회원은 “몰”의 면책을 위하여 노력하여야 하며 “몰”이 면책되지 못한 경우 회원은 그로 인해 “몰”에 발생한 모든 손해를 부담하여야 합니다. <BR>(4) “몰”은 회원이 해지하거나 적법한 사유로 해지된 경우 회원이 게시하였던 게시물을 삭제할 수 있습니다. <BR>(5) “몰”은 작성한 저작물에 대한 저작권은 회사에 귀속합니다. <BR>(6) 회원은 서비스를 이용하여 얻은 정보를 가공, 판매하는 행위 등 서비스에 게재된 영리목적으로 이용하거나 제3자에게 이용하게 할 수 없으며 게시물에 대한 저작권 침해는 관계 법령의 적용을 받습니다.</DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>20</FONT>조 (광고게재 및 광고주와의 거래)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰” 회원에게 서비스를 제공할 수 있는 서비스 투자기반의 일부는 광고게재를 통한 수익으로부터 나옵니다. 서비스를 이용하고자 하는 자는 서비스 이용 시 노출되는 광고게재에 대해 동의하는 것으로 간주됩니다. <BR>(2) “몰”은 본 서비스상에 게재되어 있거나 본 서비스를 통한 광고주의 판촉활동에 회원이 참여하거나 교신 또는 거래의 결과로서 발생하는 모든 손실 또는 손해에 대해 책임을 지지 않습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>21</FONT>조 (유료 서비스의 이용)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 회원은 유료 서비스 이용 시 “몰”이 지정한 결제수단으로 결제하거나 “몰”내의 활동으로 적립된 적립금과 각 서비스에서 지정한 결제수단으로 \"서비스\"를 모두 이용할 수 있습니다. 단, 서비스에 따라 적립금의 이용이 제한될 수 있습니다. <BR>(2) 비회원은 각 \"서비스\"에서 지정한 결제수단으로 이용할 수 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>22</FONT>조 (유료 서비스의 이용)</DIV><DIV style=\"MARGIN-TOP: 4px\">회사에서 정한 별도의 유료 서비스에 대해서는 이용 전에 해당 유료 정보의 내용이 표시됩니다.</DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>23</FONT>조 (적립금의 적립)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1)&nbsp;적립금 적립<BR>1. 적립금은 회원이 “몰”에서 상품구매 또는 “몰”의 이벤트 등에 참여하여 적립금을 적립할 수 있습니다.<BR>2. 적립금은 “몰”의 운영정책에 따라 사용안 할 수도 있으며 적립방법도 예고 없이 변경될 수 있습니다. <BR>(2) 적립금 정책<BR>1. 적립금을 이용하는 방법, 미성년자의 적립금 사용 등은 “몰”에서 별도로 정하는 적립금 정책에 따릅니다.<BR>2. 적립금을 사용하고자 하는 회원은 적립금 정책에 따라야만 합니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>24</FONT>조 (적립금의 이용, 차감&nbsp;및 환불)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 적립금은 유료 서비스를 이용하거나 상품을 구매하는 지불 수단으로 이용될 수 있습니다.<BR>(2) 적립금은 “몰”이 제공하는 유료서비스 이용 또는 상품구매 시점에서 즉시 차감이 됩니다. <BR>(3) 무료로 적립된 적립금은 환불되지 않는 것을 원칙으로 합니다. <BR>(4) ID(고유번호) 및 Password(비밀번호) 등의 개인정보도용 및 결제사기로 인한 환불 요청에 따른 결제자의 개인정보 확인은 관계 법령에 근거한 수사기관의 정당한 요청에 의해서만 가능합니다.</DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>25</FONT>조 (미성년자의 적립금 사용)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 법률에서 정한 미성년자에 해당되는 회원은 유료서비스 이용 전 최초 “몰”의 회원 가입 시 회사가 정한 방법으로 법정대리인의 동의를 받아야 합니다. 일단, 최초 “몰”의 회원가입 시 동의를 받은 회원은 각각의 적립금의 사용에도 동의를 받은 것으로 간주합니다. <BR>(2) 제1항의 동의절차를 받지 않은 미성년자는 최초 적립금의 적립 및 사용 시 “몰”이 정한 방법으로 법정대리인의 동의를 받아야 합니다. <BR>(3) 제1항, 제1항의 동의 절차를 받지 않은 미성년자는 적립금의 적립 및 사용에 제한을 받을 수 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>26</FONT>조 (계약해지 및 이용제한)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 회원이 이용계약을 해지하고자 하는 경우에는 회원 본인이 사이트를 통해 “몰”에 해지 신청을 하여야 합니다. <BR>(2) “몰” 회원이 다음 각 호에 해당하는 행위를 하였을 경우 사전통지 없이 이용계약을 해지하거나 또는 기간을 정하여 서비스 이용을 중지할 수 있습니다.<BR>1. 타인의 서비스 ID 및 비밀번호를 도용한 경우<BR>2. 서비스 운영을 고의로 방해한 경우<BR>3. 가입한 이름이 실명이 아닌 경우<BR>4. 같은 사용자가 다른 ID로 이중등록을 한 경우 <BR>5. 공공질서 및 미풍양속에 저해되는 내용을 고의로 유포시킨 경우<BR>6. 회원이 국익 또는 사회적 공익을 저해할 목적으로 서비스 이용을 계획 또는 실행하는 경우<BR>7. 타인의 명예를 손상시키거나 불이익을 주는 행위를 한 경우<BR>8. 서비스의 안정적 운영을 방해할 목적으로 다량의 정보를 전송하거나 광고성 정보를 전송하는 경우<BR>9. 정보통신설비의 오작동이나 정보 등의 파괴를 유발시키는 컴퓨터 바이러스 프로그램 등을 유포하는 경우<BR>10. “몰”, 다른 회원 또는 제3자의 지적재산권을 침해하는 경우 <BR>11. 위원회의 유권해석을 받은 경우<BR>12. 타인의 개인정보, 이용자 ID(고유번호) 및 Password(비밀번호)를 부정하게 사용하는 경우<BR>13. “몰”의 서비스 정보를 이용하여 얻은 정보를 “몰”의 사전 승낙 없이 복제 또는 유통시키거나 상업적으로 이용하는 경우<BR>14. 기업 회원이 사업자등록 번호 및 회사명을 허위로 입력한 경우<BR>15. 본 약관을 포함하여 기타 “몰”이 정한 이용조건에 위반한 경우 </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>27</FONT>조 (양도 금지)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 회원은 서비스의 이용권한, 기타 이용 계약상 지위를 타인에게 양도, 증여할 수 없으며 게시물에 대한 저작권을 포함한 모든 권리 및 책임은 이를 게시한 회원에게 있습니다. <BR>(2) “몰”이 제3자에게 합병 또는 분할합병 되거나 서비스를 제3자에게 양도함으로써 서비스의 제공 주체가 변경되는 경우 “몰”은 사전에 제13조의 통지방법으로 회원에게 통지합니다. 이 경우 합병, 분할합병, 서비스 양도에 반대하는 회원은 서비스 이용계약을 해지할 수 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>28</FONT>조 (손해배상)</DIV><DIV style=\"MARGIN-TOP: 4px\">“몰”은 서비스에서 무료로 제공하는 서비스의 이용과 관련하여 개인정보보호정책에서 정하는 내용에 해당하지 않는 사항에 대해서는 어떠한 손해도 책임을 지지 않습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>29</FONT>조 (면책조항)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) “몰”은 천재지변, 전쟁 및 기타 이에 준하는 불가항력으로 인하여 서비스를 제공할 수 없는 경우에는 서비스 제공에 대한 책임이 면제됩니다. <BR>(2) “몰”은 서비스용 설비의 보수, 교체, 정기점검, 공사 등 부득이한 사유로 발생한 손해에 대한 책임이 면제됩니다. <BR>(3) “몰”은 기간통신 사업자가 전기통신 서비스를 중지하거나 정상적으로 제공하지 아니하여 손해가 발생한 경우 책임이 면제됩니다. <BR>(4) “몰”은 회원의 귀책사유로 인한 서비스 이용의 장애 또는 손해에 대하여 책임을 지지 않습니다. <BR>(5) “몰”은 이용자의 컴퓨터 오류에 의해 손해가 발생한 경우 또는 회원이 신상정보 및 전자우편 주소를 부실하게 기재하여 손해가 발생한 경우 책임을 지지 않습니다. <BR>(6) “몰”은 회원이 서비스를 이용하여 기대하는 수익을 얻지 못하거나 상실한 것에 대하여 책임을 지지 않습니다. <BR>(7) “몰”은 회원이 서비스를 이용하면서 얻은 자료로 인한 손해에 대하여 책임을 지지 않습니다. 또한 “몰” 회원이 서비스를 이용하며 타 회원으로 인해 입게 되는 정신적 피해에 대하여 보상할 책임을 지지 않습니다. <BR>(8) “몰”은 회원이 서비스에 게재한 각종 정보, 자료, 사실의 신뢰도 정확성 등 내용에 대하여 책임을 지지 않습니다. <BR>(9) “몰”은 이용자 상호간 및 이용자와 제3자 상호간에 서비스를 매개로 발생한 분쟁에 대해 개입할 의무가 없으며 이로 인한 손해를 배상할 책임도 없습니다. <BR>(10) “몰”에서 회원에게 무료로 제공하는 서비스의 이용과 관련해서는 어떠한 손해도 책임을 지지 않습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">제<FONT face=Tahoma size=2>30</FONT>조 (재판권 및 준거법)</DIV><DIV style=\"MARGIN-TOP: 4px\">(1) 이 약관에 명시되지 않은 사항은 전기통신사업법 등 관계법령과 상관습에 따릅니다. <BR>(2) “몰”의 유료 서비스 이용 회원의 경우 “몰”이 별도로 정한 약관 및 정책에 따릅니다. <BR>(3) 서비스 이용으로 발생한 분쟁에 대해 소송이 제기되는 경우 “몰”의 본사 소재지를 관할하는 법원을 관할 법원으로 합니다.</DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\">부칙</DIV><DIV style=\"MARGIN-TOP: 4px\"><FONT color=#6cb200>(시행일) 본 약관은 2008년 09월 01일부터 적용됩니다.</FONT></DIV></DIV>'),(3,'C','docu_title_3_2.jpg','<DIV style=\"BORDER-RIGHT: #eee 1px solid; PADDING-RIGHT: 10px; BORDER-TOP: #eee 1px solid; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; BORDER-LEFT: #eee 1px solid; WIDTH: 698px; LINE-HEIGHT: 200%; PADDING-TOP: 10px; BORDER-BOTTOM: #eee 1px solid; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; BACKGROUND-COLOR: #fafafa; TEXT-ALIGN: left; TEXT-DECORATION: none\">\'{shopName}\' (이하 \'몰\')은 고객님의 개인정보를 중요시하며, \"정보통신망 이용촉진 및 정보보호\"에 관한 법률을 준수하고 있습니다.<BR>회사는 개인정보취급방침을 통하여 고객님께서 제공하시는 개인정보가 어떠한 용도와 방식으로 이용되고 있으며, 개인정보보호를 위해 어떠한 조치가 취해지고 있는지 알려드립니다.<BR>회사는 개인정보취급방침을 개정하는 경우 웹사이트 공지사항(또는 개별공지)을 통하여 공지할 것입니다.<BR><BR><FONT style=\"PADDING-RIGHT: 4px; FONT-SIZE: 4px; font-color: #999\">■</FONT> 본 방침은 : 2008 년 09 월 01 일 부터 시행됩니다. </DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 20px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">01</FONT> 개인정보 수집 및 이용목적</DIV><DIV style=\"MARGIN-TOP: 4px\">대부분의&nbsp;몰 서비스는 별도의 회원가입 절차 없이 언제든지 사용할 수 있습니다. <BR>그러나&nbsp;몰 회원서비스(메일, 마이페이지, 쇼핑 등)을 통하여 이용자들에게 맞춤식 서비스를 비롯한 보다 더 향상된 양질의 서비스를 제공하기 위하여 이용자 개인의 정보를 수집하고 있습니다.<BR><BR>몰은 이용자들이 몰의 개인정보 보호정책 또는 이용약관의 내용에 대하여 「동의함」버튼 또는 「동의안함」버튼을 클릭할 수 있는 절차를 마련하여, 「동의함」버튼을 클릭하면 개인정보 수집에 대해 동의한 것으로 봅니다.<BR><BR>몰&nbsp;이용자의 사전 동의 없이는 이용자의 개인 정보를 함부로 공개하지 않으며 수집된 정보는 아래와 같이 이용하고 있습니다.<BR>(수집항목 : 이름, 로그인ID, 비밀번호,&nbsp;전화번호,&nbsp;주소, 휴대전화번호, 이메일, 주민등록번호 등)<BR></DIV><DIV><BR>① 서비스 제공에 관한 계약 이행 및 서비스 제공에 따른 요금 결제<BR>&nbsp;&nbsp;&nbsp;&nbsp; 구매 및 요금 결제 , 물품배송 또는 사은품 등 발송<BR>② 회원 관리<BR>&nbsp;&nbsp;&nbsp;&nbsp; 회원제 서비스 이용에 따른 본인확인, 가입 의사 확인,&nbsp; 만14세 미만 아동 개인정보 수집 시 법정 대리인 동의여부 확인, 불만처리 등 민원처리, 고지사항 전달<BR>③ 마케팅 및 광고에 활용<BR>&nbsp;&nbsp;&nbsp;&nbsp; 신규 서비스(제품) 개발,&nbsp; 이벤트 등 광고성 정보 전달, 인구통계학적 특성에 따른 서비스 제공 및 광고 게재, 접속 빈도 파악 또는 회원의 서비스 이용에 대한 통계&nbsp; </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">02</FONT> 개인정보 보유 및 이용기간</DIV><DIV style=\"MARGIN-TOP: 4px\">몰의 회원이 자신의 개인정보 열람, 수정 및 삭제 절차에 따라 ID를 삭제하거나 가입해지를 요청한 경우에 수집된 개인의 정보는 재생할 수 없는 방법에 의하여 하드디스크에서 완전히 삭제되며 어떠한 용도로도 열람 또는 이용할 수 없도록 처리됩니다. <BR>그리고 회원의 개인정보는 다음과 같이 개인정보의 수집목적 또는 제공받은 목적이 달성되면 파기됩니다. <BR>단, 상법 등 관련법령의 규정에 의하여 다음과 같이 거래 관련 권리 의무 관계의 확인 등을 이유로 일정기간 보유하여야 할 필요가 있을 경우에는 일정기간 보유합니다.<BR><BR><BR>- 계약 또는 청약철회 등에 관한 기록 : 5년<BR>- 대금결제 및 재화등의 공급에 관한 기록 : 5년<BR>- 소비자의 불만 또는 분쟁처리에 관한 기록 : 3년</DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">03</FONT> 개인정보&nbsp;파기절차 및 방법</DIV><DIV style=\"MARGIN-TOP: 4px\">몰은 원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체없이 파기합니다. 파기절차 및 방법은 다음과 같습니다.<BR>① 파기절차<BR>회원님이 회원가입 등을 위해 입력하신 정보는 목적이 달성된 후 별도의 DB로 옮겨져(종이의 경우 별도의 서류함) 내부 방침 및 기타 관련 법령에 의한 정보보호 사유에 따라(보유 및 이용기간 참조) 일정 기간 저장된 후 파기되어집니다.<BR>별도 DB로 옮겨진 개인정보는 법률에 의한 경우가 아니고서는 보유되어지는 이외의 다른 목적으로 이용되지 않습니다.<BR><BR>②파기방법 <BR>- 전자적 파일형태로 저장된 개인정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제합니다. <BR>- 종이에 출력된 개인정보는 분쇄기로 분쇄하거나 소각을 통하여 파기합니다.</DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">04</FONT> 개인정보&nbsp;제공 및 공유</DIV><DIV style=\"MARGIN-TOP: 4px\">원칙적으로 몰 이용자의 개인정보를 서비스와 무관한 타인 또는 타기업, 기관에 공개하지 않습니다. <BR>다만 아래의 경우에는 예외적으로 동의 없이 개인정보를 제공할 수 있습니다. </DIV><DIV style=\"MARGIN-TOP: 4px\">①&nbsp;몰 서비스 이용약관을 위배하거나&nbsp;몰 서비스를 이용하여 타인에게 법적인 피해를 주거나 미풍양속을 해치는 행위를 한 경우 <BR>② 기타 법적인 조치를 취하기 위하여 개인정보를 공개해야 한다고 판단되는 충분한 근거가 있는 경우<BR>③ 주문상품 배송시 업무상 배송업체에게 최소한의 배송 정보를 제공하는 경우&nbsp;&nbsp;&nbsp; </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">05</FONT> 개인정보&nbsp;위탁처리</DIV><DIV style=\"MARGIN-TOP: 4px\">몰은 회원의 동의없이 고객님의 정보를 외부 업체에 위탁하지 않습니다. 향후 그러한 필요가 생길 경우, 위탁 대상자와 위탁 업무 내용에 대해 고객님에게 통지하고 필요한 경우 사전 동의를 받도록 하겠습니다.</DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">06</FONT> 이용자 및 법정 대리인의 권리와 행사방법</DIV><DIV style=\"MARGIN-TOP: 4px\">① 회원은 언제든지 등록되어 있는 자신의 개인정보를 조회하거나 수정할 수 있으며 가입해지를 요청할 수도 있습니다.<BR>② 개인정보 조회 및 수정을 위해서는 로그인 후에「마이페이지」버튼을 클릭하여 직접 열람, 정정 또는 회원탈퇴가 가능합니다.<BR>혹은 개인정보관리책임자에게 서면, 전화 또는 이메일로 연락하시면 지체없이 조치하겠습니다.<BR>③ 귀하가 개인정보의 오류에 대한 정정을 요청하신 경우에는 정정을 완료하기 전까지 당해 개인정보를 이용 또는 제공하지 않습니다. <BR>또한 잘못된 개인정보를 제3자에게 이미 제공한 경우에는 정정 처리결과를 제3자에게 지체없이 통지하여 정정이 이루어지도록 하겠습니다.<BR>④ 몰은 이용자의 요청에 의해 해지 또는 삭제된 개인정보는&nbsp;몰이 수집하는 개인정보의 보유 및 이용기간”에 명시된 바에 따라 처리하고 그 외의 용도로 열람 또는 이용할 수 없도록 처리하고 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">07</FONT> 개인정보 자동수집 장치의 설치, 운영 및 그 거부에 관한 사항</DIV><DIV style=\"MARGIN-TOP: 4px\">이용자에게 특화된 맞춤서비스를 제공하기 위해서&nbsp;몰 이용자의 정보를 저장하고 수시로 불러오는 \'쿠키(cookie)\'를 사용합니다. <BR>쿠키는 웹사이트를 운영하는데 이용되는 서버(HTTP)가 이용자의 컴퓨터 브라우저에게 보내는 소량의 정보이며 이용자의 PC 컴퓨터내의 하드디스크에 저장되기도 합니다. <BR>쿠키를 이용하여 이용자들이 방문한 몰의 각 서비스와 웹 사이트들에 대한 방문 및 이용형태, 인기 검색어, 이용자 규모 등을 파악하여 더욱 더 편리한 서비스를 만들어 제공할 수 있고 이용자에게 최적화된 정보를 제공할 수 있습니다. <BR><BR>이용자는 쿠키에 대하여 사용여부를 선택할 수 있습니다. <BR>웹브라우저에서 옵션을 설정함으로써 모든 쿠키를 허용할 수도 있으며 쿠키가 저장될 때마다 확인을 거치거나 모든 쿠키의 저장을 거부할 수도 있습니다. <BR>다만 쿠키의 저장을 거부할 경우에는 로그인이 필요한 몰의 일부 서비스는 이용할 수 없습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">08</FONT> 미성년자 및 아동의 보호정책 </DIV><DIV style=\"MARGIN-TOP: 4px\">현행법상 만14세 미만의 어린이들은 온라인으로 타인에게 개인정보를 보내기 전에 반드시 개인정보의 수집 및 이용목적에 대하여 충분히 숙지하고 법정대리인의 동의를 받아야 합니다.<BR>만14세 미만 어린이의 법정대리인은 어린이의 개인정보 열람, 정정, 동의철회를 요청할 수 있으며 이러한 요청이 있을 경우 회사는 지체없이 필요한 조치를 취합니다.&nbsp; </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT style=\"FONT-SIZE: 12px; FONT-FAMILY: Tahoma\">09</FONT> 개인정보에 관한 민원 서비스</DIV><DIV style=\"MARGIN-TOP: 4px\">몰는 고객의 개인정보를 보호하고 개인정보와 관련한 불만을 처리하기 위하여 아래와 같이 관련 부서 및 개인정보관리책임자를 지정하고 있습니다.<BR><BR><BR>개인정보관리책임자 성명 : {name}<BR>전화번호 : {tel}<BR>이메일 : {email}<BR><BR><BR>귀하께서는 회사의 서비스를 이용하시며 발생하는 모든 개인정보보호 관련 민원을 개인정보관리책임자 혹은 담당부서로 신고하실 수 있습니다. <BR>회사는 이용자들의 신고사항에 대해 신속하게 충분한 답변을 드릴 것입니다.<BR>기타 개인정보침해에 대한 신고나 상담이 필요하신 경우에는 아래 기관에 문의하시기 바랍니다.<BR><BR><BR>- 개인분쟁조정위원회 (<A href=\"http://www.1336.or.kr/\" target=_blank>http://www.1336.or.kr</A>/1336)<BR><BR>- 정보보호마크인증위원회 (<A href=\"http://www.eprivacy.or.kr/\" target=_blank>http://www.eprivacy.or.kr</A>/02-580-0533~4)<BR><BR>- 대검찰청 인터넷범죄수사센터 (&lt;<A href=\"http://icic.sppo.go.kr/\" target=_blank>http://icic.sppo.go.kr</A>/02-3480-3600)<BR><BR>- 경찰청 사이버테러대응센터 (<A href=\"http://www.ctrc.go.kr/\" target=_blank>http://www.ctrc.go.kr</A>/02-392-0330)</DIV></DIV>'),(4,'D','docu_title_3_3.jpg','<DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT face=Tahoma size=2>01</FONT> 제품구입 및 흐름</DIV><DIV style=\"MARGIN-TOP: 4px\"><STRONG>* 회원/비회원 구매<BR></STRONG>{shopName}에서는 회원/비회원 구매가 모두 가능합니다. <BR>단, 회원 가입을 하지 않으신 경우에는 구매에 따른 적립금이 적립되지 않습니다. 주문서 작성시 주문자 정보 및 수령자 정보를 매번 입력해야 하는 불편이 있으며, 일부 회원 전용 서비스 이용에 제한이 있습니다. 또한 신제품 정보, 행사 안내 등에 관한 메일링 서비스를 받으실 수 없습니다. </DIV><DIV style=\"MARGIN-TOP: 4px\"><A class=AutoLinkType_blue href=\"/?channel=regist\" target=_blank>▷ 회원가입</A><BR></DIV><DIV style=\"MARGIN-TOP: 4px\"><STRONG>* 쇼핑 흐름<BR></STRONG>{shopName}의 주문 방법은 아래와 같습니다. </DIV><DIV style=\"MARGIN-TOP: 4px\"><FONT style=\"COLOR: #3399ff\">장바구니담기 ▶ 주문자/배송지 정보입력 ▶ 결제정보입럭 ▶ 주문완료 ▶ 주문배송조회</FONT></DIV><DIV style=\"MARGIN-TOP: 4px\">①&nbsp;장바구니 담기<BR>원하시는 제품의 수량을 장바구니에 모두 담아주세요. <BR>장바구니에 담은 제품을 구매하시고자 할 때 회원구매/비회원구매를 클릭해주시면 됩니다. </DIV><DIV style=\"MARGIN-TOP: 4px\">②&nbsp;주문자, 배송지 정보입력<BR>주문 진행에 필요한 주문자 및 배송지 정보를 입력해주시면 됩니다. <BR>배송지 정보를 입력해 주신 후, 결제수단을 선택하는 단계입니다.</DIV><DIV style=\"MARGIN-TOP: 4px\">③ 결제정보 입력<BR>결제 수단에 필요한 정보를 입력하고 확인을 눌러주세요.</DIV><DIV style=\"MARGIN-TOP: 4px\">④ 주문완료<BR>주문번호 및 내역서는 e-mail로 보내드립니다. <BR>주문자 정보, 배송정보, 구매정보요약, 결제정보 및 결제금액을 다시 한번 확인해 드립니다.<BR>⑤ 주문/배송 조회<BR>주문이 완료된 후 배송 진행 상황은 페이지 상단 우측의 [주문배송조회]에서 확인하실 수 있습니다.&nbsp; </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT face=Tahoma size=2>02</FONT> 결제</DIV><DIV style=\"MARGIN-TOP: 4px\"><STRONG>* 신용카드<BR></STRONG>{shopName}에서는 국내외 모든 신용카드를 사용 하실 수 있습니다.&nbsp;<BR><BR><BR><STRONG>* 온라인 입금<BR></STRONG>{shopName}에서는 0개 은행을 통해 온라인 입금을 받고 있습니다.<BR>입금예정자와 실제 입금하신 분의 성함이 다를 경우, 전화 또는 메일로 문의 바랍니다. <BR>무통장 입금일 경우, 입금이 확인된 후에만 배송이 시작됩니다.<BR>{shopName}의 무통장 입금이 가능한 은행 계좌는 다음과 같습니다.<BR><BR><FONT style=\"COLOR: #3399ff\">{bankInfo}</FONT><BR><BR><BR><BR><STRONG>* 적립금<BR></STRONG>적립되어 있는 적립금으로 전체, 또는 부분 결제를 할 수 있습니다.<BR>{shopName}에서는 회원 구매 시 구매액의 일정액를 적립금으로 적립해드리고 있습니다.<BR>이 적립금은&nbsp;{shopName} 인터넷 쇼핑 이용 시 언제든지 현금과 똑같이 사용하실 수 있으며, 적립금의 최소 사용 단위는&nbsp;0,000원 입니다. 적립금은 마이페이지에서 확인 하실 수 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT face=Tahoma size=2>03</FONT> 배송료와 배송방법</DIV><DIV style=\"MARGIN-TOP: 4px\"><STRONG>* 배송료<BR></STRONG>{shopName}에서 판매하는 상품은 주문금액이 {carrLimit}원 미만일 경우에는 {carrPrice}원의 배송료를 받고 있으며, 이는 장바구니에서 실시간으로 자동 처리됩니다. {carrLimit}원 이상 구매할 경우, 배송료는 무료입니다.<BR>단, 제품에 따라 배송정책을 달리 하는 경우도 있을 수 있으며 이런 경우 제품상세보기란에 명시가 되어 있습니다.<BR>단, 특정지역 및 해외로 물품을 배송하는 경우는 별도의 국제 특급 우편(EMS) 요금을 부담하셔야 합니다. <BR><BR><BR><BR><STRONG>* 배송방법<BR></STRONG>주문 후 제품 수령까지는 입금 일로부터 2~5일이 소요됩니다.(업무일 기준)<BR>{shopName}은 00택배외 믿을 수 있는 택배사를 이용해 안전하고 빠르게 배송해 드리고 있습니다. <BR>제품에 따라서 가장 적절한 택배사를 이용하고 있습니다. </DIV></DIV><DIV style=\"PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-SIZE: 8pt; PADDING-BOTTOM: 10px; WIDTH: 700px; LINE-HEIGHT: 160%; PADDING-TOP: 10px; FONT-FAMILY: 돋움,돋움체,Tahoma; LETTER-SPACING: -1px; TEXT-ALIGN: left; TEXT-DECORATION: none\"><DIV style=\"FONT-WEIGHT: bold; BORDER-BOTTOM: #eee 1px solid\"><FONT face=Tahoma size=2>04</FONT> 교환 및 반품</DIV><DIV style=\"MARGIN-TOP: 4px\"><FONT style=\"COLOR: #3399ff\"><BR>고객센터 또는 메일로 의사접수 ▶ 가능여부확인후 이용안내 ▶ 물품반송 ▶ 환불<BR><BR>고객센터 :&nbsp;{tel} , 문의 메일 :&nbsp;{email}&nbsp;<BR><BR></FONT><BR>①&nbsp;접수 : 전화, 메일로 교환 및 반품의사 접수&nbsp;<BR>②&nbsp;교환 및 반품 가능 여부 확인 : 전화나 메일로 교환/반품 방법을 안내해 드립니다.<BR><BR>③ 물품 반송 : 구매고객 물품 반송<BR><BR>④ 환불 : 반송된 물품 확인 후 환불 처리<BR><BR><B><BR>* 교환 및 반품이 가능한 경우</B><BR>상품을 제공받은 고객님께서는 아래의 경우에 한하여 배송 완료일로부터 7일 이내에 교환 및 반품을 요청하실 수 있습니다.<BR>-배송된 상품이 주문내용과 다르거나 무아샵에서 제공한 정보와 상이할 경우 <BR>-배송된 상품이 파손, 손상되었거나 오염되었을 경우 <BR><BR><B><BR>* 교환 및 반품이 불가능한 경우 </B><BR>물품 수령 후 7일이 경과한 경우<BR>포장 개봉 후 상품 라벨을 뜯은 경우<BR>포장 개봉 후 상품 가치가 훼손된 경우<BR>포장 개봉 후 조립해보고 마음에 들지 않은 경우<BR>소비자 부주의로 인한 제품의 훼손 및 파손의 경우 <BR><BR><B><BR>* 반송비 </B><BR>제품을 이용할 의사가 없어져서 반품하는 경우(고객변심) 고객님께서 배송비를 부담하셔야 합니다 <BR><BR><B><BR>* 환 불</B><BR>무통장입금 - 제품회수 확인 후 3일 이내에 환불<BR>신 용 카 드 - 제품회수 확인 후 해당카드 사로 청구 취소요청 <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (해당카드사 별로 다르며 대략 5-7일이 소요됩니다.) </DIV></DIV>')";
	$mysql->query($sql);
}


if(!$mysql->table_list("","mall_event")) {
	$sql =  "
	CREATE TABLE mall_event (
	  uid int(11) unsigned NOT NULL auto_increment,
	  name varchar(80) binary NOT NULL default '',
	  s_date datetime default NULL,
	  e_date datetime default NULL,
	  sale int(4) unsigned NOT NULL default '0',
	  point int(4) unsigned NOT NULL default '0',
	  code mediumtext NOT NULL default '',
	  scate varchar(255) binary NOT NULL default '',
	  sgoods mediumtext NOT NULL default '',	  
	  sbrand varchar(255) binary NOT NULL default '',
	  s_check enum('0','1','2') NOT NULL default '0',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY name (name)
	)
	";
	$mysql->query($sql);
}


if(!$mysql->table_list("","mall_goods")) {
	$sql =  "
	CREATE TABLE mall_goods (
	  uid int(11) unsigned NOT NULL auto_increment,
	  cate bigint(12) unsigned NOT NULL default '0',
	  mcate varchar(250) binary NOT NULL default '',
	  number varchar(50) binary NOT NULL default '',
	  brand smallint(4) unsigned NOT NULL default '0',
	  event smallint(4) unsigned NOT NULL default '0',
	  special varchar(250) binary NOT NULL default '',
	  name varchar(150) NOT NULL default '',
	  search_name varchar(150) NOT NULL default '',
	  model varchar(100) binary NOT NULL default '',
	  comp varchar(50) binary NOT NULL default '',
	  made varchar(50) binary NOT NULL default '',
	  price int(11) unsigned NOT NULL default '0',
	  consumer_price int(11) unsigned NOT NULL default '0',
	  price_ment varchar(100) binary NOT NULL default '',
	  unit varchar(10) binary NOT NULL default '',
	  def_qty int(11) unsigned NOT NULL default '1',
	  s_qty tinyint(1) unsigned NOT NULL default '0',
	  qty int(11) unsigned NOT NULL default '0',
	  reserve varchar(20) binary NOT NULL default '',
	  carriage varchar(20) binary NOT NULL default '',
	  op_goods_type varchar(30) binary NOT NULL default 'A',
	  op_goods varchar(250) binary NOT NULL default '',
	  display varchar(10) binary NOT NULL default '',
	  icon varchar(100) binary NOT NULL default '',
	  image1 varchar(50) binary NOT NULL default '',
	  image2 varchar(50) binary NOT NULL default '',
	  image3 varchar(50) binary NOT NULL default '',
	  image4 varchar(50) binary NOT NULL default '',
	  image5 varchar(50) binary NOT NULL default '',
	  other_image varchar(80) binary NOT NULL default '',
	  explan text NOT NULL default '',
	  tag varchar(255) NOT NULL default '',  
	  type enum('A','B','C') NOT NULL default 'A',
	  p_id varchar(50) binary NOT NULL default '',
	  v_cnt int(11) unsigned NOT NULL default '0',
	  o_cnt int(11) unsigned NOT NULL default '0',
	  c_cnt int(11) unsigned NOT NULL default '0',
	  o_num1 int(8) unsigned NOT NULL default '0',
	  o_num2 int(8) unsigned NOT NULL default '0',
	  o_num3 int(8) unsigned NOT NULL default '0',
	  sequence int(8) unsigned NOT NULL default '3',	  
	  coop_sdate datetime NOT NULL default '0000-00-00 00:00:00',
	  coop_edate datetime NOT NULL default '0000-00-00 00:00:00',
	  coop_close int(11) unsigned NOT NULL default '0',
	  coop_sale int(11) unsigned NOT NULL default '0',
	  coop_cnt int(11) unsigned NOT NULL default '0',
	  coop_price int(11) unsigned NOT NULL default '0',
	  moddate int(10) unsigned NOT NULL default '0',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY cate (cate),
	  KEY number (number),
	  KEY name (name),
	  KEY brand (brand),
	  KEY p_id (p_id),
	  KEY type (type),
	  KEY sequence (sequence)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_goods_option")) {
	$sql =  "
	CREATE TABLE mall_goods_option (
	  uid int(11) unsigned NOT NULL auto_increment,
	  guid int(11) unsigned NOT NULL,
	  option1 varchar(50) binary NOT NULL default '',
	  option2 varchar(250) binary NOT NULL default '',
	  price int(11) unsigned NOT NULL default '0',
	  display enum('Y','N') NOT NULL default 'Y',
	  qty int(11) unsigned NOT NULL default '0',
	  code varchar(250) binary NOT NULL default '',
	  o_num int(8) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY guid (guid)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_goods_cooper")) {
	$sql =  "
	CREATE TABLE mall_goods_cooper (
	  uid int(11) unsigned NOT NULL auto_increment,
	  guid int(11) unsigned NOT NULL,
	  qty int(11) unsigned NOT NULL default '0',
	  price int(11) unsigned NOT NULL default '0',
	  code varchar(250) binary NOT NULL default '',
	  o_num int(8) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY guid (guid)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_goods_conf")) {
	$sql =  "
	CREATE TABLE mall_goods_conf (
	  uid int(11) unsigned NOT NULL auto_increment,
	  name varchar(50) NOT NULL default '',
	  number int(11) unsigned NOT NULL default '0',
	  mode char(1) binary NOT NULL default '',
	  code mediumtext NOT NULL default '',
	  PRIMARY KEY  (uid),
	  KEY mode (mode)
	)
	";
	$mysql->query($sql);

	$sql = "INSERT INTO mall_goods_conf VALUES (1,'개',2,'U',''), (2,'EA',3,'U',''), (3,'icon_best.gif',0,'I',''), (4,'icon_hit.gif',0,'I',''), (5,'icon_new.gif',0,'I','')";
	$mysql->query($sql);
}


if(!$mysql->table_list("","mall_goods_point")) {
	$sql =  "
	CREATE TABLE mall_goods_point ( 
		uid int(11) unsigned not null auto_increment,
		cate bigint(12) unsigned NOT NULL default '0',
		number varchar(50) binary not null default '',
		goods_name varchar(100) binary not null default '',
		id varchar(30) binary not null default '',
		name varchar(30) binary not null default '',
		passwd varchar(50) binary not null default '',
		title varchar(200) binary not null default '',
		content mediumtext not null default '',
		point int(1) unsigned not null default '0' ,
		best enum('N','Y') not null default 'N' ,
		reserve int(8) unsigned not null default '0' ,
		buy enum('0','1') not null default '0' ,
		acc_ip varchar(20) binary not null default '',
		signdate int(10) unsigned not null default '0' ,
		PRIMARY KEY  (uid),
		KEY id (id) 
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_goods_qna")) {
	$sql =  "
	CREATE TABLE mall_goods_qna ( 
		uid int(11) unsigned not null auto_increment,
		cate bigint(12) unsigned NOT NULL default '0',
		number varchar(50) binary not null default '',
		goods_name varchar(100) binary not null default '',
		id varchar(30) binary not null default '',
		name varchar(30) binary not null default '',
		passwd varchar(50) binary not null default '',
		title varchar(200) binary not null default '',
		content mediumtext not null default '',
		answer mediumtext not null default '',
		acc_ip varchar(20) binary not null default '',
		signdate int(10) unsigned not null default '0' ,
		PRIMARY KEY  (uid),
		KEY id (id) 
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_goods_view")) {
	$sql =  "
	CREATE TABLE mall_goods_view ( 
		uid int(11) unsigned not null auto_increment,
		cno bigint(16) unsigned not null default '0',
		date int(8) unsigned not null default '0' ,
		view int(8) unsigned not null default '0' ,
		PRIMARY KEY  (uid),
		KEY cno (cno) 
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_member_quit")) {
	$sql =  "
	CREATE TABLE mall_member_quit (
	  uid int(11) unsigned NOT NULL auto_increment,
	  name varchar(50) binary NOT NULL default '',
	  cate tinyint(1) unsigned NOT NULL default '0',
	  ocnt int(4) unsigned NOT NULL default '0',  
	  message mediumtext NOT NULL default '',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY cate (cate),
	  KEY name (name)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_order_goods")) {
	$sql =  "
	CREATE TABLE mall_order_goods (
	  uid int(11) unsigned NOT NULL auto_increment,
	  order_num varchar(24) binary NOT NULL default '',
	  p_cate bigint(12) unsigned NOT NULL default '0',
	  p_number int(8) unsigned NOT NULL default '0',
	  p_name varchar(100) binary NOT NULL default '',
	  p_price int(11) unsigned NOT NULL default '0',
	  p_qty int(8) unsigned NOT NULL default '0',
	  p_reserve int(11) unsigned NOT NULL default '0',
	  p_option mediumtext NOT NULL default '',
	  op_price varchar(100) binary NOT NULL default '',
	  sale_price int(11) unsigned NOT NULL default '0',
	  sale_vls varchar(20) binary NOT NULL default '',
	  order_status enum('A','B','C','D','E','X','Y','Z') NOT NULL default 'A',
	  order_status2 enum('','A','B','C','D') NOT NULL default '',
	  status_date datetime NOT NULL default '0000-00-00 00:00:00',
	  carriage int(11) unsigned default NULL,
	  carr_info varchar(255) binary NOT NULL default '',
	  affiliate varchar(30) binary NOT NULL default '',
	  signdate datetime NOT NULL default '0000-00-00 00:00:00',
	  PRIMARY KEY  (uid,order_num),
	  KEY p_cate (p_cate),
	  KEY p_number (p_number),
	  KEY p_name (p_name),
	  KEY affiliate (affiliate),
	  KEY order_status (order_status)
	)
	";
	$mysql->query($sql);
}


if(!$mysql->table_list("","mall_order_info")) {
	$sql =  "
	CREATE TABLE mall_order_info (
	  uid int(11) unsigned NOT NULL auto_increment,
	  id varchar(15) binary NOT NULL default '',
	  order_num varchar(24) binary NOT NULL default '',
	  name1 varchar(20) binary NOT NULL default '',
	  tel1 varchar(20) binary NOT NULL default '',
	  hphone1 varchar(20) binary NOT NULL default '',
	  email varchar(35) binary default NULL,
	  name2 varchar(20) binary NOT NULL default '',
	  tel2 varchar(20) binary NOT NULL default '',
	  hphone2 varchar(20) binary NOT NULL default '',
	  zipcode varchar(10) binary NOT NULL default '',
	  address varchar(150) binary NOT NULL default '',
	  pay_type enum('B','C','R','V','H') NOT NULL default 'B',
	  pay_date varchar(20) binary NOT NULL default '',
	  bank_name varchar(100) binary NOT NULL default '',
	  pay_name varchar(20) binary NOT NULL default '',
	  message varchar(250) binary default NULL,
	  use_reserve int(11) unsigned default '0',
	  use_cupon int(11) unsigned not null default '0',
	  cupon int(11) unsigned not null default '0',
	  carriage int(11) unsigned default NULL,
	  cancel_carriage int(11) unsigned NOT NULL default '0',
	  sales int(11) unsigned default NULL,
	  pay_status enum('A','B','C') NOT NULL default 'A',
	  pay_info varchar(255) binary NOT NULL default '', 
	  pay_number varchar(20) binary NOT NULL default '', 
	  cash_info varchar(100) binary NOT NULL default '', 
	  escrow enum('N','Y') NOT NULL default 'N',
	  pay_total int(11) unsigned NOT NULL default '0',
	  cancel_total int(11) unsigned NOT NULL default '0',
	  admess mediumtext NOT NULL default '',
	  carr_info varchar(255) binary NOT NULL default '',
	  order_status enum('A','B','C','D','E','X','Y','Z') NOT NULL default 'A',	
	  status_date datetime NOT NULL default '0000-00-00 00:00:00',
	  send_ok enum('N','Y') NOT NULL default 'N',	  
	  admin_view enum('N','Y') NOT NULL default 'N',
	  affiliate varchar(30) binary NOT NULL default '',
	  a_commi tinyint(2) unsigned NOT NULL default '0',
	  cash_sale tinyint(2) unsigned NOT NULL default '0',
	  signdate datetime NOT NULL default '0000-00-00 00:00:00',
	  PRIMARY KEY  (uid,order_num),
	  KEY id (id),
	  KEY affiliate (affiliate),
	  KEY name1 (name1)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_order_change")) {
	$sql =  "
	CREATE TABLE mall_order_change (
	  uid int(11) unsigned NOT NULL auto_increment,
	  sgoods varchar(250) NOT NULL default '',
	  order_num varchar(24) binary NOT NULL default '',
	  name varchar(20) binary NOT NULL default '',
	  reason_code tinyint(2) unsigned default '1',
	  bank_info varchar(100) binary NOT NULL default '',
	  message varchar(250) binary default NULL,	  
	  refund int(11) unsigned NOT NULL default '0',
	  refund_g int(11) unsigned NOT NULL default '0',
	  refund_r int(11) unsigned NOT NULL default '0',
	  refund_type enum('','C','B') NOT NULL default '',
	  status enum('X','Y','Z') NOT NULL default 'X',
	  status2 enum('A','B','C','D','Z') NOT NULL default 'A',
	  status_date datetime NOT NULL default '0000-00-00 00:00:00',
	  signdate datetime NOT NULL default '0000-00-00 00:00:00',
	  PRIMARY KEY  (uid),
	  KEY sgoods (sgoods),
	  KEY order_num (order_num)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_order_cash")) {
	$sql =  "
	CREATE TABLE mall_order_cash (
	  uid int(11) unsigned NOT NULL auto_increment,
	  cp_name varchar(24) binary NOT NULL default '',
	  order_num varchar(24) binary NOT NULL default '',
	  id varchar(15) binary NOT NULL default '',
	  name varchar(20) binary NOT NULL default '',
	  cell varchar(20) binary NOT NULL default '',
	  email varchar(35) binary NOT NULL default '',
	  price int(11) unsigned not null default '0',
	  goods_name varchar(250) binary NOT NULL default '',
	  tax_type enum('A','B') NOT NULL default 'A',	
	  cash_type enum('A','B') NOT NULL default 'A',	
	  auth_number varchar(20) binary NOT NULL default '',
	  trad_time varchar(30) binary NOT NULL default '',
	  receipt_no int(11) unsigned default NULL,
	  receipt_info varchar(100) binary NOT NULL default '',
	  receipt_status varchar(10) binary NOT NULL default '',
	  receipt_error varchar(250) binary NOT NULL default '',
	  status enum('A','B','C','D') NOT NULL default 'A',
	  status_date datetime NOT NULL default '0000-00-00 00:00:00',
	  signdate datetime NOT NULL default '0000-00-00 00:00:00',
	  PRIMARY KEY  (uid),
	  KEY order_num (order_num),
	  KEY name (name),
	  KEY status (status)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_popup")) {
	$sql =  "
	CREATE TABLE mall_popup (
	  uid int(11) unsigned NOT NULL auto_increment,
	  subject varchar(80) binary NOT NULL default '',
	  status enum('1','2') NOT NULL default '1',
	  days enum('1','2') NOT NULL default '1',
	  type enum('1','2') NOT NULL default '1',
	  end_date datetime default NULL,
	  info varchar(100) binary NOT NULL default '',
	  comment mediumtext NOT NULL default '',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY status (status)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_reserve")) {
	$sql =  "
	CREATE TABLE mall_reserve (
	  uid int(11) unsigned NOT NULL auto_increment,
	  id varchar(15) binary NOT NULL default '',
	  subject varchar(100) binary NOT NULL default '',
	  reserve int(11) unsigned NOT NULL default '0',
	  order_num varchar(24) binary NOT NULL default '',
	  goods_num varchar(20) binary NOT NULL default '',
	  status enum('A','B','C','D','E') NOT NULL default 'A',
	  signdate datetime NOT NULL default '0000-00-00 00:00:00',
	  PRIMARY KEY  (uid),
	  KEY id (id),
	  KEY status (status)
	)
	";
	$mysql->query($sql);
}


if(!$mysql->table_list("","mall_search")) {
	$sql =  "
	CREATE TABLE mall_search (
	  uid int(11) unsigned NOT NULL auto_increment,
	  word varchar(200) binary NOT NULL default '',	  
	  tag enum('0','1') NOT NULL default '0',
	  ip varchar(20) NOT NULL default '0',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_special")) {
	$sql =  "
	CREATE TABLE mall_special (
	  uid int(11) unsigned NOT NULL auto_increment,	  
	  name varchar(100) NOT NULL default '',
	  img1 varchar(50) binary NOT NULL default '',
	  img2 varchar(50) binary NOT NULL default '',
	  display varchar(10) binary NOT NULL default '',
	  code_use enum('Y','N') NOT NULL default 'Y',
	  code mediumtext NOT NULL default '',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY name (name)	 
	)
	";
	$mysql->query($sql);
}


if(!$mysql->table_list("","mall_wish")) {
	$sql =  "
	CREATE TABLE mall_wish (
	  uid int(11) unsigned NOT NULL auto_increment,
	  id varchar(20)  binary NOT NULL default '',
	  p_number int(11) unsigned NOT NULL default '0',
	  p_cate bigint(12) unsigned NOT NULL default '0',
	  memo varchar(250) binary NOT NULL default '',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY id (id)
	)
	";
	$mysql->query($sql);
}


$arr = Array("cooperation","counsel","customer","faq","notice","sales","affil_counsel");
for($i=0;$i<6;$i++) {
	$bname = $arr[$i];

	if(!$mysql->table_list("","pboard_{$bname}")) {
		$sql = "
			CREATE TABLE pboard_{$bname}( 
			no mediumint(8) unsigned DEFAULT '0' NOT NULL, 
			idx smallint(3) unsigned DEFAULT '0' NOT NULL,
			main mediumint(7) unsigned DEFAULT '0' NOT NULL, 
			depth smallint(3) unsigned DEFAULT '0' NOT NULL,
			name char(20) binary DEFAULT '' NOT NULL,
			email char(40) DEFAULT '' NOT NULL,
			subject char(150) binary DEFAULT '' NOT NULL,
			cate tinyint unsigned DEFAULT '0' NOT NULL,
			hit smallint(5) unsigned DEFAULT '0' NOT NULL,
			reco smallint(5) unsigned DEFAULT '0' NOT NULL,
			down smallint(5) unsigned DEFAULT '0' NOT NULL,
			cnt_memo smallint(5) unsigned DEFAULT '0' NOT NULL, 
			file char(80) binary DEFAULT '' NOT NULL,
			secret  enum('0','1') DEFAULT '0' NOT NULL,
			icon char(30) binary DEFAULT '' NOT NULL,
			signdate int(10) unsigned DEFAULT '0' NOT NULL,
			PRIMARY KEY(no), 
			INDEX POS(idx,main)
			)
		";
		$mysql->query($sql);

		$sql = "insert into pboard_{$bname} (no,idx,main,depth) values('1', 999, 0, 999)";
		$mysql->query($sql);
	}

	if(!$mysql->table_list("","pboard_{$bname}_body")) {
		$sql = "
			CREATE TABLE pboard_{$bname}_body( 
			no int(8) unsigned NOT NULL,
			idx int(8) unsigned DEFAULT '0' NOT NULL,
			memo enum('0','1') DEFAULT '0' NOT NULL,
			passwd varchar(50) binary DEFAULT '' NOT NULL,
			homepage varchar(50) DEFAULT '' NOT NULL,
			comment mediumtext DEFAULT '' NOT NULL,
			m_link varchar(100) DEFAULT '' NOT NULL,
			s_link varchar(150) DEFAULT '' NOT NULL,
			html_type enum('0','1') DEFAULT '0' NOT NULL,
			remail enum('0','1') DEFAULT '0' NOT NULL,
			file varchar(255) DEFAULT '' NOT NULL,
			id char(20) binary DEFAULT '' NOT NULL, 
			ip char(30) binary DEFAULT '' NOT NULL, 
			INDEX (no),
			INDEX (idx)
			)
		"; 
		$mysql->query($sql);
		
		$sql="insert into pboard_{$bname}_body (no,idx,comment) values('1',999,0)";
		$mysql->query($sql);
	}	
}

############ 게시판 관리테이블 생성 ####################
if(!$mysql->table_list("","pboard_manager")) {
	$sql =  "
	CREATE TABLE pboard_manager (
	  uid int(11) NOT NULL auto_increment,
	  name varchar(50) NOT NULL default '',
	  s_name varchar(50) NOT NULL default '',
	  title varchar(50) NOT NULL default '',
	  b_w_size int(4) unsigned NOT NULL default '0',
	  inpage int(4) unsigned NOT NULL default '0',
	  pagelink int(4) unsigned NOT NULL default '0',
	  bg_color char(30) NOT NULL default '',
	  word_limit int(4) unsigned NOT NULL default '0',
      img_limit int(4) unsigned NOT NULL default '0',
      options text NOT NULL default '',
	  header_url varchar(35) NOT NULL default '',
	  header text NOT NULL default '',
	  footer_url varchar(35) NOT NULL default '',
	  footer text NOT NULL default '',
	  accesslevel text NOT NULL default '',
	  category text NOT NULL default '',
	  ck_auto text NOT NULL default '',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY name (name)
	)
	";
	$mysql->query($sql);

	$sql = "INSERT INTO `pboard_manager` VALUES (4,'notice','itsMall_bo','공지사항',100,10,10,'#FFFFFF',0,720,'Y||||Y||||Y||Y||||||||||1||0||||||||||||||24||||','','','','','1|1|1|1|1|1|<|<|<|<|<|<|1|<','0','',{$signdate}),(5,'customer','itsMall_bo','고객게시판',100,10,10,'#FFFFFF',0,720,'Y||||Y||||Y||Y||||||||||1||0||||||||||||||24||||','','','','','1|1|1|1|1|1|<|<|<|<|<|<|1|<','','',{$signdate}),(6,'faq','itsMall_faq','자주하는 질문과 답변',100,10,10,'#FFFFFF',0,720,'Y||Y||Y||||||||||||||||0||0||||||||||Y||||24||||Y','','','','','1|1|9|9|9|9|<|<|<|<|<|<|1|<','5|주문결제|배송문의|반품/교환/환불/AS|회원정보및 탈퇴|기타','',{$signdate}),(7,'counsel','itsMall_counsel','1:1고객문의',100,10,10,'#FFFFFF',0,720,'Y||Y||Y||||Y||Y||Y||||Y||||1||2||||Y||||||||||24||||Y','','','','','2|2|1|9|1|1|<|<|<|<|<|<|1|<','8|배송문의|입금/계산서문의|회원정보문의|교환문의|반품/취소문의|상품문의|A/S 문의|기타문의','',{$signdate}),(8,'sales','itsMall_counsel2','대량구매문의',100,10,10,'#FFFFFF',0,720,'Y||||Y||||||Y||||||Y||||0||3||||||||||Y||||24||||Y','','','','','9|9|1|1|1|1|<|<|<|<|<|<|1|<','','',{$signdate}),(9,'cooperation','itsMall_cooperation','제휴/광고 문의',100,10,10,'#FFFFFF',0,720,'Y||Y||Y||||Y||Y||||||||||1||0||||||||||||||24||||Y','','','','','1|1|1|1|1|1|<|<|<|<|<|<|1|<','2|제휴문의|광고문의','',{$signdate}),(9,'affil_counsel','itsMall_counsel','1:1고객문의',100,10,10,'#FFFFFF',0,720,'Y||Y||Y||||Y||Y||Y||||Y||||1||2||||Y||||||||||24||||Y','','','','','2|2|1|9|1|1|<|<|<|<|<|<|1|<','4|정산문의|입금/계산서문의|배너문의|기타문의','',{$signdate});";
	$mysql->query($sql);
}


############  메일 로그테이블 생성 ####################
if(!$mysql->table_list("","pboard_maillog")) {
$sql =  "
	CREATE TABLE pboard_maillog (
	  uid int(11) NOT NULL auto_increment,
	  m_to varchar(255) DEFAULT '' NOT NULL,
	  subject varchar(100) DEFAULT '' NOT NULL,
	  content text,
	  m_true int(8) DEFAULT '0' NOT NULL,
	  m_false int(8) DEFAULT '0' NOT NULL,
	  m_total int(8) DEFAULT '0' NOT NULL,
	  m_cnt int(8) DEFAULT '0' NOT NULL,
	  m_search varchar(255) DEFAULT '' NOT NULL,
	  err_log mediumtext not null default '',	
	  s_time int(10) unsigned DEFAULT '0' NOT NULL, 
	  e_time int(10) unsigned DEFAULT '0' NOT NULL,
	  signdate int(10) unsigned DEFAULT '0' NOT NULL,
	  PRIMARY KEY (uid)
	)
";
$mysql->query($sql);
}


############ 회원 테이블이블 생성 ####################

if(!$mysql->table_list("","pboard_member")) {
	$sql =  "
	CREATE TABLE pboard_member (
	  uid int(11) NOT NULL auto_increment,
	  id varchar(30) NOT NULL default '',
	  name varchar(30) NOT NULL default '',
	  passwd varchar(50) NOT NULL default '',
	  jumin1 int(7) NOT NULL default '0',
	  jumin2 varchar(50) NOT NULL default '',
	  tel varchar(20) NOT NULL default '',
	  hphone varchar(20) NOT NULL default '',
	  zipcode varchar(10) NOT NULL default '',
	  address varchar(150) NOT NULL default '',
	  email varchar(50) NOT NULL default '',
	  mail_server varchar(30) NOT NULL default '',
	  homepage varchar(50) NOT NULL default '',
	  msn varchar(50) NOT NULL default '',
	  birth varchar(30) NOT NULL default '',
	  sex enum('N','M','F') default 'N',
	  marr enum('Y','N') default 'N',
	  edu varchar(30) NOT NULL default '',
	  hobby varchar(30) NOT NULL default '',
	  job varchar(30) NOT NULL default '',
	  jobname varchar(30) NOT NULL default '',
	  info mediumtext not null default '',	
	  level enum('1','2','3','4','5','6','7','8','9','10') default '2',
	  reserve int(11) NOT NULL default '0',
	  mailling enum('Y','N') default 'Y',
	  sms enum('Y','N') default 'Y',
	  icon varchar(50) NOT NULL default '',
	  add1 mediumtext not null default '',	
	  add2 mediumtext not null default '',	
	  add3 mediumtext not null default '',	
	  add4 mediumtext not null default '',	
	  add5 mediumtext not null default '',	
	  carriage1 mediumtext not null default '',	
	  carriage2 mediumtext not null default '',	
	  message1 mediumtext not null default '',	
	  message2 mediumtext not null default '',	
	  message3 mediumtext not null default '',	
	  cnts int(8) unsigned NOT NULL default '0',
	  auth enum('Y','N') not null default 'Y',
	  etc mediumtext not null default '',		  
	  logtime int(10) unsigned NOT NULL default '0',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY id (id),
	  KEY name (name),
	  KEY passwd (passwd)
	)
	";
	$mysql->query($sql);

	############ 회원가입양식 등록 ####################
	$sql = "
		INSERT INTO `pboard_member` VALUES (1,'','','',0,'0','','','','26|0|1|2|2||0|0|1|1|0|0|0|0|0|0|1|||0||0|0|0|0|0|1','','','','','','N','N','','','','','고졸,전문대재학,전문대휴학,전문대졸업,전문대중퇴,대학교재학,대학교휴학,대학교졸업,대학교중퇴,대학원재학,대학원수료,대학원졸업,박사이상,기타|컴퓨터,당구,볼링,낚시,쇼핑,독서,바둑,운동,여행,웹서핑|기획/사무직,금융/증권,회계사,연구원,정보통신,컴퓨터관련,건설/토목,서비스/영업,공무원,교직원/교사,학원강사,사업,프리랜서,학생,석/박사,의사/한의사,약사,간호사,변호사/법조인,교수/전임강사,전문직,자영업,무직,기타||||||||pmall','2',0,'Y','N','','','','','','','','','','','',0,'Y','',0,{$signdate}),(2,'{$ad_id}','관리자','".md5($ad_pw)."',0,'0','','','','','','','','','','F','N','','','','pmall','','10',0,'N','N','','','','','','','','','','','',0,'Y','','','{$signdate}')
	";
	$mysql->query($sql);
}


if(!$mysql->table_list("","pcount_list")) {
	$sql =  "
		CREATE TABLE pcount_list (
		  uid int(11) unsigned NOT NULL auto_increment,
		  year int(4) unsigned NOT NULL default '0',
		  month int(2) unsigned NOT NULL default '0',
		  day int(2) unsigned NOT NULL default '0',
		  h_00 int(8) unsigned NOT NULL default '0',
		  h_01 int(8) unsigned NOT NULL default '0',
		  h_02 int(8) unsigned NOT NULL default '0',
		  h_03 int(8) unsigned NOT NULL default '0',
		  h_04 int(8) unsigned NOT NULL default '0',
		  h_05 int(8) unsigned NOT NULL default '0',
		  h_06 int(8) unsigned NOT NULL default '0',
		  h_07 int(8) unsigned NOT NULL default '0',
		  h_08 int(8) unsigned NOT NULL default '0',
		  h_09 int(8) unsigned NOT NULL default '0',
		  h_10 int(8) unsigned NOT NULL default '0',
		  h_11 int(8) unsigned NOT NULL default '0',
		  h_12 int(8) unsigned NOT NULL default '0',
		  h_13 int(8) unsigned NOT NULL default '0',
		  h_14 int(8) unsigned NOT NULL default '0',
		  h_15 int(8) unsigned NOT NULL default '0',
		  h_16 int(8) unsigned NOT NULL default '0',
		  h_17 int(8) unsigned NOT NULL default '0',
		  h_18 int(8) unsigned NOT NULL default '0',
		  h_19 int(8) unsigned NOT NULL default '0',
		  h_20 int(8) unsigned NOT NULL default '0',
		  h_21 int(8) unsigned NOT NULL default '0',
		  h_22 int(8) unsigned NOT NULL default '0',
		  h_23 int(8) unsigned NOT NULL default '0',
		  h2_00 int(8) unsigned NOT NULL default '0',
		  h2_01 int(8) unsigned NOT NULL default '0',
		  h2_02 int(8) unsigned NOT NULL default '0',
		  h2_03 int(8) unsigned NOT NULL default '0',
		  h2_04 int(8) unsigned NOT NULL default '0',
		  h2_05 int(8) unsigned NOT NULL default '0',
		  h2_06 int(8) unsigned NOT NULL default '0',
		  h2_07 int(8) unsigned NOT NULL default '0',
		  h2_08 int(8) unsigned NOT NULL default '0',
		  h2_09 int(8) unsigned NOT NULL default '0',
		  h2_10 int(8) unsigned NOT NULL default '0',
		  h2_11 int(8) unsigned NOT NULL default '0',
		  h2_12 int(8) unsigned NOT NULL default '0',
		  h2_13 int(8) unsigned NOT NULL default '0',
		  h2_14 int(8) unsigned NOT NULL default '0',
		  h2_15 int(8) unsigned NOT NULL default '0',
		  h2_16 int(8) unsigned NOT NULL default '0',
		  h2_17 int(8) unsigned NOT NULL default '0',
		  h2_18 int(8) unsigned NOT NULL default '0',
		  h2_19 int(8) unsigned NOT NULL default '0',
		  h2_20 int(8) unsigned NOT NULL default '0',
		  h2_21 int(8) unsigned NOT NULL default '0',
		  h2_22 int(8) unsigned NOT NULL default '0',
		  h2_23 int(8) unsigned NOT NULL default '0',	  
		  total int(8) unsigned NOT NULL default '0',
		  total2 int(8) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY year (year),
		  KEY month (month),
		  KEY day (day)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","pcount_agent")) {
	$sql =  "
		CREATE TABLE pcount_agent (
		  uid int(11) NOT NULL auto_increment,
		  type enum ('O','B','S','K') NOT NULL default 'O',
		  content varchar(255) NOT NULL default '',	 
		  cnts int(8) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY content (content),
		  KEY type (type)
		)
	";

	$mysql->query($sql);
}

if(!$mysql->table_list("","pcount_check")) {
	$sql =  "
		CREATE TABLE pcount_check (
		  uid int(11) NOT NULL auto_increment,
		  ck_ip varchar(20) NOT NULL default '',
		  ck_date int(8) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY ck_ip (ck_ip)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","pcount_refer")) {
	$sql =  "
		CREATE TABLE pcount_refer (
		  uid int(11) NOT NULL auto_increment,
		  referer varchar(255) NOT NULL default '',
		  hit int(8) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY hit (hit)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_tb_send")) {
	$sql =  "
		CREATE TABLE mall_tb_send (
		  uid int(11) NOT NULL auto_increment,
		  gid int(11) unsigned NOT NULL default '0',
		  link varchar(80) binary NOT NULL default '',
		  title varchar(250) binary NOT NULL default '',
		  posts varchar(80) binary NOT NULL default '',    
		  signdate int(10) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY gid (gid)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_cupon_manager")) {
	$sql =  "
		CREATE TABLE mall_cupon_manager (
		  uid int(11) NOT NULL auto_increment,
		  name varchar(150) binary NOT NULL default '', 
		  type enum('1','2','3','4','5') NOT NULL default '1',
		  sale int(8) unsigned NOT NULL default '0',
		  stype enum('P','W') NOT NULL default 'P',
		  scate varchar(255) binary NOT NULL default '',
		  sgoods mediumtext NOT NULL default '',
		  sqty enum('0','1') NOT NULL default '1',
		  qty int(8) unsigned NOT NULL default '0',
		  sdate datetime default NULL,
		  edate datetime default NULL,
		  days int(4) NOT NULL default '0',
		  odds int(4) NOT NULL default '100',
		  cnts int(4) NOT NULL default '1',
		  lmt int(8) unsigned NOT NULL default '0',
		  signdate int(10) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_cupon")) {
	$sql =  "
		CREATE TABLE mall_cupon (
		  uid int(11) NOT NULL auto_increment,
		  pid int(11) NOT NULL default '0',
		  gid int(11) NOT NULL default '0',
		  id varchar(20) binary NOT NULL default '',
		  status enum('A','B','C','D') NOT NULL default 'A',
		  cnts int(4) NOT NULL default '1',
		  usedate int(10) unsigned NOT NULL default '0',
		  signdate int(10) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY pid (pid),
		  KEY id (id)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_sms_auto")) {
	$sql =  "
		CREATE TABLE mall_sms_auto (
		  uid int(11) unsigned NOT NULL auto_increment,
		  code varchar(30) binary NOT NULL default '',  
		  title varchar(80) binary NOT NULL default '',  
		  message1 mediumtext NOT NULL default '',
		  message2 mediumtext NOT NULL default '',
		  chk_message1 enum('0','1') not null default '0',
		  chk_message2 enum('0','1') not null default '0',
		  c_only enum('0','1','2') not null default '0',
		  PRIMARY KEY  (uid),
		  KEY code (code)
		)
	";
	$mysql->query($sql);

	############ SMS 자동발송 정보  등록 ####################
	$sql = "
		INSERT INTO `mall_sms_auto` VALUES (8,'soldout','상품품절시 발송','','[{shopName}]\r\n{goodsName}이 {name}님 주문에 의해서 품절되었습니다','','1','1'),(7,'cancel','주문취소시 발송','[{shopName}]\r\n{name}님께서 요청하신 주문이 취소되었습니다!','[{shopName}]\r\n{name}님께서 요청하신 주문이 취소되었습니다!','1','1','0'),(6,'carriage','상품배송시 발송','[{shopName}]\r\n{name}님의 주문상품이 배송되었습니다!','','1','','2'),(5,'pay_ok','입금확인시 발송','[{shopName}]\r\n{name}님의 주문의 입금이 확인되었습니다!','[{shopName}]\r\n{name}님의 주문의 입금이 확인되었습니다!','1','1',''),(3,'order','무통장 주문완료시 발송','[{shopName}]\r\n{name}님의 주문이 완료 되었습니다. 주문번호는 {number}입니다. 감사합니다.','[{shopName}]\r\n{name}고객님이 {price}원 주문하셨습니다.','','',''),(2,'pwsearch','비밀번호찾기시 발송','[{shopName}]\r\n{name}님의 패스워드는 {password}로 변경되었습니다.','','1','','2'),(9,'info','','|||','','0','0','0'),(4,'bank','무통장 입금요청 발송','[{shopName}]\r\n{name}님! {price}원을{account}로 입금부탁드립니다.','','','','2'),(1,'join','회원가입시 발송','[{shopName}]\r\n{name} 회원님의 가입을 진심으로 축하드립니다!!','[{shopName}]\r\n{name}회원님이 신규가입하셨습니다!','1','','0')
	";
	$mysql->query($sql);
}


if(!$mysql->table_list("","mall_sms_list")) {
	$sql =  "
		CREATE TABLE mall_sms_list (
		  uid int(11) unsigned NOT NULL auto_increment,
		  status enum('1','2','3') NOT NULL default '1',  
		  num varchar(30) binary NOT NULL default '',  
		  message mediumtext NOT NULL default '',  
		  succ_cnt int(8) unsigned not null default '0',
		  total_cnt int(8) unsigned not null default '0',
		  err_msg mediumtext NOT NULL default '',
		  LMS enum('N','Y') not null default 'N',
		  localkey text NOT NULL default '',
		  result enum ('1','2') not null default '1',
		  signdate int(10) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY num (num)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_sms_addr")) {
	$sql =  "
		CREATE TABLE mall_sms_addr (
		  uid int(11) unsigned NOT NULL auto_increment,
		  groups varchar(80) binary NOT NULL default '',  
		  name varchar(30) binary NOT NULL default '',  
		  cell varchar(30) binary NOT NULL default '',  
		  sex enum('M','F') NOT NULL default 'M',  
		  memo varchar(250) binary NOT NULL default '',  
		  signdate int(10) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY groups (groups)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_sms_sample")) {
	$sql =  "
		CREATE TABLE mall_sms_sample (
		  uid int(11) unsigned NOT NULL auto_increment,
		  groups varchar(80) binary NOT NULL default '',  
		  title varchar(30) binary NOT NULL default '',  
		  message mediumtext NOT NULL default '',  
		  signdate int(10) unsigned NOT NULL default '0',
		  PRIMARY KEY  (uid),
		  KEY groups (groups)
		)
	";
	$mysql->query($sql);
}


if(!$mysql->table_list("","mall_aconf")) {
	$sql =  "
	CREATE TABLE mall_aconf (
	  uid int(11) unsigned NOT NULL auto_increment,
	  name varchar(255) binary NOT NULL default '',
	  code text NOT NULL default '',
	  mode char(1) binary NOT NULL default '',
	  PRIMARY KEY  (uid),
	  KEY mode (mode)
	)
	";
	$mysql->query($sql);

	$sql = "INSERT INTO `mall_aconf` VALUES (1,'','1|1|1|1|1||||','I'),(2,'','0_2|0_3|0_7|1_3|1_4|3_1|4_3|today','M'),(3,'','','S'),(4,'','1|1|1|1|1|1|1|1|1|1|1||1|1,1,,','T'),(5,'','|9','O'),(6,'','1||customer,counsel|5','B'),(7,'','1||||1|||1||6','G'),(8,'','4|2|10','R'),(9,'','1|1|1|1|1|1|1|1|1|2','A')";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_schedule_date")) {
	$sql =  "
	CREATE TABLE mall_schedule_date (
	  uid int(11) unsigned NOT NULL auto_increment,
	  subject varchar(200) binary NOT NULL default '',
	  date varchar(5) binary NOT NULL default '',
	  sm enum('S','M') NOT NULL default 'S',
	  PRIMARY KEY  (uid),
	  KEY date (date)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_affiliate")) {
	$sql =  "
	CREATE TABLE mall_affiliate (
	  uid int(11) unsigned NOT NULL auto_increment,
	  auth enum('Y','N') NOT NULL default 'N',  
	  id varchar(30) binary NOT NULL default '',
	  passwd varchar(50) binary NOT NULL default '',
	  name varchar(100) binary NOT NULL default '',
	  cell varchar(20) binary NOT NULL default '',
	  email varchar(50) binary NOT NULL default '',	  
	  commission tinyint(2) unsigned NOT NULL default '0',
	  bank_name varchar(20) binary NOT NULL default '',
	  bank_num varchar(20) binary NOT NULL default '',
	  bank_owner varchar(20) binary NOT NULL default '',
	  bank_day varchar(50) binary NOT NULL default '',
	  memo mediumtext NOT NULL default '',	  
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY id (id),
	  KEY name (name)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_affiliate_banner")) {
	$sql =  "
	CREATE TABLE mall_affiliate_banner (
	  uid int(11) unsigned NOT NULL auto_increment,
	  type enum('T','I','F') NOT NULL default 'I',
	  title varchar(100) binary NOT NULL default '',
	  banner varchar(50) binary NOT NULL default '',	  
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY (uid),
	  KEY rank (type)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_affiliate_account")) {
	$sql =  "
	CREATE TABLE mall_affiliate_account (
	  uid int(11) NOT NULL auto_increment,
	  affiliate varchar(30) NOT NULL default '',
	  name varchar(50) NOT NULL default '',
	  a_month varchar(30) NOT NULL default '',
	  a_price int(11) NOT NULL default '0',
	  bank_info varchar(50) NOT NULL default '',
	  dates varchar(30) NOT NULL default '',
	  memo mediumtext NOT NULL default '',
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY affiliate (affiliate)
	)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","pcount_list_affiliate")) {
	$sql =  "
		CREATE TABLE pcount_list_affiliate (
		  uid int(11) unsigned NOT NULL auto_increment,
		  year int(4) unsigned NOT NULL default '0',
		  month int(2) unsigned NOT NULL default '0',
		  day int(2) unsigned NOT NULL default '0',
		  h_00 int(8) unsigned NOT NULL default '0',
		  h_01 int(8) unsigned NOT NULL default '0',
		  h_02 int(8) unsigned NOT NULL default '0',
		  h_03 int(8) unsigned NOT NULL default '0',
		  h_04 int(8) unsigned NOT NULL default '0',
		  h_05 int(8) unsigned NOT NULL default '0',
		  h_06 int(8) unsigned NOT NULL default '0',
		  h_07 int(8) unsigned NOT NULL default '0',
		  h_08 int(8) unsigned NOT NULL default '0',
		  h_09 int(8) unsigned NOT NULL default '0',
		  h_10 int(8) unsigned NOT NULL default '0',
		  h_11 int(8) unsigned NOT NULL default '0',
		  h_12 int(8) unsigned NOT NULL default '0',
		  h_13 int(8) unsigned NOT NULL default '0',
		  h_14 int(8) unsigned NOT NULL default '0',
		  h_15 int(8) unsigned NOT NULL default '0',
		  h_16 int(8) unsigned NOT NULL default '0',
		  h_17 int(8) unsigned NOT NULL default '0',
		  h_18 int(8) unsigned NOT NULL default '0',
		  h_19 int(8) unsigned NOT NULL default '0',
		  h_20 int(8) unsigned NOT NULL default '0',
		  h_21 int(8) unsigned NOT NULL default '0',
		  h_22 int(8) unsigned NOT NULL default '0',
		  h_23 int(8) unsigned NOT NULL default '0',
		  h2_00 int(8) unsigned NOT NULL default '0',
		  h2_01 int(8) unsigned NOT NULL default '0',
		  h2_02 int(8) unsigned NOT NULL default '0',
		  h2_03 int(8) unsigned NOT NULL default '0',
		  h2_04 int(8) unsigned NOT NULL default '0',
		  h2_05 int(8) unsigned NOT NULL default '0',
		  h2_06 int(8) unsigned NOT NULL default '0',
		  h2_07 int(8) unsigned NOT NULL default '0',
		  h2_08 int(8) unsigned NOT NULL default '0',
		  h2_09 int(8) unsigned NOT NULL default '0',
		  h2_10 int(8) unsigned NOT NULL default '0',
		  h2_11 int(8) unsigned NOT NULL default '0',
		  h2_12 int(8) unsigned NOT NULL default '0',
		  h2_13 int(8) unsigned NOT NULL default '0',
		  h2_14 int(8) unsigned NOT NULL default '0',
		  h2_15 int(8) unsigned NOT NULL default '0',
		  h2_16 int(8) unsigned NOT NULL default '0',
		  h2_17 int(8) unsigned NOT NULL default '0',
		  h2_18 int(8) unsigned NOT NULL default '0',
		  h2_19 int(8) unsigned NOT NULL default '0',
		  h2_20 int(8) unsigned NOT NULL default '0',
		  h2_21 int(8) unsigned NOT NULL default '0',
		  h2_22 int(8) unsigned NOT NULL default '0',
		  h2_23 int(8) unsigned NOT NULL default '0',	  
		  total int(8) unsigned NOT NULL default '0',
		  total2 int(8) unsigned NOT NULL default '0',
		  affiliate varchar(30) NOT NULL default '',
		  PRIMARY KEY  (uid),
		  KEY year (year),
		  KEY month (month),
		  KEY day (day),
		  KEY affiliate (affiliate)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","pcount_check_affiliate")) {
	$sql =  "
		CREATE TABLE pcount_check_affiliate (
		  uid int(11) NOT NULL auto_increment,
		  ck_ip varchar(20) NOT NULL default '',
		  ck_date int(8) unsigned NOT NULL default '0',
		  affiliate varchar(30) NOT NULL default '',
		  PRIMARY KEY  (uid),
		  KEY ck_ip (ck_ip),
		  KEY affiliate (affiliate)
		)
	";
	$mysql->query($sql);
}

if(!$mysql->table_list("","pcount_agent_affiliate")) {
	$sql =  "
		CREATE TABLE pcount_agent_affiliate (
		  uid int(11) NOT NULL auto_increment,
		  type enum ('O','B','S','K') NOT NULL default 'O',
		  content varchar(255) NOT NULL default '',	 
		  cnts int(8) unsigned NOT NULL default '0',
		  affiliate varchar(30) NOT NULL default '',
		  PRIMARY KEY  (uid),
		  KEY content (content),
		  KEY type (type)
		)
	";

	$mysql->query($sql);
}

if(!$mysql->table_list("","mall_cooperate")) {
	$sql =  "
	CREATE TABLE mall_cooperate (
	  uid int(11) unsigned NOT NULL auto_increment,
	  guid int(11) unsigned NOT NULL,
	  qty int(11) unsigned NOT NULL default '0',
	  p_option mediumtext NOT NULL default '',
	  op_price varchar(100) binary NOT NULL default '',
	  cell varchar(100) binary NOT NULL default '',
	  email varchar(100) binary NOT NULL default '',
	  status enum('A','B','C','D') NOT NULL default 'A',	
	  signdate int(10) unsigned NOT NULL default '0',
	  PRIMARY KEY  (uid),
	  KEY guid (guid)
	)
	";
	$mysql->query($sql);
}

echo "<b>디비가 성공적으로 생성 되었습니다.</b>";

?>