<?
include "sub_init.php";

header("Content-type: text/xml; charset=utf-8"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 

echo('<'.'?xml version="1.0" encoding="utf-8"?'.">\n<root>\n"); 

$code	= $_GET['code'];
$page	= isset($_GET['page']) ? $_GET['page'] : 1;
$Pstart	= $_GET['Pstart'];
$page_record_num = $_GET['limit'];
$word	= urldecode(trim($_GET['word']));
$seccate= $_GET['seccate'];
$limit = $_GET['limit'];

$bo_path = "../pboard";

if(!$code || !$page_record_num || strlen($Pstart)==0) { 	
	echo "<error>Error</error></root>"; 
	exit;
}

$sql  = "SELECT * FROM pboard_manager WHERE name = '{$code}'";
if(!$main_data = $mysql->one_row($sql)) {
	echo "<error>Error</error></root>"; 
	exit;
}

$options = explode("||",$main_data['options']);

if($word) {
	$where .= " && (INSTR(b.comment,'{$word}') || INSTR(m.subject,'{$word}') || INSTR(m.name,'{$word}'))";
}
if($seccate) { 
	$where .= " && m.cate = '{$seccate}' ";
}

##################### 출력할 리스트 계산 ################################
if($where) {	// 검색일경우
    
	############# 전체글 수 ########### 	
	$sql = "SELECT comment FROM pboard_{$code}_body where no=1";	
	$data			= $mysql->one_row($sql);	
	$record_arr		= explode("|",$data['comment']);
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
	$mysql->query($sql);
	  
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
	$data			= $mysql->one_row($sql);

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
##################### 출력할 리스트 계산 ################################  

if($total_record < ($page-1) * $page_record_num) $page=$page-1;
$v_num = $total_record - (($page-1) * $page_record_num);
if($total_record < ($page * $page_record_num)) $page_record_num -= (($page*$page_record_num) - $total_record);

/**************************** BOARD LIST**************************/
if($where || $options[16]=='Y') {
	if($options[16]=='Y') $add_f = ", b.comment as comment, b.s_link as slink ";

	$sql = "SELECT m.no,m.idx,m.depth,m.name,m.subject,m.cate,m.hit,m.reco,m.down,m.file,m.cnt_memo, m.secret , m.icon, m.signdate, b.file as files, b.html_type as html_type,b.id as id, b.homepage as homepage, m.email as email {$add_f} FROM pboard_{$code} m ,pboard_{$code}_body b WHERE m.no > 1 && m.no=b.no {$plus_idx} {$where} ORDER BY m.idx DESC, m.main ASC LIMIT {$Pstart},{$limit}";	
	unset($add_f);				
} 
else {	
	$sql = "SELECT no,idx,depth,name,subject,cate,hit,reco,email,down,file,cnt_memo,secret,icon, signdate FROM pboard_{$code} m WHERE idx = {$sec_idx} {$plus_idx} {$notice_idx} LIMIT {$Pstart},{$limit}";
}
$mysql->query($sql);

##################### ARTICLE LOOP #####################
while ($row=$mysql->fetch_array()){    
	$SUBJECT = $NOTICE = $REPLY = $LOCK = $FILE = $F_NAME = $IMG = $NEW = "";

	$row['subject']	= htmlspecialchars(stripslashes($row['subject']));
	$row['name']	= htmlspecialchars(stripslashes($row['name']));
	$row['email']	= htmlspecialchars(stripslashes($row['email']));       
	$NUM			= $v_num;
	$NO				= $row['no'];
	
	for($i=0;$i<$row['depth'];$i++){
		if($i>10) break;
		$SUBJECT .= '&nbsp;&nbsp;';
	}
	   
	if($row['idx']==0) {	//공지사항 일때
		$NOTICE = 1;
	}
	
	if($row['depth'] > 0) {	//답글 일때
		$REPLY = 1;
	}

	if($row['secret']==1) {	//비밀글 일때
		if($code!='counsel' && $code!='affil_counsel') {
			$LOCK = 1;
		}	
	}

	if($main_data['word_limit']!=0) $row['subject'] = hanCut($row['subject'], $main_data['word_limit']);	//제목 제한 글수	
	$SUBJECT .= $row['subject'];
		   
	if($row['cnt_memo'] >0) {	//메모글 수
		$MEMOCNT = number_format($row['cnt_memo']);		
	} 
	
	$NAME = $row['name'];
	   
	if($options[1]=='Y' && $category[0]>0){
		$CATENAME = $category[$row[cate]];		
	}
	
	$DATE	= date("Y-m-d",$row['signdate']);			   
	$HIT	= $row['hit'];
	$RECO	= $row['reco'];

	if($options[16]=='Y') {	//방명록 형태 일때
		$COMMENT = stripslashes($row['comment']); 	      		   
		if($row['html_type']==1) {
			$COMMENT = ieHackCheck($COMMENT);
		} 
		else {
			$COMMENT = htmlspecialchars($COMMENT);				
		}
		
		if($options[17]) {
			if($row['html_type']==1) {
				$COMMENT = html2txt($COMMENT);
			}
			$COMMENT = hanCut($COMMENT,$options[17]);				
		}    
	}	//end of option[16]
	   
	if($row['file']) {			
		$FILE = "{$bo_path}/data/{$code}/".urlencode($row['file']);
		$FILE = str_replace("%2F","/",$FILE);
		$F_NAME = $row['file'];			
		if(eregi("\.gif|\.jpg|\.pnp|\.bmp",$row[file])){
			$IMG = $FILE;
		} 
	} 
	   
	if($row['signdate'] > (time()-(3600*$options[18]))){ 
		$NEW = 1;
	}
	   
	echo "
	  <item>
		<num><![CDATA[{$v_num}]]></num>
		<name><![CDATA[{$NAME}]]></name>
		<subject><![CDATA[{$SUBJECT}]]></subject>
		<email><![CDATA[{$EMAIL}]]></email>
		<hit><![CDATA[{$HIT}]]></hit>
		<reco><![CDATA[{$RECO}]]></reco>
		<comment><![CDATA[{$COMMENT}]]></comment>
		<date><![CDATA[{$DATE}]]></date>
		<memocnt><![CDATA[{$MEMO_CNT}]]></memocnt>		
		<notice><![CDATA[{$NOTICE}]]></notice>	
		<reply><![CDATA[{$REPLY}]]></reply>	
		<lock><![CDATA[{$LOCK}]]></lock>	
		<new><![CDATA[{$NEW}]]></new>	
		<file><![CDATA[{$FILE}]]></file>
		<f_name><![CDATA[{$F_NAME}]]></f_name>
		<img><![CDATA[{$IMG}]]></img>
		<no><![CDATA[{$NO}]]></no>
      </item>\n	";	

	  $v_num--;
}
##################### ARTICLE LOOP #####################

echo "</root>";
?>