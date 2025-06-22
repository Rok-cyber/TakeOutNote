<?
ob_start();
######################## lib include
include "../ad_init.php";

$img_path = "../../image/design";
$code = "mall_design";

$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];

switch($mode) {
	case "skin" :    		
		$cg_skin = $_POST['skin'];
		if(!$cg_skin) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하세요!','back');
        if(!file_exists("../../skin/{$cg_skin}/skin_define.php")) alert('해당 스킨정의 파일이 존재 하지 않습니다. 스킨파일을 다시 업로드 하시기 바랍니다.!','back');
		
		
		$sql = "SELECT code FROM $code WHERE mode='C'";
		$common = explode("|",stripslashes($mysql->get_one($sql)));
		$img_data = explode(",",$common[5]);

		for($i=1;$i<11;$i++) {
			$tmp_num = ($i-1)*3;
			$tmp_num2 = $tmp_num+1;
			$tmp_num3 = $tmp_num+2;

			if(file_exists("../../skin/{$cg_skin}/img/menu{$i}_off.gif")) {
				delFile("../../image/design/menu{$i}_off.gif");
				copy("../../skin/{$cg_skin}/img/menu{$i}_off.gif", "../../image/design/menu{$i}_off.gif");			
				if(file_exists("../../skin/{$cg_skin}/img/menu{$i}_on.gif")) {
					delFile("../../image/design/menu{$i}_on.gif");
					copy("../../skin/{$cg_skin}/img/menu{$i}_on.gif", "../../image/design/menu{$i}_on.gif");			
				}				
				$img_data[$tmp_num] = "menu{$i}_off.gif";
				$img_data[$tmp_num2] = "menu{$i}_on.gif";
			}
			else {
				$img_data[$tmp_num] = $img_data[$tmp_num2] = $img_data[$tmp_num3] = '';
			}
		}

		if(file_exists("../../skin/{$cg_skin}/img/search_bg.gif")) {
			delFile("../../image/design/search_bg.gif");
			copy("../../skin/{$cg_skin}/img/search_bg.gif", "../../image/design/search_bg.gif");			
		}	
		else $common[6] = '';
		
		$common[5]=implode(",",$img_data);
		$common[3] = "2";
		$common=implode("|",$common);
		$common = addslashes($common);
		$sql = "UPDATE {$code} SET code='{$common}' WHERE mode='C'";
		$mysql->query($sql);

		$sql = "UPDATE mall_design SET code='{$cg_skin}' WHERE mode='G'";		
        $msg = "스킨을 변경했습니다!";
		$go_url='skin.html';
    break;

    case "logo" :    		
		if(!eregi("none",$_FILES['logo']['tmp_name']) && $_FILES['logo']['tmp_name']) {									
			$file = upFile($_FILES['logo']['tmp_name'],$_FILES['logo']['name'],$img_path,'','true','logo');
		}
		$sql = "SELECT code FROM {$code} WHERE mode='C'";
		if($tmps = $mysql->get_one($sql)) {
			$common = explode("|",stripslashes($tmps));
			$common[0] = $file;
			$common=implode("|",$common);
			$common = addslashes($common);
			$sql = "UPDATE  {$code} SET code='{$common}' WHERE mode='C'";
        } else $sql = "INSERT INTO {$code} VALUES('','','$file|||||','C')";
           
        $msg = "로고를 변경했습니다!";
		$go_url='common.html';
    break;

	case "bottom" : 
		$type = isset($_POST['type'])? $_POST['type']:'';
		if(!eregi("none",$_FILES['copy']['tmp_name']) && $_FILES['copy']['tmp_name']) {									
			$file = upFile($_FILES['copy']['tmp_name'],$_FILES['copy']['name'],$img_path,'','true','copy');
		}
		$sql = "SELECT code FROM {$code} WHERE mode='C'";
		$common = explode("|",stripslashes($mysql->get_one($sql)));
		if($file) $common[1] = $file;			
		$common[2] = $type;
		$common=implode("|",$common);
		$common = addslashes($common);
		$sql = "UPDATE {$code} SET code='{$common}' WHERE mode='C'";
        $msg = "하단정보를 변경했습니다!";
		$go_url='common.html';
    break;

	case "menu" :    
		if(!eregi("none",$_FILES['flash']['tmp_name']) && $_FILES['flash']['tmp_name']) {
			$file = upFile($_FILES['flash']['tmp_name'],$_FILES['flash']['name'],$img_path,'','true','menu');
		}
		$sql = "SELECT code FROM {$code} WHERE mode='C'";
		$common = explode("|",stripslashes($mysql->get_one($sql)));			
		$common[3] = "1";
		if($file) $common[4] = $file;
		$common=implode("|",$common);
		$common = addslashes($common);
		$sql = "UPDATE {$code} SET code='{$common}' WHERE mode='C'";
        $msg = "플래쉬메뉴를 변경했습니다!";
		$go_url='common.html';
     break;

	case "menu2" :  
		$num = isset($_GET['num'])?$_GET['num']:'';
		if(!$num) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하세요!','close');
			
		$link = addslashes($_POST['link']);
		if(!eregi("none",$_FILES['img1']['tmp_name']) && $_FILES['img1']['tmp_name']) {									
			$file1 = upFile($_FILES['img1']['tmp_name'],$_FILES['img1']['name'],$img_path,'','true',"menu{$num}_off");
		}
		if(!eregi("none",$_FILES['img2']['tmp_name']) && $_FILES['img2']['tmp_name']) {									
			$file2 = upFile($_FILES['img2']['tmp_name'],$_FILES['img2']['name'],$img_path,'','true',"menu{$num}_on");
		}

		$sql = "SELECT code FROM $code WHERE mode='C'";
		$common = explode("|",stripslashes($mysql->get_one($sql)));
		$img_data = explode(",",$common[5]);
        $tmp_num = ($num-1)*3;
		$tmp_num2 = $tmp_num+1;
		$tmp_num3 = $tmp_num+2;
		if($file1) $img_data[$tmp_num] = $file1;
		if($file2) $img_data[$tmp_num2] = $file2;
		$img_data[$tmp_num3] = $link;
            
		if($_POST['del_img1']) { 			
			delFile($img_path."/".$_POST['del_img1']);			
			$img_data[$tmp_num] = $file1;			
        }

		if($_POST['del_img2']) { 
			delFile($img_path."/".$_POST['del_img2']);
			$img_data[$tmp_num2] = $file2;
        }
			
		$common[5]=implode(",",$img_data);
		$common[3] = "2";
		$common=implode("|",$common);
		$common = addslashes($common);
		$sql = "UPDATE {$code} SET code='{$common}' WHERE mode='C'";
        $msg = "메뉴{$num} 이미지를 변경했습니다!";

		$mysql->query($sql);
		alert($msg,"close4");
     break;

	 case "menu3" :    		
		if(!eregi("none",$_FILES['mbg']['tmp_name']) && $_FILES['mbg']['tmp_name']) {									
			$file = upFile($_FILES['mbg']['tmp_name'],$_FILES['mbg']['name'],$img_path,'','true','search_bg');
		}
		$sql = "SELECT code FROM {$code} WHERE mode='C'";
		$tmps = $mysql->get_one($sql);
		$common = explode("|",stripslashes($tmps));
		$common[6] = $file;
		$common=implode("|",$common);
		$common = addslashes($common);
		$sql = "UPDATE  {$code} SET code='{$common}' WHERE mode='C'";
                   
        $msg = "검색바 배경 이미지를 변경했습니다!";
		$go_url='common.html';
    break;

	 case "main" :    
		if(!eregi("none",$_FILES['main_img']['tmp_name']) && $_FILES['main_img']['tmp_name']) {
			$file = upFile($_FILES['main_img']['tmp_name'],$_FILES['main_img']['name'],$img_path,'','true','main');
		}		
		$sql = "SELECT code FROM {$code} WHERE mode='M'";
		$tmps = $mysql->get_one($sql);
		if($tmps) {
			$main	= explode("|*|",stripslashes($tmps));
			if($file) $main[0] = $file;
			$main[14] = $_POST['img_use'];
			$main[26] = $_POST['main_link'];
			$main	=implode("|*|",$main);
			$main	= addslashes($main);			
			$sql	= "UPDATE {$code} SET code='{$main}' WHERE mode='M'";
		} 
		else $sql = "INSERT INTO {$code} VALUES('','','{$file}|*||*||*||*||*||*||*||*||*||*||*||*||*||*|{$_POST[img_use]}|*||*||*||*||*||*||*||*||*||*||*|{$_POST['main_link']}','M')";
		$msg	= "메인이미지 설정을 변경했습니다!";
        $go_url = 'main.html';
     break;

	 case "mgoods" :    
			$sql = "SELECT code FROM {$code} WHERE mode='M'";
			$main = explode("|*|",stripslashes($mysql->get_one($sql)));
			$main[1] = $_POST['hit_use'];
			$main[2] = $_POST['reco_use'];
			$main[3] = $_POST['reco_num'];
			$main[4] = $_POST['new_use'];
			$main[5] = $_POST['new_num'];
			$main[15] = $_POST['hit_num'];
			$main[20] = $_POST['mbox_use'];
			$main[25] = $_POST['brand_use'];
			$main[27] = $_POST['cooper_use'];
			$main[28] = $_POST['cooper_num'];
			$main=implode("|*|",$main);
			$main = addslashes($main);
			$sql = "UPDATE  {$code} SET code='{$main}' WHERE mode='M'";
            $msg = "메인 상품정보를 수정했습니다!";
			$mysql->query($sql);
			alert($msg,"close");
     break;

	 case "mgoods2" :    
			$cate = $_POST['cate'];
			if(!$cate) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하세요!','close');			

			$sql = "SELECT code FROM mall_cate WHERE cate='{$cate}'";
			$main = explode("|*|",stripslashes($mysql->get_one($sql)));
			$main[0] = $_POST['hit_use'];
			$main[1] = $_POST['hit_num'];
			$main[2] = $_POST['reco_use'];
			$main[3] = $_POST['reco_num'];
			$main[4] = $_POST['new_use'];
			$main[5] = $_POST['new_num'];			
			$main=implode("|*|",$main);
			$main = addslashes($main);
			
			$sql = "UPDATE mall_cate SET code='{$main}' WHERE cate='{$cate}'";
            $msg = "분류 상품정보를 수정했습니다!";
            $go_url = "main2.html?cate={$cate}";
     break;

	 case "code" :    
			$sql = "SELECT code FROM {$code} WHERE mode='M'";
			$main = explode("|*|",stripslashes($mysql->get_one($sql)));
			
			switch($_POST['code_name']) {
				case "hit_insert" :
					$main[6] = $_POST['hit_up_use'];
					$main[7] = addslashes($_POST['hit_insert']);
				break;

				case "reco_insert" :
					$main[8] = $_POST['reco_up_use'];
					$main[9] = addslashes($_POST['reco_insert']);
				break;

				case "new_insert" :
					$main[10] = $_POST['new_up_use'];
					$main[11] = addslashes($_POST['new_insert']);
				break;

				case "new_down_insert" :
					$main[21] = $_POST['new_down_use'];
					$main[22] = addslashes($_POST['new_down_insert']);
				break;

				case "cooper_down_insert" :
					$main[29] = $_POST['cooper_down_use'];
					$main[30] = addslashes($_POST['cooper_down_insert']);
				break;

				case "box_insert" :
					$main[23] = $_POST['box_up_use'];
					$main[24] = addslashes($_POST['box_insert']);
				break;

				case "copy_insert" :
					$main[12] = $_POST['copy_up_use'];
					$main[13] = addslashes($_POST['copy_insert']);
				break;

				case "cate_insert" :
					$main[16] = $_POST['cate_down_use'];
					$main[17] = addslashes($_POST['cate_insert']);
				break;

				case "banner_insert" :
					$main[18] = $_POST['banner_down_use'];
					$main[19] = addslashes($_POST['banner_insert']);
				break;
			}
			$main=implode("|*|",$main);
			$sql = "UPDATE  {$code} SET code='{$main}' WHERE mode='M'";
            $msg = "메인 삽입코드 정보를 수정했습니다!";
            $mysql->query($sql);
			alert($msg,"close");
     break;

	 case "code2" :    
			$cate = $_POST['cate'];
			if(!$cate) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하세요!','close');			

			$sql = "SELECT code FROM mall_cate WHERE cate='{$cate}'";
			$main = explode("|*|",stripslashes($mysql->get_one($sql)));
			
			$main[0] = $main[0];
			$main[1] = $main[1];
			$main[2] = $main[2];
			$main[3] = $main[3];
			$main[4] = $main[4];
			$main[5] = $main[5];
	 	    $main[6] = $_POST['cate_up_use'];
			$main[7] = addslashes($_POST['cate_insert']);
			$main[8] = $_POST['hit_up_use'];
			$main[9] = addslashes($_POST['hit_insert']);

			$main=implode("|*|",$main);
			
	        $sql = "UPDATE mall_cate SET code='{$main}' WHERE cate='{$cate}'";
			$msg = "분류 삽입코드 정보를 수정했습니다!";
            $go_url = "main2.html?cate={$cate}";
     break;

	 case "mail1" :    
			if(!eregi("none",$_FILES['img1']['tmp_name']) && $_FILES['img1']['tmp_name']) {									
				$file = upFile($_FILES['img1']['tmp_name'],$_FILES['img1']['name'],$img_path,'','true','m_main');
			}

			$sql = "SELECT code FROM {$code} WHERE mode='F'";
			$mail = explode("|*|",stripslashes($mysql->get_one($sql)));
			$mail[0] = $file;
			$mail=implode("|*|",$mail);
			$sql = "UPDATE  {$code} SET code='{$mail}' WHERE mode='F'";
            $msg = "쇼핑몰 메일이미지를 변경했습니다!";
            $go_url = 'mail.html';
     break;	

	 case "mail2" :    			
			$sql = "SELECT code FROM {$code} WHERE mode='F'";
			$mail = explode("|*|",stripslashes($mysql->get_one($sql)));
			$mail[2] = $_POST['type'];
			$mail[3] = addslashes($_POST['info1']);			
			$mail=implode("|*|",$mail);
			$sql = "UPDATE  {$code} SET code='{$mail}' WHERE mode='F'";
            $msg = "회원가입 축하문구를 변경했습니다!";
            $go_url = 'mail.html';
     break;

	 case "mail3" :    
			$sql = "SELECT code FROM {$code} WHERE mode='F'";
			$mail = explode("|*|",stripslashes($mysql->get_one($sql)));
			$mail[4] = $_POST['type'];
			$mail[5] = addslashes($_POST['info2']);			
			$mail=implode("|*|",$mail);
			$sql = "UPDATE  {$code} SET code='{$mail}' WHERE mode='F'";
            $msg = "상품 구매 문구를 변경했습니다!";
            $go_url = 'mail.html';
     break;

	 case "mail4" :    
			$sql = "SELECT code FROM {$code} WHERE mode='F'";
			$mail = explode("|*|",stripslashes($mysql->get_one($sql)));
			$mail[6] = $_POST['type'];
			$mail[7] = addslashes($_POST['info3']);	
			$mail=implode("|*|",$mail);
			$sql = "UPDATE  {$code} SET code='{$mail}' WHERE mode='F'";
            $msg = "상품 발송 문구를 변경했습니다!";
            $go_url = 'mail.html';
     break;

	 default: alert("정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.","back");
	 break;
}

$mysql->query($sql);
if($mode=='menu2') alert($msg,'close2',$go_url);
else alert($msg,$go_url);
?>
