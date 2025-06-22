<?
set_time_limit(0); 
######################## lib include
include "../ad_init.php";
include "./conf.php";

###################### 변수 정의 ##########################
$field		= isset($_GET['field']) ? $_GET['field'] : '';
$word		= isset($_GET['word']) ? $_GET['word'] : '';
$page		= isset($_GET['page']) ? $_GET['page'] : 1;
$seccate	= isset($_GET['seccate']) ? $_GET['seccate'] : '';
$mode		= isset($_GET['mode']) ? $_GET['mode'] : '';
$limit		= isset($_GET['limit']) ? $_GET['limit'] : '';
$location	= isset($_GET['location']) ? $_GET['location'] : '';
$point		= isset($_GET['point']) ? $_GET['point'] : '';
$status		= isset($_GET['status']) ? $_GET['status'] : '';
$code		= isset($_GET['code'])? $_GET['code']:$_POST['code']; 
$uid		= isset($_GET['uid'])? $_GET['uid']:$_POST['uid']; 

##################### addstring ############################ 
if($field && $word) $addstring .= "&field={$field}&word={$word}";
if($limit) $addstring .= "&limit={$limit}";
if($location) $addstring .= "&location={$location}";
if($status) $addstring .= "&status={$status}";
if($point) $addstring .= "&point={$point}";
if($seccate) $addstring = "&seccate={$seccate}";
if($page) $addstring .="&page={$page}";
	 
// DATE
$signdate = time();

