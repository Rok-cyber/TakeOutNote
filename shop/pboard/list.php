<?
if(!$page) $page = 1;	
$page_record_num = $PGConf['page_record_num'];

if($code=='counsel' && $admin_board2 !='Y') {
	$idck = 1;
	$where = " && b.id = '{$my_id}'";
}

if($code=='affil_counsel' && $admin_board2 !='Y') {
	$idck = 1;
	$where = " && b.id = '{$a_my_id}'";
}

if($field && $word) {
	if($field=='comment') $where .= " && INSTR(b.{$field},'{$word}') > 0 ";
	else $where .= " && INSTR(m.{$field},'{$word}') > 0 ";
}
if($seccate && $options[1]=='Y') $where .= " && m.cate = '{$seccate}' ";

##################### 게시판 자동등록 방지(글쓰기폼연결용) ###########################
$ck_w	= base64_encode(time());	// 현재 시간 정보를 생성 ...  글쓰기 버튼에서 받아옴
$ck_w2	= md5($ck_w.$cook_rand);    // 암호화

##################### 출력할 리스트 계산 ################################
if($where) {	// 검색일경우
	
	${"sec".$field} = "selected";
    
	############# 전체글 수 ########### 	
	$sql = "SELECT comment FROM pboard_{$code}_body where no=1";
	$SQL_TIME[]		= microtime(); //SQL 시간 측정
	$data			= $mysql->one_row($sql);
	$SQL_TIME[]		= microtime(); //SQL 시간 측정
	$record_arr		= explode("|",$data[comment]);
	$total_record	= $record_arr[0];
	
	########## 게시물수에 따른 2중페이징 분할건수(만건) #################
	if($total_record <300000) $sepa_idx = 10;
    else if($total_record <1000000) $sepa_idx = 8;
	else if($total_record <2000000) $sepa_idx = 6;
	else if($total_record <3000000) $sepa_idx = 4;
    else     $sepa_idx = 2;
    
	$h_record=$i=$sepa_start_idx=$total_record=0;
    $record_arr='';
	$sepa_last_idx =999;
	
	if($field=='comment') $sepa_idx=($sepa_idx/2);
   
	if(!$spage) {
		$sql = "SELECT depth FROM pboard_{$code} WHERE no=1";
	    $slast_idx = $mysql->get_one($sql);
	    if($slast_idx < (999-$sepa_idx))  $spage=1;
	} 
	if($spage && $slast_idx) {
        $sepa_start_idx = ($slast_idx + ($sepa_idx * ($spage-1)))-1;
        $sepa_last_idx	= $sepa_start_idx + ($sepa_idx+1);
		$spagestring	= $pagestring."&slast_idx={$slast_idx}";;
     	$addstring		.= "&slast_idx={$slast_idx}&spage={$spage}";
	    $pagestring		.= "&slast_idx={$slast_idx}&spage={$spage}";
	    if($sepa_last_idx>998) $sepa_last_idx = 999;
	}

	$sql = "SELECT count(*) as cnt, m.idx FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no=b.no && m.idx < {$sepa_last_idx} && m.idx > {$sepa_start_idx} {$where} GROUP BY m.idx";	
	$SQL_TIME[] = microtime(); //SQL 시간 측정
    $mysql->query($sql);
	$SQL_TIME[] = microtime(); //SQL 시간 측정
  
	while($data=$mysql->fetch_array()){
		$record_arr[$i] = $data[cnt];
		$total_record += $data[cnt];
		$tmp_idx = $data['idx'];
		$i++;
	}

	$start_record = ($page-1) * $page_record_num;
	$last_record = $start_record +  $page_record_num;
	for($ch=0;$ch<$i;$ch++){
		$tmp_record = $h_record;
		$h_record	= $h_record + $record_arr[$ch];
		if(($start_record < $h_record) && $st_ck !=1)  { 
			$start_idx = $sepa_last_idx - ($i -$ch);  //시작 idx 
			$first_tmp_record = $tmp_record;
			if($last_record <= $h_record) break;
			else $st_ck=1; 
		}  
		if(($last_record <= $h_record) || ($ch == ($i-1))) { $last_idx = $sepa_last_idx - ($i-$ch);  break; }  //마지막 idx
	}

	if($i==1) $ckk=1;
	if($ch==0 && $sepa_start_idx) $start_idx= $sepa_start_idx+1;
	if(!$last_idx || ($start_idx == $last_idx) || $ckk==1) {
		if($sepa_start_idx) $plus_idx = "&& m.idx = ".($sepa_start_idx+1); 
		else if($ckk==1) $plus_idx = "&& m.idx = {$tmp_idx}";         
		else $plus_idx = "&& m.idx = {$start_idx}";         
	} 
	else { 
		$plus_idx = "&& m.idx <= {$last_idx} && m.idx >= {$start_idx}"; 
	}
	$start_record =  $start_record - $first_tmp_record;  

} 
else {

	$sql = "SELECT comment,file FROM pboard_{$code}_body where no=1";
	$SQL_TIME[]		= microtime(); //SQL 시간 측정
	$data			= $mysql->one_row($sql);
	$SQL_TIME[]		= microtime(); //SQL 시간 측정
	$record_arr		= explode("|", $data['comment']);
	$nrecord		= explode("|", $data['file']);
	$total_record	= $record_arr[0]+$nrecord[0];
	$num_idx		= count($record_arr)-1;
    
	$start_record = ($page-1) * $page_record_num;
	for($ch=$num_idx;$ch>0;$ch--){
	    $h_record = $h_record + $record_arr[$ch];
	    if($start_record < $h_record + $nrecord[0]){
			$sec_idx = 999 - $ch;
			break;
		}
	}

	if(!$record_arr[0] && $nrecord[0]) $sec_idx=0;
	else {
		if(($start_record + $page_record_num) > $h_record && $sec_idx !=998) $plus_idx = "&& m.idx=".($sec_idx+1)." "; 
		$start_record =  $start_record -  ($h_record - $record_arr[$ch]);

		if($nrecord[0] && $nrecord[0] > (($page-1) * $page_record_num)) $notice_idx = "|| m.idx = 0 ";  //공지사항용
		else { $notice_idx=""; $start_record = $start_record - $nrecord[0]; }
	}    
}	

