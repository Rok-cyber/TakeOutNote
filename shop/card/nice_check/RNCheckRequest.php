<?php

header("Content-Type: text/html; charset=utf-8");

	$lib_path = "../../lib";
	$inc_path = "../../include";

	require "{$lib_path}/lib.Function.php";
	include "{$inc_path}/dbconn.php";
	require "{$lib_path}/class.Mysql.php";
	$mysql = new mysqlClass();
	
	$sql	= "SELECT code FROM mall_design WHERE mode='Y'";
	$tmp	= $mysql->get_one($sql);
	$confirm	= explode("|*|",$tmp);
	
	if($confirm[0]==0 || !$confirm[1]) alert("실명인증 미사용상태","back");
	if(!$confirm[1] || !$confirm[3]) alert("실명인증 정보 미입력","back");
	
	$sSiteID = $confirm[1];
	$sSitePW = $confirm[3];
	
	$cb_encode_path = "{$_SERVER['DOCUMENT_ROOT']}/card/nice_check/cb_namecheck64";	// cb_namecheck 모듈이 설치된 위치의 절대경로와 cb_namecheck 모듈명까지 입력한다.

	$strJumin= $_POST["jumin1"].$_POST["jumin2"];		// 주민번호
	$strName = iconv("utf-8","euc-kr",$_POST['name']);;		//이름
	
	$iReturnCode  = "";	

								// shell_exec() 와 같은 실행함수 호출부 입니다. 홑따옴표가 아니오니 이점 참고해 주세요.
	$iReturnCode = `$cb_encode_path $sSiteID $sSitePW $strJumin $strName`;	//실행함수 호출하여 iReturnCode 의 변수에 값을 담는다.		
								
								//iReturnCode 변수값에 따라 아래 참고하셔서 처리해주세요.(결과값의 자세한 사항은 리턴코드.txt 파일을 참고해 주세요~)
								//iReturnCode :	1 이면 --> 실명인증 성공 : XXX.php 로 페이지 이동. 
								//							2 이면 --> 실명인증 실패 : 주민과 이름이 일치하지 않음. 사용자가 직접 www.namecheck.co.kr 로 접속하여 등록 or 1600-1522 콜센터로 접수요청.
								//												아래와 같이 나신평에서 제공한 자바스크립트 이용하셔도 됩니다.		
								//							3 이면 --> 나신평 해당자료 없음 : 사용자가 직접 www.namecheck.co.kr 로 접속하여 등록 or 1600-1522 콜센터로 접수요청.
								//												아래와 같이 나신평에서 제공한 자바스크립트 이용하셔도 됩니다.
								//							5 이면 --> 체크썸오류(주민번호생성규칙에 어긋난 경우: 임의로 생성한 값입니다.)
								//							50이면 --> 크레딧뱅크의 명의도용차단 서비스 가입자임 : 직접 명의도용차단 해제 후 실명인증 재시도.
								//												아래와 같이 나신평에서 제공한 자바스크립트 이용하셔도 됩니다.
								//							그밖에 --> 30번대, 60번대 : 통신오류 ip: 203.234.219.72 port: 81~85(5개) 방화벽 관련 오픈등록해준다. 
								//												(결과값의 자세한 사항은 리턴코드.txt 파일을 참고해 주세요~) 

        switch($iReturnCode){
        //실명인증 성공입니다. 업체에 맞게 페이지 처리 하시면 됩니다.
    	case 1:
           
?>
		                             
			<form name="sendForm" method="post" action="../../index.php?channel=regist">
			<input type="hidden" name="jmode" value="new">
			<input type="hidden" name="name" value="<? echo iconv("euc-kr","utf-8",$strName); ?>">
			<input type="hidden" name="jumin" value="<? echo previlEncode(iconv("euc-kr","utf-8",$strName)) ; ?>">
			</form>
			 <script language="javascript">
				document.sendForm.submit();
			 </script> 
     
<?
			break;
			//리턴값 2인 사용자의 경우, www.namecheck.co.kr 의 실명등록확인 또는 02-1600-1522 콜센터로 문의주시기 바랍니다.   			
		case 2:   
?>
            <script language="javascript">
               history.go(-1); 
               var URL ="http://www.creditbank.co.kr/its/its.cb?m=namecheckMismatch"; 
               var status = "toolbar=no,directories=no,scrollbars=no,resizable=no,status=no,menubar=no, width= 640, height= 480, top=0,left=20"; 
               window.open(URL,"",status); 
            </script> 

<?
			break;
			//'리턴값 3인 사용자의 경우, www.namecheck.co.kr 의 실명등록확인 또는 02-1600-1522 콜센터로 문의주시기 바랍니다.   			
		case 3:
?>
            <script language="javascript">
               history.go(-1); 
               var URL ="http://www.creditbank.co.kr/its/its.cb?m=namecheckMismatch"; 
               var status = "toolbar=no,directories=no,scrollbars=no,resizable=no,status=no,menubar=no, width= 640, height= 480, top=0,left=20"; 
               window.open(URL,"",status); 
            </script> 

<?
			break;
			//리턴값 50 명의도용차단 서비스 가입자의 경우, www.creditbank.co.kr 에서 명의도용차단해제 후 재시도 해주시면 됩니다. 
			// 또는 02-1600-1533 콜센터로문의주세요.                                                                             
		case 50;
?>
            <script language="javascript">
               history.go(-1); 
               var URL ="http://www.creditbank.co.kr/its/itsProtect.cb?m=namecheckProtected"; 
               var status = "toolbar=no,directories=no,scrollbars=no,resizable=no,status=no,menubar=no, width= 640, height= 480, top=0,left=20"; 
               window.open(URL,"",status); 
            </script> 

<?
			break;
		default:
		//인증에 실패한 경우는 리턴코드.txt 를 참고하여 리턴값을 확인해 주세요~
?>
		   <script language='javascript'>
				alert("인증에 실패 하였습니다. 리턴코드:[<?=$iReturnCode?>]");
				history.go(-1);
		   </script>
<?
			break;
 }
?>