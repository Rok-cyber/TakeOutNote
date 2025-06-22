<?
$code   = $_GET['code'];
$no		= $_GET['no'];
$word	= $_GET['word'];
$seccate	= $_GET['seccate'];
$page	= $_GET['page'];

if($word) $addstring = "&amp;word=".urlencode($word);
if($seccate) $addstring .= "&amp;seccate={$seccate}"; 
if($page) $addstring .="&amp;page={$page}";

$sql = "SELECT m.no as no, m.idx as idx,m.main as main,m.depth as depth,m.name as name, m.email as email, b.homepage as homepage, m.subject as subject,m.cate as cate, b.comment as comment, m.hit as hit, m.reco as reco, m.down as down, b.file as file, b.m_link as mlink, b.s_link as slink, m.cnt_memo as cnt_memo,b.html_type as html_type, b.ip as ip,m.secret as secret, m.icon as icon, m.signdate as signdate, b.id as id, b.passwd as passwd FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no={$no} && m.no=b.no && (b.memo=0 || b.memo='0')";

if(!$data = $mysql->one_row($sql)) movePage("{$Main}?channel=board&amp;code={$code}");

############ 비밀글 체크 ############
if($data['secret']==1 && $my_level < 9) {
    $passwd = isset($_POST['passwd']) ? $_POST['passwd']  : $_GET['passwd'] ; 
	if($data['id']) {
		if($data['id'] != $my_id) alert("비밀글을 볼 수 있는 권한이 없습니다.",'back');
    } 
	else if(!$passwd) movePage("{$Main}?channel=board_confirm&amp;code={$code}&amp;no={$no}{$addstring}"); 
  
    ############ 비밀번호를 비교한다 ####################
    if(!$data['id'] || ($data['id'] != $my_id && $my_level <9)) {
		$origin_pass = $data['passwd'];		
		$user_pass = md5($passwd);
		if($origin_pass != $user_pass) alert("비밀번호가 일치 하지 않습니다.",'back'); 
    }
} 

$sql  = "SELECT * FROM pboard_manager WHERE name = '{$code}'";
if(!$main_data = $mysql->one_row($sql)) alert("삭제된 게시판 이거나 존재하지 않습니다");
$options = explode("||",$main_data['options']);
$B_NAME = $main_data['title'];

$tpl = new classTemplate;
$tpl->define('main',"{$skin}/board_view_{$code}.html");
$tpl->scan_area('main');

$CATE = $category[$data['cate']];

$NAME = htmlspecialchars(stripslashes($data['name']));

if($data['email']) {
	$EMAIL1 = htmlspecialchars(stripslashes($data['email']));
	$tpl->parse('is_email');
}

if($data['homepage']){
   $HOMEPAGE1 = htmlspecialchars(stripslashes($data['homepage']));
   $HOMEPAGE2 = $HOMEPAGE1;
   if(!eregi("http://",$HOMEPAGE1)) $HOMEPAGE1="http://".$HOMEPAGE1;
   $HOMEPAGE = $HOMEPAGE1;
   $tpl->parse("is_home");
}

################## 관련사이트 링크 ##################
if($data['slink']) {         

	$data['slink'] = htmlspecialchars(stripslashes($data['slink']));
	$slink=explode("||",$data['slink']);
    	
	for($i=0,$cnt=count($slink);$i<$cnt;$i++){
		if($slink[$i]) {
			${"SLINK".($i+1)} = $slink[$i];
			if(!eregi("http://",$slink[$i])) {
				$slink[$i]="http://".$slink[$i];
			}
			$SLINK = $slink[$i];			
			
			if($code=='counsel') $tpl->parse("is_slink".($i+1));
				
			$tpl->parse('is_slink');
		}
	}
}

if($HOMEPAGE2 || $SLINK1 || $SLINK2) $tpl->parse("is_other");


################## 첨부 파일 이미지, 동영상 화면에 출력 ##################
if($data['file'] && $options[4]=='Y'){
	$file_list = explode("||",$data['file']);
	for($i=0,$cnt=count($file_list);$i<$cnt;$i++){		
		if(eregi("\.gif|\.jpg|\.pnp|\.bmp",$file_list[$i])) {
		    $IMAGE .= imgSizeCh("{$bo_path}/data/{$code}/",$file_list[$i],"","",$main_data['img_limit'])."<br /><br />";
		} 		
    }
	$tpl->parse('is_img');
}

$SUBJECT = htmlspecialchars(stripslashes($data['subject']));

################## 내용 출력 ##################
$COMMENT = stripslashes($data['comment']);
if($data['html_type']==1) {
	$COMMENT = ieHackCheck($COMMENT);
} else {
    $COMMENT = htmlspecialchars($COMMENT);
    $COMMENT = preg_replace("/  /", "&nbsp;&nbsp;", $COMMENT); 
    $COMMENT = makeLink(nl2br($COMMENT));
}

################## 조회수 증가 ##################
@session_start();
$tmp=explode(',',$_SESSION['pboard_view']);
if(!in_array("{$code}:{$no}",$tmp)){      
	  $sql = "UPDATE pboard_{$code} SET hit = hit+1 WHERE no='{$no}'";
	  $SQL_TIME[] = microtime();
	  $mysql->query($sql);
	  $SQL_TIME[] = microtime();
	  array_push($tmp, $code.':'.$no);
	  $pboard_view = implode(',',$tmp);	  
      $_SESSION['pboard_view'] = $pboard_view;
	  $HIT = $data['hit']+1;
	  unset($tmp);
} else $HIT = $data['hit'];