if($total_record < ($page-1) * $page_record_num) $page=$page-1;
$v_num = $total_record - (($page-1) * $page_record_num);
if($total_record < ($page * $page_record_num)) $page_record_num -= (($page*$page_record_num) - $total_record);
##################### 출력할 리스트 계산 ################################    
   		
$tpl = new classTemplate;	// 템플릿 생성
$tpl->define('main',"{$skin}/list.html");
$tpl->scan_area('main');

##################### 카데고리 #####################
if($options[1]=='Y' && $category[0]>0){
	$CATE		= "<option value=''>".$LANG_ETC_MSG[3]."</option>\n";
	$CATELIST	= "<a href='{$Main}' onfocus='this.blur();'>".$LANG_ETC_MSG[3]."</a>";	   	
	
	if($seccate) $CSEC = "2_off";
	else $CSEC = "2_on";
	
	$C_NAME = "전체";
	$i=0;
	$tpl->parse("loop_cate");
	
	for($i=1,$ck2=1;$i<=$category[0];$i++) {
		$C_NAME = stripslashes($category[$i]);		
		if($seccate==$i) {
			if(strlen($C_NAME)>8) $CSEC = "on";
			else $CSEC = "2_on";
			$CSEC2 = "selected";
		}
		else {
			if(strlen($C_NAME)>8) $CSEC = "off";
			else $CSEC = "2_off";
			$CSEC2 = "";
		}
				
		$CATE .="<option value='{$i}' {$CSEC2}>{$C_NAME}</option>";		
		$CATELIST .= " | <a href='{$Main}&seccate={$i}' onfocus='this.blur();'>{$C_NAME}</a>";	  
		
		$tpl->parse("loop_cate");
	}	    
    $tpl->parse('is_cate');
}

