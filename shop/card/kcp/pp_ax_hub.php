<?
    /* ============================================================================== */
    /* =   PAGE : 지불 요청 및 결과 처리 PAGE                                       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2006   KCP Inc.   All Rights Reserved.                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://testpay.kcp.co.kr/pgsample/FAQ/search_error.jsp       = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   01. 지불 데이터 셋업 (업체에 맞게 수정)                                  = */
    /* = -------------------------------------------------------------------------- = */
    /* =   1. $g_conf_home_dir 설정                                                 = */
    /* =       pp_cli 파일이 존재하는 bin 디렉토리의 절대경로 입력                  = */
    /* =       (bin디렉토리 전까지의 절대경로 입력)                                 = */
    /* =                                                                            = */
    /* =   2. $g_conf_pa_url 설정                                                   = */
    /* =       테스트/실 결제 서버를 설정합니다.                                    = */
    /* =       테스트시 : testpaygw.kcp.co.kr                                       = */    
    /* =       실결제시 : paygw.kcp.co.kr                                           = */
    /* = -------------------------------------------------------------------------- = */
    
	$lib_path = "../../lib";
	$inc_path = "../../include";

	require "{$lib_path}/lib.Function.php";
	include "{$inc_path}/dbconn.php";
	require "{$lib_path}/class.Mysql.php";
	$mysql = new mysqlClass();

	$sql = "SELECT code FROM mall_design WHERE mode='B'";
	$tmp_cash = $mysql->get_one($sql);
	$cash = explode("|*|",stripslashes($tmp_cash));
	
	$g_conf_home_dir  = "{$_SERVER['DOCUMENT_ROOT']}/card/kcp/payplus"; // BIN 절대경로 입력
    if($_POST['test_mode']==1) {
		$g_conf_pa_url    = "testpaygw.kcp.co.kr";    
		$site_key = "3grptw1.zW0GSo4PQdaGvsF__";
	}
	else {
		$g_conf_pa_url    = "paygw.kcp.co.kr"; 
		$site_key = trim($cash[15]);
	}

    /* = -------------------------------------------------------------------------- = */    
    /* =   아래 부분은 변경하지 마십시오.                                           = */    
    /* = -------------------------------------------------------------------------- = */    
    $g_conf_pa_port   = "8090";                   // 포트번호 , 변경불가
    $g_conf_mode      = 0;                        // 변경불가
    $g_conf_log_level = "3";                      // 변경불가
    /* = -------------------------------------------------------------------------- = */
    require "pp_ax_hub_lib.php";                  // library [수정불가]
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   02. 지불 요청 정보 설정                                                  = */
    /* = -------------------------------------------------------------------------- = */
    $site_cd        = $_POST[ "site_cd"        ]; // 사이트 코드
    //$site_key       = $_POST[ "site_key"       ]; // 사이트 키
    $req_tx         = $_POST[ "req_tx"         ]; // 요청 종류
    $cust_ip        = getenv( "REMOTE_ADDR"    ); // 요청 IP
    $ordr_idxx      = $_POST[ "ordr_idxx"      ]; // 쇼핑몰 주문번호
    $good_name      = $_POST[ "good_name"      ]; // 상품명
    /* = -------------------------------------------------------------------------- = */
    $good_mny       = $_POST[ "good_mny"       ]; // 결제 총금액
    $tran_cd        = $_POST[ "tran_cd"        ]; // 처리 종류
    /* = -------------------------------------------------------------------------- = */
    $res_cd         = "";                         // 응답코드
    $res_msg        = "";                         // 응답메시지
    $tno            = $_POST[ "tno"            ]; // KCP 거래 고유 번호
    /* = -------------------------------------------------------------------------- = */
    $buyr_name      = $_POST[ "buyr_name"      ]; // 주문자명
    $buyr_tel1      = $_POST[ "buyr_tel1"      ]; // 주문자 전화번호
    $buyr_tel2      = $_POST[ "buyr_tel2"      ]; // 주문자 핸드폰 번호
    $buyr_mail      = $_POST[ "buyr_mail"      ]; // 주문자 E-mail 주소
    /* = -------------------------------------------------------------------------- = */
    $bank_name      = "";                         // 은행명
    $bank_code      = "";                         // 은행코드
    $bank_issu      = $_POST[ "bank_issu"      ]; // 계좌이체 서비스사
    /* = -------------------------------------------------------------------------- = */
    $mod_type       = $_POST[ "mod_type"       ]; // 변경TYPE VALUE 승인취소시 필요
    $mod_desc       = $_POST[ "mod_desc"       ]; // 변경사유
    /* = -------------------------------------------------------------------------- = */
    $use_pay_method = $_POST[ "use_pay_method" ]; // 결제 방법
    $epnt_issu      = $_POST[ "epnt_issu"      ]; //포인트(OK캐쉬백,복지포인트)
    $bSucc          = "";                         // 업체 DB 처리 성공 여부
    $acnt_yn        = $_POST[  "acnt_yn"       ]; // 상태변경시 계좌이체, 가상계좌 여부
    /* = -------------------------------------------------------------------------- = */
    $escw_used      = $_POST[  "escw_used"     ]; // 에스크로 사용 여부
    $pay_mod        = $_POST[  "pay_mod"       ]; // 에스크로 결제처리 모드
    $deli_term      = $_POST[  "deli_term"     ]; // 배송 소요일
    $bask_cntx      = $_POST[  "bask_cntx"     ]; // 장바구니 상품 개수
    $good_info      = $_POST[  "good_info"     ]; // 장바구니 상품 상세 정보
    $rcvr_name      = $_POST[  "rcvr_name"     ]; // 수취인 이름
    $rcvr_tel1      = $_POST[  "rcvr_tel1"     ]; // 수취인 전화번호
    $rcvr_tel2      = $_POST[  "rcvr_tel2"     ]; // 수취인 휴대폰번호
    $rcvr_mail      = $_POST[  "rcvr_mail"     ]; // 수취인 E-Mail
    $rcvr_zipx      = $_POST[  "rcvr_zipx"     ]; // 수취인 우편번호
    $rcvr_add1      = $_POST[  "rcvr_add1"     ]; // 수취인 주소
    $rcvr_add2      = $_POST[  "rcvr_add2"     ]; // 수취인 상세주소
    /* = -------------------------------------------------------------------------- = */
    $card_cd        = "";                         // 신용카드 코드
    $card_name      = "";                         // 신용카드 명
    $app_time       = "";                         // 승인시간 (모든 결제 수단 공통)
    $app_no         = "";                         // 신용카드 승인번호
    $noinf          = "";                         // 신용카드 무이자 여부
    $quota          = "";                         // 신용카드 할부개월
    $bankname       = "";                         // 은행명
    $depositor      = "";                         // 입금 계좌 예금주 성명
    $account        = "";                         // 입금 계좌 번호
    /* = -------------------------------------------------------------------------- = */
    $amount         = "";                         // KCP 실제 거래 금액
    /* = -------------------------------------------------------------------------- = */
    $add_pnt        = "";                         // 발생 포인트
    $use_pnt        = "";                         // 사용가능 포인트
    $rsv_pnt        = "";                         // 적립 포인트
    $pnt_app_time   = "";                         // 승인시간
    $pnt_app_no     = "";                         // 승인번호
    $pnt_amount     = "";                         // 적립금액 or 사용금액
    /* = -------------------------------------------------------------------------- = */
    $cash_yn        = $_POST[ "cash_yn"        ]; // 현금영수증 등록 여부
    $cash_authno    = "";                         // 현금 영수증 승인 번호
    $cash_tr_code   = $_POST[ "cash_tr_code"   ]; // 현금 영수증 발행 구분
    $cash_id_info   = $_POST[ "cash_id_info"   ]; // 현금 영수증 등록 번호
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   03. 인스턴스 생성 및 초기화                                              = */
    /* = -------------------------------------------------------------------------- = */
    /* =       결제에 필요한 인스턴스를 생성하고 초기화 합니다.                     = */
    /* = -------------------------------------------------------------------------- = */
    $c_PayPlus = new C_PP_CLI;

    $c_PayPlus->mf_clear();
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   04. 처리 요청 정보 설정, 실행                                            = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   04-1. 승인 요청                                                          = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
        if ( $bank_issu == "SCOB" ) // 동방시스템 계좌이체 시
        {
            $tran_cd = "00200000";

            $c_PayPlus->mf_set_modx_data( "tno",           $tno       ); // KCP 원거래 거래번호
            $c_PayPlus->mf_set_modx_data( "mod_type",      "STAQ"     ); // 원거래 변경 요청 종류
            $c_PayPlus->mf_set_modx_data( "mod_ip",        $cust_ip   ); // 변경 요청자 IP
            $c_PayPlus->mf_set_modx_data( "mod_ordr_idxx", $ordr_idxx ); // 주문번호
        }
        else
        {
            $c_PayPlus->mf_set_encx_data( $_POST[ "enc_data" ], $_POST[ "enc_info" ] );
        }
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   04-2. 취소/매입 요청                                                     = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod" )
    {
        $tran_cd = "00200000";

        $c_PayPlus->mf_set_modx_data( "tno",      $tno      ); // KCP 원거래 거래번호
        $c_PayPlus->mf_set_modx_data( "mod_type", $mod_type ); // 원거래 변경 요청 종류
        $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip  ); // 변경 요청자 IP
        $c_PayPlus->mf_set_modx_data( "mod_desc", $mod_desc ); // 변경 사유
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   04-3. 에스크로 상태변경 요청                                              = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod_escrow" )
    {
        $tran_cd = "00200000";

        $c_PayPlus->mf_set_modx_data( "tno",        $tno            );          // KCP 원거래 거래번호
        $c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );          // 원거래 변경 요청 종류
        $c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );          // 변경 요청자 IP
        $c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );          // 변경 사유
        if ($mod_type == "STE1")                                                // 상태변경 타입이 [배송요청]인 경우
        {
            $c_PayPlus->mf_set_modx_data( "deli_numb",   $_POST[ "deli_numb" ] );          // 운송장 번호
            $c_PayPlus->mf_set_modx_data( "deli_corp",   $_POST[ "deli_corp" ] );          // 택배 업체명
        }
        else if ($mod_type == "STE2" || $mod_type == "STE4")                    // 상태변경 타입이 [즉시취소] 또는 [취소]인 계좌이체, 가상계좌의 경우
        {
            if ($acnt_yn == "Y")
            {
                $c_PayPlus->mf_set_modx_data( "refund_account",   $_POST[ "refund_account" ] );      // 환불수취계좌번호
                $c_PayPlus->mf_set_modx_data( "refund_nm",        $_POST[ "refund_nm"      ] );      // 환불수취계좌주명
                $c_PayPlus->mf_set_modx_data( "bank_code",        $_POST[ "bank_code"      ] );      // 환불수취은행코드
            }
        }
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   04-4. 실행                                                               = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tran_cd != "" )
    {
        $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $site_cd, $site_key, $tran_cd, "",
                              $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx,
                              $cust_ip, $g_conf_log_level, 0, $g_conf_mode );
    }
    else
    {
        $c_PayPlus->m_res_cd  = "9562";
        $c_PayPlus->m_res_msg = "연동 오류 TRAN_CD[" . $tran_cd . "]";
    }

    $res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
    $res_msg = $c_PayPlus->m_res_msg; // 결과 메시지
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   05. 승인 결과 처리                                                       = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
        if( $res_cd == "0000" )
        {
            $tno     = $c_PayPlus->mf_get_res_data( "tno"    );  // KCP 거래 고유 번호
            $amount  = $c_PayPlus->mf_get_res_data( "amount" );  // KCP 실제 거래 금액
            $escw_yn = $c_PayPlus->mf_get_res_data( "escw_yn" ); // 에스크로 여부

    /* = -------------------------------------------------------------------------- = */
    /* =   05-1. 신용카드 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "100000000000" )
            {
                $card_cd   = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드 코드
                $card_name = iconv("euc-kr","utf-8",$c_PayPlus->mf_get_res_data( "card_name" )); // 카드 종류
                $app_time  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
                $app_no    = $c_PayPlus->mf_get_res_data( "app_no"    ); // 승인 번호
                $noinf     = $c_PayPlus->mf_get_res_data( "noinf"     ); // 무이자 여부 ( 'Y' : 무이자 )
                $quota     = $c_PayPlus->mf_get_res_data( "quota"     ); // 할부 개월

                /* = -------------------------------------------------------------- = */
                /* =   05-1.1. 복합결제(포인트+신용카드) 승인 결과 처리               = */
                /* = -------------------------------------------------------------- = */
                if ( $epnt_issu == "SCSK" || $epnt_issu == "SCWB" )
                {
                    $pnt_amount   = $c_PayPlus->mf_get_res_data ( "pnt_amount"   );
	                $pnt_app_time = $c_PayPlus->mf_get_res_data ( "pnt_app_time" );
	                $pnt_app_no   = $c_PayPlus->mf_get_res_data ( "pnt_app_no"   );
	                $add_pnt      = $c_PayPlus->mf_get_res_data ( "add_pnt"      );
                    $use_pnt      = $c_PayPlus->mf_get_res_data ( "use_pnt"      );
                    $rsv_pnt      = $c_PayPlus->mf_get_res_data ( "rsv_pnt"      );
                }
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-2. 계좌이체 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "010000000000" )
            {
                $bank_name = iconv("euc-kr","utf-8",$c_PayPlus->mf_get_res_data( "bank_name"  ));  // 은행명
                $bank_code = $c_PayPlus->mf_get_res_data( "bank_code"  );  // 은행코드
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-3. 가상계좌 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "001000000000" )
            {
                $bankname  = iconv("euc-kr","utf-8",$c_PayPlus->mf_get_res_data( "bankname"  )); // 입금할 은행 이름
                $depositor = iconv("euc-kr","utf-8",$c_PayPlus->mf_get_res_data( "depositor" )); // 입금할 계좌 예금주
                $account   = $c_PayPlus->mf_get_res_data( "account"   ); // 입금할 계좌 번호
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-4. 포인트 승인 결과 처리                                               = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000100000000" )
            {
                $pnt_amount   = $c_PayPlus->mf_get_res_data( "pnt_amount"   );
	            $pnt_app_time = $c_PayPlus->mf_get_res_data( "pnt_app_time" );
	            $pnt_app_no   = $c_PayPlus->mf_get_res_data( "pnt_app_no"   );
	            $add_pnt      = $c_PayPlus->mf_get_res_data( "add_pnt"      );
                $use_pnt      = $c_PayPlus->mf_get_res_data( "use_pnt"      );
                $rsv_pnt      = $c_PayPlus->mf_get_res_data( "rsv_pnt"      );
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-5. 휴대폰 승인 결과 처리                                              = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000010000000" )
            {
                $app_time = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // 승인 시간
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-6. 상품권 승인 결과 처리                                              = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000000001000" )
            {
                $app_time = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // 승인 시간
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-7. 티머니 승인 결과 처리                                              = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000000000100" )
            {
                $app_time = $c_PayPlus->mf_get_res_data("app_time"      ); // 승인시간
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-8. ARS 승인 결과 처리                                                 = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000000000010" )
            {
                $app_time = $c_PayPlus->mf_get_res_data( "ars_app_time" ); // 승인 시간
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-9. 현금영수증 결과 처리                                               = */
    /* = -------------------------------------------------------------------------- = */
            if ( $cash_yn == "Y" )
            {
                $cash_authno  = $c_PayPlus->mf_get_res_data( "cash_authno"  ); // 현금 영수증 승인 번호
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-10. 승인 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
    /* = -------------------------------------------------------------------------- = */
    /* =         승인 결과를 DB 작업 하는 과정에서 정상적으로 승인된 건에 대해      = */
    /* =         DB 작업을 실패하여 DB update 가 완료되지 않은 경우, 자동으로       = */
    /* =         승인 취소 요청을 하는 프로세스가 구성되어 있습니다.                = */
    /* =         DB 작업이 실패 한 경우, bSucc 라는 변수(String)의 값을 "false"     = */
    /* =         로 세팅해 주시기 바랍니다. (DB 작업 성공의 경우에는 "false" 이외의 = */
    /* =         값을 세팅하시면 됩니다.)                                           = */
    /* =         amount(KCP실제 거래금액)과 업체가 DB 처리하실 금액이 다를 경우의   = */
    /* =         비교 루틴을 추가 하셔서 다를 경우 마찬가지로 "false"로 셋팅하여    = */
    /* =         주시길 바랍니다.                                                   = */
    /* = -------------------------------------------------------------------------- = */
            $bSucc = $bSucc2 = "";
			
			$sql = "SELECT pay_total, pay_status FROM mall_order_info WHERE order_num = '{$ordr_idxx}'";
			$order_infos = $mysql->one_row($sql);

			$pay_total = $order_infos['pay_total'];
			if($pay_total != $good_mny) {
				$bSucc = "false"; // DB 작업 실패 또는 금액 불일치의 경우 "false" 로 세팅						
			}

			if($order_infos['pay_status']=='B') { 
				$bSucc = "false"; // DB 작업 실패 또는 이미 결제완료일 경우 "false" 로 세팅										
			}			

    /* = -------------------------------------------------------------------------- = */
    /* =   05-11. DB 작업 실패일 경우 자동 승인 취소                                 = */
    /* = -------------------------------------------------------------------------- = */
            if ( $bSucc == "false" )
            {
                $c_PayPlus->mf_clear();

                $bSucc_mod_type = ""; // 즉시 취소 시 사용하는 mod_type

                if ( $escw_yn == "Y" && $use_pay_method == "001000000000" ) {
                    $bSucc_mod_type = "STE5"; // 에스크로 가상계좌 건의 경우 가상계좌 발급취소(STE5)
                }
                else if ( $escw_yn == "Y" ) {
                    $bSucc_mod_type = "STE2"; // 에스크로 가상계좌 이외 건은 즉시취소(STE2)
                }
                else {
                    $bSucc_mod_type = "STSC"; // 에스크로 거래 건이 아닌 경우(일반건)(STSC)
                }

                $tran_cd = "00200000";

                $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
                $c_PayPlus->mf_set_modx_data( "mod_type", $bSucc_mod_type              );  // 원거래 변경 요청 종류
                $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
                $c_PayPlus->mf_set_modx_data( "mod_desc", "결과 처리 오류 - 자동 취소" );  // 변경 사유

                $c_PayPlus->mf_do_tx( "",  $g_conf_home_dir, $site_cd,
                                      $site_key,  $tran_cd,    "",
                                      $g_conf_pa_url,  $g_conf_pa_port,  "payplus_cli_slib",
                                      $ordr_idxx, $cust_ip,    $g_conf_log_level,
                                      0,    $g_conf_mode );

                $res_cd  = $c_PayPlus->m_res_cd;
                $res_msg = $c_PayPlus->m_res_msg;

				movePage("../../index.php?channel=card_pay&order_num={$ordr_idxx}&ck=2");
            }
			else {
				$adds = '';
				switch($use_pay_method) {
					case "100000000000" : 
						if($noinf=='Y') $adds = ',무이자';					
						if($quota=='00') $quota = '일시불';
						$card_info = "{$card_name} ($quota{$adds}), 승인시간 : {$app_time}, 승인번호 : {$app_no}, 거래번호 : {$tno}";		
					break;
					case "010000000000" : 
						$card_info = "거래번호 : {$tno}, 은행명 : {$bank_name}, 은행코드 : {$bank_code}";		
					break;
					case "001000000000" : 
						$card_info = "거래번호 : {$tno}, 입금은행명 : {$bankname}, 입금예금주 : {$depositor}, 입금계좌번호 : {$account}";		
					break;
					case "000010000000" : 
						$card_info = "거래번호 : {$tno}, 승인시간 : {$app_time}";		
					break;
				}
						
				if($use_pay_method=="001000000000") {
					$sql = "UPDATE mall_order_info SET pay_status='A' , pay_info='{$card_info}', pay_number='{$tno}',escrow='{$escw_used}' WHERE order_num = '{$ordr_idxx}'";
					mysql_query($sql,$mysql->con) or $bSucc = "false";				
				}
				else {
					$sql = "UPDATE mall_order_info SET pay_status='B' , pay_info='{$card_info}', order_status='B', pay_number='{$tno}',escrow='{$escw_used}' WHERE order_num = '{$ordr_idxx}'";
					mysql_query($sql,$mysql->con) or $bSucc = "false";				
					
					$signdate = date("Y-m-d H:i:s",time());
					$sql = "UPDATE mall_order_goods SET  order_status = 'B', status_date = '{$signdate}' WHERE order_num='{$ordr_idxx}' && order_status = 'A'";
					mysql_query($sql,$mysql->con) or $bSucc2 = "false";		
				}

				if($bSucc2=='false') {
					$mysql->query($sql);
				}
			}

        } // End of [res_cd = "0000"]

    /* = -------------------------------------------------------------------------- = */
    /* =   05-12. 승인 실패를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
    /* = -------------------------------------------------------------------------- = */
        else
        {

			$card_info = "거래번호 : {$tno}, 결제금액 : {$good_mny}, 결과 메세지 : {$res_msg}";

			$sql = "UPDATE mall_order_info SET pay_status='C' , pay_info = '{$card_info}', pay_number='{$tno}', escrow='{$escw_used}' WHERE order_num = '{$ordr_idxx}'";
			$mysql->query($sql);
			movePage("../../index.php?channel=card_pay&order_num={$ordr_idxx}&ck=2");
        }
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   06. 취소/매입 결과 처리                                                  = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod" )
    {
    } // End of Process
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   07. 에스크로 상태변경 결과 처리                                          = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod_escrow" )
    {
    } // End of Process
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   07. 폼 구성 및 결과페이지 호출                                           = */
    /* ============================================================================== */

	movePage("../../index.php?channel=order_end&order_num={$ordr_idxx}");

  /*
        <input type="hidden" name="req_tx"            value="<?=$req_tx?>">            <!-- 요청 구분 -->
        <input type="hidden" name="use_pay_method"    value="<?=$use_pay_method?>">    <!-- 사용한 결제 수단 -->
        <input type="hidden" name="bSucc"             value="<?=$bSucc?>">             <!-- 쇼핑몰 DB 처리 성공 여부 -->

        <input type="hidden" name="res_cd"            value="<?=$res_cd?>">            <!-- 결과 코드 -->
        <input type="hidden" name="res_msg"           value="<?=$res_msg?>">           <!-- 결과 메세지 -->
        <input type="hidden" name="ordr_idxx"         value="<?=$ordr_idxx?>">         <!-- 주문번호 -->
        <input type="hidden" name="tno"               value="<?=$tno?>">               <!-- KCP 거래번호 -->
        <input type="hidden" name="good_mny"          value="<?=$good_mny?>">          <!-- 결제금액 -->
        <input type="hidden" name="good_name"         value="<?=$good_name?>">         <!-- 상품명 -->
        <input type="hidden" name="buyr_name"         value="<?=$buyr_name?>">         <!-- 주문자명 -->
        <input type="hidden" name="buyr_tel1"         value="<?=$buyr_tel1?>">         <!-- 주문자 전화번호 -->
        <input type="hidden" name="buyr_tel2"         value="<?=$buyr_tel2?>">         <!-- 주문자 휴대폰번호 -->
        <input type="hidden" name="buyr_mail"         value="<?=$buyr_mail?>">         <!-- 주문자 E-mail -->

        <input type="hidden" name="escw_yn"           value="<?=$escw_yn?>">         <!-- 에스크로 사용 여부 -->
        <input type="hidden" name="pay_mod"           value="<?=$pay_mod?>">           <!-- 에스크로 결제처리 모드 -->
        <input type="hidden" name="deli_term"         value="<?=$deli_term?>">         <!-- 배송 소요일 -->
        <input type="hidden" name="bask_cntx"         value="<?=$bask_cntx?>">         <!-- 장바구니 상품 개수 -->
        <input type="hidden" name="good_info"         value="<?=$good_info?>">         <!-- 장바구니 상품 상세 정보 -->
        <input type="hidden" name="rcvr_name"         value="<?=$rcvr_name?>">         <!-- 수취인 이름 -->
        <input type="hidden" name="rcvr_tel1"         value="<?=$rcvr_tel1?>">         <!-- 수취인 전화번호 -->
        <input type="hidden" name="rcvr_tel2"         value="<?=$rcvr_tel2?>">         <!-- 수취인 휴대폰번호 -->
        <input type="hidden" name="rcvr_mail"         value="<?=$rcvr_mail?>">         <!-- 수취인 E-Mail -->
        <input type="hidden" name="rcvr_zipx"         value="<?=$rcvr_zipx?>">         <!-- 수취인 우편번호 -->
        <input type="hidden" name="rcvr_add1"         value="<?=$rcvr_add1?>">         <!-- 수취인 주소 -->
        <input type="hidden" name="rcvr_add2"         value="<?=$rcvr_add2?>">         <!-- 수취인 상세주소 -->

        <input type="hidden" name="card_cd"           value="<?=$card_cd?>">           <!-- 카드코드 -->
        <input type="hidden" name="card_name"         value="<?=$card_name?>">         <!-- 카드명 -->
        <input type="hidden" name="app_time"          value="<?=$app_time?>">          <!-- 승인시간 -->
        <input type="hidden" name="app_no"            value="<?=$app_no?>">            <!-- 승인번호 -->
        <input type="hidden" name="quota"             value="<?=$quota?>">             <!-- 할부개월 -->

        <input type="hidden" name="bank_name"         value="<?=$bank_name?>">         <!-- 은행명 -->
        <input type="hidden" name="bank_code"         value="<?=$bank_code?>">         <!-- 은행코드 -->

        <input type="hidden" name="bankname"          value="<?=$bankname?>">          <!-- 입금 은행 -->
        <input type="hidden" name="depositor"         value="<?=$depositor?>">         <!-- 입금계좌 예금주 -->
        <input type="hidden" name="account"           value="<?=$account?>">           <!-- 입금계좌 번호 -->

        <input type="hidden" name="epnt_issu"         value="<?=$epnt_issu?>">         <!-- 포인트 서비스사 -->
        <input type="hidden" name="pnt_app_time"      value="<?=$pnt_app_time?>">      <!-- 승인시간 -->
        <input type="hidden" name="pnt_app_no"        value="<?=$pnt_app_no?>">        <!-- 승인번호 -->
        <input type="hidden" name="pnt_amount"        value="<?=$pnt_amount?>">        <!-- 적립금액 or 사용금액 -->
        <input type="hidden" name="add_pnt"           value="<?=$add_pnt?>">           <!-- 발생 포인트 -->
        <input type="hidden" name="use_pnt"           value="<?=$use_pnt?>">           <!-- 사용가능 포인트 -->
        <input type="hidden" name="rsv_pnt"           value="<?=$rsv_pnt?>">           <!-- 적립 포인트 -->

        <input type="hidden" name="cash_yn"           value="<?=$cash_yn?>">            <!-- 현금영수증 등록 여부 -->
        <input type="hidden" name="cash_authno"       value="<?=$cash_authno?>">        <!-- 현금 영수증 승인 번호 -->
        <input type="hidden" name="cash_tr_code"      value="<?=$cash_tr_code?>">       <!-- 현금 영수증 발행 구분 -->
        <input type="hidden" name="cash_id_info"      value="<?=$cash_id_info?>">       <!-- 현금 영수증 등록 번호 -->
   */
   ?>