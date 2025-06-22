<?
include "sub_init.php";

require "{$lib_path}/class.Template.php";
$tpl = new classTemplate;

$up_path = "../image/up_img/qna";
$cate	= $_GET['cate'];
$number = $_GET['number'];
$uid	= $_GET['uid'];
$pmode	= isset($_GET['pmode'])	? $_GET['pmode']:'write';
if($uid) $addstring .= "&uid={$uid}";

if($pmode=='write') {
	if(!$cate || !$number) {
		echo "
			<script>
				alert('정보가 제대로 넘어오지 못했습니다!');\n
				parent.pLightBox.hide();\n
			</script>
		";
		exit;
	}
}
else if(!$uid) {
	echo "
		<script>
			alert('정보가 제대로 넘어오지 못했습니다!');\n
			parent.pLightBox.hide();\n
		</script>
	";
	exit;
}

$skin = "../skin/{$tmp_skin}";
$skin2 = $skin."/";

$tpl->define("main","{$skin}/qna_write.html");
$tpl->scan_area("main");

switch($pmode){
	case "delete" : 
		$sql = "SELECT id,title FROM mall_goods_qna WHERE uid={$uid}";
		$row = $mysql->one_row($sql); 
		$TITLE = stripslashes($row['title']);
		$tmp_id = $row['id']; 
		if(($tmp_id && $tmp_id==$my_id) || $my_level>8) {
			$tpl->parse("is_con2");	
		} 
		else $tpl->parse("is_con1");

				
		$MACTION = "qna_ok.php?pmode=delete{$addstring}";			
	
	break;

	case "delete2" : 
		if($my_level<9) {
			echo "<script>
					alert('삭제 권한이 없습니다.');\n
					parent.pLightBox.hide();\n
				  </script>";
			exit;	
		}
		$sql = "SELECT title FROM mall_goods_qna WHERE uid={$uid}";
		$row = $mysql->get_one($sql); 
		$TITLE = stripslashes($row);

		$tpl->parse("is_con3");
			
		$MACTION = "qna_ok.php?pmode=delete2{$addstring}";			
	
	break;
	case "confirm" : 		
		$sql = "SELECT id FROM mall_goods_qna WHERE uid={$uid}";
		$tmp_id = $mysql->get_one($sql);
		if(($tmp_id && $tmp_id==$my_id) || $my_level>8) {
			echo "<script>parent.rtnModify2('xxx','{$addstring}');</script>";
			exit;
		}
		$MACTION = "{$PHP_SELF}?pmode=modify{$addstring}";			
		$tpl->parse("is_con1");
	break;
	case "secret" : 		
		$passwd	= isset($_GET['passwd']) ? $_GET['passwd'] : $_POST['passwd'];
		if($passwd) {
			$sql = "SELECT * FROM mall_goods_qna WHERE uid={$uid}";
			$row = $mysql->one_row($sql);
			$origin_pass = $row['passwd'];			
			$user_pass = md5($passwd);				
			if($origin_pass != $user_pass) {
				alert('비밀번호가 일치하지 않습니다.','back');
			}
			
			$CONTENT = stripslashes($row['content']);
			$CONTENT = ieHackCheck($CONTENT);
			if($row['answer']) {
				$ANSWER = stripslashes($row['answer']);
				$ANSWER = ieHackCheck($ANSWER);
				$ANSWER = str_replace("\r","",$ANSWER);
				$ANSWER = str_replace("\n","",$ANSWER);
			}
			else $ANSWER = "";

			echo "<script>\n
					parent.viewQnaLock2({$uid},	'{$CONTENT}', '{$ANSWER}');\n
					parent.pLightBox.hide();\n
				  </script>\n
				 ";
			exit;
		}
		else {
			$sql = "SELECT * FROM mall_goods_qna WHERE uid={$uid}";
			$row = $mysql->one_row($sql);

			if($row['id'] && $row['id'] != $my_id) {
				$smem = 'Y';
			}
			$MACTION = "{$PHP_SELF}?pmode=secret{$addstring}";			
			$tpl->parse("is_con1");
		}
	break;
	case "modify" : 		
		############ 비밀번호를 비교한다 ####################
		$passwd	= isset($_GET['passwd']) ? $_GET['passwd'] : $_POST['passwd'];
		$sql = "SELECT * FROM mall_goods_qna WHERE uid={$uid}";
		$row = $mysql->one_row($sql);
		if((!$row['id'] || $row['id'] != $my_id) && $my_level <9) {
			$origin_pass = $row['passwd'];			
			$user_pass = md5($passwd);				
			if($origin_pass != $user_pass) {
				echo "<script>alert('비밀번호가 일치하지 않거나 수정권한이 없습니다.'); parent.rtnModify2('','{$addstring}');</script>";
				exit;
			}
        }
		$MACTION	= "qna_ok.php?pmode=modify{$addstring}";			
		$NAME		= stripslashes($row['name']);
		$TITLE		= stripslashes($row['title']);
		$CONTENT	= stripslashes($row['content']);
		if($row['secret']=='Y') $SECRET	= 'checked' ;
		
		if(is_dir("{$up_path}/".date("Ym")."/qna_{$uid}")) {
			$tmp_qdir = 	"{$up_path}/".date("Ym")."/qna_{$uid}";
			$tmp_qdir2 = previlEncode($tmp_qdir);		 
		}
		else {
			if(!is_dir("{$up_path}/".date("Ym"))) mkdir("{$up_path}/".date("Ym"),0707);	
			$mk_dir = '';
			if($_COOKIE['tmp_qdir']) {		
				$tmp_qdir2 = $_COOKIE['tmp_qdir'];		
				$tmp_qdir = previlDecode($tmp_qdir2);		
				if(!is_dir($tmp_qdir)) mkdir($tmp_qdir,0707);							
			} 
			else {		
				$tmp_qdir = "{$up_path}/".date("Ym")."/".date("Ymd_his").getCode(4);	
				if(!is_dir($tmp_qdir)) mkdir($tmp_qdir,0707);	
				$tmp_qdir2 = previlEncode($tmp_qdir);
				SetCookie("tmp_qdir",$tmp_qdir2,0,"/");	
			}
		}
		$up_dir = previlEncode("../{$tmp_qdir}/");
	break;
	case "answer" : case "modify2" :				
		if($my_level<9) {
			echo "<script>
					alert('답변 권한이 없습니다.');\n
					parent.pLightBox.hide();\n
				  </script>";
			exit;			
        }
		
		$sql = "SELECT * FROM mall_goods_qna WHERE uid={$uid}";
		$row = $mysql->one_row($sql);

		$MACTION	= "qna_ok.php?pmode={$pmode}{$addstring}";			
		$NAME		= stripslashes($row['name']);
		$TITLE		= stripslashes($row['title']);
		$QUESTION	= stripslashes($row['content']);		
		$QUESTION	= nl2br($QUESTION);
		$CONTENT	= stripslashes($row['answer']);
	break;
	default : 
		$ck_w = base64_encode(time()); //현재 시간 정보를 생성 ...  글쓰기 버튼에서 받아옴
		$ck_w2 = md5($ck_w.$cook_rand); //암호화

		$MACTION	= "qna_ok.php";	
		$NAME		= $my_name;

		################ 이전 임시폴더 삭제 ################
		if(is_dir("{$up_path}/".date("Ym"))) {
			$handle = opendir("{$up_path}/".date("Ym"));
			while ($tmps = readdir($handle)) {	
				if(!eregi("\.",$tmps)) {
					if(substr($tmps,0,8) < date("Ymd",time()-(3600*24))) {
						delTree("{$up_path}/".date("Ym")."/".$tmps); 
					}
				}
			}
			closedir($handle);
		}
		
		if(!is_dir("{$up_path}/".date("Ym"))) mkdir("{$up_path}/".date("Ym"),0707);	
		$mk_dir = '';
		if($_COOKIE['tmp_qdir']) {		
			$tmp_qdir2 = $_COOKIE['tmp_qdir'];		
			$tmp_qdir = previlDecode($tmp_qdir2);		
			if(!is_dir($tmp_qdir)) mkdir($tmp_qdir,0707);							
		} 
		else {		
			$tmp_qdir = "{$up_path}/".date("Ym")."/".date("Ymd_his").getCode(4);	
			if(!is_dir($tmp_qdir)) mkdir($tmp_qdir,0707);	
			$tmp_qdir2 = previlEncode($tmp_qdir);
			SetCookie("tmp_qdir",$tmp_qdir2,0,"/");	
		}
		$up_dir = previlEncode("../{$tmp_qdir}/");
	break;
}


if($pmode=='delete' || $pmode=='delete2' || $pmode=='confirm' || $pmode=='secret'){	
	if($smem=='Y') {
		$tpl->parse("is_confirm3");
	}
	else {
		$tpl->parse("is_confirm1");
		$tpl->parse("is_confirm2");	
	}
} 
else {
	if($NAME) {		
		$tpl->parse('is_mname1');
		if(!$my_id) {
			$tpl->parse('is_mpw');
			$tpl->parse('is_login');
		}
	}
	else {
		$tpl->parse('is_login');
		$tpl->parse('is_mname2');
	}
	if($pmode=="answer" || $pmode=="modify2") $tpl->parse("is_answer");
	else $tpl->parse("is_default");
	$tpl->parse("is_write1");
	$tpl->parse("is_write2");
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>