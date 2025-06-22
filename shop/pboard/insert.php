<?
ob_start();
@set_time_limit(0);
$bo_path = ".";
include "{$bo_path}/init.php";

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) Error($LANG_ERR_MSG[0]); 
$access_ip	= $_SERVER['REMOTE_ADDR'];
$signdate	= time();

###################### 게시판 자동등록 방지(등록파일용) #####################
if($pmode=='write' || $pmode=='reply'){
	$ck_w	= isset($_POST['ck_w'])		? $_POST['ck_w'] : '';
	$ck_w2	= isset($_POST['ck_w2'])	? $_POST['ck_w2'] : '';

	$ck_w3 = md5($ck_w.$cook_rand);     //  암호화
	$ck_w4 = base64_decode($ck_w);
	$i=intval($signdate) - intval($ck_w4);

	if(!$ck_w || !$ck_w2 || ($ck_w2 !=$ck_w3) || $i<3 || $i>1800) Error($LANG_ERR_MSG[7]); 

	$sql = "SELECT ck_auto FROM pboard_manager WHERE name = '{$code}'";     //등록키 불러옴
	
	if(!$ck_list = $mysql->get_one($sql)) {
	    $ck_num = 1;
	    $ck_data[1] = $ck_w4;
	}  
	else {  
	    $ck_data =explode("|",$ck_list); 
	    $ck_num = 0;         //저장된 키 갯수

	    for($j=1;$j<=$ck_data[0];$j++) {             //30분안에 등록된 키는 에러 처리    
		    if($ck_data[$j] == $ck_w4)  Error($LANG_ERR_MSG[7]); 
			$ck_time = intval($signdate)  -  intval($ck_data[$j]);  
		
			if($ck_time > 1800) {
			   $ck_data[$j] = "";     //30분이 지난 키값은 삭제
			}  
			else  {
			   $ck_num ++;
			   $ck_data[$ck_num] = $ck_data[$j];   //배열 다시지정
			}
		}

		$ck_num ++;             //지금값 저장
		$ck_data[$ck_num] = $ck_w4;
	}  

	$w2_list = $ck_num;            // 배열을 조인 후 저장
	for($w=1;$w<=$ck_num;$w++){
       	if(intval($ck_data[$w]) < 1000 or !$ck_data[$w]) Error($LANG_ERR_MSG[7]); 
		$w2_list = $w2_list."|".$ck_data[$w];
	}

	$sql = "UPDATE pboard_manager SET ck_auto = '{$w2_list}' WHERE name = '{$code}'";     
	$mysql->query($sql);

} //end of if(write)

$notice_cg1 = $notice_cg2 = "";
######################### 변수정의 및 AddString 정의 ################################
$page		= isset($_GET['page'])		? $_GET['page'] : '';
$spage		= isset($_GET['spage'])		? $_GET['spage'] : '';
$slast_idx	= isset($_GET['slast_idx'])	? $_GET['slast_idx'] : '';
$field		= isset($_GET['field'])		? $_GET['field'] : '';
$word		= isset($_GET['word'])		? $_GET['word'] : '';
$seccate	= isset($_GET['seccate'])	? $_GET['seccate'] : '';
$no			= isset($_GET['no'])	? $_GET['no'] : $_POST['no'];
$Main		= isset($_POST['Main'])	? "../".str_replace("../../","",addslashes($_POST['Main'])) : 'pboard.php';

$html_type	= addslashes($_POST['html_type']);
$notice		= addslashes($_POST['notice']);
$secret		= addslashes($_POST['secret']);
$remail		= addslashes($_POST['remail']);
$cate		= addslashes($_POST['cate']);
$passwd		= addslashes($_POST['passwd']);
$delfile	= $_POST['delfile'];
$mlink		= $_POST['mlink'];
$slink		= $_POST['slink'];

$name		= addslashes($_POST['name']);
$subject	= addslashes($_POST['subject']);
$email		= addslashes($_POST['email']);
$homepage	= addslashes($_POST['homepage']);
$comment	= addslashes($_POST['comment']);

$tmp_dir	= previlDecode(addslashes($_POST['tmp_dir']));

if($field && $word) $addstring = "&amp;field={$field}&amp;word={$word}";
if($page) $addstring .="&amp;page={$page}";
if($seccate) $addstring .="&amp;seccate={$seccate}";
if($spage && $slast_idx) $addstring .= "&amp;slast_idx={$slast_idx}&amp;spage={$spage}";