##################### 로그인 보이기 #####################
if($options[9] =='Y'){
    if(!$my_id) $tpl->parse('is_login');
	else $tpl->parse('is_logout');	
	$tpl->parse('is_login_v');
}

if($total_record > 0) {	

	##################### DB QUERY #####################
	if($where || $options[16]=='Y') {
		if($field=='comment' || $options[16]=='Y' || $idck==1) {	 
			if($seccate && $options[1]=='Y') $where .= " && m.cate = '{$seccate}' ";
			if($options[16]=='Y') $add_f = ", b.comment as comment, b.s_link as slink ";

			$sql = "SELECT m.no,m.idx,m.depth,m.name,m.subject,m.cate,m.hit,m.reco,m.down,m.file,m.cnt_memo, m.secret , m.icon, m.signdate, b.file as files, b.html_type as html_type,b.id as id, b.homepage as homepage, m.email as email {$add_f} FROM pboard_{$code} m ,pboard_{$code}_body b WHERE m.no > 1 && m.no=b.no {$plus_idx} {$where} ORDER BY m.idx DESC, m.main ASC LIMIT {$start_record},{$page_record_num}";
			if(!$spage || $spage==1) $sql2 = "SELECT count(m.no) FROM pboard_{$code} m ,pboard_{$code}_body b WHERE m.no >1 && m.no=b.no && m.idx>= ".($sec_idx-1)." {$where} && from_unixtime(m.signdate) like '".date("Y-m-d")."%'"; 
			unset($add_f);			
		} 
		else { 
			$sql = "SELECT no,idx,depth,name,subject,cate,hit,email,reco,file,down,cnt_memo, secret, icon, signdate FROM pboard_{$code} m WHERE m.no>0 {$plus_idx} {$where} LIMIT {$start_record},{$page_record_num}";
			if(!$spage || $spage==1) $sql2 = "SELECT count(no) FROM pboard_{$code} m WHERE no>1 && idx>= ".($sec_idx-1)." {$where} && from_unixtime(signdate) like '".date("Y-m-d")."%'"; 
		}
	} 
	else {	
		$sql = "SELECT no,idx,depth,name,subject,cate,hit,reco,email,down,file,cnt_memo,secret,icon, signdate FROM pboard_{$code} m WHERE idx = {$sec_idx} {$plus_idx} {$notice_idx} LIMIT {$start_record},{$page_record_num}";
		$sql2 = "SELECT count(no) FROM pboard_{$code} m WHERE no>1 && idx>= ".($sec_idx-1)." && from_unixtime(signdate) like '".date("Y-m-d")."%'"; 
	}
	
	if($spage && $spage>1) $TODAY = 0;
	else {
		$SQL_TIME[] = microtime(); //SQL 시간 측정
		$TODAY		= $mysql->get_one($sql2);
		$SQL_TIME[] = microtime(); //SQL 시간 측정	
	}

	$SQL_TIME[] = microtime(); //SQL 시간 측정
	$mysql->query($sql);
	$SQL_TIME[] = microtime(); //SQL 시간 측정	
	##################### DB QUERY #####################

	$tmp_vnum	= $v_num;
	$ck_tr		= 1;

	##################### ARTICLE LOOP #####################
	while ($row=$mysql->fetch_array()){    
		$NEWS = $OSLINK1 = $OSLINK2 = $OSLINK3 = $OSLINK4 = '';

		$row['subject']	= htmlspecialchars(stripslashes($row['subject']));
		$row['name']	= htmlspecialchars(stripslashes($row['name']));
		$row['email']	= htmlspecialchars(stripslashes($row['email']));       
		$NUM			= $v_num;
		$NO				= $row['no'];
		$SUBJECT		= '';
		$VLINK			= $Main.$addstring."&amp;pmode=view&amp;no={$NO}";
		$SNLINK			= $Main."&amp;field=name&amp;word={$row[name]}";

		if($my_level>8) {
			$DEL = "<input type='checkbox' value='{$NO}' name='item[]' onfocus='blur();' style='border:0px' />";
			$tpl->parse('is_man3','1');
			$tpl->parse('is_man5','1');
		}
		
		for($i=0;$i<$row['depth'];$i++){
			if($i>10) break;
			$SUBJECT .= '&nbsp;&nbsp;';
		}
		   
		if($row['idx']==0) {	//공지사항 일때
			$tpl->parse('is_notice','1');
			$SUBJECT	.= "<img src='{$skin}/img/notice.gif' alt='Notice' />&nbsp;";
		}
		else $tpl->parse('is_num','1');

		if($row['no']==$no) {	//글보기 일때
			$NUM = "<img src='{$skin}/img/me_icon.gif' alt='This Article' />";
		}

		if($row[depth] >'0') {	//답글 일때
			$SUBJECT .= "<img src='{$skin}/img/re_icon.gif' border='0' alt='Reply' />&nbsp;";
		}

		if($row[secret] =='1') {	//비밀글 일때
			if($code!='counsel' && $code!='affil_counsel') $SUBJECT .= "<img src='{$skin}/img/icon_lock.gif' border='0' alt='Lock' />&nbsp;";
		}

		if($main_data[word_limit]!=0) $row['subject'] = hanCut($row['subject'], $main_data['word_limit']);	//제목 제한 글수
		   
		if($row['secret'] ==1 && $my_level < 9) $SUBJECT2 = $SUBJECT.$row['subject'];	// 비밀글 레이어 이용시 처리요망
		else $SUBJECT2 .= $row['subject'];

		$SUBJECT .= $row['subject'];
			   
		if($row['cnt_memo'] >0) {	//메모글 수
			$MEMOCNT = number_format($row['cnt_memo']);
			$tpl->parse('is_mcnt','1');
		} 

		   
		if($row['icon'] && @file_exists("{$bo_path}/icon/{$row[icon]}")) {
			$NAME = "<img src='{$bo_path}/icon/".urlencode($row[icon])."' border='0' alt='{$row['name']}' />";
		}
		else $NAME = $row['name'];
		   
		if($options[1]=='Y' && $category[0]>0){
			$CATENAME = $category[$row[cate]];
			$tpl->parse('is_catename','1');
		}
		
		$TIMES	= date("h:i a",$row['signdate']);
		if(date("Ymd",$row['signdate'])==date("Ymd")) {	//오늘글 일때 시간 출력			
			$tpl->parse('is_times','1');
		} 
		else {
			$D_YYYY	= date("Y",$row['signdate']);
			$D_YY	= date("y",$row['signdate']);
			$D_MM	= date("m",$row['signdate']);
			$D_DD	= date("d",$row['signdate']);
			$tpl->parse('is_dates','1');
		}
		   
		if($row['secret']==1) $TYPE = $LANG_ETC_MSG[1];
		else $TYPE = $LANG_ETC_MSG[2];

		$HIT	= $row['hit'];
		$RECO	= $row['reco'];
		$DOWN	= $row['down'];

		if($options[16]=='Y') {	//방명록 형태 일때
			$DELETE = $Main.$addstring."&amp;pmode=del&amp;no={$NO}";
			if(!$row['id'] || $row['id']==$my_id || $my_level>8) $tpl->parse('is_delete');

			$COMMENT = stripslashes($row['comment']); 	      		   
			if($row['html_type']==1) {
				$COMMENT = ieHackCheck($COMMENT);
			} 
			else {
				$COMMENT = htmlspecialchars($COMMENT);				
			}
			$COMMENT2 = makeLink(nl2br($COMMENT));

			if($row['email']) {
				$EMAIL1 = htmlspecialchars(stripslashes($row['email']));
				$EMAIL = "{$bo_path}/mailto.php?mail=".previlEncode($EMAIL1);
				$MEMAIL = "<a href='{$EMAIL}' onfocus='this.blur();' target='HFrm'><img src='img/icon_mail.gif' alt='Mail to {$row[name]}' border=0></a>";
			} else $MEMAIL='';

			if($row['homepage']){
				$HOMEPAGE = htmlspecialchars(stripslashes($row['homepage']));
				if(!eregi("http://",$HOMEPAGE)) $HOMEPAGE="http://".$HOMEPAGE;
				$HOMEPAGE = $HOMEPAGE;
				$MHOMEPAGE = "<a href='{$HOMEPAGE}' target='_blank' onfocus='this.blur();'><img src='img/icon_home.gif' alt='HomePage Link {$HOMEPAGE}' border=0></a>";
			} 
			else $MHOMEPAGE='';
		   
			if($options[17]) {
				if($row['html_type']==1) {
					$COMMENT = html2txt($COMMENT);
				}
				$COMMENT = hanCut($COMMENT,$options[17]);				
			}    
			
			if($row['slink']) {
				$row['slink'] = htmlspecialchars(stripslashes($row['slink']));
				$oslink = $row['slink'];
				if(!eregi("http://",$row['slink'])) $row['slink']="http://".$row['slink'];
				$SLINK = "<a href='{$row[slink]}' target='_blank' onfocus='this.blur();'>{$row[slink]}</a>";			   		

				$slink=explode("||",$oslink);
				
				for($i=0,$cnt=count($slink);$i<$cnt;$i++){
					if($slink[$i]) {
						${"OSLINK".($i+1)} = $slink[$i];
						if(!eregi("http://",$slink[$i])) {
							$slink[$i]="http://".$slink[$i];
						}
						${"SLINK".($i+1)} = $slink[$i];
					}
				}
			}
			$MACTION = "{$bo_path}/me_insert.php?code={$code}&amp;pmode=write{$addstring}";

		
			if($row[cnt_memo] >0) {    //메모가 있으면
				$sql = "SELECT no,homepage,comment,m_link,s_link,file,id FROM pboard_{$code}_body  WHERE memo='1' && idx = {$row[no]} && no >= 30000000 ORDER BY no ASC";
				$SQL_TIME[] = microtime();
				$mysql->query2($sql);
				$SQL_TIME[] = microtime();
				while($row2 = $mysql->fetch_array('2')){
					if($row2['file'] && @file_exists("{$bo_path}/icon/{$row2[file]}")) {
						$MNAME = "<img src='{$bo_path}/icon/".urlencode($row2['file'])."' alt='{$row['homepage']}' />";
					}
					else $MNAME = htmlspecialchars(stripslashes($row2['homepage']));
					$MDATE = date('Y-n-d A h:i',$row2[m_link]);
					
					if($row2['id']) { //회원글일때 (관리자도 삭제가능)
						if($row2[id] == $my_id || $my_level >8) {    
							$MDEL = "{$bo_path}/me_insert.php?code={$code}&amp;no={$NO}&amp;no2={$row2[no]}&amp;pmode=del&amp;mode=list&amp;Main=".urlencode($Main)."{$addstring}";
							$tpl->parse('is_user','1');
						}
					} else {     // 비회원이 글일때
						$MDEL = "{$Main}&amp;no={$NO}&amp;no2={$row2[no]}&amp;pmode=mdel{$addstring}";
						$tpl->parse('is_user','1');
					}
					$MCOMMENT = htmlspecialchars(stripslashes($row2['comment']));
					$MCOMMENT = makeLink(nl2br($MCOMMENT));
					$tpl->parse('mloop');    				  
				}  			  
			}
			if($my_level>8) $tpl->parse('is_mcomm');	   			
		}	//end of option[16]
		   
		if($row['file']) {			
			$SIZE = getFilesize("{$bo_path}/data/{$code}/".$row['file']);
			$tpl->parse('is_file','1');
			$FILE = "{$bo_path}/data/{$code}/".urlencode($row['file']);
			$FILE = str_replace("%2F","/",$FILE);

			$ext = getExtension($row['file']);
			switch($ext) {
				case "bmp": case "exe": case "excel": case "gif" : case "hwp": case "jpg": case "ppt": case "swf": case "txt": case "word" : case "zip":				
					$F_ICON = "<img src='{$skin}/img/{$ext}.gif' border='0' alt='{$ext} File' />";
				break;
				default : $F_ICON = "<img src='{$skin}/img/etc.gif' border='0' alt='Etc File' />";
			}
			$F_NAME = $row['file'];
			$F_DOWN = "{$bo_path}/download.php?code={$code}&amp;no={$row[no]}&amp;filename={$row['file']}";
		} 
		else { 
			$SIZE = $F_ICON = "";
			$FILE = "{$skin}/img/no_pic.gif";
		}
		   
		if($tmp_vnum != $v_num && !$img_cnt) $tpl->parse('is_line','1');		   
       
		if($img_sz) {
			$y_size = ''; 
			if($options[20]=='Y') $y_size = intval(3*$img_sz/4);			

			if(eregi("\.gif|\.jpg|\.pnp|\.bmp",$row[file])){
				$IMG = imgSizeCh("{$bo_path}/data/{$code}/",$row['file'],'',$y_size,$img_sz);				
			} 
			else {
				if($img_cnt) $no_img = 1;			   
				$IMG = imgSizeCh("{$skin}/img/","no_pic.gif",'',$y_size,$img_sz);  
			}    
		}

		########################## 갤러리용 ############################
		if($img_cnt) {
			$ISIZE1	= ($img_sz + 10);
			$size = getImgSize("{$bo_path}/data/{$code}/",$row['file'],$img_sz,$y_size);				
			$ISIZE2 = $size[1];
			$ISIZE3 = $size[1] + 80;
			if($ISIZE4 < $ISIZE3) $ISIZE4 = $ISIZE3+12;
			$ILINK	= $Main.$addstring."&amp;pmode=view&amp;no={$NO}";
			
			if($options[10]>1) {  //이미지 등록가능수가 둘 이상일때 
				if($comm) {
					$tmp_cnt = explode("||",$row['files']);
					$IMGSCNT = count($tmp_cnt)-1;
				} else {
					$sql = "SELECT file FROM pboard_{$code}_body WHERE no={$NO} && memo='0'";
					$tmp_cnt = $mysql->get_one($sql);
					$tmp_cnt = explode("||",$tmp_cnt);
					$IMGSCNT = count($tmp_cnt)-1;
				}
				$tmp_cnt='';
			} 
			
			if($IMGSCNT>0) $tpl->parse('is_icnt','1');				
			
			$clear = '';
			if($ck_tr%$img_cnt!=0) $tpl->parse('is_line','1');
			if($ck_tr%$img_cnt==1) { $clear = "clear:both;"; $tpl->parse('is_tr','1'); }			
			$tpl->parse('is_bars','1');
		}
		########################## 갤러리용 ############################
		
		if($row['signdate'] > (time()-(3600*$options[18]))) $NEWS = "<img src='{$skin}/img/icon_new.gif' alt='New Article' />";	   
		   
		$tpl->parse('loop');
		// 템플릿 저장변수 해제
		$tpl->parse('is_line','2');
		$tpl->parse('is_tr','2');
		$tpl->parse('is_catename','2');
		$tpl->parse('is_icnt','2');
		$tpl->parse('is_times','2');
		$tpl->parse('is_dates','2');
		$tpl->parse('is_mcnt','2');
		$tpl->parse('is_file','2');
		$tpl->parse('is_notice','2');
		$tpl->parse('is_num','2');
		$tpl->parse('is_bars','2');

		if($options[16]=='Y') { 
			$tpl->parse('mloop','2');
			$tpl->parse('is_mcomm','2');
			$tpl->parse('is_delete','2');
		}

		$v_num--;
		$ck_tr++;
	}
	##################### ARTICLE LOOP #####################

	$tpl->parse('is_man3','2');

	########################## 갤러리용 ############################
	/*
	if($ck_tr!=1 && $img_cnt){
		for($i=$ck_tr;$i<=$img_cnt;$i++) { 
		    $IMG	= imgSizeCh("{$skin}/img/","no_pic.gif",$img_sz);
		    $SUBJECT= $SUBJECT2=$CNTS=$NAME="";
		    $ILINK	= "#";
		    if($i%$img_cnt!=0) $tpl->parse('is_line','1');
		    $tpl->parse('loop');
		    $tpl->parse('is_line','2');
		}
	}
	*/
	########################## 갤러리용 ############################

	$TOTAL = $total_record;      //토탈수 
	$TOPAGE = ceil($total_record/$PGConf['page_record_num']);
} else { 
	$tpl->parse('no_loop'); 
    $TODAY	=0;	
	$TOTAL	=0;	
	$TOPAGE	=0;
}

