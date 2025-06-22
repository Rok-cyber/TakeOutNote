<?
$Main2 = str_replace("&","|",$Main);
$no		= isset($_GET['no'])  ? $_GET['no']:'';

##################### 게시판 자동등록 방지(글쓰기폼연결용) #####################
$ck_w = base64_encode(time()).$cook_rand; //현재 시간 정보를 생성 ...  글쓰기 버튼에서 받아옴
$ck_w2 = md5($ck_w.$cook_rand); //암호화

$sql = "SELECT m.no as no, m.idx as idx,m.main as main,m.depth as depth,m.name as name, m.email as email, b.homepage as homepage, m.subject as subject,m.cate as cate, b.comment as comment, m.hit as hit, m.reco as reco, m.down as down, b.file as file, b.m_link as mlink, b.s_link as slink, m.cnt_memo as cnt_memo,b.html_type as html_type, b.ip as ip,m.secret as secret, m.icon as icon, m.signdate as signdate, b.id as id, b.passwd as passwd FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no={$no} && m.no=b.no && (b.memo=0 || b.memo='0')";

$SQL_TIME[] = microtime();
if(!$data = $mysql->one_row($sql)) movePage($Main.$addstring);
$SQL_TIME[] = microtime();

############ 비밀글 체크 ############
if($data['secret']==1 && $my_level < 9) {
    $passwd = isset($_POST['passwd']) ? $_POST['passwd']  : $_GET['passwd'] ; 
	if($data['id']) {
		if($data['id'] != $my_id) alert($LANG_ACC_MSG[3],'back');
    } 
	else if(!$passwd) movePage($Main.$addstring."&amp;pmode=secret&amp;no={$no}"); 
  
    ############ 비밀번호를 비교한다 ####################
    if(!$data['id'] || ($data['id'] != $my_id && $my_level <9)) {
		$origin_pass = $data['passwd'];		
		$user_pass = md5($passwd);
		if($origin_pass != $user_pass) alert($LANG_CHK_MSG[0],'back'); 
    }
} 

$tpl = new classTemplate;
$tpl->define('main',"{$skin}/view.html");
$tpl->scan_area('main');

$CATE = $category[$data['cate']];

$NAME2 = htmlspecialchars(stripslashes($data['name']));
if($data['icon'] && @file_exists("{$bo_path}/icon/{$data[icon]}")) $NAME = "<img src='{$bo_path}/icon/".urlencode($data['icon'])."' alt='{$NAME2}' />";
else $NAME = $NAME2;

if($data['email']) {
	$EMAIL1 = htmlspecialchars(stripslashes($data['email']));
	$EMAIL = "{$bo_path}/mailto.php?mail=".previlEncode($EMAIL1);
	$tpl->parse('is_email');
}

if($data['homepage']){
   $HOMEPAGE1 = htmlspecialchars(stripslashes($data['homepage']));
   $HOMEPAGE2 = $HOMEPAGE1;
   if(!eregi("http://",$HOMEPAGE1)) $HOMEPAGE1="http://".$HOMEPAGE1;
   $HOMEPAGE = $HOMEPAGE1;
   $tpl->parse("is_home");
}

if($data['secret']==1) $TYPE = $LANG_ETC_MSG[1];
else $TYPE = $LANG_ETC_MSG[2];

################## 파일 다운로드 ###################
if($data['file'] && $options[4]=='Y') {         
    $down_file=explode("||",$data['file']);
    $SAVEDFILE='';
    for($i=0,$cnt=count($down_file);$i<$cnt;$i++){	 
		if(eregi("\.gif|\.jpg|\.pnp|\.bmp",$down_file[$i])) continue;
		if($i!=0) $FDOT=',&nbsp;';
		$FLINK	= "{$bo_path}/download.php?code={$code}&amp;no={$data[no]}&amp;filename=".urlencode($down_file[$i]);
		$FILE	= $down_file[$i];
		$FSIZE	= getFilesize("{$bo_path}/data/{$code}/".$down_file[$i]);
		$tpl->parse('pds_loop');	
		$ck_file = 'Y';
    }
    if($data['down']) $DOWN = $data['down'];
    else $DOWN = 0;
    if($ck_file=='Y') $tpl->parse('is_pds');
}