if(($pmode=='modify' || $pmode=='del') && $my_level <9){
	if($pmode=='modify') $passwd = previlDecode($_POST['impass']); 
	############ 비밀번호를 비교한다 ####################
	$sql = "SELECT passwd,id FROM pboard_{$code}_body WHERE no={$no}";
	$row = $mysql->one_row($sql);
	if(!$row['id'] || ($row['id'] != $my_id && $my_level <9)) {
		$origin_pass = $row['passwd'];
		$user_pass = md5($passwd);
		if($origin_pass != $user_pass) alert($LANG_CHK_MSG[0],'back'); 
    }

	$sql = "SELECT depth, secret FROM pboard_{$code} WHERE no={$no}";
	$row = $mysql->one_row($sql);

	if($row['secret']==1 && $row['depth']>0) {
		if($pmode=='modify') alert($LANG_ACC_MSG[5],'back');
		else alert($LANG_ACC_MSG[6],'back');
	}
} 
if($pmode=='modify') {
    ############ 등록인 정보 불러오기 ####################
	$sql = "SELECT  m.name, m.email, b.homepage FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no={$no} && m.no=b.no && b.memo=0";
	$row = $mysql->one_row($sql);
	if(!$name) $name = $row['name'];
    if(!$email) $email = $row['email'];
	if(!$homepage) $homepage = $row['homepage'];
}

// 변수값 체크

if(!$name && $my_name) $name = $my_name;
if(!$email && $my_email) $email = $my_email;
if(!$homepage && $my_homepage && $code!='sales') $homepage = $my_homepage;

if($pmode!='del' && $pmode!='ad_del'){
	chrtrim($name);
	chrtrim($subject);
	chrtrim($comment);
}

################ 파일 업로드 ########################
if($options[4]=='Y') {
	for($i=1;$i<=$options[10];$i++) {
		if(!eregi('none',$_FILES['userfile'.$i][tmp_name]) && $_FILES['userfile'.$i][tmp_name]) {
		    $file = upFile($_FILES['userfile'.$i][tmp_name],$_FILES['userfile'.$i][name],"{$ho_path}/pboard/data/{$code}","",$img_cnt);
				
			$up_nums[]	= $i;
			$up_files[] = $file;			
	    }      
	}
}

if($mlink && $options[7]=='Y') {
	$mlink = $mlink."||".$width."||".$height;
	$mlink = addslashes($mlink);
}

if($slink && $options[8] =='Y'){  // 관련사이트 링크
	$slink = @implode("||",$slink);       
	$slink = addslashes($slink);
}

