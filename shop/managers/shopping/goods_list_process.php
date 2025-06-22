<?php
/*********************************** QUERY **********************************/
    $query = "SELECT ";
	for($i=0,$cnt=count($MTlist);$i<($cnt-1);$i++){  
         $query .= $MTlist[$i].", ";	
	}
	$query .= $MTlist[$i]." FROM {$code} WHERE uid != '0' && SUBSTRING(cate,1,3)!='999' {$where} ORDER BY {$order} LIMIT $Pstart,$limit";
    $mysql->query($query);	
/*********************************** QUERY  ***********************************/

/*********************************** LOOP  ***********************************/
	while ($row=$mysql->fetch_array()){
		$NUM = $v_num;
	  
		if($v_num%2 ==0) $BGCOLOR = "#fafafa";
		else $BGCOLOR = "#ffffff";
		
		$UID	= $row['uid'];
		$DEL	= "<input type='checkbox' value='{$UID}' name='item[]' onfocus='blur();'>";
		$MLINK	= "goods_write.php?mode=modify&uid={$UID}&back={$back}{$addstring}";
		
		for($i=1;$i<=($cnt-1);$i++){  
			$fd = $MTlist[$i];			
			${"LIST".($i+1)} = stripslashes($row[$fd]);  
		}
	   
		$LIST2 = "<img src='{$img_path}{$LIST2}' border=0 width=50 height=50>";
		$LIST3 = "<a href='{$MLINK}' onfocus='this.blur();'>$LIST3</a>";
		$LIST4 = number_format($LIST4,$ckFloatCnt); 

		switch($LIST5){
			case "1" : $LIST5 = "무제한";
			break;
			case "2" : $LIST5 = "<font style='color:#3366CC'>품절</font>";
			break;
			case "3" : $LIST5 = "<font style='color:#3366CC'>상품숨김</font>";
			break;
			case "4" : 
				$LIST5 = $LIST6;
			break;
		}
		if($row['type']=='B') $LIST5 = "<font style='color:#3366CC'>분류<br />상품숨김</font>";

		/************************* 적립금 관련 ***********************/
		$reserve = explode("|",$LIST9);
		if($reserve[0] =='2') { //쇼핑몰 정책일때
			if($cash[6] =='1') { 
				$LIST9 = number_format(($row['price'] * $cash[8])/100,$ckFloatCnt);
			} else $LIST9 = 0;
		} 
		else if($reserve[0] =='3') { //별도 책정일때
			$LIST9 = number_format(($row['price'] * $reserve[1])/100,$ckFloatCnt);
		}		
		else $LIST9 = 0;
		/************************* 적립금 관련 ***********************/

		$tmps = explode("|",$LIST10);		
		if($tmps[0] || $tmps[1]) {
			$LIST10 = "<font class='small blue'>전시</font> :";
			if($tmps[0]) $LIST10 .= " [메인 - <a href='goods_display.php?disp={$tmps[0]}'><font class='green small'>{$disp_arr1[$tmps[0]]}</font></a>]";
			if($tmps[1]) $LIST10 .= " [분류 - <a href='goods_display.php?disp={$tmps[0]}&seccate=".substr($row['cate'],0,3)."000000'><font class='green small'>{$disp_arr2[$tmps[1]]}</font></a>]";
		} 
		else $LIST10 = '';

		if($LIST16 && $LIST16!=0) {
			if($LIST10) $tmp_empty = "&nbsp;&nbsp;&nbsp;&nbsp;";
			else $tmp_empty = "";
			$LIST16 = "{$tmp_empty}<font class='small blue'>브랜드</font> : <a href='{$_SERVER['PHP_SELF']}?brands={$LIST16}{$sa_string2}'>".$brand_arr[$LIST16]."</a>";
		}
		else $LIST16 = '';
	   
		$DATE = date("Y-m-d",$LIST7);
		if($LIST8>0) $MDATE = date("Y-m-d",$LIST8);		
		else $MDATE = '';

		$LOCATION = getMLocation($row['cate']);
	         
		$tpl->parse("is_man2","1");
		$tpl->parse("loop");
		if($disp=='Y') $v_num++;
		else $v_num--;
	}

	$pg = new paging($total_record,$page);
	$pg->addQueryString("?".$pagestring); 
	$PAGING = $pg->print_page();  //페이징 
?>