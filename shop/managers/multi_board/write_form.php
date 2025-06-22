<?
$tpl = new classTemplate;
$tpl->define("main","{$skin}/write.html");
$tpl->scan_area("main");

if($mode=='modify'){
    $mysql = new  mysqlClass(); //디비 클래스
    $sql =  "SELECT * FROM mall_{$code} WHERE uid = '{$uid}'";
    $row = $mysql->one_row($sql);
    for($i=1;$i<=$MTform[0];$i++){  
        $fd = $MTform[$i];
		${"FORM".$i} = stripslashes($row[$fd]); 
		${"FORM".$i} = str_replace("\"","&#034;",${"FORM".$i});
		${"FORM".$i} = str_replace("'","&#039;",${"FORM".$i});
    }
    switch($code){	    
	     case 'banner' : case 'mobile_banner' :
		    ${"CKD1".$FORM4} = "checked";		    
		    if($FORM5 =='1') $CKD6 = "checked";
		    else $CKD7 = "checked";
		    if($FORM6 =='1') $CKD8 = "checked";
		    else $CKD9 = "checked";
		    $row['file'] = $row['banner'];
			
			if(substr($FORM8,0,4)=='0000') $FORM8 ='';
			else $FORM8 = substr($FORM8,0,10);
        break;

		case "brand" :
			${"CKD".$FORM4} = "checked";
			if($row['img1']){
				$IMG = $row['img1'];
				$IMG2 = imgSizeCh("{$dir}/",$row['img1'],'600');	 
				$tpl->parse("is_img");
			}

			if($row['img2']){
				$IMG21 = $row['img2'];
				$IMG22 = imgSizeCh("{$dir}/",$row['img2'],'600');	 
				$tpl->parse("is_img2");
			}

			$LINK_URL = "http://{$_SERVER[HTTP_HOST]}/index.php?channel=brand&uid={$row[uid]}";
			$LINK_URL = "<a href='{$LINK_URL}' onfocus='this.blur();' target=_blank><font class=eng>{$LINK_URL}</font></a>";
		break;

		case "special" :
			${"CKD".$FORM3} = "checked";
			if($row['img1']){
				$IMG = $row['img1'];
				$IMG2 = imgSizeCh("{$dir}/",$row['img1'],'600');	 
				$tpl->parse("is_img");
			}

			if($row['img2']){
				$IMG21 = $row['img2'];
				$IMG22 = imgSizeCh("{$dir}/",$row['img2'],'600');	 
				$tpl->parse("is_img2");
			}
			
			$LINK_URL = "http://{$_SERVER[HTTP_HOST]}/index.php?channel=special&uid={$row[uid]}";
			$LINK_URL = "<a href='{$LINK_URL}' onfocus='this.blur();' target=_blank><font class=eng>{$LINK_URL}</font></a>";
		break;
       
	    case 'popup' :
		    if($FORM2 =='1') $CKD1 = "checked";
	        else $CKD2 = "checked";
		    if($FORM3 =='1') $CKD3 = "checked";
	        else $CKD4 = "checked";
			if($FORM4 =='1') $CKD5 = "checked";
	        else $CKD6 = "checked";
		    if(substr($FORM5,0,4)=='0000') $FORM5 = '';
			else $FORM5 = substr($FORM5,0,10);

		    $info = explode("|",$FORM6);
		    $INFO1 = $info[0];
		    $INFO2 = $info[1];
		    $INFO3 = $info[2];
		    $INFO4 = $info[3];	    
        break;

	    case 'reserve' :		   
			switch ($FORM4){
				case "A" : $CKD1 = "checked";
				break;
				case "B" : $CKD2 = "checked";
				break;
				case "C" : $CKD3 = "checked";
				break;
				case "D" : $CKD4 = "checked";
				break;
			}
			$tpl->parse("is_modify1");
			$tpl->parse("is_modify2");
        break;

	    case "goods_point" :			     
			$FORM1 = "<a href='{$SMain}?channel=view&uid={$FORM10}&cate={$FORM9}' target='_blank'>{$FORM1}</a>";	
			$POINT='';
	        
			if(!$FORM5) $FORM5 = "Guest";
			else $tpl->parse("is_crm");

			for($i=0;$i<$row['point'];$i++){
	            $POINT .= "★";
	        }
			if($row['best']=='Y') $CKD1 = "checked";
			
			$FORM6 = str_replace("&#034;","\"",$FORM6);
			$FORM6 = str_replace("&#039;","'",$FORM6);
			$FORM6 = str_replace("../image/up_img/point/","../../image/up_img/point/",$FORM6);
	    break;

		case "goods_qna" :	
			$FORM1 = "<a href='{$SMain}?channel=view&uid={$FORM8}&cate={$FORM7}' target='_blank'>{$FORM1}</a>";
			
			if(!$FORM4) $FORM4 = "Guest";
			else $tpl->parse("is_crm");

			$FORM5 = str_replace("&#034;","\"",$FORM5);
			$FORM5 = str_replace("&#039;","'",$FORM5);
			$FORM5 = str_replace("../image/up_img/qna/","../../image/up_img/qna/",$FORM5);
	    break;

		case "member_quit" :
			$FORM2 = $MTcate[$FORM2];
		break;

		case "event" :
			$FORM2 = substr($FORM2,0,10);
			$FORM3 = substr($FORM3,0,10);

			if($FORM7 || $FORM8) $CKD2 = "checked";
			else $CKD1 = "checked";
			
			if($FORM7) {
				$tmps = explode("|",$FORM7);
				for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
					$CATE = $tmps[$i];
					if(substr($CATE,3,9)=='000000000') {					
						$CATE1 = $CATE;
						$CATE2 = " ==== 2차분류 ==== ";
						$CATE3 = " ==== 3차분류 ==== ";
						$CATE4 = " ==== 4차분류 ==== ";					
					}
					else if(substr($CATE,6,6)=='000000') {
						$CATE1 = substr($CATE,0,3)."000000000";
						$CATE2 = $CATE;
						$CATE3 = " ==== 3차분류 ==== ";	
						$CATE4 = " ==== 4차분류 ==== ";	
					}
					else if(substr($CATE,9,3)=='000') {
						$CATE1 = substr($CATE,0,3)."000000000";
						$CATE2 = substr($CATE,0,6)."000000";
						$CATE3 = $CATE;
						$CATE4 = " ==== 4차분류 ==== ";	
					}
					else {
						$CATE1 = substr($CATE,0,3)."000000000";
						$CATE2 = substr($CATE,0,6)."000000";
						$CATE3 = substr($CATE,0,9)."000";
						$CATE4 = $CATE;
					}					
					$tpl->parse("loop_scate");
				}
			}

			if($FORM8) {
				$FORM8 = str_replace("|",",",$FORM8);
				$tpl->parse("is_sgoods");
			}

			if($FORM9) {
				$FORM9 = str_replace("|",",",$FORM9);
				$tpl->parse("is_sbrand");
			}

			if($row['img1']){
				$IMG = $row['img1'];
				$IMG2 = imgSizeCh("{$dir}/",$row['img1'],'600');	 
				$tpl->parse("is_img");
			}

			if($row['img2']){
				$IMG21 = $row['img2'];
				$IMG22 = imgSizeCh("{$dir}/",$row['img2'],'600');	 
				$tpl->parse("is_img2");
			}
			${"CKD".$FORM10} = "checked";

			$ELINKS = "http://{$_SERVER["HTTP_HOST"]}/index.php?channel=event&amp;uid={$uid}";			
		break;

		case "sms_addr" : 
			$tmps = explode("-",$FORM3);
			$FORM31 = $tmps[0];
			$FORM32 = $tmps[1];
			$FORM33 = $tmps[2];
			${"CKD".$FORM4} = "checked";
		break;

		case "sms_list" :
			$FORM1 = date("Y-m-d H:i",$FORM1);
			switch($FORM7) {
				case "1" : 
					if($row['result']==2) $FORM7 = "발송완료"; 
					else $FORM7 = "발송중"; 	
				break;
				case "2" : $FORM7 = "발송실패"; break;
				case "3" : 
					$FORM7 = "예약발송"; 
					$FORM6 = "예약시간 : ".substr($FORM6,0,4)."-".substr($FORM6,4,2)."-".substr($FORM6,6,2)." ".substr($FORM6,8,2).":".substr($FORM6,10,2);
				break;
			}
			$FORM41 = $FORM4 - $FORM3;
			$FORM3 = number_format($FORM3);
			$FORM4 = number_format($FORM4);
			$FORM41 = number_format($FORM41);

			if($FORM8=='N' || !$FORM8) $FORM8 = "단문";
			else $FORM8 = "장문(LMS)";
		break;

		case "affiliate" :			
			${"CKD1".$FORM1} = "checked";
			$tpl->parse("is_modify1");
		break;

		case 'affiliate_banner' :
			 ${"CKD".$FORM1} = "checked";	

			if($row['banner']){
				$IMG = $row['banner'];
				$IMG2 = imgSizeCh("{$dir}/",$row['banner'],'600');	 
				$tpl->parse("is_img");
			}
		break;
   }

   
	if($row['file']){
		$IMG = $row['file'];
		$IMG2 = imgSizeCh("{$dir}/",$row['file'],'600');	 
	}

	$TMODE = "수정";
	$tpl->parse("is_modify");

} 
else {
	switch($code) {
		
		case 'banner' : case 'mobile_banner' :
			$mysql = new  mysqlClass(); //디비 클래스
			$sql = "SELECT max(rank) FROM mall_banner";
			$FORM1 = $mysql->get_one($sql) + 1;
			$CKD1 = $CKD6 = $CKD8 = $CKD11 = "checked";
		break;

		case "brand" : case "special" : case "event" :
			$CKDY = 'checked';			
		break;
		
		case 'popup' :
			$CKD1 = "checked";
			$CKD3 = "checked";  
			$CKD5 = "checked";  
			$INFO1 = "100";
			$INFO2 = "100";
		break;
		
		case 'reserve' :
			$CKD2='checked';
		break;
		
		case 'auto_search' :
			$FORM2='0';
		break;

		case 'event' :
			$CKD2 = 'checked';
		break;

		case 'affiliate' :
			$CKD1Y = 'checked';
			$tpl->parse("is_write1");
			$tpl->parse("is_write2");
		break;

		case 'affiliate_banner' :
			$CKDI = "checked";			
		break;
	}		
	$TMODE = "등록";
}