################## 관련사이트 링크 ##################
if($data['slink']) {         

	$data['slink'] = htmlspecialchars(stripslashes($data['slink']));
	$slink=explode("||",$data['slink']);
    
	################## 설문조사용 ##################
	if($main_data['s_name']=='vote') {
		for($i=0,$cnt=count($slink);$i<$cnt;$i++){
			if($slink[$i]) {
				$VOTES = "{$bo_path}/vote.php?code={$code}&amp;no={$data[no]}&amp;no2={$i}{$addstring}&amp;Main=$Main2";
				$data['mlink'] = htmlspecialchars(stripslashes($data['mlink']));    
				$mlink=explode("||",$data['mlink']);
				
				if($mlink[$i]) $VP1 = round($mlink[$i]/$data['reco']*100);
				else { 
					$VP1		= 0;
					$mlink[$i]	= 0;
				}
				$VP2	= 100 - $VP1;
				$VOTEP	= "{$mlink[$i]} ({$VP1}%)";
				$SLINK	= $slink[$i];							
				$tpl->parse('is_slink');
			}
		}
	}
	else {			
		
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
}

if($HOMEPAGE2 || $SLINK1 || $SLINK2) $tpl->parse("is_other");

################## 동영상 링크 출력 ##################
if($data['mlink']) {
	$data['mlink'] = htmlspecialchars(stripslashes($data['mlink']));    
	$mlink=explode("||",$data['mlink']);
	$MLINK1 =$mlink[0];
	if(!eregi("http://|mms://",$mlink[0])) $mlink[0]="http://".$mlink[0];
    $ext = getExtension($mlink[0]);
	if($mlink[1] > $main_data['img_limit']) $mlink[1]=$main_data['img_limit'];
	if($mlink[1]) $width= " width={$mlink[1]} ";
	if($mlink[2]) $height= " height={$mlink[2]} ";
    if($ext =='swf') {   //플래쉬 파일일때
	    $MLINK = "<EMBED pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' src='{$mlink[0]}' {$width} {$height} type=application/x-shockwave-flash quality='high'></EMBED>";
    } 
	else {
        $MLINK = "<EMBED src='{$mlink[0]}' {$width} {$height}></EMBED>";
    }
	$tpl->parse('is_mlink');
}

################## 첨부 파일 이미지, 동영상 화면에 출력 ##################
if($data['file'] && $options[4]=='Y'){
	$file_list = explode("||",$data['file']);
	for($i=0,$cnt=count($file_list);$i<$cnt;$i++){		
		if(eregi("\.gif|\.jpg|\.pnp|\.bmp",$file_list[$i])) {
		    $IMAGE .= imgSizeCh("{$bo_path}/data/{$code}/",$file_list[$i],"","",$main_data['img_limit'])."<br /><br />";
		} 
		else if(eregi("\.flv",$file_list[$i])) {
			if(eregi("\.gif|\.jpg|\.pnp|\.bmp|\.swf",$file_list[$i-1])) $tmp_img = "{$bo_path}/data/{$code}/{$file_list[$i-1]}";
			else $tmp_img = '';
			$IMAGE .= "
					<script type='text/javascript' src='{$bo_path}/player/swfobject.js'></script>
					<p id='player{$i}'><a href='http://www.macromedia.com/go/getflashplayer' target='_blank'>Get the Flash Player</a> to see this player.</p>
					<script type='text/javascript'>
						var s1 = new SWFObject('{$bo_path}/player/flvplayer.swf','single','400','300','7');
						s1.addParam('allowfullscreen','true');
						s1.addVariable('file','../data/{$code}/{$file_list[$i]}');
						s1.addVariable('image','{$tmp_img}');
						s1.write('player{$i}');
					</script><br /><br />
					";
		}
		else if(eregi("\.flv|\.mpeg|\.mpg|\.avi|\.wmv|\.asf|\.mov|\.mp4",$file_list[$i])) {
			if(eregi("\.gif|\.jpg|\.pnp|\.bmp",$file_list[$i-1])) $tmp_img = "../data/{$code}/".urlencode($file_list[$i-1]);
			else $tmp_img = "../{$skin}/img/blank.gif";
			
			$browser = ckBrowser();
			if(eregi('Explorer',$browser)) { //익스일경우
				$IMAGE .= "
					<iframe name='mplayer' id='mplayer' border='0' frameborder='0' framespacing='0' marginheight='0' marginwidth='0'  scrolling='no'	src='{$bo_path}/player/mplayer.html?url=../data/{$code}/".urlencode($file_list[$i])."&amp;img={$tmp_img}' width='422' height='364'></iframe><br /><br />
					";
			}
			else { //익스가 아닐경우
				$IMAGE .= "
					<OBJECT id='mediaPlayer' width='400' height='300' 
					classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95' 
					codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#
					Version=5,1,52,701'
					standby='Loading Microsoft Windows Media Player components...' type='application/
					x-oleobject'>					
					<param name='fileName' value='{$bo_path}/data/{$code}/{$file_list[$i]}'>
					<EMBED type='application/x-mplayer2'
					pluginspage='http://microsoft.com/windows/mediaplayer/en/download/' width='400' height='300'
					id='mediaPlayer' name='mediaPlayer'	src='{$bo_path}/data/{$code}/{$file_list[$i]}'>
					</EMBED>
					</OBJECT><br /><br />
				";
			}
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

	$tpl->parse("is_talk_title");

	$sql = "SELECT no,homepage,comment,m_link,s_link,file,id,ip FROM pboard_{$code}_body  WHERE memo='1' && idx = {$data[no]} && no >= 30000000 ORDER BY no ASC";
	$SQL_TIME[] = microtime();
	$mysql->query($sql);
	$SQL_TIME[] = microtime();
	while($row = $mysql->fetch_array()){
		
		if($row['file'] && @file_exists("{$bo_path}/icon/{$row[file]}")) $MNAME = "<img src='{$bo_path}/icon/".urlencode($row['file'])."' align='absmiddle'>";
		else $MNAME = htmlspecialchars(stripslashes($row['homepage']));
		
		if($row['s_link']) {
			if($row['s_link']=='Y') $tpl->parse("is_talk_icon1","1");
			else if($row['s_link']=='N') $tpl->parse("is_talk_icon2","1");
			else $tpl->parse("is_talk_icon3","1");
		}

		
		$MDATE = date('Y-m-d A h:i',$row['m_link']);
		$MD_YYYY	= date("Y",$row['m_link']);
		$MD_YY	= date("y",$row['m_link']);
		$MD_MM	= date("m",$row['m_link']);
		$MD_DD	= date("d",$row['m_link']);
		$MD_HH	= date("h",$row['m_link']);	 
		$MD_II	= date("i",$row['m_link']);	 
		$MD_AP	= date("A",$row['m_link']);	 
		$MD_ap	= date("a",$row['m_link']);	 

		if($row['id']) { //회원글일때 (관리자도 삭제가능)
			if($row['id']==$my_id || $my_level>8) {    					
				$MDEL = "{$bo_path}/me_insert.php?code={$code}&amp;no={$data[no]}&amp;no2={$row[no]}&amp;pmode=del{$addstring}&amp;Main=$Main2";
				$tpl->parse('is_user','1');
			 }
		} 
		else {     // 비회원이 글일때
			if($my_level>8) {
				$MDEL = "{$bo_path}/me_insert.php?code={$code}&amp;no={$data[no]}&amp;no2={$row[no]}&amp;pmode=del{$addstring}&amp;Main=$Main2";
			} 
			else $MDEL = "{$Main}&amp;no={$data[no]}&amp;no2={$row[no]}&amp;pmode=mdel{$addstring}";
			$tpl->parse('is_user','1');
		}

		$MCOMMENT = htmlspecialchars(stripslashes($row['comment']));
		$MCOMMENT = preg_replace("/  /", "&nbsp;&nbsp;", $MCOMMENT);
		$MCOMMENT = makeLink(nl2br($MCOMMENT));		 
		
		$tpl->parse('mloop');    
		if($row['s_link']) {
			$tpl->parse("is_talk_icon1","2");
			$tpl->parse("is_talk_icon2","2");
			$tpl->parse("is_talk_icon3","2");
		}
		$tpl->parse('is_user','2');
	}
}

if($options[5] =='Y') {
?>

<script language="javascript">
<!--
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, ""); 
}

function checkItme(cks) {      
    f=document.comform;
<? if(!$my_name) { ?>
	f.mename.value=f.mename.value.trim();
    if(!f.mename.value) {
        alert('<?=$LANG_FORM_MSG[0]?>');
        f.mename.focus();
        return false;
    }
    
	f.mepasswd.value=f.mepasswd.value.trim();
    if(!f.mepasswd.value) {
        alert('<?=$LANG_FORM_MSG[4]?>');
        f.mepasswd.focus();
        return false;
    }
<? } ?>
    f.mecomment.value=f.mecomment.value.trim();
	if(!f.mecomment.value) {
        alert('<?=$LANG_FORM_MSG[5]?>');
        f.mecomment.focus();
        return false;
    }
	f.ck_w.value	= '<?=$ck_w?>';
	f.ck_w2.value	= '<?=$ck_w2?>';

	if(cks==1) f.submit();
}
//-->
</script>

<?
	if(($acc_level[9] == '!=' && ($acc_level[3]==$my_level || $my_level>8)) || ($acc_level[9] == '<' && $acc_level[3]<=$my_level)) { 

		$MACTION = "{$bo_path}/me_insert.php?code={$code}&amp;pmode=write&amp;no={$data[no]}{$addstring}";
		if($my_icon && @file_exists("{$bo_path}/icon/{$my_icon}")) {
			$my_name = "<img src='{$bo_path}/icon/".urlencode($my_icon)."' align='absmiddle'>";
		} 
		if($my_name) $tpl->parse('is_mname1');
		else $tpl->parse('is_mname2');
		
		if($code=='counsel' || $code=='affil_counsel') {
			if($MCNT==0) $tpl->parse('is_mwrite');
		}
		else $tpl->parse('is_mwrite');
	}
	$tpl->parse('is_memo');
}

################## 이전글 다음글 ##################
if($code=='counsel' && $admin_board2 !='Y') {
	$where = " && b.id = '{$my_id}'";
}

if($code=='affil_counsel' && $admin_board2 !='Y') {
	$where = " && b.id = '{$a_my_id}'";
}

if($options[0] !='Y') { 
    if($field && $word) {
	    if($field=='comment') $where .= "&& INSTR(b.{$field},'{$word}') > 0  ";
	    else $where .= "&& INSTR(m.{$field},'{$word}') > 0 ";
	}
	
	if($seccate && $options[1]=='Y') $where .= " && m.cate = '{$seccate}' ";
  
	$sql = "SELECT m.no as no,m.subject as subject,m.name as name,m.cnt_memo as cnt_meno, m.signdate FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no=b.no && m.no!=1 && m.main > {$data[main]} {$where} ORDER BY m.idx DESC, m.main ASC LIMIT 1";
	$SQL_TIME[] = microtime();
	$prow = $mysql->one_row($sql);
	$SQL_TIME[] = microtime();
	if($prow) {		
		$prow['subject'] = htmlspecialchars(stripslashes($prow['subject']));
		if($main_data['word_limit']!=0) $prow['subject'] = hanCut($prow['subject'],($main_data['word_limit']+20));   //제목 제한 글수
		$PLINK		= "{$Main}{$addstring}&amp;pmode=view&amp;no={$prow[no]}";
		$PSUBJECT	= $prow[subject];
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
	$SQL_TIME[] = microtime();
	$nrow = $mysql->one_row($sql);
	$SQL_TIME[] = microtime();
	if($nrow) {
		$nrow['subject'] = htmlspecialchars(stripslashes($nrow['subject']));
		if($main_data['word_limit']!=0) $nrow['subject'] = hanCut($nrow['subject'],($main_data['word_limit']+20));   //제목 제한 글수
		$NLINK		= "{$Main}{$addstring}&amp;pmode=view&amp;no={$nrow[no]}";
		$NSUBJECT	= $nrow[subject];
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
	unset($prow);
	unset($nrow);
}


##################***** 글쓰기 권한 체크 ##################
if(($acc_level[8] == '!=' && ($acc_level[2]==$my_level || $my_level>8)) || ($acc_level[8] == '<' && $acc_level[2]<=$my_level)) {  
	$LINK1 = $Main.$addstring."&amp;pmode=reply&amp;no={$no}&amp;ck_w={$ck_w}&amp;ck_w2={$ck_w2}"; //답글쓰기링크 
	$LINK2 = $Main.$addstring."&amp;pmode=modify&amp;no={$no}"; //수정하기 링크 
	$LINK3 = $Main.$addstring."&amp;pmode=del&amp;no={$no}"; //삭제하기 링크 
	$LINK4 = $Main.$addstring."&amp;pmode=write&amp;ck_w={$ck_w}&amp;ck_w2={$ck_w2}"; //글쓰기 링크 
	
	if(($acc_level[13] == '!=' && ($acc_level[12]==$my_level || $my_level>8)) || ($acc_level[13] == '<' && $acc_level[12]<=$my_level)) { //답글권한 
		if($data['idx']!=0) $tpl->parse('is_reply');
	}

	if(!$data['id'] || $data['id'] == $my_id || $my_level > 8) {
		if($data['secret']==1) {
			if($data['depth']==0 || $my_level > 8) $tpl->parse('is_modify');
		}
		else $tpl->parse('is_modify');
	}
	$tpl->parse('is_write');
	
	if(($acc_level[11] == '!=' && ($acc_level[5]==$my_level || $my_level>8)) || ($acc_level[11] == '<' && $acc_level[5]<=$my_level)) { //추천권한 
		$tmp=explode(',',$_SESSION['pboard_reco']);
		
		if(!in_array("{$code}:{$no}",$tmp)) { //추천
			$LINK5 = "{$bo_path}/reco.php?Main={$Main2}&amp;code={$code}&amp;no={$no}{$addstring}";
			$tpl->parse('is_reco');
		}
	}
}

$LINK5 = $Main.$addstring;    // 목록보기 링크 
$sACTION =  $Main.$addstring;

$imgLimit = $main_data['img_limit'];

$tpl->parse('main');
$tpl->tprint('main');


if($options[0] =='Y') {   //전체 목록 출력
	$tpl->close();
    $mysql->free_result();
	echo "<br /><br />";
	include "$bo_path/list.php";	
}

?>