switch($pmode) {
	case "write" : 
		if($notice) {  //공지사항일 경우
			$sql = "SELECT file FROM pboard_{$code}_body WHERE no=1";
            $last_idx=0;
			if($data = $mysql->get_one($sql)){
				$data  = explode("|",$data);
				$total_no = $data[0]+ 1;
				$order_no = $data[1]-1;
			} 
			else {
				$total_no=1;
                $order_no = "9999";
			}
			$order_num = $order_no."000";
            $ntotal_record = $total_no."|".$order_no;

			$sql = "SELECT main, depth FROM pboard_{$code} WHERE no=1";
			$data = $mysql->one_row($sql);
			$tmp_order_no	= $data['main'];
			$tmp_last_idx	= $data['depth'];
			if($tmp_order_no==0) { $tmp_last_idx--; $tmp_order_no=10000; }
			$tmp_order_no--;
			$access_ip .= "|{$tmp_last_idx}|{$tmp_order_no}";

			$secret = 0;
        } 
		else { 
            ########## 마지막 값을 가져온다. main ->글정렬번호,depth -> idx ##########
			$sql = "SELECT m.main as main, m.depth as depth, b.comment as comment FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no=b.no && m.idx=999";
			$data = $mysql->one_row($sql);
			$order_no	= $data['main'];
			$last_idx	= $data['depth'];
			if($order_no==0) { $last_idx--; $order_no=10000; }
			$t_record	= explode("|",$data['comment']);
			$sec_idx	= 999 - $last_idx;
			$t_record[0]++;
			$t_record[$sec_idx]++;
			$total_record = join("|",$t_record);
			$order_no--;
			$order_num	= $order_no."000";
        }  

		$sql = "SELECT max(no) FROM pboard_{$code}";
        $insert_no = $mysql->get_one($sql)+1;

		$view_file = $file_list = '';		
		if($options['19']=='Y') {
			######################## 임시 저장 파일 이동 ##################		
			$handle	= @opendir($tmp_dir);
			while ($file = @readdir($handle)) {
				if($file != '.' && $file != '..') $tmps[] = $file;			
			}
			closedir($handle);	

			$dir_name = $view_file = "";
			$cnts = count($tmps);

			if($cnts==0) @RmDir($tmp_dir);					
			else {
				sort($tmps);
				$dir_name = "data/{$code}/".date("Ym")."/article_{$insert_no}";
				$comment = str_replace($tmp_dir,$dir_name,$comment);
				$tmps2 = explode($dir_name,$comment);
				
				for($i=0;$i<$cnts;$i++){
					if(eregi($tmps[$i],$tmps2[1]) && eregi("\.jpg|\.jpeg|\.gif|\.pnp|\.swf|\.bmp",$tmps[$i])) {
						$view_file = date("Ym")."/article_{$insert_no}/{$tmps[$i]}";				
						break;
					}
				}			
				rename("{$tmp_dir}/","{$dir_name}/");				
				SetCookie("tmp_dir","",-999,"/"); 			
			}	
		} 

		if($options[4]=='Y' && $up_files) {
			$view_file = $up_files[0];
			$file_list = join("||",$up_files);
		}
					
		############ 글 등록 쿼리 ####################		
		$sql = "INSERT INTO pboard_{$code} VALUES ('{$insert_no}', '{$last_idx}', '{$order_num}', '{$depth}', '{$name}', '{$email}', '{$subject}', '{$cate}', 0, '{$reco}', '{$down}', 0, '{$view_file}', '{$secret}', '{$my_icon}', '{$signdate}')";		
		$mysql->query($sql);		
		
		$sql = "INSERT INTO pboard_{$code}_body VALUES ('{$insert_no}', '{$last_idx}', 0, '".md5($passwd)."', '{$homepage}', '{$comment}', '{$mlink}', '{$slink}', '{$html_type}', '{$remail}', '{$file_list}', '{$my_id}', '{$access_ip}')";
		$mysql->query($sql,$code,$insert_no);
		$msg = $LANG_ETC_MSG[4];
		$pstr = "&pmode=view&no={$insert_no}";
    break;

    case "modify" :
 	    ############ 글 수정 쿼리 ####################
		$view_file = $file_list = '';	
		if($options['19']=='Y') {
			######################## 임시 저장 파일 이동 ##################		
			$sql = "SELECT signdate FROM pboard_{$code} WHERE no={$no}";
			$tmp_date = $mysql->get_one($sql);

			if(is_dir("data/{$code}/".date("Ym",$tmp_date)."/article_{$no}")) {
				$tmp_dir = "data/{$code}/".date("Ym",$tmp_date)."/article_{$no}";
			}
			else $tmp_date = time();
			
			$handle	= @opendir($tmp_dir);
			while ($file = @readdir($handle)) {
				if($file != '.' && $file != '..') $tmps[] = $file;			
			}
			closedir($handle);	

			$dir_name = $view_file = "";
			$cnts = count($tmps);

			if($cnts==0) @RmDir($tmp_dir);					
			else {
				sort($tmps);
				$dir_name = "data/{$code}/".date("Ym",$tmp_date)."/article_{$no}";
				$comment = str_replace($tmp_dir,$dir_name,$comment);
				$tmps2 = explode($dir_name,$comment);
								
				for($i=0;$i<$cnts;$i++){
					if(eregi($tmps[$i],$tmps2[1]) && eregi("\.jpg|\.jpeg|\.gif|\.pnp|\.swf|\.bmp",$tmps[$i])) {
						$view_file = ", file = '".date("Ym",$tmp_date)."/article_{$no}/{$tmps[$i]}'";				
						break;
					}
				}	
				rename("{$tmp_dir}/","{$dir_name}/");			
				SetCookie("tmp_dir","",-999,"/"); 
			}	
		} 
		
		if($options[4]=='Y' && ($up_files || $delfile)){                 
			$sql = "SELECT file FROM pboard_{$code}_body WHERE no = {$no} && memo=0";
			$sa_file = $mysql->get_one($sql);						
			
			if($sa_file) {
				$sa_file= explode("||",$sa_file);
				$ck_nums = count($sa_file);

				if($up_nums) {				
					for($i=0,$cnt=count($up_nums);$i<$cnt;$i++){
						if(($ck_nums + ($i+1)) <= $option[4]) {
							$sa_file[$ck_nums+$i] = $up_files[$i];						
						}
						else {
							$delfile[]	= $sa_file[$i];
							$sa_file[$i] = $up_files[$i];												
						}
					}
				}

				if($delfile){
					for($i=0,$cnt=count($delfile);$i<$cnt;$i++){  
						$delfile2 = addslashes($delfile[$i]);
						delFile("{$bo_path}/data/{$code}/{$delfile2}");
						for($k=0,$cnt=count($sa_file);$k<$cnt;$k++) {
							if($sa_file[$k] == $delfile[$i]) {								
								$sa_file[$k] = '';
								array_splice($sa_file,$k,1);
							}
						}		
					} 
					unset($delfile2);
				} 

				$view_file = ", file = '{$sa_file[0]}'";
				$file_list = join("||",$sa_file);
				$file_list = " , file = '{$file_list}'";
			}
			else {
				$view_file = ", file = '{$up_files[0]}'";
				$file_list = join("||",$up_files);
				$file_list = " , file = '{$file_list}'";
			}  
					
        }

		$sql = "SELECT idx, main FROM pboard_{$code} WHERE no={$no}";
		$tmp_row = $mysql->one_row($sql);

		if($notice=='1') {
			if($tmp_row['idx']!=0) {
				$sql = "SELECT file FROM pboard_{$code}_body WHERE no=1";
				$last_idx=0;
				if($data = $mysql->get_one($sql)){
					$data  = explode("|",$data);
					$total_no = $data[0]+ 1;
					$order_no = $data[1]-1;
				} 
				else {
					$total_no=1;
					$order_no = "9999";
				}
				$ntotal_record = $total_no."|".$order_no;

				$sql = "SELECT ip FROM pboard_{$code}_body WHERE no={$no}";
				$access_ip = $mysql->get_one($sql);
				
				if($order_no!='9999') {
					$sql = "SELECT signdate FROM pboard_{$code} WHERE no={$no}";
					$tmp_date = $mysql->get_one($sql);
					
					$sql = "SELECT main FROM pboard_{$code} WHERE idx='0' && signdate > {$tmp_date} ORDER BY signdate ASC LIMIT 1";
					if($tmp_ono = $mysql->get_one($sql)) {
						$sql = "UPDATE pboard_{$code} SET main = main - 1000 WHERE no>1 && idx='0' && main<={$tmp_ono}";
						$mysql->query($sql);
						$order_no = str_replace("000","",$tmp_ono);
					}
				}

				$tmp_add = "idx = '0', main = '{$order_no}000', ";
				$tmp_row['main'] = str_replace("000","",$tmp_row['main']);	
				$tmp_add2 = ", ip = '{$access_ip}|{$tmp_row['idx']}|{$tmp_row['main']}', idx = '0'";
				$notice_cg2 = 'Y';				
			}
			$secret = 0;
		}
		else {			
			if($tmp_row['idx']==0) {
				$sql = "SELECT ip FROM pboard_{$code}_body WHERE no={$no}";
				$tmp_vls = $mysql->get_one($sql);
				$tmp_vls = explode("|",$tmp_vls);
				$tmp_add = "idx = '{$tmp_vls[1]}', main = '{$tmp_vls[2]}000', ";
				$tmp_add2 = ", ip = '{$tmp_vls[0]}', idx = '{$tmp_vls[1]}'";
				$notice_cg1 = 'Y';
			}
		}		
        
        $sql = "UPDATE pboard_{$code} SET {$tmp_add} name='{$name}', email='{$email}', subject='{$subject}', cate='{$cate}', secret='{$secret}' {$view_file} {$preco} WHERE no={$no}";
        $mysql->query($sql);
		
		$sql = "UPDATE pboard_{$code}_body SET homepage='{$homepage}', comment='{$comment}', s_link='{$slink}', m_link='{$mlink}', html_type='{$html_type}', remail='{$remail}' {$file_list} {$tmp_add2} WHERE no={$no} && memo=0";
		$mysql->query($sql);		
 		$msg = "수정했습니다!";
		$pstr = "&pmode=view&no={$no}";							
   break;

   case "reply" : case "del" :
        ############ 원글의 정보를 가져온다 ####################
		$sql = "SELECT m.idx,m.main,m.depth,m.secret,m.cnt_memo,m.email, b.passwd, b.remail,b.id, b.file FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no={$no} && m.no=b.no && b.memo=0";
		$data = $mysql->one_row($sql);
		$last_idx	= $data['idx'];
		$order_no	= $data['main']+1;
		$depth		= $data['depth'];
		$no_len		= strlen($order_no)-3;
		$no_len2	= $no_len+1;
		$ch_num1	= substr($order_no,0,$no_len);
		$ch_num2	= substr($order_no,$no_len,3);
					
		$ori_email = $data['email'];
		$ori_remail = $data['remail'];

		if($data['secret']=='1') {  //비밀글 답글일때는 원글의 비밀번호를 저장
            $s_passwd = "{$data['passwd']}";
			$s_id = $data['id']; 
        } 
		else {
			$s_passwd = md5($passwd);
			$s_id = $my_id;
        }
	
		############ 전체 글수를 가져온다 ####################
		if($last_idx==0) {   //공지사항일때
			$sql		= "SELECT file FROM pboard_{$code}_body where no=1";
			$data2		= $mysql->get_one($sql);
			$t_record	= explode("|",$data2);
		}
		else {	
			$sql = "SELECT comment FROM pboard_{$code}_body where no=1";
			$data2		= $mysql->get_one($sql);
			$t_record	= explode("|",$data2);
			$sec_idx	= 999 - $last_idx;
		}
		
		if($pmode=='reply'){  //답글일 경우
		    if($last_idx==0) alert($LANG_CHK_MSG[5],'back'); 			
			############ 글 수를 증가한다 ####################
			$depth++;
			$t_record[0]++;
			$t_record[$sec_idx]++;
			$total_record = join("|",$t_record);
					 
			############ 답글을 정렬한다 ####################
			$sql = "UPDATE pboard_{$code} SET main = main+1 WHERE idx=$last_idx && substring(main,1,{$no_len})={$ch_num1} && substring(main,{$no_len}+1,3) >= {$ch_num2}";
			$mysql->query($sql);
			$order_num = $ch_num1.$ch_num2;

			$sql = "SELECT max(no) FROM pboard_{$code}";
            $insert_no = $mysql->get_one($sql)+1;

			if($options['19']=='Y') {
				######################## 임시 저장 파일 이동 ##################		
				$handle	= @opendir($tmp_dir);
				while ($file = @readdir($handle)) {
					if($file != '.' && $file != '..') $tmps[] = $file;			
				}
				closedir($handle);	

				$dir_name = $view_file = "";
				$cnts = count($tmps);

				if($cnts==0) @RmDir($tmp_dir);					
				else {
					sort($tmps);
					$dir_name = "data/{$code}/article_{$insert_no}";
					$comment = str_replace($tmp_dir,$dir_name,$comment);
					$tmps2 = explode($dir_name,$comment);
					
					for($i=0;$i<$cnts;$i++){
						if(eregi($tmps[$i],$tmps2[1]) && eregi("\.jpg|\.jpeg|\.gif|\.pnp|\.swf|\.bmp",$tmps[$i])) $view_file = "article_{$insert_no}/{$tmps[$i]}";				
					}			
					rename("{$tmp_dir}/","{$dir_name}/");				
					SetCookie("tmp_dir","",-999,"/"); 
					//$file_list = $dir_name;
				}	
			}
			else if($options[4]=='Y' && $up_files) {
				$view_file = $up_files[0];
				$file_list = join("||",$up_files);
			} 
			else $file_list = $view_file = '';
						
			############ 글 등록 쿼리 ####################	

			$sql = "INSERT INTO pboard_{$code} VALUES ('{$insert_no}', '{$last_idx}', '{$order_num}', '{$depth}', '{$name}', '{$email}', '{$subject}', '{$cate}', 0, 0, 0, 0, '{$view_file}', '{$secret}', '{$my_icon}', {$signdate})";
			$mysql->query($sql);

			$sql = "INSERT INTO pboard_{$code}_body VALUES ('{$insert_no}', '{$last_idx}', 0, '{$s_passwd}' , '{$homepage}', '{$comment}', '{$mlink}', '{$slink}', '{$html_type}', '{$remail}' ,'{$file_list}', '{$s_id}', '{$access_ip}')";
			$mysql->query($sql,$code,$insert_no);
			$msg = $LANG_ETC_MSG[4];
			$pstr = "&pmode=view&no={$insert_no}";
        } 
		else {   //삭제일경우
        	############ 글 수를 감소한다 ####################
			$depth++;
			if($last_idx=='0') {  //공지사항일때
				$notice='1';
				$t_record[0]--;
				$ntotal_record = join("|",$t_record);
            } 
			else {
                $t_record[0]--;
				$t_record[$sec_idx]--;
				$total_record = join("|",$t_record); 
            }
			
			############ 답글이 있는지 확인한다 ####################
            $sql = "SELECT count(no) FROM pboard_{$code} WHERE idx={$last_idx} && substring(main,1,{$no_len}) = {$ch_num1} && substring(main,{$no_len2},3) = {$ch_num2} && depth = {$depth}";
			$ch_reply = $mysql->get_one($sql);
			if($ch_reply==1) alert($LANG_CHK_MSG[3],'back');
					
			############ 답글을 정렬한다 ####################
			$sql = "UPDATE pboard_{$code} SET main = main-1 WHERE idx={$last_idx} && substring(main,1,{$no_len}) = {$ch_num1} && substring(main,{$no_len2},3) >= {$ch_num2}";
			$mysql->query($sql);
						
			############ 글 삭제 쿼리 ####################
			if($options['19']=='Y') {
				$sql = "SELECT signdate FROM pboard_{$code} WHERE no={$no}";
				$tmp_date = $mysql->get_one($sql);
				$dir_name = "data/{$code}/".date("Ym",$tmp_date)."/article_{$no}";		
				delTree($dir_name);								
			}
			$sql = "DELETE FROM pboard_{$code} WHERE no={$no}";
			$mysql->query($sql);
			$sql = "DELETE FROM pboard_{$code}_body WHERE no={$no}";
			$mysql->query($sql);
			$msg = $LANG_ETC_MSG[6];

			if($options['19']=='Y' && $options[4]!='Y') {
				$dir_name = "data/{$code}/article_{$no}";
				delTree($dir_name);								
			}	

            ############ 메모글 삭제 쿼리 ####################
			if($data['cnt_memo']>0) {
			     $sql = "DELETE FROM pboard_{$code}_body WHERE memo='1' && idx={$no} && no >= 30000000";
			     $mysql->query($sql);
            }
						
			if($data['file']) {   //등록된 파일이 있으면
			    $file_list = explode("||",$data['file']);
                for($i=0,$cnt=count($file_list);$i<$cnt;$i++){  
				    delFile("{$bo_path}/data/{$code}/{$file_list[$i]}");
                }
            }
			
        }
    break;
   
    case 'ad_del' :   // 관리자 삭제 기능일때
	    
			function del($no){   
			    global $del_ct , $mysql, $code, $bo_path;
													
				############ 원글의 정보를 가져온다 ####################
				$sql = "SELECT m.idx,m.main,m.depth, m.cnt_memo, b.file FROM pboard_{$code} m, pboard_{$code}_body b WHERE m.no={$no} && m.no=b.no && b.memo=0";
				$data = $mysql->one_row($sql);
				$last_idx	= $data['idx'];
				$order_no	= $data['main']+1;
				$depth		= $data['depth']+1;
				$no_len		= strlen($order_no)-3;
				$no_len2	= $no_len+1;
				$ch_num1	= substr($order_no,0,$no_len);
				$ch_num2	= substr($order_no,$no_len,3);
				$sec_idx	= 999 - $last_idx;
					       
				############ 답글이 있는지 확인한다 ####################
				$sql = "SELECT count(no) FROM pboard_{$code} WHERE idx={$last_idx} && substring(main,1,{$no_len}) = {$ch_num1} && substring(main,{$no_len2},3) = {$ch_num2} && depth = {$depth}";
				$ch_reply = $mysql->get_one($sql);
							
				if($ch_reply!=1) {  //답글이 없으면 삭제							
					############ 답글을 정렬한다 ####################
					$sql = "UPDATE pboard_{$code} SET main = main-1 WHERE idx={$last_idx} && substring(main,1,{$no_len}) = {$ch_num1} && substring(main,{$no_len2},3) >= {$ch_num2}";
					$mysql->query($sql);
					
					############ 글 삭제 쿼리 ####################
					if($options['19']=='Y') {
						$sql = "SELECT signdate FROM pboard_{$code} WHERE no={$no}";
						$tmp_date = $mysql->get_one($sql);
						$dir_name = "data/{$code}/".date("Ym",$tmp_date)."/article_{$no}";
						$dir_name = "data/{$code}/article_{$no}";
						delTree($dir_name);								
					}

					$sql = "DELETE FROM pboard_{$code} WHERE no={$no}";
					$mysql->query($sql);
					$sql = "DELETE FROM pboard_{$code}_body WHERE no={$no}";
					$mysql->query($sql);

					
					############ 메모글 삭제 쿼리 ####################
					if($data[cnt_memo]>0) {
						 $sql = "DELETE FROM pboard_{$code}_body WHERE memo='1' && idx={$no} && no >= 30000000";
						 $mysql->query($sql);
					}

					############ 전체 글수를 가져온다 ####################
					if($last_idx=='0') {   //공지사항일때
						$sql = "SELECT file FROM pboard_{$code}_body where no=1";
						$data2 = $mysql->get_one($sql);
						$t_record = explode("|",$data2);

						############ 글수를 감소한다 ####################							    
						$t_record[0]--;
						$del_ct++;
						$total_record = join("|",$t_record);
						$sql = "UPDATE pboard_{$code}_body SET file = '{$total_record}' WHERE no=1"; 
						$mysql->query($sql);  
					} 
					else {
						$sql = "SELECT comment FROM pboard_{$code}_body where no=1";
						$data2 = $mysql->get_one($sql);
						$t_record = explode("|",$data2);
										   
						############ 글수를 감소한다 ####################							    
						$t_record[0]--;
						$t_record[$sec_idx]--;
						$del_ct++;
						$total_record = join("|",$t_record);
						$sql = "UPDATE pboard_{$code}_body SET comment = '{$total_record}' WHERE no=1"; 
						$mysql->query($sql);
					}

					if($data['file']) {   //등록된 파일이 있으면
						$file_list = explode("||",$data['file']);
						for($i=0,$cnt=count($file_list);$i<$cnt;$i++){  
							delFile("{$bo_path}/data/{$code}/{$file_list[$i]}");
						}
					}
				}
			}//den of Function(del)

								
		################ 삭제처리 
		$del_ct	=0;       //삭제 카운터
		$item = $_POST['item'];
		$ct_num = sizeof($item);     
		for($i=0;$i<$ct_num; $i++) {
		    del($item[$i]);
		} 
		$msg = $LANG_ETC_MSG[7].($ct_num-$del_ct).$LANG_ETC_MSG[8].$del_ct.$LANG_ETC_MSG[9];

   break;

   default : alert($LANG_ERR_MSG[2],'back');
   break;
 }       


