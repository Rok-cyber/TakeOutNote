<?
/******************************************************************
* =======================================================
* 클래스: DB프로그래밍을 위한 클래스
*
* 제 작: jinoos Lee (jinoos@korea.com)         
*
* 수 정: gubok kim (email : previl@previl.net,  homepage : http://previl.net)
*
* 제작일: 2002.10.16 
*
* 수정일: 2003.02.11
*        
* =======================================================
******************************************************************/


// require나 include시 중복선언 방지를 위한 부분 
if( !$__PREVIL_MYSQL__ )   
{ 
  $__PREVIL_MYSQL__ = 1; 


class mysqlClass {

	var $dbHost;				//디비 서버           
    var $dbUser;               //디비 유저
    var $dbPass;              //디비 패스워드
    var $dbName;             //디비 네임
    var $debug;                //디버그
    var $con;                   // Connection Resource
    var $res;                    // Result Set Resource
	var $res2;                    // Result Set Resource
    var $query;                // Last use query
    var $affRows;             // affected row
    var $lockTable;           // 락이 걸린 테이블 배열



/*
####################################################################
     ::: 생성자 :::          
     새롭게 인자를 받지 않으면 Conf에 저장되어 있는 정보를 가지고 접속한다.
	 기본적으로 생성만 해놓고.. 대기메모리에 올린다.
####################################################################
*/

    function mysqlClass($host = "", $user = "", $pass = "", $name = "", $debug = "") {
        global $DBConf;
        $this->lockTable = 0;

        if($host)   $this->dbHost = $host;
        else        $this->dbHost = $DBConf['host'];
        if($user)   $this->dbUser = $user;
        else        $this->dbUser = $DBConf['user'];
        if($pass)   $this->dbPass = $pass;
        else        $this->dbPass = $DBConf['passwd'];
        if($name)   $this->dbName = $name;
        else        $this->dbName = $DBConf['database'];
        if($debug)  $this->debug  = $debug;
        else        $this->debug  = $DBConf['debug'];
           
    }



/*
####################################################################
     ::: 환경 재세팅 :::          
     새롭게 인자를 받지 않으면 Conf에 저장되어 있는 정보를 가지고 접속한다.
     클라스내에서 다른 환경으로 세팅
####################################################################
*/

 function setConf($host = "", $user = "", $pass = "", $name = "", $debug = "") {
        global $DBConf;

        if($host)   $this->dbHost = $host;
        else        $this->dbHost = $DBConf['host'];
        if($user)   $this->dbUser = $user;
        else        $this->dbUser = $DBConf['user'];
        if($pass)   $this->dbPass = $pass;
        else        $this->dbPass = $DBConf['passwd'];
        if($name)   $this->dbName = $name;
        else        $this->dbName = $DBConf['database'];
        if($debug)  $this->debug  = $debug;
        else        $this->debug  = $DBConf['debug'];
           
    }


/*
####################################################################
     ::: 디비 연결 :::          
     기존 연결이 존재하면 재 연결 하지 않는다.
####################################################################
*/

    function connect() {

        if(!is_resource($this->con)) {
			$this->con = mysql_connect($this->dbHost, $this->dbUser, $this->dbPass) or $this->errorMsg("con");
        }	
	
        mysql_select_db($this->dbName, $this->con) or $this->errorMsg("name");
		mysql_query("set names utf8",$this->con);
        
    }



/*
####################################################################
     ::: 디비 선택 :::          
     새롭게 인자를 받지 않으면 Conf에 저장되어 있는 디비를 선택한다.
####################################################################
*/

    function select_db($name = "") {

        $this->connect();
        if(!$name) $name = $this->dbName;
        mysql_select_db($name, $this->con) or $this->errorMsg("name");
    
	}


	
/*
####################################################################
     ::: 현재 디비명 리턴 :::          
####################################################################
*/

    function now_db() {

        $this->connect();
        $this->query = "select database()";
        $result      = mysql_query($this->query,$this->con);
        $row         = mysql_fetch_row($result);
        mysql_free_result($result);
        return $row[0];

    }


/*
####################################################################
     ::: 쿼리 실행 :::          
	 쿼리를 실행하고 result 리소스를 객체에 저장한다.
     후에 fetch를 할때 resultSet 을 사용한다.
####################################################################
*/
 
	function query($query,$ck1="",$ck2="") {

        $this->connect();
        $this->query   = $query;
        $this->res     = mysql_query($this->query,$this->con) or $this->errorMsg("que","",$ck1,$ck2);
        $this->affRows = mysql_affected_rows();
         
	}



   function query2($query) {

        $this->connect();
        $this->query   = $query;
        $this->res2 = mysql_query($this->query,$this->con) or $this->errorMsg("que");  
        
	}

/*
####################################################################
     ::: 쿼리 결과 :::          
	 mysql_result 함수와 유사
     row 존재, column 없음 : 해당 Row 를 Array로 Fetch해서 Array로 넘김
     row 없음, column 존재 : column의 내용을 모든 Row에서 뽑아 Array로 넘김
     row 존재, column 존재 : 해당 row의 해당 column의 내용만 뽑아 String로 넘김
     row 없음, column 없음 : 이중배열로 array[row][column]으로 넘김.
####################################################################
*/

	function result($row="", $column ="") {

        if(!$this->res)
        {
            $this->errorMsg("res");
            return false;
        }
        if($row >= $this->affRows) return false;

        $num = mysql_num_rows($this->res);
        
        if(strlen($row)==0 && strlen($column)==0){

            for($i=0;$i<$num;$i++)
            {
                mysql_data_seek($this->res, $i);
                $return_var[$i] = mysql_fetch_assoc($this->res);
            }
            @mysql_free_result($this->res);
            return $return_var;

        }elseif(strlen($row)!=0 && strlen($column)==0){

            mysql_data_seek($this->res, $row);
            $return_var = mysql_fetch_assoc($this->res);
            return $return_var;

        }elseif(strlen($row)==0 && strlen($column)!=0){

            for($i=0;$i<$num;$i++){
                $return_var[$i] = @mysql_result($this->res, $i, $column);
            }
            return $return_var;

        }else{
			return @mysql_result($this->res, $row, $column);
        }
    }


/*
####################################################################
     :::  결과 Fetch 함수 모음:::          
     mysql_fetch 와 유사
####################################################################
*/
	    
    function fetch()
    {   if(!$this->res) return false; return mysql_fetch_assoc($this->res); }

    function fetch_assoc()
    {   if(!$this->res) return false; return mysql_fetch_assoc($this->res); }

    function fetch_row()
    {   if(!$this->res) return false; return mysql_fetch_row($this->res); }

    function fetch_array($no="")
    {   
	  if(!$no) {	
		if(!$this->res) return false; return mysql_fetch_array($this->res); 
	  } else {
        if(!$this->res2) return false; return mysql_fetch_array($this->res2); 
      }
    }

    function fetch_object($no="")
    {   
	  if(!$no) {
		if(!$this->res) return false; return mysql_fetch_object($this->res); 
	  } else {
		if(!$this->res2) return false; return mysql_fetch_object($this->res2); 
	  }
	}
    
	function fetch_seek($no) {
		if(!$this->res) return false; 
		if(!$no) $no=0;
		mysql_data_seek($this->res,$no);
		return mysql_fetch_array($this->res); 
    }   


/*
####################################################################
     :::  쿼리 결과 row수 :::          
     마지막 쿼리로 영향을 받은 row의 수를 리턴
####################################################################
*/
		 
	function affected_rows() {
        
		return $this->affRows;
    
	}

    
/*
####################################################################
     :::  쿼리 + 결과  :::          
     결과 값 row가 1개인 경우 사용
	 칼럼명을 Key로 갖는 배열로 바로 뽑아옴.
	 사용법 : $data = $mysql->one_row($query);
####################################################################
*/
	
    function one_row($query) {

        $this->connect();
        $result = mysql_query($query,$this->con) or $this->errorMsg("que2",$query);
        if(mysql_affected_rows($this->con) == 0) return false;
        $row    = mysql_fetch_assoc($result);
        mysql_free_result($result);
        return($row);
    
	}


	
/*
####################################################################
     :::  쿼리 + 결과  :::          
     결과 값 row가 1개, colomn이 1개인 경우
	 사용법 : $data = $mysql->get_one($query);
####################################################################
*/
	
	function get_one($query) {

        $this->connect();
        $result = mysql_query($query,$this->con) or $this->errorMsg("que2",$query);
        if(mysql_affected_rows($this->con) == 0) return false;
        $row    = mysql_fetch_row($result);
        mysql_free_result($result);
        return($row[0]);
    
	}


/*
####################################################################
     :::  리소스 해제 :::          
     스크립트를 실행하는 동안 너무 많은 메모리를 사용하고 있다고 생각될 때 사용
	 인자로 쓰인 result와 관계된 모든 메모리를 비웁니다.
####################################################################
*/
	    
    function free_result($res = "default") {

        if($res == "default") return @mysql_free_result($this->res);
        else                  return @mysql_free_result($res);
    
	}


/*
####################################################################
     :::  테이블 리스트 :::          
     선택된 디비의 테이블 목록을 리턴 (테이블 존재 확인)
####################################################################
*/

    function table_list($dbName = "",$isTable="")
    {
        $this->connect();
        if(!$dbName) $dbName = $this->dbName;
        $result = mysql_list_tables($dbName,$this->con);
        $num    = mysql_num_rows($result);
        for($i=0; $i<$num; $i++){
            $return[$i] = mysql_tablename($result, $i);
            if($isTable == $return[$i]) return 1;
		}
        mysql_free_result($result);
        if($isTable) return 0;
		else return($return);
    }


/*
####################################################################
     ::: 테이블 락 :::          
     $table 는 Array 로 Key 에 테이블명 , Value에 락타입을 갖는 배열구조
	 사용법 : $mysql->lock($tableLock);
	 ex : $tableLock = array("firstTableName"=>"WRITE","secondTableName"=>"WRITE");
####################################################################
*/

    function lock($table) {

        $this->connect();
        $query = "LOCK TABLES ";
        $i=0;
        while(list($tableName, $lockType) = each($table))
        {
            if($i) $query .= ", ";
            $query .= $tableName." ".$lockType;
            $i++;
        }
        $this->lockTable = 1;
        return $this->query($query,$this->con);
    
	}


/*
####################################################################
     ::: 테이블 언락 :::          
     테이블에 걸린 락을 제거
####################################################################
*/

    function unlock() {

        $this->connect();
        $query = "UNLOCK TABLES";
        $this->lockTable = 0;
        return $this->query($query,$this->con);
    
	}


/*
####################################################################
     ::: 에러 :::          
     mysql_error()을 리턴한다
####################################################################
*/

	 function error() {

        return mysql_error();
    
	}


/*
####################################################################
     ::: Insert number :::          
     insert시 auto_incresement 키의  ID(마지막 번호)를 리턴  
####################################################################
*/

	function InsertNo() { 
           
			return $insert_id=mysql_insert_id($this->con) or $this->errorMsg("que");
           
    } 


/*
####################################################################
     ::: 디비 close :::          
     class의 사용한 메모리를 지우고 디비를 닫는다
####################################################################
*/
	
	function close() {

        if(is_resource($this->con))
        {
            if(is_resource($this->res)) mysql_free_result($this->res);
            if($this->lockTable) $this->unlock();
            return mysql_close($this->con);
        }
        return true;

    }


/*
####################################################################
     ::: 에러 메세지 :::          
     에러 메세지 출력후 종료
####################################################################
*/

    function errorMsg($msg = "",$msg2="",$code="",$d_no="") {
              
             if($code && $d_no) { // 게시판용 삭제 쿼리(등록 에러시)
			     $query = "DELETE FROM pboard_".$code." WHERE no ='$d_no'";
				 mysql_query($query,$this->con) or $this->errorMsg("que");
			 }

			 if($this->debug == "Y") {
				 switch($msg) {
					 case "con" : $str = "Connection 을 할수 없습니다!<br>dbHost : ".$this->dbHost.", dbUser : ".$this->dbUser.", dbPass : ".$this->dbPass;
					 break;
					 case "name" : $str = "디비가 없습니다!<br>dbName : ".$this->dbName;
					 break;
					 case "que" : $str = "쿼리 에러입니다!<br>query : ".$this->query;
					 break;
					 case "que2" : $str = "쿼리 에러입니다!<br>query : ".$msg2;
					 break;
					 case "res" : $str = "결과값이 없습니다!";
					 break;
				 }
			    $str .= "<br>".mysql_error();
			 } else {
				$str = "데이타 베이스 에러입니다!\n 관리자에게 문의 하시기 바랍니다.";
             }			 		     
             header("Content-Type: text/html; charset=utf-8");
			 echo "<style type='text/css'>
					  <!-- 
						font {font-family:굴림; font-size: 9.3pt;}
						font.1 {font-weight:bold;}
						.submit {background-color:rgb(232,232,232); color:#1E1F20; border-width:1; border-color:rgb(130,132,131); border-style:solid; font-family:굴림,Verdana; font-size:9pt; height=20px}</style>
						-->
						</style>";

	          echo ("<br><br><br><br><br>
					  <table width='400' border='0' cellspacing='0' cellpadding='0' align=center style='border-width:2; border-color:gray; border-style:solid;'> 
					  <tr><td width ='400' height = '25' bgcolor=#A8A9A8 align='center'>
					  <font class=1> ERROR </font>
					  </td></tr>
					  <tr><td width ='400' height = '100' align=center valign=middle> 
					  <font>$str</font>
					  <td><tr></table><br>
					  <center><input class=submit type=button value=' Move Back ' onclick=history.back()>
					  "); 
               exit;        //종료

     }


} // End Class

/*

//////////////////////////////////////////////////////////////////////////////////
/// Config (다른 파일에 설정하고 include 하듯이 사용하면 되겠죠..)
//////////////////////////////////////////////////////////////////////////////////
$DBConf['host']         = ":/tmp/mysql.sock";    
$DBConf['database']     = "************";              // 디비명
$DBConf['user']         = "********";                  // 유저명
$DBConf['passwd']       = "****";                      // 비밀번호
$DBConf['debug']        = "Y";                         // 디비에러를 echo 한다. "Y" 
//////////////////////////////////////////////////////////////////////////////////
*/

} // End of if( !$__PREVIL_MYSQL__ ) 