${"SELECT".$field} = "selected";
if(!$field)  $CHECKsubject = "checked"; 
else ${"CHECK".$field} = "checked";

$PAGE	= $page;
$WLINK	= $Main.$addstring."&amp;pmode=write&amp;ck_w={$ck_w}&amp;ck_w2={$ck_w2}"; //글쓰기 링크 
$RSSLINK = "http://".$_SERVER["HTTP_HOST"]."/{$ShopPath}rss/board.php?code={$code}";

if($total_record >$page_record_num){
    $pg_string = explode(",",$tpl->getPgstring());
	$pg = new paging($total_record,$page);
    $pg->addQueryString($Main.$pagestring); 
    $PAGING = $pg->print_page($pg_string[0],$pg_string[1],$pg_string[2]);  //페이징 
	$tpl->parse("define_pg");	
}
if($spage && $slast_idx) {
	$pg_string2 = explode(",",$tpl->getPgstring2());
    $total_idx = 999-$slast_idx;
    $pg2 = new paging($total_idx,$spage,$sepa_idx);
    $pg2->addQueryString($Main.$spagestring);     
    $SEPALIMIT = $sepa_idx;
	$PAGING2 = $pg->print_page($pg_string[0],$pg_string[1],$pg_string[2]);  //페이징 
	$tpl->parse("define_pg");	
    $tpl->parse('is_paging');
}