$D_YYYY	= date("Y",$data['signdate']);
$D_YY	= date("y",$data['signdate']);
$D_MM	= date("m",$data['signdate']);
$D_DD	= date("d",$data['signdate']);
$D_HH	= date("h",$data['signdate']);	 
$D_II	= date("i",$data['signdate']);	 
$D_AP	= date("A",$data['signdate']);	 
$D_ap	= date("a",$data['signdate']);	 

$RECO	= $data['reco'];
$IP		= $data['ip'];

################## 간단한 답글(메모) ##################
$MCNT = 0;	
if($data['cnt_memo'] >0) {      //메모가 있으면

	$MCNT	= $data['cnt_memo'];
	$MCNT2	= $data['cnt_memo'] - $RECO - $DOWN;

	$sql = "SELECT no,homepage,comment,m_link,s_link,file,id,ip FROM pboard_{$code}_body  WHERE memo='1' && idx = {$data[no]} && no >= 30000000 ORDER BY no ASC";
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){
		
		$MNAME = htmlspecialchars(stripslashes($row['homepage']));
		$MDATE = date('Y-m-d A h:i',$row['m_link']);
		$MD_YYYY	= date("Y",$row['m_link']);
		$MD_YY	= date("y",$row['m_link']);
		$MD_MM	= date("m",$row['m_link']);
		$MD_DD	= date("d",$row['m_link']);
		$MD_HH	= date("h",$row['m_link']);	 
		$MD_II	= date("i",$row['m_link']);	 
		$MD_AP	= date("A",$row['m_link']);	 
		$MD_ap	= date("a",$row['m_link']);	 

		$MCOMMENT = htmlspecialchars(stripslashes($row['comment']));
		$MCOMMENT = preg_replace("/  /", "&nbsp;&nbsp;", $MCOMMENT);
		$MCOMMENT = makeLink(nl2br($MCOMMENT));		 
		
		$tpl->parse('mloop');    
		$tpl->parse('is_user','2');
	}

	$tpl->parse('is_memo');    
}

################## 이전글 다음글 ##################
if($code=='counsel') {
	$where = " && b.id = '{$my_id}'";
}
if($word) {
	$where .= " && (INSTR(b.comment,'{$word}') || INSTR(m.subject,'{$word}') || INSTR(m.name,'{$word}'))";
}	
if($seccate && $options[1]=='Y') $where .= " && m.cate = '{$seccate}' ";

$sql = "SELECT m.no as no,m.subject as subject,m.name as name,m.cnt_memo as cnt_meno, m.signdate FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no=b.no && m.no!=1 && m.main > {$data[main]} {$where} ORDER BY m.idx DESC, m.main ASC LIMIT 1";
$prow = $mysql->one_row($sql);
if($prow) {		
	$prow['subject'] = htmlspecialchars(stripslashes($prow['subject']));
	$PLINK		= "{$Main}?channel=board_view&amp;code={$code}&amp;no={$prow[no]}{$addstring}";
	$PSUBJECT	= $prow['subject'];
	if($prow['cnt_memo'] >0) {   //메모글 수
		$PMEMOCNT = $prow['cnt_memo'];
		$tpl->parse('is_pmcnt');
	}
	
	$PD_YYYY	= date("Y",$prow['signdate']);
	$PD_YY	= date("y",$prow['signdate']);
	$PD_MM	= date("m",$prow['signdate']);
	$PD_DD	= date("d",$prow['signdate']);
	$PD_HH	= date("h",$prow['signdate']);	 
	$PD_II	= date("i",$prow['signdate']);	 
	$PD_AP	= date("A",$prow['signdate']);	 
	$PD_ap	= date("a",$prow['signdate']);	 

	$PNAME = $prow['name'];
	$tpl->parse('is_prev');
}

$sql="SELECT m.no as no,m.subject as subject,m.name as name,m.cnt_memo as cnt_meno, m.signdate FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no=b.no && m.no!=1 && m.main < {$data[main]} {$where} LIMIT 1";
$nrow = $mysql->one_row($sql);

if($nrow) {
	$nrow['subject'] = htmlspecialchars(stripslashes($nrow['subject']));
	$NLINK		= "{$Main}?channel=board_view&amp;code={$code}&amp;no={$nrow[no]}{$addstring}";
	$NSUBJECT	= $nrow['subject'];
	if($nrow['cnt_memo'] >0) {   //메모글 수
	   $NMEMOCNT = $nrow['cnt_memo'];
	   $tpl->parse('is_nmcnt');
	}

	$ND_YYYY	= date("Y",$prow['signdate']);
	$ND_YY	= date("y",$nrow['signdate']);
	$ND_MM	= date("m",$nrow['signdate']);
	$ND_DD	= date("d",$nrow['signdate']);
	$ND_HH	= date("h",$nrow['signdate']);	 
	$ND_II	= date("i",$nrow['signdate']);	 
	$ND_AP	= date("A",$nrow['signdate']);	 
	$ND_ap	= date("a",$nrow['signdate']);	 

	$NNAME = $nrow['name'];
	$tpl->parse('is_next');
}

$LINK5 = "board.php?code={$code}{$addstring}";    // 목록보기 링크 

$tpl->parse('main');
$tpl->parse('close');
?>
