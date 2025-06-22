<?
######################## lib include
include "../ad_init.php";
$img_path = "../../image/cate/";

$cate_mode	= isset($_GET['cate_mode'])? $_GET['cate_mode']:'';
$v_list		= isset($_POST['v_list'])? $_POST['v_list']:'2';
$sub		= isset($_POST['sub'])? $_POST['sub']:'';

switch($cate_mode) {
	#################################### 1차분류 생성 ####################################
	case "b_create" :
		$big_cate = isset($_POST['big_cate'])? trim($_POST['big_cate']):'';
		chrtrim($big_cate); // 공백쿼리검사 함수호출
		
		// 현재 대분류 카테고리중 가장 큰 카테고리번호 쿼리
		$sql = "SELECT MAX(cate) as m_cate, MAX(number) as m_num FROM mall_cate WHERE cate_dep = 1 && cate!='999000000000'";
		$row = $mysql->one_row($sql);
		$max_cate_num = $row['m_cate'];
		if(!$max_cate_num) { // 등록된 대분류 카테고리가 없을경우 100000000 초기화
			$next_cate = 100000000000;
		} 
		else {
			$next_cate = $max_cate_num + 1000000000;
		}
				
		$row['m_num']++;
		// 1차카테고리 추가
		
		$add_sql = "INSERT INTO mall_cate(cate,cate_name,cate_dep,cate_parent,cate_sub,img1,img2,img3,list_mode,valid,soldout,number) VALUES('{$next_cate}','{$big_cate}',1,'0','{$sub}','','','','{$v_list}','1','0','{$row[m_num]}')";
		$mysql->query($add_sql);
		alert('1차분류를 추가했습니다!','cate_manager.html?keep1='.$next_cate);

	break;

	#################################### 2차분류 생성 ####################################
	case "m_create": // 중분류 카테고리 생성
	    $big_cate = isset($_POST['big_cate'])? $_POST['big_cate']:'';
		$m_cate = isset($_POST['m_cate'])? trim($_POST['m_cate']):'';
			
		chrtrim($m_cate); 

		// 현재 가장큰 해당중분류 카테고리 번호에서 +100을 적용해 카테고리번호 설정
		$sql = "SELECT MAX(cate) as m_cate, MAX(number) as m_num FROM mall_cate WHERE cate_dep = 2 && cate_parent = {$big_cate}";
		$row = $mysql->one_row($sql);
		$max_cate_num = $row['m_cate'];
		if($max_cate_num <= $big_cate) { // 아직 중분류가 하나도 없을경우 중분류 카테고리번호 설정			
			$next_cate = substr($big_cate,0,6) + 100;
			$next_cate .= "000000";
		} 
		else {
			$next_cate = substr($max_cate_num,0,6) + 1;
			$next_cate .= "000000";
		}
					
		$row['m_num']++;
		// 2차 카테고리 추가
		
		$add_sql = "INSERT INTO mall_cate(cate,cate_name,cate_dep,cate_parent,cate_sub,img1,img2,img3,list_mode,valid,soldout,number)  VALUES('{$next_cate}','{$m_cate}',2,'{$big_cate}','{$sub}','','','','{$v_list}','1','0','{$row[m_num]}')";
		$mysql->query($add_sql);
		alert('2차분류를 추가했습니다!','cate_manager.html?keep1='.$big_cate);
	break;

	#################################### 3차분류 생성 ####################################
	case "s_create": // 중분류 카테고리 생성
	    $big_cate = isset($_POST['big_cate'])? $_POST['big_cate']:'';
		$m_cate = isset($_POST['m_cate'])? $_POST['m_cate']:'';
		$s_cate = isset($_POST['s_cate'])? trim($_POST['s_cate']):'';
			
		chrtrim($s_cate); 

		// 현재 가장큰 해당중분류 카테고리 번호에서 +100을 적용해 카테고리번호 설정
		$sql = "SELECT MAX(cate) as m_cate, MAX(number) as m_num FROM mall_cate WHERE cate_dep = 3 && cate_parent = {$m_cate}";
		$row = $mysql->one_row($sql);
		$max_cate_num = $row['m_cate'];
		
		if($max_cate_num <= $m_cate) { // 아직 중분류가 하나도 없을경우 중분류 카테고리번호 설정			
			$next_cate = substr($m_cate,0,9) + 100;
			$next_cate .= "000";
		} 
		else {
			$next_cate = substr($max_cate_num,0,9) + 1;
			$next_cate .= "000";
		}
					
		$row['m_num']++;
		// 3차 카테고리 추가
		
		$add_sql = "INSERT INTO mall_cate(cate,cate_name,cate_dep,cate_parent,cate_sub,img1,img2,img3,list_mode,valid,soldout,number)  VALUES('{$next_cate}','{$s_cate}',3,'{$m_cate}','{$sub}','','','','{$v_list}','1','0','{$row[m_num]}')";

		$mysql->query($add_sql);
		alert('3차분류를 추가했습니다!','cate_manager.html?keep1='.$big_cate.'&keep5='.$m_cate);
	break;
    
	#################################### 4차분류 생성 ####################################
	case "d_create" :
		$s_cate = isset($_POST['s_cate'])? $_POST['s_cate']:'';
		$last_cate = isset($_POST['last_cate'])? trim($_POST['last_cate']):'';
		chrtrim($s_cate); // 공백쿼리검사 함수호출
		
		// 폼으로 넘어온 last_cate 문자열 배열처리
		$last_arry = split(":",$last_cate);
		$last_cate = $last_arry[0];
		
		// 3차카테고리 번호설정		
		$sql = "SELECT MAX(cate) as m_cate,  MAX(number) as m_num  FROM mall_cate WHERE cate_parent = '{$last_cate}' && cate_dep = '4'";
		$row = $mysql->one_row($sql);
		$max_cate_num = $row[m_cate];
		if($max_cate_num <= $last_cate) { // 아직 중분류가 하나도 없을경우 중분류 카테고리번호 설정
			$next_cate = $last_cate + 100;
		} 
		else {
			$next_cate = $max_cate_num + 1;
		}

        $row['m_num']++;
		
		// 4차카테고리 추가쿼리
		$add_sql = "INSERT INTO mall_cate(cate,cate_name,cate_dep,cate_parent,cate_sub,img1,img2,img3,list_mode,valid,soldout,number)  VALUES('{$next_cate}','{$s_cate}',4,'{$last_cate}','0','','','','{$v_list}','1','0','{$row[m_num]}')";

		$mysql->query($add_sql);
		alert('4차분류를 생성했습니다!','cate_manager.html?keep2='.substr($next_cate,0,3).'000000000:O&keep3='.substr($next_cate,0,6).'000000:O&keep4='.substr($next_cate,0,9).'000:O');
	break;
	
	#################################### 카테고리명 수정 ####################################
	case "cate_modify" :
		$c_number	= addslashes($_POST['c_number']);
		$soldout	= addslashes($_POST['soldout']);
		$valid		= addslashes($_POST['valid']);

		$re_cate		= isset($_POST['re_cate'])? trim($_POST['re_cate']):'';
		$last_cate		= isset($_POST['last_cate'])? $_POST['last_cate']:'';   
		$access_level	= isset($_POST['access_level'])? $_POST['access_level']:'';
		$access_type	= isset($_POST['access_type'])? $_POST['access_type']:'';
		if($access_level && $access_type) {
			$access_level = "{$access_level}|{$access_type}";
		}
		else $access_level = "";

		chrtrim("$re_cate"); // 공백쿼리검사 함수호출 
        
		if($sub=='0'){
			$sql = "SELECT count(*) FROM mall_cate WHERE cate_parent = '{$last_cate}'";
			if($mysql->get_one($sql) >0) alert('먼저 하위분류를 삭제하셔야 하위분류 없음 적용이 가능합니다','back');
        }
		
		################ 파일 삭제 ########################
		$sql = "SELECT img1,img2,img3 FROM mall_cate WHERE cate='{$last_cate}'";
		$row = $mysql->one_row($sql);
	
		$del_arr = Array('',$row[img1],$row[img2],$row[img3]);
	
		for($i=1;$i<4;$i++) {
            if($_POST["del_img".$i] =='1') {
			    delFile($img_path.$del_arr[$i]);
                ${"m_img".$i} = ", img".$i."='' ";
		    }
        }
		
		################ 파일 업로드 ########################
		for($i =1; $i <= 3; $i++) {
		   if(!eregi("none",$_FILES["img".$i]['tmp_name']) && $_FILES["img".$i]['tmp_name']) {									
			   if($i=='1') $save_name = "CT_{$last_cate}";
			   else if($i=='2') $save_name = "MT_{$last_cate}_on";
			   else $save_name = "MT_{$last_cate}_off";

			   $file = upFile($_FILES["img".$i]['tmp_name'],$_FILES["img".$i]['name'],$img_path,'','true',$save_name);
			   ${"m_img".$i} = ", img".$i." = '$file'";
			}      
		}

		
		if(!$sub) $sub='0';

        $sql = "SELECT cate_dep, cate_parent, number, valid FROM mall_cate WHERE cate='{$last_cate}'";
		$row = $mysql->one_row($sql);

		if($row['number'] > $c_number) {
			$sql = "UPDATE mall_cate SET number = number +1 WHERE number >= {$c_number} && number < {$row[number]} && cate_dep = '{$row[cate_dep]}' && cate_parent = '{$row[cate_parent]}' && cate!='999000000000'";
        } else {
            $sql = "UPDATE mall_cate SET number = number -1 WHERE number <= {$c_number} && number > {$row[number]} && cate_dep = '{$row[cate_dep]}' && cate_parent = '{$row[cate_parent]}' && cate!='999000000000'";
        }
		$mysql->query($sql);

		if($row['valid'] != $valid){
			
			switch($row['cate_dep']) {
				case "1" : $where = "SUBSTRING(cate,1,3) = '".substr($last_cate,0,3)."' "; 
				break;
				case "2" : $where = "SUBSTRING(cate,1,6) = '".substr($last_cate,0,6)."' "; 
				break;
				case "3" : $where = "SUBSTRING(cate,1,9) = '".substr($last_cate,0,9)."' "; 
				break;
				case "4" : $where = "cate='{$last_cate}' "; 
				break;
			}

			 if($valid==0) {
				$sql = "UPDATE mall_goods SET type = 'B' WHERE {$where}";
             } else {
				$sql  = "UPDATE mall_goods SET type='A' WHERE {$where}";
			 }
			 $mysql->query($sql);
        }

		$sql = "UPDATE mall_cate SET cate_name = '{$re_cate}', cate_sub='{$sub}', list_mode='{$v_list}', valid='{$valid}', number='{$c_number}', soldout='{$soldout}', access_level='{$access_level}' {$m_img1} {$m_img2} {$m_img3} WHERE cate = '{$last_cate}'";
		$mysql->query($sql);
		
		switch($row['cate_dep']) {
			case "2" : $addstring = '?keep2='.substr($last_cate,0,3).'000000000:O';					
			break;
			case "3" : $addstring = '?keep2='.substr($last_cate,0,3).'000000000:O&keep3='.substr($last_cate,0,6).'000000:O';
			break;
			case "4" : $addstring = '?keep2='.substr($last_cate,0,3).'000000000:O&keep3='.substr($last_cate,0,6).'000000:O&keep4='.substr($last_cate,0,9).'000:O';
			break;
		}
	
		echo "<script>alert('분류정보를 수정 했습니다.'); parent.location.href='cate_manager.html{$addstring}';</script>";
		exit;
    break;
    
	#################################### 카테고리명 삭제 ####################################
	case "cate_del" :		
		$last_cate = isset($_POST['last_cate'])? $_POST['last_cate']:'';        
		// 폼으로 넘어온 last_cate 문자열 배열처리
		$last_arry = split(":",$last_cate);		
		$last_cate = $last_arry[0];

		$sql = "SELECT * FROM mall_cate WHERE cate='{$last_cate}'";
        $row = $mysql->one_row($sql);

		if($row['img1']) delFile("{$img_path}/{$row[img1]}");
		if($row['img2']) delFile("{$img_path}/{$row[img2]}");
		if($row['img3']) delFile("{$img_path}/{$row[img3]}");

		$sql = "DELETE FROM mall_cate WHERE cate = '{$last_cate}'";
		$mysql->query($sql);

        $sql = "UPDATE mall_cate SET number = number -1 WHERE number > {$row[number]} && cate_dep = '{$row[cate_dep]}' && cate_parent = '{$row[cate_parent]}'";
        $mysql->query($sql);

		switch($row['cate_dep']) {
			case "2" : $addstring = '?keep2='.substr($last_cate,0,3).'000000000:O';					
			break;
			case "3" : $addstring = '?keep2='.substr($last_cate,0,3).'000000000:O&keep3='.substr($last_cate,0,6).'000000:O';
			break;
			case "4" : $addstring = '?keep2='.substr($last_cate,0,3).'000000000:O&keep3='.substr($last_cate,0,6).'000000:O&keep4='.substr($last_cate,0,9).'000:O';
			break;
		}
 
		alert('분류를 삭제했습니다!',"cate_manager.html{$addstring}");
	break;

    #################################### 카테고리 정렬 ####################################
	case "sort1" :
		$cate_sort = isset($_GET['cate_sort'])? $_GET['cate_sort']:''; 
		if(!$cate_sort) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도 하시기 바랍니다.','back');	

		$tmp = explode("|",$cate_sort);
		for($i=0,$cnt=count($tmp);$i<$cnt;$i++){
			$sql = "UPDATE mall_cate SET number = ".($i+1)." WHERE cate='{$tmp[$i]}'";
			$mysql->query($sql);			
        }	

	    alert('분류 순서를 변경했습니다!','cate_manager.html');

    break; 

	#################################### 공동구매 설정 ####################################
	case "coop_conf" :
		$valid		= addslashes($_POST['valid']);
		$number		= addslashes($_POST['number']);
		$last_cate	= "999000000000";
		$access_level	= isset($_POST['access_level'])? $_POST['access_level']:'';
		$access_type	= isset($_POST['access_type'])? $_POST['access_type']:'';
		if($access_level && $access_type) {
			$access_level = "{$access_level}|{$access_type}";
		}
		else $access_level = "";

		################ 파일 삭제 ########################
		$sql = "SELECT img1,img2,img3 FROM mall_cate WHERE cate='{$last_cate}'";
		$row = $mysql->one_row($sql);
	
		$del_arr = Array('',$row[img1],$row[img2],$row[img3]);
	
		for($i=1;$i<4;$i++) {
            if($_POST["del_img".$i] =='1') {
			    delFile($img_path.$del_arr[$i]);
                ${"m_img".$i} = ", img".$i."='' ";
		    }
        }
		
		################ 파일 업로드 ########################
		for($i =1; $i <= 3; $i++) {
		   if(!eregi("none",$_FILES["img".$i]['tmp_name']) && $_FILES["img".$i]['tmp_name']) {									
			   if($i=='1') $save_name = "CT_{$last_cate}";
			   else if($i=='2') $save_name = "MT_{$last_cate}_on";
			   else $save_name = "MT_{$last_cate}_off";

			   $file = upFile($_FILES["img".$i]['tmp_name'],$_FILES["img".$i]['name'],$img_path,'','true',$save_name);
			   ${"m_img".$i} = ", img".$i." = '$file'";
			}      
		}

		$code = "{$_POST['code_use']}|*|{$_POST['code']}";
		$code = addslashes($code);

		$sql = "UPDATE mall_cate SET valid='{$valid}', access_level='{$access_level}', number='{$number}', code='{$code}' {$m_img1} {$m_img2} {$m_img3} WHERE cate = '{$last_cate}'";
		$mysql->query($sql);

		alert('공동구매 설정을 변경했습니다!','coop_conf.html');
    break;

    default :
		movePage('cate_manager.html');
	break;
}
movePage('cate_manager.html');
?>
