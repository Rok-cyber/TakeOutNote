<? 
if($TESTMODE==1) {
	$platform = "test";
	$SHOP_ID = "lgdacomxpay";
	$SHOP_KEY = "95160cce09854ef44d2edb2bfb05f9f3";
}
else $platform = "service";

$LGD_NOTEURL			= "{$MAIN}card/lgdacom/note_url.php";          //상점결제결과 처리(DB) 페이지(URL을 변경해 주세요)
$LGD_CASNOTEURL		= "{$MAIN}card/lgdacom/cas_noteurl.php";   

$LGD_MID = (("test" == $platform)?"t":"").$SHOP_ID;
$LGD_CUSTOM_SKIN        = "red"; //상점정의 결제창 스킨 (red, blue, cyan, green, yellow)
$LGD_TIMESTAMP			= date("Ymdhis");
$LGD_HASHDATA = md5($LGD_MID.$order_num.$cash_total.$LGD_TIMESTAMP.$SHOP_KEY);

?>

<script language='javascript'>

function doPay_ActiveX(){
    ret = xpay_check(document.getElementById('LGD_PAYINFO'), '<?= $platform ?>');
 
	if (ret=="00"){     //ActiveX 로딩 성공  
        var LGD_RESPCODE        = dpop.getData('LGD_RESPCODE');       	  //결과코드
        var LGD_RESPMSG         = dpop.getData('LGD_RESPMSG');        	  //결과메세지 
                      
        if( "0000" == LGD_RESPCODE ) { //결제성공
	        var LGD_TID             = dpop.getData('LGD_TID');            //LG텔레콤 거래번호
	        var LGD_OID             = dpop.getData('LGD_OID');            //주문번호 
	        var LGD_PAYTYPE         = dpop.getData('LGD_PAYTYPE');        //결제수단
	        var LGD_PAYDATE         = dpop.getData('LGD_PAYDATE');        //결제일자
	        var LGD_FINANCECODE     = dpop.getData('LGD_FINANCECODE');    //결제기관코드
	        var LGD_FINANCENAME     = dpop.getData('LGD_FINANCENAME');    //결제기관이름        
	        var LGD_FINANCEAUTHNUM  = dpop.getData('LGD_FINANCEAUTHNUM'); //결제사승인번호
	        var LGD_ACCOUNTNUM      = dpop.getData('LGD_ACCOUNTNUM');     //입금할 계좌 (가상계좌)
	        var LGD_BUYER           = dpop.getData('LGD_BUYER');          //구매자명
	        var LGD_PRODUCTINFO     = dpop.getData('LGD_PRODUCTINFO');    //상품명
	        var LGD_AMOUNT          = dpop.getData('LGD_AMOUNT');         //결제금액
            var LGD_NOTEURL_RESULT  = dpop.getData('LGD_NOTEURL_RESULT'); //상점DB처리(LGD_NOTEURL)결과 ('OK':정상,그외:실패)
			var LGD_ESCROWYN		= dpop.getData('LGD_ESCROWYN');  // 에스크로
			var LGD_CARDNOINTYN		= dpop.getData('LGD_CARDNOINTYN');  // 신용카드무이자여부
			var LGD_CARDINSTALLMONTH= dpop.getData('LGD_CARDINSTALLMONTH');  // 할부개월



	        //메뉴얼의 결제결과 파라미터내용을 참고하시어 필요하신 파라미터를 추가하여 사용하시기 바랍니다. 
	                     
            var msg = "결제결과 : " + LGD_RESPMSG + "\n";            
            msg += "LG텔레콤거래TID : " + LGD_TID +"\n";
                                    
            if( LGD_NOTEURL_RESULT != "null" ) msg += LGD_NOTEURL_RESULT +"\n";
            //alert(msg);
 
            document.getElementById('LGD_RESPCODE').value = LGD_RESPCODE;
            document.getElementById('LGD_RESPMSG').value = LGD_RESPMSG;
            document.getElementById('LGD_TID').value = LGD_TID;
            document.getElementById('LGD_OID').value = LGD_OID;
            document.getElementById('LGD_PAYTYPE').value = LGD_PAYTYPE;
            document.getElementById('LGD_PAYDATE').value = LGD_PAYDATE;
            document.getElementById('LGD_FINANCECODE').value = LGD_FINANCECODE;
            document.getElementById('LGD_FINANCENAME').value = LGD_FINANCENAME;
            document.getElementById('LGD_FINANCEAUTHNUM').value = LGD_FINANCEAUTHNUM;
            document.getElementById('LGD_ACCOUNTNUM').value = LGD_ACCOUNTNUM;
            document.getElementById('LGD_BUYER').value = LGD_BUYER;
            document.getElementById('LGD_PRODUCTINFO').value = LGD_PRODUCTINFO;
            document.getElementById('LGD_AMOUNT').value = LGD_AMOUNT;
			document.getElementById('LGD_ESCROWYN').value = LGD_ESCROWYN;
			document.getElementById('LGD_CARDNOINTYN').value = LGD_CARDNOINTYN;
			document.getElementById('LGD_CARDINSTALLMONTH').value = LGD_CARDINSTALLMONTH;

              
            document.getElementById('LGD_PAYINFO').submit();
     
        } else { //결제실패
            alert("결제가 실패하였습니다. " + LGD_RESPMSG);
        }
    } else {
            alert("LG텔레콤 전자결제를 위한 ActiveX 설치 실패");
    }     
}

