<? if($TESTMODE==1) { ?>
<script language='javascript' src='https://pay.kcp.co.kr/plugin/payplus_test_un.js'></script>
<? } else { ?>
<script language='javascript' src='https://pay.kcp.co.kr/plugin/payplus_un.js'></script>
<? } ?>
<!-- ※ 주의!!!
     테스트 결제시 : src='https://pay.kcp.co.kr/plugin/payplus_test.js'
     리얼   결제시 : src='https://pay.kcp.co.kr/plugin/payplus.js'     로 설정해 주시기 바랍니다. -->

<script language='javascript'>

    // 플러그인 설치(확인)
    StartSmartUpdate();

    function  jsf__pay( form )
    {
        if( document.Payplus.object == null )
        {
            openwin = window.open( 'card/kcp/chk_plugin.html', 'chk_plugin', 'width=420, height=100, top=300, left=300' );
        }

        if ( MakePayMessage( form ) == true )
        {
            openwin = window.open( 'card/kcp/proc_win.html', 'proc_win', 'width=449, height=209, top=300, left=300' );
            form.submit();
			openwin.close();
			return  true;
        }
        else
        {
            return  false;
        }		
    }

    function check(){
		jsf__pay(document.order_info);
	}

</script>

<form name="order_info" action="card/kcp/pp_ax_hub.php" method="post">
	<input type="hidden" name="test_mode" value="<?=$TESTMODE?>" />
	<input type="hidden" name="pay_method" value="<?=$PAYMODE?>" />

	<!-- <option value="100000000000">신용카드</option>
    <option value="010000000000">계좌이체</option>
    <option value="001000000000">가상계좌</option>
    <option value="000010000000">휴대폰</option> -->

	<input type="hidden" name='good_name' value='<?=$title?>' />
	<input type="hidden" name='good_mny'  value='<?=$cash_total?>' />
	<input type="hidden" name='buyr_name' value="<?=$order_name?>" />
	<input type="hidden" name='buyr_mail' value='<?=$email?>' />
	<input type="hidden" name='buyr_tel1' value='<?=$order_tel?>' />
	<input type="hidden" name='buyr_tel2' value='' />

	<input type="hidden" name='quotaopt' value="<?=$halbu?>" />
	<input type="hidden" name='site_logo' value="https://testpay.kcp.co.kr/plugin_test/images/shop_logo.gif" />
	

	<!-- 필수 항목 -->