if($pmode!='modify' && $pmode!='ad_del') {
	if($notice) {	  //공지사항일 경우
		$sql = "UPDATE pboard_{$code}_body SET file = '{$ntotal_record}' WHERE no=1"; 
		$mysql->query($sql);

		if($pmode=='write') {
			$sql = "UPDATE pboard_{$code} SET main={$tmp_order_no}, depth={$tmp_last_idx} WHERE no=1";
			$mysql->query($sql);
		}	
	} 
	else {		
		if($pmode=='write') {
			$sql = "UPDATE pboard_{$code} SET main={$order_no}, depth={$last_idx} WHERE no=1";
			$mysql->query($sql);
		}
		$sql = "UPDATE pboard_{$code}_body SET comment='{$total_record}' WHERE no=1"; 
		$mysql->query($sql);
	}
}
if($notice_cg1=='Y') {
	$sql		= "SELECT file FROM pboard_{$code}_body where no=1";
	$data2		= $mysql->get_one($sql);
	$t_record	= explode("|",$data2);
	$t_record[0]--;
	if($tmp_row['main'] > $t_record[1]) { 
		$t_record[1]++;
		$sql = "UPDATE pboard_{$code} SET main = main + 1000 WHERE no > 1 && main < '{$tmp_row['main']}000' && idx='0'";
		$mysql->query($sql);
	}
	if($t_record[0]==0) $ntotal_record = "";
	else $ntotal_record = join("|",$t_record);

	$sql = "SELECT comment FROM pboard_{$code}_body WHERE no=1";
	$t_record	= explode("|",$mysql->get_one($sql));
	$sec_idx	= 999 - $tmp_vls[1];
	$t_record[0]++;
	$t_record[$sec_idx]++;
	$total_record = join("|",$t_record);

	$sql = "UPDATE pboard_{$code}_body SET file = '{$ntotal_record}', comment='{$total_record}' WHERE no=1"; 
	$mysql->query($sql);
}

