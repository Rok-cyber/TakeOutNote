<?
include "sub_init.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

$up_path = "../image/up_img/point";
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

$skin = "../skin/$tmp_skin";
$skin2 = $skin."/";

$tpl->define("main","{$skin}/after_write.html");
$tpl->scan_area("main");

switch($pmode){
	case "delete" : case "delete2" :  
		$sql = "SELECT id,title FROM mall_goods_point WHERE uid={$uid}";
		$row = $mysql->one_row($sql); 
		$TITLE = stripslashes($row['title']);
		$tmp_id = $row['id']; 

		$tmp_id = $mysql->get_one($sql);
		if(($tmp_id && $tmp_id==$my_id) || $my_level>8) {
			$tpl->parse("is_con2");	
		} 
		else $tpl->parse("is_con1");
			
		$MACTION = "after_ok.php?pmode={$pmode}{$addstring}";			
	
	break;
	case "confirm" : 		
		$sql = "SELECT id FROM mall_goods_point WHERE uid={$uid}";
		$tmp_id = $mysql->get_one($sql);
		if(($tmp_id && $tmp_id==$my_id) || $my_level>8) {
			echo "<script>parent.rtnModify('xxx','{$addstring}');</script>";
			exit;
		}
		$MACTION = "{$PHP_SELF}?pmode=modify{$addstring}";			
		$tpl->parse("is_con1");
	break;
	case "modify" : 		
		############ 비밀번호를 비교한다 ####################
		$passwd	= isset($_GET['passwd']) ? $_GET['passwd'] : $_POST['passwd'];
		$sql = "SELECT * FROM mall_goods_point WHERE uid={$uid}";
		$row = $mysql->one_row($sql);
		if((!$row['id'] || $row['id'] != $my_id) && $my_level <9) {
			$origin_pass = $row['passwd'];			
			$user_pass = md5($passwd);		
			if($origin_pass != $user_pass) {
				echo "<script>alert('비밀번호가 일치하지 않거나 수정권한이 없습니다.'); parent.rtnModify('','{$addstring}');</script>";
				exit;
			}
        }
		$MACTION	= "after_ok.php?pmode=modify{$addstring}";			
		$NAME		= stripslashes($row['name']);
		$TITLE		= stripslashes($row['title']);
		$CONTENT	= stripslashes($row['content']);
		$POINT		= $row['point'];

		if(is_dir("{$up_path}/".date("Ym")."/point_{$uid}")) {
			$tmp_adir = 	"{$up_path}/".date("Ym")."/point_{$uid}";
			$tmp_adir2 = previlEncode($tmp_adir);		 
		}
		else {
			if(!is_dir("{$up_path}/".date("Ym"))) mkdir("{$up_path}/".date("Ym"),0707);	
			$mk_dir = '';
			if($_COOKIE['tmp_adir']) {		
				$tmp_adir2 = $_COOKIE['tmp_adir'];		
				$tmp_adir = previlDecode($tmp_adir2);		
				if(!is_dir($tmp_adir)) mkdir($tmp_adir,0707);							
			} 
			else {		
				$tmp_adir = "{$up_path}/".date("Ym")."/".date("Ymd_his").getCode(4);	
				if(!is_dir($tmp_adir)) mkdir($tmp_adir,0707);	
				$tmp_adir2 = previlEncode($tmp_adir);
				SetCookie("tmp_adir",$tmp_adir2,0,"/");	
			}
		}
		$up_dir = previlEncode("../{$tmp_adir}/");
	break;
	default : 
		$ck_w = base64_encode(time()); //현재 시간 정보를 생성 ...  글쓰기 버튼에서 받아옴
		$ck_w2 = md5($ck_w.$cook_rand); //암호화

		$MACTION	= "after_ok.php";	
		$NAME		= $my_name;
		$POINT		= 5;
		
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
		if($_COOKIE['tmp_adir']) {		
			$tmp_adir2 = $_COOKIE['tmp_adir'];		
			$tmp_adir = previlDecode($tmp_adir2);		
			if(!is_dir($tmp_adir)) mkdir($tmp_adir,0707);							
		} 
		else {		
			$tmp_adir = "{$up_path}/".date("Ym")."/".date("Ymd_his").getCode(4);	
			if(!is_dir($tmp_adir)) mkdir($tmp_adir,0707);	
			$tmp_adir2 = previlEncode($tmp_adir);
			SetCookie("tmp_adir",$tmp_adir2,0,"/");	
		}
		$up_dir = previlEncode("../{$tmp_adir}/");

	break;
}


if($pmode=='delete' || $pmode=='delete2' || $pmode=='confirm'){	
	$tpl->parse("is_confirm1");
	$tpl->parse("is_confirm2");	
} else {
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
	$tpl->parse("is_write1");
	$tpl->parse("is_write2");
}

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();

?>