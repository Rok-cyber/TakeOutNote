<?
/******************************************************************
* =======================================================
* 클래스: 소켓을 이용한 SMTP 클래스
*
* 제 작 : 하근호 
*
* 수 정 : gubok kim (email : previl@previl.net,  homepage : http://previl.net)
*
* 제작일: 2003.02.12 
*
* =======================================================
******************************************************************/

class classSmtp
{

    var $host;                  // SMTP host
	var $port;                  // SMTP port
    var $fp;
    var $self;
    var $lastmsg;
    var $parts;
    var $error;
    var $debug;
    var $charset;
    var $ctype;
	var $tmp_server;

       
/*
####################################################################
     ::: 생성자 :::          
     새롭게 인자를 받지 않으면 Conf에 저장되어 있는 정보를 가지고 접속한다.
####################################################################
*/

		function classSmtp($host="localhost", $port="25") {

            if($host == "self") $this->self = true;
            else $this->host = $host;
            $this->port=$port;
			$this->parts = array();
            $this->error = array();
			$this->debug = 0;
            $this->charset = "euc-kr";
            $this->ctype = "text/html";
			$this->ctype2 = "";
    	 }
       

/*
####################################################################
     :::  debug :::          
      디버그 모드 : 1
####################################################################
*/

    function debug($n=1) {
        $this->debug = $n;
    }


/*
####################################################################
     :::  smtp dialogue :::          
      smtp 통신을 한다.
####################################################################
*/

     function dialogue($code, $cmd) {
        
		fputs($this->fp, $cmd."\r\n");
        $line = fgets($this->fp, 1024);
        ereg("^([0-9]+).(.*)$", $line, &$data);
        $this->lastmsg = $data[0];

        if($this->debug) {
            echo htmlspecialchars($cmd)."<br>".$this->lastmsg."<br>";
            flush();
        }

        if($data[1] != $code) return false;
        return true;
		
    }


/*
####################################################################
     :::  smtp commect :::          
      smptp 서버에 접속을 한다.
####################################################################
*/
       
       function smtp_connect($host) {
            
			if($this->debug) {
				echo "SMTP($host) Connecting...<br>";
				flush();
			}

			if(!$host) $host = $this->host;
			if(!$port) $port = $this->port;
			if(!$this->fp = @fsockopen($host, $port, $errno, $errstr, 10)) {
				$this->lastmsg = "SMTP($host) 서버접속에 실패했습니다.[$errno:$errstr]";
				return false;
			}

			$line = fgets($this->fp, 1024);
			ereg("^([0-9]+).(.*)$", $line, &$data);
			$this->lastmsg = $data[0];
			if($data[1] != "220") return false;

			if($this->debug) {
				echo $this->lastmsg."<br>";
				flush();
			}

			$this->dialogue(250, "HELO phpmail");
			return true;
				
      }
	  

/*
####################################################################
     :::  smtp close :::          
      smptp 서버에 접속을 끝는다.
####################################################################
*/

		function smtp_close() {

			 $this->dialogue(221, "QUIT");
             fclose($this->fp);
             return true;
			
		}
		   
	   
/*
####################################################################
     :::  smtp send :::          
      메시지를 보낸다.
####################################################################
*/
	   
    function smtp_send($email, $from, $data) {

        if(!$mail_from = $this->get_email($from)) return false;
        if(!$rcpt_to = $this->get_email($email)) return false;

        if(!$this->dialogue(250, "MAIL FROM:$mail_from")) {
            $this->error[] = $email.":MAIL FROM 실패($this->lastmsg)";
			return false;
        }
        if(!$this->dialogue(250, "RCPT TO:$rcpt_to")) {
            $this->error[] = $email.":RCPT TO 실패($this->lastmsg)";
            return false;
        }
        $this->dialogue(354, "DATA");

        $mime = "Message-ID: <".$this->get_message_id().">\r\n";
        $mime .= "From: $from\r\n";
        $mime .= "To: $email\r\n";

        fputs($this->fp, $mime);
        fputs($this->fp, $data);
        $this->dialogue(250, ".");
		return true;

    }

/*
####################################################################
     :::  메일타입 입력 :::          
####################################################################
*/
		 
	function mail_type($str) {
        
		if($str =='text') $this->ctype = "text/plain";
		if($str =='html') $this->ctype = "text/html";
        
	}

/*
####################################################################
     :::  get_message_id :::          
     Message ID 를 얻는다.
####################################################################
*/
  
	  function get_message_id() {
		$id = date("YmdHis",time());
		mt_srand((float) microtime() * 1000000);
		$randval = mt_rand();
		$id .= $randval."@phpmail";
		return $id;
	  }


/*
####################################################################
     :::  get_mx_server :::          
     MX 값을 찾는다.
####################################################################
*/

    function get_mx_server($email) {
        
        if(!ereg("([\._0-9a-zA-Z-]+)@([0-9a-zA-Z-]+\.[a-zA-Z\.]+)", $email, $reg)) {
			$this->error[] = $email.": 메일형식 오류";
			return false;
        }
        getmxrr($reg[2], $host);
        if(!$host) $host[0] = $reg[2];
        return $host;

    }

   
/*
####################################################################
     :::  get_email :::          
     이메일의 형식이 맞는지 체크한다.
####################################################################
*/

