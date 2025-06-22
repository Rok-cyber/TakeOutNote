<?php

$lib_path = "../../lib";

require "{$lib_path}/lib.Function.php";

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('비정상적인 접금!','back');

    /*
     * [결제결과 화면페이지]
     */

    $LGD_TID               	= $_POST["LGD_TID"];				//LG텔레콤 거래번호
    $LGD_OID                = $_POST["LGD_OID"];				//주문번호
    $LGD_PAYTYPE            = $_POST["LGD_PAYTYPE"];			//결제수단
    $LGD_PAYDATE  			= $_POST["LGD_PAYDATE"];			//결제일자
    $LGD_FINANCECODE        = $_POST["LGD_FINANCECODE"];		//결제기관코드	
    $LGD_FINANCENAME        = $_POST["LGD_FINANCENAME"];		//결제기관이름
    $LGD_FINANCEAUTHNUM     = $_POST["LGD_FINANCEAUTHNUM"];	//결제사승인번호
    $LGD_ACCOUNTNUM         = $_POST["LGD_ACCOUNTNUM"];		//입금할 계좌 (가상계좌)
    $LGD_BUYER              = $_POST["LGD_BUYER"];				//구매자명
    $LGD_PRODUCTINFO        = $_POST["LGD_PRODUCTINFO"];		//상품명
    $LGD_AMOUNT             = $_POST["LGD_AMOUNT"];			//결제금액
    $LGD_RESPCODE           = $_POST["LGD_RESPCODE"];			//결과코드
    $LGD_RESPMSG            = $_POST["LGD_RESPMSG"];			//결과메세지
	$LGD_ESCROWYN			= $_POST["LGD_ESCROWYN"];			//에스크로유무
	$LGD_CARDNOINTYN		= $_POST["LGD_CARDNOINTYN"];		//신용카드무이자여부 1: 무이자, 0:일반
	$LGD_CARDINSTALLMONTH	= $_POST["LGD_CARDINSTALLMONTH"];	//할부개월
 
    if ("0000" == $LGD_RESPCODE) { 	//결제성공시
    	/*
		echo "* Xpay-lite (화면)결과리턴페이지 예제입니다." . "<p>";		 
    	echo "결과코드 : " . $LGD_RESPCODE . "<br>";
    	echo "결과메세지 : " . $LGD_RESPMSG . "<br>";
    	echo "거래번호 : " . $LGD_TID . "<br>";
    	echo "주문번호 : " . $LGD_OID . "<br>";
    	echo "구매자 : " . $LGD_BUYER . "<br>";
    	echo "상품명 : " . $LGD_PRODUCTINFO . "<br>";
    	echo "결제금액 : " . $LGD_AMOUNT . "<br>";
    	echo "결제수단 : " . $LGD_PAYTYPE . "<br>";
    	echo "결제일시 : " . $LGD_PAYDATE . "<br>";
    	echo "결제사코드 : " . $LGD_FINANCECODE . "<br>";
		*/
		movePage("../../index.php?channel=order_end&order_num={$LGD_OID}");

    } else {	 //결제실패시
    	/*
		echo "결제가 실패되었습니다." . "<p>";
    	echo "결과코드 : " . $LGD_RESPCODE . "<br>";
    	echo "결과메세지 : " . $LGD_RESPMSG . "<br>";
		*/
		
		alert("(결제가 실패되었습니다. 결과메세지 : {$LGD_RESPMSG}","../../index.php?channel=card_pay&order_num={$LGD_OID}&ck=2");
    }
 
?>
