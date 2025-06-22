<?
ob_start();
include "../ad_init.php";

############ 파라미터(값) 검사 ####################
if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
if($_SERVER['REQUEST_METHOD']=='GET') alert('정상적으로 등록하세요!','back');

$uid	= isset($_POST['uid']) ? $_POST['uid']:'';
$mode	= isset($_POST['mode']) ? $_POST['mode']: $_GET['mode'];

if(!$uid && $mode && $mode!='del') alert('정보가 제대로 넘어오지 못했습니다.\\n다시 시도해 주세요!','back');
if(!$mode || $mode=='modify'){
	$b_name		= str_replace("'","",$_POST['b_name']);
	$b_name		= str_replace("\"","",$b_name);
	$b_name		= str_replace(" ","",$b_name);
	$b_name		= trim(addslashes($b_name));
	$title		= addslashes($_POST['title']);
	$b_w_size	= addslashes($_POST['b_w_size']);

	$inpage		= $_POST['inpage'];
	$pagelink	= $_POST['pagelink'];
	$s_name		= $_POST['s_name'];
	$word_limit	= $_POST['word_limit'];
	$img_limit	= $_POST['img_limit'];

	$bg_color	= addslashes($_POST['bg_color']);	
	$bo_admin	= base64_encode(addslashes($_POST['bo_admin']));
	$header_url = addslashes($_POST['header_url']);
	$header		= addslashes($_POST['header']);
	$footer_url = addslashes($_POST['footer_url']);
	$footer		= addslashes($_POST['footer']);
	$options = $_POST['option1']."||".$_POST['option2']."||".$_POST['option3']."||".$_POST['option4']."||".$_POST['option5']."||".$_POST['option6']."||".$_POST['option7'][0]."||".$_POST['option8']."||".$_POST['option9']."||".$_POST['option10']."||".$_POST['up_num']."||".$_POST['link_num']."||".$bo_admin."||".$_POST['option11']."||".$_POST['img_cnt']."||".$_POST['img_sz']."||".$_POST['option12']."||".$_POST['option13']."||".$_POST['option18']."||".$_POST['editor']."||".$_POST['option20']."||".$_POST['option21'];   
	$signdate = time();
}