if(!eregi("none",$_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name']) {									
	$_POST['file'] = upFile($_FILES['file']['tmp_name'],$_FILES['file']['name'],$dir);
}
else $_POST['file'] = '';

if(!eregi("none",$_FILES['file2']['tmp_name']) && $_FILES['file2']['tmp_name']) {									
	$_POST['file2'] = upFile($_FILES['file2']['tmp_name'],$_FILES['file2']['name'],$dir);
}
else $_POST['file2'] = '';

if(!eregi("none",$_FILES['img1']['tmp_name']) && $_FILES['img1']['tmp_name']) {									
	$_POST['img1'] = upFile($_FILES['img1']['tmp_name'],$_FILES['img1']['name'],$dir,'','true');
}
else $_POST['img1'] = '';

if(!eregi("none",$_FILES['img2']['tmp_name']) && $_FILES['img2']['tmp_name']) {									
	$_POST['img2'] = upFile($_FILES['img2']['tmp_name'],$_FILES['img2']['name'],$dir,'','true');
}
else $_POST['img2'] = '';

switch($code) {
	case "banner" : case 'mobile_banner' :
		if(!eregi("none",$_FILES['banner']['tmp_name']) && $_FILES['banner']['tmp_name']) {									
			$_POST['banner'] = upFile($_FILES['banner']['tmp_name'],$_FILES['banner']['name'],$dir,'','true');
		} 
		else $_POST['banner'] = '';

		//if(!eregi("http://",$_POST['link']) && $_POST['link']) $_POST['link']="http://".$_POST['link'];	
		
		if($_POST['cate']) $where = " && cate = '{$_POST['cate']}'";
		$sql = "SELECT MAX(rank) FROM mall_{$code} WHERE location = '{$_POST['location']}' {$where}";
		$_POST['rank'] = $mysql->get_one($sql) + 1;

		if($mode=='modify') {
			$_POST['rank'] = '';
			$sql = "SELECT rank, location, cate FROM mall_{$code} WHERE uid='{$uid}'";
			$row = $mysql->one_row($sql);

			if($row['location']==$_POST['location']) {
				if($row['cate']!=$_POST['cate'] && $_POST['cate']) {
					$sql = "UPDATE mall_{$code} SET rank = rank - 1 WHERE location='{$row['location']}' && cate='{$row['cate']}' && rank > '{$row['rank']}'";
					$mysql->query($sql);
				}
				else $_POST['rank'] = '';			
			} 
			else {
				if($row['location']=='5') {
					$sql = "UPDATE mall_{$code} SET rank = rank - 1 WHERE location='{$row['location']}' && cate='{$row['cate']}' && rank > '{$row['rank']}'";
				}
				else {
					$sql = "UPDATE mall_{$code} SET rank = rank - 1 WHERE location='{$row['location']}' && rank > '{$row['rank']}'";
				}		
				$mysql->query($sql);
			}
			
			if($_POST['rank']) {
				$sql = "UPDATE mall_{$code} SET rank='{$_POST['rank']}' WHERE uid='{$uid}'";
				$mysql->query($sql);
			}			
		}
	break;

	case "popup" :
		$_POST[info] = "{$_POST['info1']}|{$_POST['info2']}|{$_POST['info3']}|{$_POST['info4']}";  
	break;

	case "event" :
		if($_POST['sbrand']) $_POST['scate'] = $_POST['sgoods'] = '';
		if($_POST['scate'] || $_POST['sgoods']) $_POST['sbrand'] = '';		
	break;

	case "affiliate_banner" :
		if(!eregi("none",$_FILES['banner']['tmp_name']) && $_FILES['banner']['tmp_name']) {									
			$_POST['banner'] = upFile($_FILES['banner']['tmp_name'],$_FILES['banner']['name'],$dir);
		} 
		else $_POST['banner'] = '';
	break;

	case "affiliate_account" :		
		$_POST['a_month'] = $_POST['year']."-".$_POST['month'];
		$sql = "SELECT name FROM mall_affiliate WHERE id='{$_POST['affiliate']}'";
		$_POST['name'] = $mysql->get_one($sql);		
	break;
}

switch($mode) {
    case "write" : 
		if($code=='reserve') $signdate = date("Y-m-d H:i:s",time());
	    
		if($code=='auto_search') {
			$sql = "SELECT count(*) FROM mall_auto_search WHERE word='{$_POST['word']}'";
			if($mysql->get_one($sql)>0) alert("'{$_POST['word']}'는 이미 등록 되어 있습니다.","back");
		}

		$sql = "INSERT INTO mall_{$code} (";
	    for($i=1;$i<$MTins[0];$i++){  
            $sql .= $MTins[$i].", ";	
		}
		$sql .= $MTins[$i].") VALUES (";
        for($i=1;$i<$MTins[0];$i++){  
            $fd = $MTins[$i];
			$_POST[$fd] = addslashes($_POST[$fd]);
			if($fd=='passwd') $_POST['passwd'] = md5($_POST['passwd']);
			$sql .= "'{$_POST[$fd]}', ";	
		}
		$sql .= "'{$signdate}')";
		$msg = "등록했습니다";
		$mysql->query($sql);

		if($code=='event') {
			if($s_date<=date("Y-m-d")) {  //이벤트 실행

				$sql = "SELECT MAX(uid) FROM mall_event";
				$uid = $mysql->get_one($sql);

				if($e_date<date("Y-m-d")) {
					$sql = "UPDATE mall_event SET s_check ='2' WHERE uid='{$uid}'";
					$mysql->query($sql);					
					break;
				}

				$scate = $_POST['scate'];
				$sgoods = $_POST['sgoods'];
				$sbrand = $_POST['sbrand'];

				if(!$scate && !$sgoods && !$sbrand) {
					$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE SUBSTRING(cate,1,3)!='999' ";
					$mysql->query($sql);
				}
				else {
					if($scate) {
						$tmps = explode("|",$scate);
						for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
							if(substr($tmps[$i],3,9)=='000000000') {
								$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE SUBSTRING(cate,1,3)='".substr($tmps[$i],0,3)."'";
							}
							else if(substr($tmps[$i],6,6)=='000000') {
								$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE SUBSTRING(cate,1,6)='".substr($tmps[$i],0,6)."'";
							}
							else if(substr($tmps[$i],9,3)=='000') {
								$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE SUBSTRING(cate,1,9)='".substr($tmps[$i],0,9)."'";
							}
							else {
								$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE cate='{$tmps[$i]}'";
							}
							$mysql->query($sql);
						}
					}
					
					if($sgoods) {
						$tmps = explode("|",$sgoods);
						for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
							$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE uid='{$tmps[$i]}'";
							$mysql->query($sql);
						}
					}

					if($sbrand) {
						$tmps = explode("|",$sbrand);
						for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
							$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE brand='{$tmps[$i]}'";
							$mysql->query($sql);
						}
					}
				}

				$sql ="UPDATE mall_event SET s_check = '1' WHERE uid='{$uid}'";
				$mysql->query($sql);
			}
		}
    break;
    case "modify" :  
	    if($code=='goods_point') {
			if($_POST['best']=='Y' && $_POST['reserve']>0) {
				$sql = "SELECT id, best FROM mall_goods_point WHERE uid='{$uid}'";
				$row = $mysql->one_row($sql); 
				if($row['best']!='Y') {			
					$pid = $row['id'];
					$reserve = $_POST['reserve'];					
					$subject = "우수상품평 채택 적립금";
					$signdate = date("Y-m-d H:i:s",time());
					$sql = "INSERT INTO mall_reserve (uid, id, subject, reserve, order_num, goods_num, status, signdate) VALUES ('','{$pid}','{$subject}','{$reserve}','','','B','{$signdate}')";
					$mysql->query($sql);
					$sql = "UPDATE pboard_member SET reserve = reserve + '{$reserve}' WHERE id = '{$pid}'"; 
					$mysql->query($sql);
				}
            }                           
        }
					 
	    $sql = "UPDATE mall_{$code} SET ";
		for($i=1;$i<=($MTmod[0]);$i++){  
            $fd = $MTmod[$i];
			$_POST[$fd] = addslashes($_POST[$fd]);
			if(eregi('file',$fd) || eregi('banner',$fd) || eregi('img',$fd)) {
				if($_POST[$fd]) {
					if($_POST[$fd])	$sql .= ", {$fd} =  '{$_POST[$fd]}' ";								
					$sql2 = "SELECT {$fd} FROM mall_{$code} WHERE uid='{$uid}'";
					$tmp_img = $mysql->get_one($sql2);
					if($tmp_img) @unlink("{$dir}/".$tmp_img); 
                }
            } 
			else if($fd=='passwd') {
				if($_POST[$fd]) $sql .= ", {$fd} =  '".md5($_POST[$fd])."'";		
			}
			else {
				if($i=='1') $sql .= $MTmod[$i]." =  '{$_POST[$fd]}'";		
			    else $sql .= ", {$MTmod[$i]} =  '{$_POST[$fd]}'";		
			}					
		}

		$sql .= " WHERE uid='{$uid}'";			 				 						
 		$msg = "수정했습니다";
		$mysql->query($sql);

		if($code=='event') {
			$scate = $_POST['scate'];
			$sgoods = $_POST['sgoods'];
			$sbrand = $_POST['sbrand'];
			$s_date = $_POST['s_date'];
			$e_date = $_POST['e_date'];

			if($s_date<=date("Y-m-d")) {  //이벤트 실행				
				$sql = "UPDATE mall_goods SET event='0' WHERE event='{$uid}'";
				$mysql->query($sql);
				
				if($e_date<date("Y-m-d")) {
					$sql = "UPDATE mall_event SET s_check ='2' WHERE uid='{$uid}'";
					$mysql->query($sql);					
					break;
				}

				if(!$scate && !$sgoods && !$sbrand) {
					$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE SUBSTRING(cate,1,3)!='999' ";
					$mysql->query($sql);
				}
				else {				
					if($scate) {
						$tmps = explode("|",$scate);
						for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
							if(substr($tmps[$i],3,6)=='000000') {
								$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE SUBSTRING(cate,1,3)='".substr($tmps[$i],0,3)."'";
							}
							else if(substr($tmps[$i],6,3)=='000') {
								$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE SUBSTRING(cate,1,6)='".substr($tmps[$i],0,6)."'";
							}
							else {
								$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE cate='{$tmps[$i]}'";
							}
							$mysql->query($sql);
						}
					}
					
					if($sgoods) {
						$tmps = explode("|",$sgoods);
						for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
							$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE uid='{$tmps[$i]}'";
							$mysql->query($sql);
						}
					}

					if($sbrand) {
						$tmps = explode("|",$sbrand);
						for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
							$sql = "UPDATE mall_goods SET event = '{$uid}' WHERE brand='{$tmps[$i]}'";
							$mysql->query($sql);
						}
					}
				}				

				$sql ="UPDATE mall_event SET s_check = '1', scate='{$scate}', sgoods='{$sgoods}', sbrand='{$sbrand}' WHERE uid='{$uid}'";
				$mysql->query($sql);
			}			
			else {
				$sql = "SELECT s_check FROM mall_event WHERE uid='{$uid}'";
				$check = $mysql->get_one($sql);

				if($check==1) {
					$sql = "UPDATE mall_goods SET event='0' WHERE event='{$uid}'";
					$mysql->query($sql);
					$sql ="UPDATE mall_event SET s_check = '0' WHERE uid='{$uid}'";
					$mysql->query($sql);
				}
			}
		}

	break;					
}  


if($code=='reserve' && $_POST['id']) {
	$sql = "SELECT SUM(reserve) FROM  mall_reserve WHERE id='{$_POST[id]}' && status='B'";
	$MONEY1= $mysql->get_one($sql);
	$sql = "SELECT SUM(reserve) FROM  mall_reserve WHERE id='{$_POST[id]}' && status='C'";
	$MONEY2 = $mysql->get_one($sql);
	$MONEY3 = ($MONEY1 - $MONEY2);
	$sql = "UPDATE pboard_member SET reserve = '{$MONEY3}' WHERE id = '{$_POST[id]}'"; 
	$mysql->query($sql);
}

alert($msg,$Main.$addstring);

//메모리제거
ignore_user_abort(true); 
register_shutdown_function('userAbortFunc');

?>