</script>

<form method="post" id="LGD_PAYINFO" action ="card/lgdacom/payres.php">
	<input type="hidden" name="LGD_MID" value="<?=$LGD_MID?>"/>				  				<!-- 상점아이디 -->
	<input type="hidden" name="LGD_OID" id = 'LGD_OID' value="<?=$order_num?>"/>            <!-- 주문번호 -->
	<input type="hidden" name="LGD_BUYER" id = 'LGD_BUYER' value="<?=$order_name?>"/>      <!-- 구매자 -->
	<input type="hidden" name="LGD_PRODUCTINFO" id = 'LGD_PRODUCTINFO' value="<?=$title?>"/>    <!-- 상품정보 -->
	<input type="hidden" name="LGD_AMOUNT" id = 'LGD_AMOUNT' value="<?=$cash_total?>"/>         <!-- 결제금액 -->
	<input type="hidden" name="LGD_BUYEREMAIL" value="<?=$email?>"/>					<!-- 구매자 이메일 -->
	<input type="hidden" name="LGD_CUSTOM_SKIN" value="<?=$LGD_CUSTOM_SKIN?>"/>			<!-- 결제창 SKIN -->
	<input type="hidden" name="LGD_TIMESTAMP" value="<?=$LGD_TIMESTAMP?>"/>				<!-- 타임스탬프 -->
	<input type="hidden" name="LGD_HASHDATA" value="<?= $LGD_HASHDATA ?>"/> 			<!-- MD5 해쉬암호값 -->
	<input type="hidden" name="LGD_NOTEURL"	value="<?= $LGD_NOTEURL ?>"/>  				<!-- 결제결과 수신페이지 URL --> 
	<input type="hidden" name="LGD_ESCROW_USEYN" value="<?=$EUSE?>"/>					<!-- 에스크로 -->
	<input type="hidden" name="LGD_INSTALLRANGE" value="<?=$halbu?>"/>					<!-- 할부기간 -->
	<input type="hidden" name="LGD_NOINTINF" value="<?=$noint_str?>"/>					<!-- 무이자할부기간 -->	
	<input type="hidden" name="LGD_VERSION" value="PHP_XPay_lite_1.0"/>					<!-- 버전정보 (삭제하지 마세요) -->
	
	<input type="hidden" name="LGD_TID"			    id = 'LGD_TID'              value=""/>
	<input type="hidden" name="LGD_CUSTOM_FIRSTPAY"	id = 'LGD_CUSTOM_FIRSTPAY'  value="<?=$PAYMODE?>"/>	
	<input type="hidden" name="LGD_CUSTOM_USABLEPAY"	id = 'LGD_CUSTOM_USABLEPAY'  value="<?=$PAYMODE?>"/>	
	<input type="hidden" name="LGD_PAYTYPE"	        id = 'LGD_PAYTYPE'		    value=""/>
	<input type="hidden" name="LGD_PAYDATE"	        id = 'LGD_PAYDATE'		    value=""/>
	<input type="hidden" name="LGD_FINANCECODE"	    id = 'LGD_FINANCECODE'		value=""/>
	<input type="hidden" name="LGD_FINANCENAME"	    id = 'LGD_FINANCENAME'		value=""/>
	<input type="hidden" name="LGD_FINANCEAUTHNUM"	id = 'LGD_FINANCEAUTHNUM'	value=""/> 
	<input type="hidden" name="LGD_ACCOUNTNUM"	    id = 'LGD_ACCOUNTNUM'		value=""/>                   
	<input type="hidden" name="LGD_RESPCODE"        id = 'LGD_RESPCODE'         value=""/>
	<input type="hidden" name="LGD_RESPMSG"         id = 'LGD_RESPMSG'          value=""/>
	<input type="hidden" name="LGD_ESCROWYN"        id = 'LGD_ESCROWYN'          value=""/>
	<input type="hidden" name="LGD_CARDNOINTYN"     id = 'LGD_CARDNOINTYN'          value=""/>
	<input type="hidden" name="LGD_CARDINSTALLMONTH" id = 'LGD_CARDINSTALLMONTH'          value=""/>
	
	<? if($cash_type=='V') { ?>
	<input type="hidden" name="LGD_CASNOTEURL" value="<?=$LGD_CASNOTEURL?>"/>  <!-- 가상계좌 NOTEURL -->
	<? } ?>
</form>