############ 게시판 정보 등록 ####################
switch($mode) {  
	case 'del' :   //삭제	
		$item = isset($_POST['item']) ? $_POST['item'] : '';		

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			$code = $item[$i];
			$sql = "DROP TABLE pboard_{$code}, pboard_{$code}_body";
			$mysql->query($sql);
			$sql = "DELETE FROM pboard_manager where name='{$code}'";
			$mysql->query($sql);

			######### 첨부 파일 삭제 ###############
			if($dp=@opendir("{$bo_path}/data/{$code}")) { // 디렉토리 오픈 
				while($file = readdir($dp)){ 
					if($file != '.' && $file != '..')  {
					   delFile("{$bo_path}/data/{$code}/{$file}");
					}	
				}
				closedir($dp); 
				@RmDir("{$bo_path}/data/{$code}"); 
			}
		} 
		$msg = "게시판 {$i}건을 삭제했습니다!";
	break;

	case 'access' :   //access level 수정
		$acc_write = $_POST['v_list']."|".$_POST['v_read']."|".$_POST['w_art']."|".$_POST['w_com']."|".$_POST['w_not']."|".$_POST['w_reco']."|".$_POST['v_list2']."|".$_POST['v_read2']."|".$_POST['w_art2']."|".$_POST['w_com2']."|".$_POST['w_not2']."|".$_POST['w_reco2']."|".$_POST['w_reply']."|".$_POST['w_reply2'];
		$acc_write = addslashes($acc_write);
		$sql = "UPDATE pboard_manager SET accesslevel = '{$acc_write}' WHERE uid={$uid}";
		$mysql->query($sql);
		alert("Access Level을 수정했습니다.","close4");
	break;

	case 'cate' :   //category 수정
		$cmode		= isset($_POST['cmode']) ? $_POST['cmode']:'';
		$cate_name	= isset($_POST['cate_name']) ? $_POST['cate_name']:'';
		$cate_num	= isset($_POST['cate_num']) ? $_POST['cate_num']:'';

		$sql = "SELECT category FROM pboard_manager WHERE uid={$uid}";
		$catelist = $mysql->get_one($sql);
		if($catelist) {
			$data=explode("|",$catelist);              //카타고리 배열로 저장
			$cate_st = $data[0];
		}

		switch($cmode) {
			case "modify":
				########## cate 수정 ##########
				$data[$cate_num] = $_POST['cate_md']; 
				$cate_write =  $cate_st;
				for($i = 1; $i<=$cate_st ;$i++){
					$cate_write .=  "|".$data[$i];
				}
																	 
				$sql = "UPDATE pboard_manager SET category = '{$cate_write}' WHERE uid={$uid}";
				$msg = '카테고리명을 수정했습니다!';
			break;
		
			case "delete":
				$sql = "SELECT name FROM pboard_manager WHERE uid='{$uid}'";
				$tmps = $mysql->get_one($sql);
				//if($tmps=='counsel' || $tmps=='faq') alert("해당 게시판의 분류는 삭제 할 수 없습니다.","back");
			
				########## cate 삭제 ##########
				$cate_st--;
				$cate_write =  $cate_st;
				for($i = 1; $i<= ($cate_st+1) ;$i++){
					if($i!=$cate_num)	$cate_write .= "|".$data[$i];
				}
																		 
				$sql = "UPDATE pboard_manager SET category = '{$cate_write}' WHERE uid='{$uid}'";
				$msg = '카테고리를 삭제했습니다!';
			break;

			default :
				########## cate 생성 ##########
				if(!$data[0]) {	
					$cate_write = "1|".$cate_name;  
				} else {
					$cate_st ++;
					$cate_write =  $cate_st."|";
					for($i = 1; $i<$cate_st ;$i++){
						$cate_write .= $data[$i]."|";
					}
					$cate_write .= $cate_name;  
				}
				$sql = "UPDATE pboard_manager SET category = '{$cate_write}' WHERE uid={$uid}";				
				$msg = '카테고리를 생성했습니다!';
			break;

		}  //End of switch(cmode)

		$mysql->query($sql);
	break;

	case 'modify' :   //수정
 		$sql = "UPDATE pboard_manager SET  s_name='{$s_name}', title='{$title}', b_w_size='{$b_w_size}', inpage='{$inpage}', pagelink ='{$pagelink}', bg_color='{$bg_color}', word_limit='{$word_limit}', img_limit='{$img_limit}', options='{$options}', header_url ='{$header_url}', header='{$header}', footer_url='{$footer_url}', footer='{$footer}' WHERE uid={$uid}";
		$mysql->query($sql);
		
		$msg = '게시판을 수정했습니다.'; 
	break;

	default :    //생성
		$sql ="SELECT count(*) FROM pboard_manager WHERE name='{$b_name}'";
		if($mysql->get_one($sql)>0) alert('이 게시판 이름이 이미 사용중입니다. 다른 이름으로 다시 시도하세요.','back');

		$sql = "INSERT INTO pboard_manager (name, s_name, title, b_w_size, inpage, pagelink, bg_color, word_limit, img_limit, options, header_url, header, footer_url, footer, accesslevel, signdate) 
		VALUES ('{$b_name}', '{$s_name}', '{$title}', '{$b_w_size}', '{$inpage}', '{$pagelink}', '{$bg_color}', '{$word_limit}', '{$img_limit}', '{$options}', '{$header_url}', '{$header}', '{$footer_url}', '{$footer}','1|1|1|1|1|1|<|<|<|<|<|<|1|<', '{$signdate}')";
		$mysql->query($sql);

		if(!$mysql->table_list("","pboard_".$b_name)) {
			$sql = "
				CREATE TABLE pboard_{$b_name}( 
				no mediumint(8) unsigned DEFAULT '0' NOT NULL, 
				idx smallint(3) unsigned DEFAULT '0' NOT NULL,
				main mediumint(7) unsigned DEFAULT '0' NOT NULL, 
				depth smallint(3) unsigned DEFAULT '0' NOT NULL,
				name char(20) binary DEFAULT '' NOT NULL,
				email char(40) DEFAULT '' NOT NULL,
				subject char(150) binary DEFAULT '' NOT NULL,
				cate tinyint unsigned DEFAULT '0' NOT NULL,
				hit smallint(5) unsigned DEFAULT '0' NOT NULL,
				reco smallint(5) unsigned DEFAULT '0' NOT NULL,
				down smallint(5) unsigned DEFAULT '0' NOT NULL,
				cnt_memo smallint(5) unsigned DEFAULT '0' NOT NULL, 
				file char(80) binary DEFAULT '' NOT NULL,
				secret  enum('0','1') DEFAULT '0' NOT NULL,
				icon char(30) binary DEFAULT '' NOT NULL,
				signdate int(10) unsigned DEFAULT '0' NOT NULL,
				PRIMARY KEY(no), 
				INDEX POS(idx,main)
				)
			";
			$mysql->query($sql);
		}

		if(!$mysql->table_list("","pboard_".$b_name."_body")) {
			$sql = "
				CREATE TABLE pboard_{$b_name}_body( 
				no int(8) unsigned NOT NULL,
				idx int(8) unsigned DEFAULT '0' NOT NULL,
				memo enum('0','1') DEFAULT '0' NOT NULL,
				passwd varchar(50) binary DEFAULT '' NOT NULL,
				homepage varchar(50) DEFAULT '' NOT NULL,
				comment mediumtext DEFAULT '' NOT NULL,
				m_link varchar(100) DEFAULT '' NOT NULL,
				s_link varchar(150) DEFAULT '' NOT NULL,
				html_type enum('0','1') DEFAULT '0' NOT NULL,
				remail enum('0','1') DEFAULT '0' NOT NULL,
				file varchar(255) DEFAULT '' NOT NULL,
				id char(20) binary DEFAULT '' NOT NULL, 
				ip char(30) binary DEFAULT '' NOT NULL, 
				INDEX (no),
				INDEX (idx)
				)
			"; 
			$mysql->query($sql);
		}

		$sql = "insert into pboard_{$b_name} (no,idx,main,depth) values('1', 999, 0, 999)";
		$mysql->query($sql);
		$sql="insert into pboard_{$b_name}_body (no,idx,comment) values('1',999,0)";
		$mysql->query($sql);

		@mkdir("{$bo_path}/data/{$b_name}",0707);
		@chmod("{$bo_path}/data/{$b_name}",0707);

		$msg = '게시판을 성공적으로 생성했습니다.';
	break;

} //End of switch

include "{$bo_path}/close.php";

if($mode=='cate') alert($msg,"./cate_manager.html?uid={$uid}");
else alert($msg,'./board_list.php');

?>