if($notice_cg2=='Y') {
	$sql = "SELECT comment FROM pboard_{$code}_body WHERE no=1";
	$t_record	= explode("|",$mysql->get_one($sql));
	$sec_idx	= 999 - $tmp_row['idx'];
	$t_record[0]--;
	$t_record[$sec_idx]--;
	$total_record = join("|",$t_record);

	$sql = "UPDATE pboard_{$code}_body SET file = '{$ntotal_record}', comment='{$total_record}' WHERE no=1"; 
	$mysql->query($sql);
}

####################### 답변메일 보내기 ##################################
if($ori_remail){
	$comment = stripslashes($comment);
	if($html_type!=1) $comment = nl2br($comment);
	$MAIL_COMMENT = "
	    <table border=0 cellpadding=0 cellspacing=0 width=100%>
	    <tr><td>{$comment}</td></tr>
	    </table>
    ";
		
	############ 관리자 정보가져오기  ####################
	$sql = "SELECT code FROM mall_design WHERE mode='A'";
	$tmp_basic = $mysql->get_one($sql);
	$basic = explode("|*|",stripslashes($tmp_basic));
	//0:주소,1:쇼핑몰명,2:상호,3:대표자명,4:사번호,5:부가번호,6:주소,7:연락처,8:팩스,9:관리자명,10:이메일
	if(!eregi("http://",$basic[0])) $basic[0]="http://".$basic[0];       

	$sub= $basic[1].$LANG_ETC_MSG[10];
	$from = "{$basic[1]}<{$basic[10]}>\nContent-Type:text/html"; 
	$comment="
		<HTML>
		<HEAD>
		<STYLE>
		<!-- 
		a:link {  color: #165063; text-decoration: none}
		a:visited {  color: #165063; text-decoration: none}
		a:hover {  color: #0A069E; text-decoration: underline}
		body,table,tr,th,td{font-size:9.3pt;}
		font {font-family:굴림; font-size: 9.3pt; line-height=13pt;}
		-->
		</STYLE>
		</HEAD>

		<BODY>
		<table border=0 cellpadding=0 cellspacing=4 width=600 bgcolor=#EBEBEB align=center>
		<tr><td>
			   <table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#808080>
			   <tr><td align=center>
				<table border=0 cellpadding=0 cellspacing=1 width=100% align=center>
				<tr height=36>
				   <td bgcolor=cccccc align=center>{$subject}</td>
				</tr>
				<tr>
				   <td bgcolor=ffffff align=center>
					  <table border=0 cellpadding=0 cellspacing=0 width=96%>
					  <tr><td height=20></td><tr>
					  <tr><td>{$MAIL_COMMENT}</td></tr>
					  <tr><td height=20></td><tr>
					  </table>
					</td>
				</tr>
				</table>
				</td></tr>
				</table>
		</td></tr>
		</table>
		</BODY>
		</HTML>
	";
    
	if($socket_mail=='Y') {
		require "{$bo_path}/lib/class.Smtp.php";
		$mail = new classSmtp('self'); 
		$mail->send($ori_email, $from, $sub, $comment); 
	}
	else {
		/* HTML 메일을 보내려면, Content-type 헤더를 설정해야 합니다. */
		
		$subject = '=?UTF-8?B?'.base64_encode($sub).'?=';
		$sender = '=?UTF-8?B?'.base64_encode($basic[1]).'?=';

		$headers = "From: {$sender} <{$basic[10]}>\n";
		$headers .= "MIME-Version: 1.0\n";
		$message = chunk_split(base64_encode($comment));
		$boundary = "b".md5(uniqid(time()));
		$headers .= "Content-Type: multipart/mixed; boundary = {$boundary}\n\nThis is a MIME encoded message.\n\n--{$boundary}";
		$headers .= "\n";
		$headers .= "Content-type: text/html; charset=utf-8\n";
		$headers .= "Content-Transfer-Encoding: base64\n\n{$message}\n";
		$headers .= "--{$boundary}";
		$headers .= "--\n";
		$headers .= "X-Mailer: PHP/" . phpversion();

		mail($ori_email, $subject, "", $headers);	
	}
}
####################### 답변메일 보내기 ##################################

include "{$bo_path}/close.php";

if($pmode=='del' || $pmode =='ad_del') alert($msg,$Main.$pstr.$addstring);
elseif($secret=='1')  {
	if($pmode=="write" && ($code=="counsel")) {
		alert("감사합니다. 문의가 접수 되었습니다.\\n 검토 후 연락 드리겠습니다.",$Main);
	}
	else if($pmode == 'write' && !$my_id) movePage($Main.$addstring);
	else movePage($Main.$pstr.$addstring);
}
else {
	if($pmode=="write" && ($code=="sales" || $code=="cooperation")) {
		alert("감사합니다. 문의가 접수 되었습니다.\\n 검토 후 연락 드리겠습니다.",$Main);
	}
	else movePage($Main.$pstr.$addstring);
	
}
?>