switch($code) {
	case "popup" :
		$up_dir = previlEncode("../../image/up_img/popup/");
	break;

	case "banner" :
		for($i=1;$i<9;$i++) {
			${"BN".$i} = $IMG_DEFINE['banner'.$i];
		}		
		$sql = "SELECT cate, cate_name FROM mall_cate WHERE cate_dep='1' && SUBSTRING(cate,1,3)!='999' ORDER BY number ASC";
		$mysql->query($sql);
		while($data=$mysql->fetch_array()){
			if($FORM9==$data['cate']) $sec = "selected";
			else $sec = '';
			$CATE .= "<option value='{$data['cate']}' {$sec}>{$data['cate_name']}</option>";
		}
	break;

	case "brand" :
		$up_dir = previlEncode("../../image/up_img/brand/");
	break;

	case "special" :
		$up_dir = previlEncode("../../image/up_img/special/");
	break;

	case "add_page" :
		$defBoard = Array('notice','customer','faq','counsel','sales','cooperation');  
		$sql = "SELECT name, title FROM pboard_manager ORDER BY uid desc";
		$mysql->query($sql);
		while($row2 = $mysql->fetch_array()){
			if(in_array($row2['name'],$defBoard)) continue;
			if($row2['name']==$row['board']) $BOARD .= "<option value='{$row2['name']}' selected>{$row2['title']}</option>";
			else $BOARD .= "<option value='{$row2['name']}' >{$row2['title']}</option>";
		}

		$up_dir = previlEncode("../../image/up_img/add_page/");	
		$SZ = $IMG_DEFINE['addpage'];
	break;

	case "event" :
		$up_dir = previlEncode("../../image/up_img/event/");		
		
		######################## 분류 생성 ##############################
		$tmps1	= "CATEname = [[' ==== 1차분류 ==== ',[' ==== 2차분류 ==== ',[' ==== 3차분류 ==== ',' ==== 4차분류 ==== ']]]";
		$tmps2	= "CATEnum	= [['',['',['','']]]";
		$cnts=0;
		$sql = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = 1 && SUBSTRING(cate,1,3)!='999' ORDER BY number ASC";
		$mysql->query($sql);
		while($row=$mysql->fetch_array()){    
			$row['cate_name'] = addslashes($row['cate_name']);
			if($row['cate_sub']==1) {
				$tmps1.= ",['{$row[cate_name]}'";		
				$tmps2.= ",['{$row[cate]}'";		
				$sql2 = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = '2' AND cate_parent = '{$row[cate]}' ORDER BY number ASC";
				$mysql->query2($sql2);
				while($row2=$mysql->fetch_array(2)){
					$row2['cate_name'] = addslashes($row2['cate_name']);
					if($row2['cate_sub']==1) {
						$tmps1.= ",['{$row2[cate_name]}'";	
						$tmps2.= ",['{$row2[cate]}'";	
						$sql3 = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = '3' AND cate_parent = '{$row2[cate]}' ORDER BY number ASC";
						$mysql->query3($sql3);
						while($row3=$mysql->fetch_array(3)){
							$row3['cate_name'] = addslashes($row3['cate_name']);
							if($row3['cate_sub']==1) {
								$tmps1.= ",['{$row3[cate_name]}'";	
								$tmps2.= ",['{$row3[cate]}'";	
								$sql4 = "SELECT cate,cate_name FROM mall_cate WHERE cate_dep = '4' AND cate_parent = '{$row3[cate]}' ORDER BY number ASC";
								$mysql->query4($sql4);						
								while($row4=$mysql->fetch_array(4)){							
									$row4['cate_name'] = addslashes($row4['cate_name']);
									$tmps1.= ",'{$row4[cate_name]}'";
									$tmps2.= ",'{$row4[cate]}'";
								}
								$tmps1.= "]";
								$tmps2.= "]";
							} 
							else {
								$tmps1.= ",['{$row3[cate_name]}']";		
								$tmps2.= ",['{$row3[cate]}']";		
							}	
						}
						$tmps1.= "]";
						$tmps2.= "]";
					} 
					else {
						$tmps1.= ",['{$row2[cate_name]}']";		
						$tmps2.= ",['{$row2[cate]}']";		
					}
				}
			} 
			else {
				$tmps1.= ",['{$row[cate_name]}'";		
				$tmps2.= ",['{$row[cate]}'";		
			}
			$tmps1.= "]";
			$tmps2.= "]";	
			$cnts=1;	
		}
		$tmps1.= "]";
		$tmps2.= "]";
		######################## 분류 생성 ##############################

		######################## 브랜드 설정 ############################
		$sql = "SELECT uid, name FROM mall_brand ORDER BY name ASC";
		$mysql->query($sql);

		while($row=$mysql->fetch_array()){
			$row['name'] = stripslashes($row['name']);
			$row['name'] = str_replace("\"","&#034;",$row['name']);
			$row['name'] = str_replace("'","&#039;",$row['name']);
			$BRAND_LIST .= "<option value='{$row[uid]}'>{$row['name']}</option>\n";
		}			
	break;

	case "sms_addr" : case "sms_sample" :
		$sql = "SELECT groups FROM mall_{$code} WHERE groups!='' GROUP BY groups ORDER BY groups DESC";
		$mysql->query($sql);
		$SECGROUP = "";
		while($row = $mysql->fetch_array()){
			if($row['groups']==$FORM1) $sec = "selected";
			else $sec ="";
			$SECGROUP .= "<option value='{$row['groups']}' {$sec}>{$row['groups']}</option>";
		}		
	break;

	case "affiliate_account" :
		######################## 입점사 설정 ############################
		$sql = "SELECT * FROM mall_affiliate ORDER BY uid ASC";
		$mysql->query($sql);
		
		while($row=$mysql->fetch_array()){
			if($row['id'] == $FORM1) $sec = 'selected';
			else $sec='';
			$AFFILIATE .= "<option value='{$row['id']}' {$sec}>{$row['id']}</option>\n";
			$BANK_INFO .= "bank_info['{$row['id']}'] = '{$row['bank_name']} {$row['bank_num']} (예금주:{$row['bank_owner']})';\n"; 		
		}	

		$SELECT = "<select name=year>\n";
		for($i=2010;$i<=date("Y");$i++){
			if($i==$FORM21) $SELECT .="<option value=$i selected>{$i}년</option>\n";
			else $SELECT .="<option value=$i>{$i}년</option>\n";
		}
		$SELECT .= "</select>\n";
    
		if(!$FORM22) $FORM22 = date("m")-1;
		$SELECT .= "<select name=month>\n";
		for($i=1;$i<13;$i++){
			if($i<10) $i2 = "0{$i}";
			else $i2 = $i;
			if($i==$FORM22) $SELECT .="<option value=$i2 selected>{$i}월</option>\n";
			else $SELECT .="<option value=$i2>{$i}월</option>\n";
		}
		$SELECT .= "</select>\n";

	break;
}


$IMGSIZE = $SKIN_DEFINE['ctitle_img'];
$HELP_SIZE = $SKIN_DEFINE['brand_code'];

if($MTcate){
	$OPTION = "<option>선택</option>\n";
	for($i=1;$i<=$MTcate[0];$i++) {
	    if($row['cate'] ==$i) $OPTION .="<option value='{$i}' selected>{$MTcate[$i]}</option>";
	    else $OPTION .="<option value='{$i}'>{$MTcate[$i]}</option>";
	}
}

$ACTION = "./insert.php?code={$code}&mode={$mode}&uid={$uid}{$addstring}";
$LIST	= "board.php?code={$code}{$addstring}";

if($row['file']) $tpl->parse("is_img");
$tpl->parse("main");
$tpl->tprint("main");

?>