<? if($TESTMODE==1) { ?>
	<!-- 테스트 결제시 : T0000 으로 설정, 리얼 결제시 : 부여받은 사이트코드 입력 -->
	<input type='hidden' name='site_cd'         value='T0000' />
	<!-- http://testpay.kcp.co.kr/Pay/Test/site_key.jsp 로 접속하신후 부여받은 사이트코드를 입력하고 나온 값을 입력하시기 바랍니다. -->	
	<!-- MPI 결제창에서 사용 한글 사용 불가 -->
	<input type='hidden' name='site_name'       value='TEST SHOP' />	
<? } else { ?>
	<input type='hidden' name='site_cd'         value='<?=$SHOP_ID?>' />
	<!-- MPI 결제창에서 사용 한글 사용 불가 -->
	<input type='hidden' name='site_name'       value='<?=$_SERVER["HTTP_HOST"]?>' />	
<? } ?>

	<!-- 주문 번호 (자바 스크립트 샘플(init_orderid()) 참고) -->
	<input type='hidden' name='ordr_idxx'       value='<?=$order_num?>' />

	<!-- 요청종류 승인(pay)/취소,매입(mod) 요청시 사용 -->
	<input type='hidden' name='req_tx'          value='pay' />	
	<!-- 필수 항목 : PULGIN 설정 정보 변경하지 마세요 -->
	<input type='hidden' name='module_type'     value='01' />
	<!-- 필수 항목 : 결제 금액/화폐단위 -->
	<input type='hidden' name='currency'        value='WON' />
	
	<!-- 필수 항목 : PLUGIN에서 값을 설정하는 부분으로 반드시 포함되어야 합니다. ※수정하지 마십시오.-->
	<input type='hidden' name='res_cd'          value='' />
	<input type='hidden' name='res_msg'         value='' />
	<input type='hidden' name='tno'             value='' />
	<input type='hidden' name='trace_no'        value='' />
	<input type='hidden' name='enc_info'        value='' />
	<input type='hidden' name='enc_data'        value='' />
	<input type='hidden' name='ret_pay_method'  value='' />
	<input type='hidden' name='tran_cd'         value='' />
	<input type='hidden' name='bank_name'       value='' />
	<input type='hidden' name='bank_issu'       value='' />
	<input type='hidden' name='use_pay_method'  value='' />


	<!-- 신용카드사 삭제 파라미터 입니다. -->
	<!--input type='hidden' name='not_used_card' value='CCPH:CCSS:CCKE:CCHM:CCSH:CCLO:CCLG:CCJB:CCHN:CCCH'-->
	<!-- 신용카드 결제시 OK캐쉬백 적립 여부를 묻는 창을 설정하는 파라미터 입니다. - 포인트 가맹점의 경우에만 창이 보여집니다.-->
	<input type='hidden' name='save_ocb'        value='Y' />
	<!--무이자 옵션
			※ 설정할부    (가맹점 관리자 페이지에 설정 된 무이자 설정을 따른다)                            - '' 로 세팅
			※ 일반할부    (KCP 이벤트 이외에 설정 된 모든 무이자 설정을 무시한다)                          - 'N' 로 세팅
			※ 무이자 할부 (가맹점 관리자 페이지에 설정 된 무이자 이벤트 중 원하는 무이자 설정을 세팅한다)  - 'Y' 로 세팅-->
	<input type='hidden' name='kcp_noint'       value='<?=$noint?>' />
	<!--무이자 설정
			※ 주의 1 : 할부는 결제금액이 50,000 원 이상일경우에만 가능합니다.
			※ 주의 2 : 무이자 설정값은 무이자 옵션이 Y일 경우에만 결제 창에 적용 됩니다.
			예) 전 카드 2,3,6개월 무이자(국민,비씨,엘지,삼성,신한,현대,롯데,외환) : ALL-02:03:06
			BC 2,3,6개월, 국민 3,6개월, 삼성 6,9개월 무이자 : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09-->
	<input type='hidden' name='kcp_noint_quota' value='<?=$noint_str?>' />


	<!-- 가상계좌 은행 선택 파라미터 입니다. -->
	<!--input type='hidden' name='wish_vbank_list' value='05:03:04:07:11:26:81:71'-->
	<!-- 가상계좌 입금 기한 설정하는 파라미터 입니다. - 발급일 + 3일 -->
	<!--input type='hidden' name='vcnt_expire_term'value='3'-->
	<!-- 가상계좌 입금 시간 설정하는 파라미터 입니다. - 설정을 안하시는경우 기본적으로 23시59분59초가 세팅이 됩니다.-->
	<!--input type='hidden' name='vcnt_expire_term_time' value='235959'-->


	<!-- 복합 포인트 결제시 넘어오는 포인트사 코드 : OK캐쉬백(SCSK), 복지(SCWB) -->
	<input type='hidden' name='epnt_issu'       value='' />
	<!-- 포인트 결제시 복합 결제(신용카드+포인트) 여부를 결정할 수 있습니다.- N 일경우 복합결제 사용안함-->
	<!--<input type="hidden" name="complex_pnt_yn" value="N">-->


	<!-- 현금영수증 등록 창을 보여줄지 여부를 세팅하는 파라미터 입니다. -->
	<input type='hidden' name='disp_tax_yn'     value='Y' />
	<!-- 현금영수증 관련 정보 : PLUGIN 에서 내려받는 정보입니다 -->
	<input type='hidden' name='cash_tsdtime'    value='' />
	<input type='hidden' name='cash_yn'         value='' />
	<input type='hidden' name='cash_authno'     value='' />
	<input type='hidden' name='cash_tr_code'    value='' />
	<input type='hidden' name='cash_id_info'    value='' />

	<!-- 에스크로 사용 여부(필수) : 반드시 Y 로 세팅 -->
	<input type="hidden" name="escw_used"             value="<?=$EUSE?>" />
	<!-- 에스크로 결제처리 모드(필수) : 에스크로: Y, 일반: N, KCP 설정 조건: O -->
	<input type="hidden" name="pay_mod"               value="<?=$EUSE2?>" />
	<!-- 배송 소요일(필수) : 예상 배송 소요일을 입력 -->
	<input type="hidden" name="deli_term"             value="03" />
	<!-- 장바구니 상품 개수(필수) : 장바구니에 담겨있는 상품의 개수를 입력 -->
	<input type="hidden" name="bask_cntx"             value="<?=$GCNT?>" />                 
	<!-- 장바구니 상품 상세 정보 (자바 스크립트 샘플(create_goodInfo()) 참고) -->
	<input type="hidden" name="good_info" value="" />

	<!-- 에스크로정보 : 에스크로 사용업체에 적용되는 정보입니다. -->
	<input type="hidden" name="rcvr_name" value="<?=$rece_name?>" /><!-- 수취인 이름 -->
	<input type="hidden" name="rcvr_tel1" value="<?=$rece_tel?>" /><!-- 수취인 전화번호 -->
	<input type="hidden" name="rcvr_tel2" value="<?=$rece_cell?>" /><!-- 수취친 휴대폰번호 -->
	<input type="hidden" name="rcvr_mail" value="" /><!-- 수취인 E-Mail -->
	<input type="hidden" name="rcvr_zipx" value="<?=$rece_zip?>" /><!-- 수취인 우편번호 -->
	<input type="hidden" name="rcvr_add1" value="<?=$rece_addr?>" /><!-- 수취인 주소 -->
	<input type="hidden" name="rcvr_add2" value="<?=$rece_addr?>" /><!-- 수취인 상세 주소 -->



	<!-- 교통카드 테스트용 파라미터 (교통카드 테스트 시에만 이용하시기 바랍니다.) -->
	<input type='hidden' name='test_flag' value='' />
</form>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function create_goodInfo()
    {
        var chr30 = String.fromCharCode(30);    // ASCII Code 30
        var chr31 = String.fromCharCode(31);    // ASCII Code 31

		<?php
			$sql = "SELECT p_name, p_price, p_qty FROM mall_order_goods WHERE order_num='{$order_num}'";
			$mysql->query($sql);

			$good_info = 'var good_info = ';
			$no = 1;
			while($row = $mysql->fetch_array()) {
				if ($no != 1) {
					$good_info .= ' + chr30 +';
				}
				$good_info .= '"seq='.$no.'" + chr31';
				$good_info .= ' + "ordr_numb='.$order_num.'"';
				$good_info .= ' + chr31 + "good_name='.$row['p_name'].'"';
				$good_info .= ' + chr31 + "good_cntx='.$row['p_qty'].'"';
				$good_info .= ' + chr31 + "good_amtx='.$row['p_price'].'"';
				$no++;
			}
			$good_info .= ";";
			echo $good_info;
		?>
        document.order_info.good_info.value = good_info;
    }

	create_goodInfo();
//-->
</SCRIPT>