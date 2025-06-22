<?
include "sub_init.php";

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');
//if(!$my_id) alert('먼저 로그인을 하시기 바랍니다','back');

$signdate	= time();
$up_path = "../image/up_img/qna";
$pmode		= isset($_POST['pmode']) ? $_POST['pmode'] : $_GET['pmode'];
$uid		= isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
$access_ip	= $_SERVER['REMOTE_ADDR'];
$ck_w		= $_POST['ck_w'];
$ck_w2		= $_POST['ck_w2'];
$cate		= $_POST['cate'];
$number		= $_POST['number'];
$passwd		= $_POST['passwd'];
$secret		= $_POST['secret'];
$title		= addslashes($_POST['title']);
$content	= addslashes($_POST['content']);
$tmp_qdir	= previlDecode($_POST['tmp_qdir']);

switch($pmode) {
	case "delete" : case "modify" :
		if(!$uid) alert('정보가 제대로 넘어오지 못했습니다.','back');
		############ 비밀번호를 비교한다 ####################
		$passwd	= $_POST['passwd'];
		$sql = "SELECT id,passwd,cate,number FROM mall_goods_qna WHERE uid={$uid}";
		$row = $mysql->one_row($sql);
		if((!$row['id'] || $row['id'] != $my_id) && $my_level <9) {
			$origin_pass = $row['passwd'];			
			$user_pass = md5($passwd);		
			if($origin_pass != $user_pass) alert("비밀번호가 일치하지 않거나 수정권한이 없습니다.","back");
        }

		$cate = $row['cate'];
		$number = $row['number'];

		if($pmode=="modify") {
			######################## 임시 저장 파일 이동 ##################		
			$sql = "SELECT signdate FROM mall_goods_qna WHERE uid={$uid}";
			$tmp_date = $mysql->get_one($sql);

			if(is_dir("{$up_path}/".date("Ym",$tmp_date)."/qna_{$uid}")) {
				$tmp_qdir = "{$up_path}/".date("Ym",$tmp_date)."/qna_{$uid}";
			}
			else $tmp_date = time();
			
			$cnts = 0;
			$handle	= @opendir($tmp_qdir);
			while ($file = @readdir($handle)) {
				if($file != '.' && $file != '..') { $cnts=0; break; }			
			}
			@closedir($handle);	

			if($cnts==0) @RmDir($tmp_qdir);					
			else {
				$dir_name = "{$up_path}/".date("Ym")."/qna_{$uid}";			
				rename("{$tmp_qdir}/","{$dir_name}/");		
				$tmp_qdir = str_replace("../","",$tmp_qdir);
				$dir_name = str_replace("../","",$dir_name);
				$content = str_replace($tmp_qdir,$dir_name,$content);
				SetCookie("tmp_qdir","",-999,"/"); 
			}

			$sql = "UPDATE mall_goods_qna SET secret='{$secret}', title='{$title}', content='{$content}' WHERE uid='{$uid}'";
		}
		else {
			$sql = "SELECT signdate FROM mall_goods_qna WHERE uid='{$uid}'";
			$tmp_date = $mysql->get_one($sql);

			$dir_name = "{$up_path}/".date("Ym",$tmp_date)."/qna_{$uid}";
			delTree($dir_name);	

			$sql = "DELETE FROM mall_goods_qna WHERE uid='{$uid}'";
		}
		$mysql->query($sql);
	break;		

	case "answer" : case "modify2" : case "delete2" :
		if(!$uid) alert('정보가 제대로 넘어오지 못했습니다.','back');
		if($my_level <9) alert("관리자만 접속이 가능 합니다.","back");
        
		$sql = "SELECT cate,number FROM mall_goods_qna WHERE uid={$uid}";
		$row = $mysql->one_row($sql);

		$cate	= $row['cate'];
		$number = $row['number'];

		if($pmode=='delete2') $content = '';
		
		$sql = "UPDATE mall_goods_qna SET answer='{$content}' WHERE uid='{$uid}'";	
		$mysql->query($sql);
	break;		

	default :
		$ck_w3 = md5($ck_w.$cook_rand);     //  암호화
		$ck_w4 = base64_decode($ck_w);
		$i=intval($signdate) - intval($ck_w4);
		
		if(!$ck_w || !$ck_w2 || ($ck_w2 !=$ck_w3) || $i<5 || $i>1800) alert('스팸글일 가능성이 높아 차단 되었습니다.','back'); 

		if(!$cate || !$number || !$content) alert('정보가 제대로 넘어오지 못했습니다.','back');
		$sql = "SELECT name FROM mall_goods WHERE uid='{$number}'";
		$goods_name = $mysql->get_one($sql);
		if(!$my_id) $my_name = $_POST['name'];


		if($my_id) $passwd = '';			
		
		$sql = "INSERT INTO mall_goods_qna (uid,cate,number,goods_name,id,name,passwd,title,content,answer,secret,acc_ip,signdate) VALUES('','{$cate}','{$number}','{$goods_name}','{$my_id}','{$my_name}','".md5($passwd)."','{$title}','{$content}','','{$secret}','{$access_ip}','{$signdate}')";
		$mysql->query($sql);
		
		######################## 임시 저장 파일 이동 ##################		
		$cnts = 0;
		$handle	= @opendir($tmp_qdir);
		while ($file = @readdir($handle)) {
			if($file != '.' && $file != '..') { $cnts = 1; break; }
		}
		@closedir($handle);	
		
		if($cnts==0) @RmDir($tmp_qdir);					
		else {
			$sql = "SELECT MAX(uid) FROM mall_goods_qna";
			$insert_no = $mysql->get_one($sql);
			$dir_name = "{$up_path}/".date("Ym")."/qna_{$insert_no}";			
			rename("{$tmp_qdir}/","{$dir_name}/");		
			$tmp_qdir = str_replace("../","",$tmp_qdir);
			$dir_name = str_replace("../","",$dir_name);
			$content = str_replace($tmp_qdir,$dir_name,$content);
			SetCookie("tmp_qdir","",-999,"/"); 

			$sql = "UPDATE mall_goods_qna SET content='{$content}' WHERE uid='{$insert_no}'";			
			$mysql->query($sql);
		}

	break;
}

echo "
	<script>
		top.location.href='../{$Main}?channel=view&uid={$number}&cate={$cate}&focus=content04';
	</script>
	";
?>