    function get_email($email) {
        if(!ereg("([\._0-9a-zA-Z-]+)@([0-9a-zA-Z-]+\.[a-zA-Z\.]+)", $email, $reg)) {
			$this->error[] = $email.": 메일형식 오류";
			return false;
        }
        return "<".$reg[0].">";
    }


/*
####################################################################
     :::  attach :::          
     첨부파일이 있을 경우 이 함수를 이용해 파일을 첨부한다.
####################################################################
*/
 
    function attach($file, $file_name, $ctype="application/octet-stream") {
        
		if($file_name!="") {
			$Attachment = fread(fopen($file, "rb"), filesize($file)); 
		    $this->parts[] = array ("ctype" => $ctype, "message" => $Attachment, "name" => $file_name);
			$this->ctype2 = "multipart/mixed"; 
        } else return false;		
    }


/*
####################################################################
     :::  build_message :::          
     Multipart 메시지를 생성시킨다.
####################################################################
*/
    
    function build_message($part) {

        $msg .= "Content-Type: ".$part['ctype'];
        if($part['name']) $msg .= "; name=\"".$part['name']."\"\r\n";        
        $msg .= "Content-Disposition: inline; filename=\"".$part['name']."\"\r\n";
		$msg .= "Content-Transfer-Encoding: base64\r\n";
		$msg .= "X-HN-INDEX: attach\r\n\r\n";        
		$msg .= chunk_split(base64_encode($part['message']));
        return $msg;
    }


/*
####################################################################
     :::  build_data :::          
     SMTP에 보낼 DATA를 생성시킨다.
####################################################################
*/

    function build_data($subject, $body) {
        if($this->ctype=="text/html") {
			$body = stripslashes($body);
        } else {
            $body = htmlspecialchars($body);
			$body = stripslashes($body);
			$body = str_replace("  ", "&nbsp;",$body);			
        }

		$boundary =  "b".md5(uniqid(time()));
		$mime .= "Subject: $subject\r\n";
        $mime .= "Date: ".date ("D, j M Y H:i:s T",time())."\r\n";
        $mime .= "MIME-Version: 1.0\r\n";
		$mime .= "X-Priority: 1\r\n"; 
        $mime .= "X-MSMail-Priority: High\r\n";

		if($this->ctype2) {
			$mime .= "Content-Type: ".$this->ctype2."; boundary =\"$boundary\"\r\n";
			$mime .= "This is MIME Preamble\r\n\r\n";       
        	$mime .="--$boundary\r\n";
        }
		$mime .= "Content-Type: ".$this->ctype."; charset=".$this->charset."\r\n".
				 "Content-Transfer-Encoding: base64 \r\n\r\n".
				 chunk_split(base64_encode($body))."\r\n\r\n";
			
		$max = count($this->parts);
		for($i=0; $i<$max; $i++) {
			$mime .= "--$boundary\r\n".$this->build_message($this->parts[$i])."\r\n\r\n";
		}
		if($this->ctype2) $mime .= "--$boundary\r\nThis is MIME Epilogue\r\n";
        return $mime;

    }

 
/*
####################################################################
     :::  send :::          
     메일을 전송한다.
####################################################################
*/

    function send($to, $from, $subject, $body) {
        
        if(!is_array($to)) { $to = split("[,;]",$to); $ck_all = 1; }
        if($this->self) {
			$data = $this->build_data($subject, $body);
            foreach($to as $email) {
            
			  $email = trim($email); 
              $right = strrchr($email, "@"); 
              $ck_server = strtolower(substr($right,1));
              if($this->tmp_server && $this->tmp_server != $ck_server)  $this->smtp_close();                 	
            
				if($host = $this->get_mx_server($email)) {
                    $flag = false; $i = 0;
					if($this->tmp_server == $ck_server) $flag=true;                   
						while($flag == false) {
							if($host[$i]) {
								$flag = $this->smtp_connect($host[$i]);
								$i++;
							} else break;
						}
                    
					if($flag) {
                        if(!$this->smtp_send($email, $from, $data)) {
				           if($ck_all=='1') return false;
                        }
                        $this->tmp_server = $ck_server;
						//$this->smtp_close();
                    } else {
                        $this->error[] = $email.":SMTP 접속실패";
						if($ck_all=='1') return false;
                    }
                } else {
                    $this->error[] = $email.":형식이 잘못됨";
					if($ck_all=='1') return false;
                }
            }

        } else {

            if(!$this->smtp_connect($this->host)) {
                $this->error[] = "$this->host SMTP 접속실패";
                return false;
            }
            $data = $this->build_data($subject, $body);
            foreach($to as $email) {
				if(!$this->smtp_send($email, $from, $data)) {
				   if($ck_all=='1') return false;
                }
            }
            $this->smtp_close();

        }
		return true;
    }


/*
####################################################################
     :::  errror 출력 :::          
####################################################################
*/

		function pt_error() {
        
		  $error = implode("\n",$this->error);
		  return "$error";
         
		}
		   


} //end of class


/*
################ 사용예  ################################
require "class.Smtp.php";

$mail = new classSmtp("self"); 

$from = "{$from}<$email>\nContent-Type:text/html";

//$mail->debug(); 
$mail->mail_type("text");

if($addfile) $mail->attach($addfile,$addfile_name,$addfile_type); 
$mail->send("to@hanmail.net", "from@hanmail.net", "메일제목", "메일내용"); 

if($mail->error) {
	 $error=$mail->pt_error();
	 $error = str_replace("\r\n", " ",$error);
     alert($error." 올바른 정보를 입력하시기 바랍니다.","back");
} else alert("성공적으로 접수 되었습니다.","url");

*/
?>