if($my_level>8) {
	$tpl->parse('is_man1');
	$tpl->parse('is_man2');
	$tpl->parse('is_man4');	
}

if(($acc_level[8] == '!=' && ($acc_level[2]==$my_level || $my_level>8)) || ($acc_level[8] == '<' && $acc_level[2]<=$my_level)) {
	if($options[21]!='Y' || ($options[21]=='Y' && $TOTAL==0)) $tpl->parse('is_write');
}

if($admin_board=='Y' && $code=='faq') $tpl->parse("is_admin");

$tpl->parse('main');

if($my_level>8) {
	$addstring2 = str_replace("&amp;","&",$addstring);
?>

<!--  관리자 전용 삭제 스크립트 -->
<script type="text/javascript">
<!--

function delSend(f){
	for (i=cnt=0;i<f.elements.length;i++) {
		if(f.elements[i].name == 'item[]' && f.elements[i].checked == true) {
			cnt++;
		}
	}

	if (cnt<=0) {
		alert("<?=$LANG_FORM_MSG[7]?>");
		return false;
	} 
	else {
		if (window.confirm("<?=$LANG_FORM_MSG[8]?>")) {
			f.action = "<? echo "{$bo_path}/insert.php?code={$code}&pmode=ad_del{$addstring2}";?>";
			f.submit();
			return;
		} else return;		

	}
}

function ch_check(f){
	for (j=0;j<f.elements.length;j++) {
		if(f.elements[j].name == 'item[]' && f.elements[j].checked == true) { 
			f.elements[j].checked = false;
		} else {
			f.elements[j].checked = true;
		}
	}
}

//-->
</script>
<!-- @관리자 전용 삭제 스크립트 -->

<? 
}

if($options[9] =='Y'){ 

?>

<script type="text/javascript">
<!--
function join(mode){
    window.open("<?=$bo_path?>/join.php?jmode="+mode,"join","toolbar=no,directories=no,status=no,resizable=no,width=700,height=700,left=100,top=50,scrollbars=yes");
}
//-->
</script>
<?
} 

$tpl->tprint("main");

?>
