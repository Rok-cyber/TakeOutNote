<?

function getCode($len) {
    $SID = md5(uniqid(rand()));
    $code = substr($SID, 0, $len);
    return $code;
}


function previlEncode($og_string){
    if(!$og_string) return false;
	$len_string = strlen($og_string);
	$rand_str = getCode(21);

	//echo "원문 : $og_string";

	for($h=$h2=0;$h < $len_string;$h++){      //원문과 랜덤문을 썩는다.
		$h_str1 = substr($rand_str,$h2,1);
		$h_str2 = substr($og_string,$h,1);
		$string .= $h_str1.$h_str2;				
		if($h2==20) $h2=0;
		else $h2++;
	}
	$h_str1 = substr($rand_str,-1,1);
	$string .= $h_str1;

	$ba_string = base64_encode($string); // 1차 base64로 암호화
	$str_length=strlen($ba_string);

	$lens=0;
	$cnt=1;
	while($lens < $str_length){      // p re vil 1,2,3,4,5,6,7,8,9 1,2.. 글자식 짤라 배열로저장	  
	  $en_str[] = substr($ba_string,$lens,$cnt);
	  $lens+=$cnt;
	  if($cnt==9) $cnt=1;
	  else $cnt++;
	}
         
	for($k=0;$k<count($en_str);$k++){         // 리버스 시킴
	  $en_str[$k] = strrev($en_str[$k]);
	}

	$k2=2;
	for($k=0;$k<count($en_str);$k++){         // 2차 base64로 암호화
	  $en_str[$k] = base64_encode($en_str[$k]);	 
	  //echo $en_str[$k]."<br>";
	  if($k2==2) $en_str[$k] = str_replace("==","",$en_str[$k]);
	  else if($k2==1) $en_str[$k] = str_replace("=","",$en_str[$k]);
	  
	  if($k2==0) $k2=2;
	  else $k2--;
	}

	$encode_string = join("",$en_str);   //조인

	//echo "<br>암호문 : $encode_str";       

	return $encode_string;

}


function previlDecode($encode_string){
    if(!$encode_string) return false;
	$de_length = strlen($encode_string);
    
	$k_total=0;	
	$k1=2;
	$k2=2;
	
	for($k=0;$de_length > $k_total;$k++){         // 배열로 저장	  
	  $de_str[$k] = substr($encode_string,$k_total,$k1);
	  $k_total = $k_total + $k1;	  
	  if($k2==0) $k1++;
	  else if($k2==2 && ($de_length > $k_total)) $de_str[$k] .= "==";
	  else if($k2==1 && ($de_length > $k_total)) $de_str[$k] .= "=";	  
	  //echo $de_str[$k]."<br>";
	  $de_str[$k] =  base64_decode($de_str[$k]);  //복호화
	  if($k1==13) $k1=2;
	  else $k1++;	  
      if($k2==0) $k2=2;
	  else $k2--;
	}

	for($i=0;$i<$k;$i++){         // 리버스 시킴
	  $de_str[$i] = strrev($de_str[$i]);
	}

	$decode_str = join("",$de_str);   //조인
	$decode_str = base64_decode($decode_str);   // 복호화

	$len_string2 = strlen($decode_str);

	for($h=1;$h < $len_string2;$h+=2){
		$h_str3 = substr($decode_str,$h,1);		
		$decode_string .= $h_str3;
	}

	//echo "<br> 복호문 : $decode_string";
    return $decode_string;
}
?>