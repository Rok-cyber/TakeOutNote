<?
@set_time_limit(0);
$skin_inc = "Y";
include "../ad_init.php";
include "{$lib_path}/class.Thumb.php";

function goodsDel($no) {
	global $mysql, $img_path, $mcnt, $up_dir;

	if($no>9999) $tmp_uid = floor($no/10000);
	$up_dir2 = $up_dir.$tmp_uid."/";
	
	$sql = "SELECT * FROM mall_goods WHERE uid = '{$no}'";
	if(!$row = $mysql->one_row($sql)) alert("해당상품은 이미 삭제 되었습니다","back");

	for($k=1;$k<=$mcnt;$k++) {		   				
		$tmps ="image{$k}";
		if($row[$tmps]) {
			delFile($img_path.$row[$tmps]);  			
		}		
	}

	if($row['other_image']) delTree($row['other_image']);
	if(is_dir("{$up_dir2}goods_{$no}")) delTree("{$up_dir2}goods_{$no}");
	
	$tmps = explode("|",$row['display']);
	if($tmps[0]!="0") {
		$sql = "UPDATE mall_goods SET o_num1 = o_num1 - 1 WHERE o_num1!=0 && o_num1>{$row['o_num1']} && SUBSTRING(display,1,1)='{$tmps[0]}'";
		$mysql->query($sql);
	}

	if($tmps[1]!="0") {
		$sql = "UPDATE mall_goods SET o_num2 = o_num2 - 1 WHERE o_num2!=0 && o_num2>{$row['o_num2']} && SUBSTRING(display,3,1)='{$tmps[1]}'";
		$mysql->query($sql);		
		$sql = "UPDATE mall_goods SET o_num3 = o_num3 - 1 WHERE o_num3!=0 && o_num3>{$row['o_num3']} && SUBSTRING(display,3,1)='{$tmps[1]}'";
		$mysql->query($sql);		
	}

	$sql = "DELETE FROM mall_goods WHERE uid = '{$no}'";
	$mysql->query($sql);	

	$sql = "DELETE FROM mall_goods_option WHERE guid = '{$no}'";
	$mysql->query($sql);	
	
	if(substr($row['cate'],0,3)=='999') {
		$sql = "DELETE FROM mall_goods_cooper WHERE guid = '{$no}'";
		$mysql->query($sql);	

		$sql = "DELETE FROM mall_cooperate WHERE guid = '{$no}'";
		$mysql->query($sql);	
	}

	$sql = "DELETE FROM mall_goods_view WHERE cno='{$row['cate']}{$no}'";
	$mysql->query($sql);
	
}

function goodsCopy($no,$cate_num) {
	global $mysql, $img_path, $up_path, $up_dir, $mcnt, $signdate, $tmp_uid,$my_id;	
	
	
	// 번호 지정
	$sql = "SELECT number FROM mall_goods WHERE cate = '{$cate_num}' ORDER BY number DESC LIMIT 1";
	$number = $mysql->get_one($sql);
	if($number) $number++;
	else $number = "1000";
		
	$sql = "SELECT * FROM mall_goods WHERE uid='{$no}'";
	$row = $mysql->one_row($sql);   
							 
	for($i=1;$i<=$mcnt;$i++) {   
		$tmps = "image{$i}";
		if($row[$tmps]) {
			$save_ext = ".".getExtension($row[$tmps]);
			$save_name = $cate_num.$number.$save_ext;
			copy($img_path.$row[$tmps], $img_path.$i."/{$tmp_uid}".$save_name);				
			${"image".$i} = "{$i}/{$tmp_uid}$save_name";
		 }
	}     

	if($row['other_image']) {			
		$dir_name = $up_path."goods_".$cate_num.$number;		
		copyTree($row['other_image'], $dir_name);			
		$other_image = $dir_name;
	}
	
	$tmps = explode("|",$row['display']);
	$disp1 = $tmps[0];
	$disp2 = $tmps[1];
	$val_add1 = $val_add2 = $val_add3 = '';

	if($disp1!="0") {
		$sql = "UPDATE mall_goods SET o_num1 = o_num1 + 1 WHERE SUBSTRING(display,1,1)='{$disp1}'";
		$tmps = $mysql->query($sql);
		$val_add1 = 1;
	}
		
	if($disp2!="0") {					
		$cate1 = substr($cate_num,0,3);
		$cate2 = substr($cate_num,0,6);

		$sql = "UPDATE mall_goods SET o_num2 = o_num2 + 1 WHERE SUBSTRING(display,3,1)='{$disp2}' && SUBSTRING(cate,1,3)='{$cate1}'";
		$mysql->query($sql);		

		$sql = "UPDATE mall_goods SET o_num3 = o_num3 + 1 WHERE SUBSTRING(display,3,1)='{$disp2}' && SUBSTRING(cate,1,6)='{$cate2}'";
		$mysql->query($sql);
			
		$val_add2 =	$val_add3 = 1;
	}

	$explan = addslashes($row['explan']);

	if($cate_num=='999000000000') {
		$coop_sdate = $coop_edate = date("Y-m-d 00:00",time()+(86400*3));
	}

	$sql = "INSERT INTO mall_goods (uid,cate,mcate,number,brand,special,name,search_name,add_msg,model,comp,made,price,consumer_price,price_ment,unit,def_qty,s_qty,qty,reserve,carriage,op_goods_type,op_goods,display,icon,image1,image2,image3,image4,image5,other_image,explan,tag,type,o_num1,o_num2,o_num3,sequence,coop_sdate,coop_edate,coop_close,coop_pay,p_id,signdate) 
	VALUES ('','{$cate_num}','{$row['mcate']}','{$number}','{$row['brand']}','{$row['special']}','".addslashes($row['name'])."','".addslashes($row['search_name'])."','".addslashes($row['add_msg'])."','{$row['model']}','{$row['comp']}','{$row['made']}','{$row['price']}','{$row['consumer_price']}','{$row['price_ment']}','{$row['unit']}','{$row['def_qty']}','{$row['s_qty']}','{$row['qty']}','{$row['reserve']}','{$row['carriage']}','{$row['op_goods_type']}','{$row['op_goods']}','{$row['display']}','{$row['icon']}','{$image1}','{$image2}','{$image3}','{$image4}','{$image5}','{$other_image}','{$explan}','{$row['tag']}','{$row['type']}','{$val_add1}','{$val_add2}','{$val_add3}','{$row['sequence']}','{$coop_sdate}','{$coop_edate}','{$row['coop_close']}','{$row['coop_pay']}','{$my_id}','{$signdate}')";  	
	$mysql->query($sql);		

	$sql = "SELECT uid FROM mall_goods WHERE cate='{$cate_num}' && number='{$number}' LIMIT 1";
	$uid = $mysql->get_one($sql);

	$sql = "SELECT * FROM mall_goods_info WHERE guid='{$no}' ORDER BY o_num ASC";
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){
		$opName1 = $row['name1'];
		$opContent1 = $row['content1'];
		$opName2 = $row['name2'];
		$opContent2 = $row['content2'];
		$sql = "INSERT INTO mall_goods_info VALUES('','{$uid}','{$opName1}','{$opContent1}','{$opName2}','{$opContent2}','{$row['o_num']}')";
		$mysql->query2($sql);
	}
	$opName1 = $opContent1 = $opName2 = $opContent2 = '';

	$sql = "SELECT * FROM mall_goods_option WHERE guid='{$no}' ORDER BY o_num ASC";
	$mysql->query($sql);
	while($row = $mysql->fetch_array()){
		$opType1 = $row['option1'];
		$opType2 = $row['option2'];
		$opPrice = $row['price'];
		$opDisplay = $row['display'];
		$opQty = $row['qty'];
		$opCode = $row['code'];
		$sql = "INSERT INTO mall_goods_option VALUES('','{$uid}','{$opType1}','{$opType2}','{$opPrice}','{$opDisplay}','{$opQty}','{$opCode}','{$row['o_num']}')";
		$mysql->query2($sql);
	}
	$opType1 = $opType2 = $opPrice = $opDisplay = $opQty = $opCode = '';

	if($cate_num=='999000000000') {
		$sql = "SELECT * FROM mall_goods_cooper WHERE guid='{$no}' ORDER BY o_num ASC";
		$mysql->query($sql);
		while($row = $mysql->fetch_array()){
			$coopQty = $row['qty'];
			$coopPrice = $row['price'];
			$coopCode = $row['code'];
			$sql = "INSERT INTO mall_goods_cooper VALUES('','{$uid}','{$coopQty}','{$coopPrice}','{$opCode}','{$row['o_num']}')";
			$mysql->query2($sql);
		}
		$coopPrice = $coopQty = $coopCode = '';
	}

	if(is_dir("{$up_dir}{$tmp_uid}goods_{$no}/")) {
		if($uid>9999) $tmp_uid2 = floor($uid/10000);
		$tmp_uid2 .= "/";

		$dir_name = "{$up_dir}{$tmp_uid2}goods_{$uid}";		
		copyTree("{$up_dir}{$tmp_uid}goods_{$no}/", $dir_name);			
		
		$explan = str_replace("/goods_{$no}/","/goods_{$uid}/",$explan);
		$sql = "UPDATE mall_goods SET explan = '{$explan}' WHERE uid='{$uid}'";
		$mysql->query($sql);

	}
}

function goodsCate($uid,$cate_num){
	global $mysql, $img_path, $mcnt, $signdate;	

	if($uid>9999) $tmp_uid = floor($uid/10000);
	$tmp_uid .= "/";

	// 번호 지정
	$sql = "SELECT number FROM mall_goods WHERE cate = '{$cate_num}' ORDER BY number DESC LIMIT 1";
	$number = $mysql->get_one($sql);
	if($number) $number++;
	else $number = "1000";
			
	$sql = "SELECT * FROM mall_goods WHERE uid='{$uid}'";
	$row = $mysql->one_row($sql);   
							 
	for($i=1;$i<=$mcnt;$i++) {   
		$tmps = "image{$i}";
		if($row[$tmps]) {
			$save_ext = ".".getExtension($row[$tmps]);
			$save_name = $cate_num.$number.$save_ext;
			@rename($img_path.$row[$tmps], $img_path.$i."/{$tmp_uid}".$save_name);				
			${"m_image".$i} = ", {$tmps} = '{$i}/{$tmp_uid}{$save_name}'";
		 }
	}     

	if($row['other_image']) {
		$tmps = explode("goods_",$row['other_image']);
		$dir_name = $tmps[0]."goods_".$cate_num.$number;
		@rename($row['other_image'],$dir_name);
		$other_image = ", other_image = '{$dir_name}' ";
	}

	$tmps = explode("|",$row['display']);		
	$disp2 = $tmps[1];
	$val_add2 = $val_add3 = '';
			
	if($disp2!="0") {					
		$cate1 = substr($cate_num,0,3);
		$cate2 = substr($cate_num,0,6);

		$sql = "UPDATE mall_goods SET o_num2 = o_num2 + 1 WHERE SUBSTRING(display,3,1)='{$disp2}' && SUBSTRING(cate,1,3)='{$cate1}'";
		$mysql->query($sql);

		$sql = "UPDATE mall_goods SET o_num3 = o_num3 + 1 WHERE SUBSTRING(display,3,1)='{$disp2}' && SUBSTRING(cate,1,6)='{$cate2}'";
		$mysql->query($sql);
				
		$val_add2 = ", o_num2 = '1'";
		$val_add3 = ", o_num3 = '1'";		
	}
			
	$sql = "UPDATE mall_goods SET cate='{$cate_num}', number='{$number}' {$m_image1} {$m_image2} {$m_image3} {$m_image4} {$m_image5} {$other_image} {$val_add2} {$val_add3} WHERE uid='{$uid}'";
	$mysql->query($sql);
					  
}

$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER']) && ($mode!='del' && $mode!='copy' && $mode!='cgVls')) alert('정상적으로 등록하세요!','back');

###################### 변수 정의 ##########################
$field		= $_GET['field'];
$word		= $_GET['word'];
$smoney1	= $_GET['smoney1'];
$smoney2	= $_GET['smoney2'];
$sdate1		= $_GET['sdate1'];
$sdate2		= $_GET['sdate2'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$seccate	= $_GET['seccate'];
$brands		= $_GET['brands'];
$coop_p		= $_GET['coop_p'];
$s_qty		= $_GET['s_qty'];
$uid		= isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
$up_dir		= $up_dir2 = "../../image/up_img/detail/";
$up_path	= "../../image/other_img/";
$img_path	= "../../image/goods_img";
$mcnt		= 5; //기본 이미지 개수

$tmp_uid	= '';

if($mode=='modify') {	
	if($uid>9999) $tmp_uid = floor($uid/10000);
}		
else if(!$mode || $mode=='copy' || $mode=='scopy') {
	$sql = "SELECT MAX(uid) FROM mall_goods";
	$maxUid = $mysql->get_one($sql);
	if($maxUid>9999) $tmp_uid = floor($maxUid/10000);	
}

if($tmp_uid) {
	$tmp_uid .= "/";
	$up_dir .= $tmp_uid;
	if(!is_dir($up_dir)) mkdir($up_dir,0707);	
	$up_path .= $tmp_uid;
	if(!is_dir($up_path)) mkdir($up_path,0707);	
	for($i=1;$i<6;$i++) {
		if(!is_dir($img_path."{$i}/{$tmp_uid}")) mkdir($img_path."{$i}/{$tmp_uid}",0707);
	}
}

##################### addstring ############################
if($field && $word) $addstring = "&field=$field&word={$word}";
if($seccate) $addstring .= "&seccate={$seccate}";
if($brands) $addstring .="&brands={$brands}";
if($coop_p) $addstring .="&coop_p={$coop_p}";
if($smoney1 && $smoney2) $addstring .= "&smoney1={$smoney1}&smoney2={$smoney2}";
if($sdate1 && $sdate2) $addstring .= "&sdate1={$sdate1}&sdate2={$sdate2}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($s_qty) $addstring .="&s_qty={$s_qty}";

$back = isset($_POST['back']) ? $_POST['back'] : "goods_list";
if($back=='goods_list' && $_GET['coop']==1) $back = "coop_goods_list";

$url		= "./{$back}.php?{$addstring}";
$signdate = time();

if($_POST['cate_num']) {
	$cate_num = $_POST['cate_num'];
	$cate_arr = split(":",$cate_num);
	$cate_num = $cate_arr[0];
}
else $cate_num = $_GET['cate_num'];

if($mode=='modify' || !$mode) {
	############################ 변수 처리 ############################	
	$mcate		= addslashes($_POST['mcate']);
	if($mcate) $mcate = ",{$mcate},";
	$brand		= addslashes($_POST['brand']);
	$special	= addslashes($_POST['special']);
	if($special) $special = ",{$special},";
	$sequence	= $_POST['sequence'];
	$name		= addslashes($_POST['name']);
	$search_name= str_replace(" ","",$name);
	$add_msg	= addslashes($_POST['add_msg']);
	$model		= addslashes($_POST['model']);
	$comp		= addslashes($_POST['comp']);
	$made		= addslashes($_POST['made']);
	$s_qty		= $_POST['dsp2'][0];
	$reserve	= $_POST['dsp3'][0]."|".addslashes($_POST['reserve']);
	$carriage	= $_POST['dsp4'][0]."|".addslashes($_POST['carriage']);
	$price		= $_POST['price'];
	$consumer_price	= $_POST['consumer_price'];
	$price_ment	= addslashes($_POST['price_ment']);
	$unit		= $_POST['unit'];
	$def_qty	= $_POST['def_qty'];
	$qty		= $_POST['qty'];     
	$explan		= addslashes($_POST['explan']);
	$icon_arr	= $_POST['icon_arr'];
	$img_arr	= $_POST['img_arr'];
	$thumUse	= $_POST['thumUse'];
	$tag		= isset($_POST['tag']) ? ",".addslashes($_POST['tag'])."," :'';
	$tmp_gdir	= previlDecode($_POST['tmp_gdir']);	
	$tmp2_dir	= previlDecode($_POST['tmp2_dir']);	

	if($_POST['op_type']=='B') {
		$op_goods_type = "B|{$_POST['type21']}|{$_POST['type22']}|{$_POST['type23']}";
	}
	else $op_goods_type = 'A';

	for($i=0,$cnt=count($icon_arr);$i<$cnt;$i++) {
		$icon .= "|".$icon_arr[$i];
	}

	$op_goods	= isset($_POST['op_goods']) ? addslashes($_POST['op_goods']):'';

	// 디스플래이 정보
	$disp1 = isset($_POST['dsp5'][0]) ? $_POST['dsp5'][0] : "0";
	$disp2 = isset($_POST['dsp6'][0]) ? $_POST['dsp6'][0] : "0";

	$display = "{$disp1}|{$disp2}";

	if($cate_num=='999000000000') {
		$coop_sdate = "{$_POST['sdate']} {$_POST['shour']}:{$_POST['smin']}";
		$coop_edate = "{$_POST['edate']} {$_POST['ehour']}:{$_POST['emin']}";
		$coop_close = $_POST['coop_close'];
		$coop_pay = isset($_POST['dsp8'][0]) ? $_POST['dsp8'][0] : "N";
	}
	else $coop_sdate = $coop_edate = $coop_close = '';
}

switch ($mode) {
	case "excel" :
		if(!eregi("none",$_FILES["excels"]['tmp_name']) && $_FILES["excels"]['tmp_name']) {

			function checkString($str){
				$str = str_replace("|#|",",",$str);
				$str = str_replace('""','|#|',$str);
				$str = str_replace('"','',$str);
				$str = str_replace('|#|','"',$str);
				$str = iconv("euc-kr","utf-8",$str);
				return addslashes(trim($str));
			}

			$ext = getExtension($_FILES["excels"]['name']);
			if($ext!="csv") {			
				alert("엑셀 파일이 아닙니다.","back");
			}

			if($_POST['martching']==1) {
				$sql = "SELECT martching,cate FROM mall_cate WHERE cate_sub='0' ORDER BY cate ASC";
				$mysql->query($sql);
				$cate_martching = array();
				while($row = $mysql->fetch_array()){
					$cate_martching[$row['martching']] = $row['cate'];
				}
			}

			$tmps = readFiles($_FILES["excels"]['tmp_name']);
			$tmps = explode("\"",$tmps);
			for($i=1,$cnt=count($tmps);$i<$cnt;$i+=2) {
				$tmps[$i] = str_replace(",","|#|",$tmps[$i]);
			}
			$tmps = join("\"",$tmps);
			
			$start_uid = '';
			$cnt_rt = 0;
			$xx = 0;
			
			$tmps = explode("|itsmall|,",$tmps);
			for($j=1,$cntj=count($tmps);$j<$cntj;$j++) {
				$data = explode(",",$tmps[$j]);
				
				if($data[1] && $data[3]) {
					$simage1 = $simage2 = $simage3 = $simage4 = $simage5 = '';

					$cate_num	= checkString($data[0]);
					$cate_num   = str_replace("/","",$cate_num);
					$cate_num	= floatval($cate_num);
					if($_POST['martching']==1) $cate_num = $cate_martching[$cate_num];
					$name		= checkString($data[1]);	
					$search_name= str_replace(" ","",$name);
					$consumer_price	= checkString($data[2]);
					$price		= checkString($data[3]);
					$comp		= checkString($data[4]);
					$made		= checkString($data[5]);
					$brand		= checkString($data[6]);
					$model		= checkString($data[7]);
					$reserve	= checkString($data[8]);
					if($reserve=='A') $reserve	= "1|";
					else if($reserve=='B') $reserve	= "2|";
					else $reserve	= "3|{$reserve}";
					$s_qty		= checkString($data[9]);
					switch($s_qty) {
						case "A" : $s_qty = 1; break;
						case "B" : $s_qty = 2; break;
						case "C" : $s_qty = 3; break;
						default : 
							$qty = $s_qty;
							$s_qty = 4;
						break;
					}
						
					$image1		= checkString($data[10]);	
					$thumUse	= checkString($data[15]);
					$oimage		= checkString($data[16]);
					$explan		= checkString($data[17]);	
					$option		= checkString($data[18]);	
						
					// 번호 지정
					$sql = "SELECT number FROM mall_goods WHERE cate = '{$cate_num}' ORDER BY number DESC LIMIT 1";
					$number = $mysql->get_one($sql);
					if($number) $number++;
					else $number = "1000";
						
					$sql = "SELECT MAX(uid) FROM mall_goods";
					$maxUid = $mysql->get_one($sql);
					if($maxUid>9999) $tmp_uid = floor($maxUid/10000);	
					else $tmp_uid = '';		
					
					if($tmp_uid) {
						$tmp_uid .= "/";
						$up_dir .= $tmp_uid;
						if(!is_dir($up_dir)) mkdir($up_dir,0707);	
						$up_path .= $tmp_uid;
						if(!is_dir($up_path)) mkdir($up_path,0707);	
						for($i=1;$i<6;$i++) {
							if(!is_dir($img_path."{$i}/{$tmp_uid}")) mkdir($img_path."{$i}/{$tmp_uid}",0707);
						}
					}
					
					if(!$start_uid) $start_uid = $maxUid + 1;
						
					if($image1) {
						if(substr($image1,0,7)!="http://") $image1 = "http://".$image1;
						$orig_img = getURLimg($image1);							
							if(!eregi('not found',$orig_img)) {
							$img_size = $IMG_DEFINE['img1'];
							$save_ext = getExtension($image1);
							$simage1 = "1/{$tmp_uid}{$cate_num}{$number}.{$save_ext}";
							writeFile($img_path.$simage1,$orig_img);							
						}
					}
					
					if($thumUse=='N') {
						for($i=2;$i<=$mcnt;$i++) {
							$tmp_image	= checkString($data[(9+$i)]);
							$img_size = $IMG_DEFINE['img'.$i];
							if($tmp_image) {
								if(substr($tmp_image,0,7)!="http://") $tmp_image = "http://".$tmp_image;
								$orig_img = getURLimg($tmp_image);			
								if(!eregi('not found',$orig_img)) {
									${"simage".$i} = $i."/{$tmp_uid}{$cate_num}{$number}.{$save_ext}";
									writeFile($img_path.${"simage".$i},$orig_img);							
								}
							}
						}
					}
					else if($simage1) {
						for($i=2;$i<=$mcnt;$i++) {
							$img_size = $IMG_DEFINE['img'.$i];						
							$save_name = "{$cate_num}{$number}.{$save_ext}";
							$thum = createThumbnail($img_path.$simage1,$img_size,'w');								
							@rename($thum, $img_path.$i."/{$tmp_uid}".$save_name);
							${"simage".$i} = $i."/{$tmp_uid}".$save_name;
						}
					}				

					if($oimage) {						
						if(!is_dir($up_path)) mkdir($up_path,0707);	

						$dir_name = $up_path."goods_".$cate_num.$number;
						$other_image = $dir_name;

						if(!is_dir($dir_name)) mkdir($dir_name,0707);	
							
						$tmp_oimage = explode("|",$oimage);
						$cks = 0;
						for($i=0,$cnt=count($tmp_oimage);$i<$cnt;$i++) {
							if(!$tmp_oimage[$i]) continue;
							if(substr($tmp_oimage[$i],0,7)!="http://") $tmp_oimage[$i] = "http://".$tmp_oimage[$i];
							$orig_img = getURLimg($tmp_oimage[$i]);							
							if(!eregi('not found',$orig_img)) {
								$save_ext = ".".getExtension($tmp_oimage[$i]);
								writeFile($dir_name."/".$cate_num.$number."_".$i.$save_ext,$orig_img);							
								$thum = createThumbnail($dir_name."/".$cate_num.$number."_".$i.$save_ext,$IMG_DEFINE['other_ms'],'w');
								rename($thum, $dir_name."/".$cate_num.$number."_".$i."_Pthum1.".$save_ext);
								$thum = createThumbnail($dir_name."/".$cate_num.$number."_".$i.$save_ext,$IMG_DEFINE['other_s'],'w');
								rename($thum, $dir_name."/".$cate_num.$number."_".$i."_Pthum2.".$save_ext);
								$cks = 1;
							}	
						}
						if($cks==0) {
							@RmDir($dir_name);	
							$other_image = '';
						}
					}

					if($brand) {
						$sql = "SELECT uid FROM mall_brand WHERE name='{$brand}'";
						if(!$brand_id = $mysql->get_one($sql)) {								
							$sql = "INSERT INTO mall_brand (uid, name, code_use, signdate) VALUES('','{$brand}','N','{$signdate}')";
							$mysql->query($sql);
							$sql = "SELECT MAX(uid) FROM mall_brand";
							$brand_id = $mysql->get_one($sql);
						}
					}
					else $brand_id = '';

					$sql = "INSERT INTO mall_goods (uid,cate,number,brand,name,search_name,model,comp,made,price,consumer_price,unit,def_qty,s_qty,qty,reserve,carriage,display,op_goods_type,image1,image2,image3,image4,image5,other_image,explan,p_id,signdate) VALUES ('','{$cate_num}','{$number}','{$brand_id}','{$name}','{$search_name}','{$model}','{$comp}','{$made}','{$price}','{$consumer_price}','개','1','{$s_qty}','{$qty}','{$reserve}','2|','0|0|0','B|1|3|5','{$simage1}','{$simage2}','{$simage3}','{$simage4}','{$simage5}','{$other_image}','{$explan}','{$my_id}','{$signdate}')";      		
					$mysql->query($sql);

					$sql = "SELECT uid FROM mall_goods WHERE cate='{$cate_num}' && number='{$number}' LIMIT 1";
					$uid = $mysql->get_one($sql);
									
					if($uid) {
						/*  옵션정보 저장하기 */
						if($option) {
							$tmp_option = explode("|",$option);
							for($i=$i2=0,$cnt=count($tmp_option);$i<$cnt;$i++) {
								$tmp_option2 = explode(",",$tmp_option[$i]);

								if($tmp_option2[0] && $tmp_option2[1]) {				
									$i2++;
									$sql = "INSERT INTO mall_goods_option VALUES('','{$uid}','{$tmp_option2[0]}','{$tmp_option2[1]}','{$tmp_option2[2]}','Y','{$tmp_option2[3]}','{$tmp_option2[4]}','{$i2}')";
									$mysql->query($sql);									
								}
							}
						}
						
						$tmp2_dir = $up_dir.date("Ymd_his").getCode(4)."/";
						if(!is_dir($tmp2_dir)) mkdir($tmp2_dir,0707);	
						
						if($_POST['get_img']=='Y') {
							$arrimgurl=array();
							$p=explode("\n", $explan);
							for($u=0;$u<count($p);$u++) {
								while(eregi("[^=\"']*\.(gif|jpg|bmp|png)([\"|\'| |>]){1}", $p[$u], $val)){
									$arrimgurl[substr($val[0],0,-1)]=substr($val[0],0,-1);
									$p[$u]=str_replace(substr($val[0],0,-1),"",$p[$u]);
								}
							}

							while(list($key,$val)=each($arrimgurl)) {
								$file_url = $val;
								if(substr($file_url,0,7)!="http://") continue;
								if(eregi($_SERVER['HTTP_HOST'],$file_url)) continue;																
								$file_url = str_replace(" ","%20",$file_url);
								$filename = substr(strrchr($file_url,"/"),1);
								$orig_img = getURLimg($file_url);
								
								$filename = urldecode($filename);
								if(@is_file("{$tmp2_dir}{$filename}"))   {   
									$savefilename = proc_file_dup($tmp2_dir, $filename); 
								}
								else $savefilename = $filename;		
								
								writeFile($tmp2_dir.$savefilename,$orig_img);			
								$savefilename = urlencode($savefilename);
								$explan = str_replace($val,$tmp2_dir.$savefilename,$explan);
								$cks = 1;
							}
						}

						if($cks!=1) @RmDir($tmp2_dir);						
						else {
							$dir_name = $up_dir."goods_".$uid;
							rename($tmp2_dir,$dir_name);				
							$tmp2_dir = str_replace($up_dir,"",$tmp2_dir);
							$dir_name = str_replace($up_dir,"",$dir_name);
							$explan = str_replace($tmp2_dir,$dir_name."/",$explan);
							$sql = "UPDATE mall_goods SET explan = '{$explan}' WHERE uid='{$uid}'";
							$mysql->query($sql);
						}	
					}

					$cnt_rt++;
				}
				
			}

			$msg = "{$cnt_rt}건의 상품을 일괄 등록했습니다!";		

			//$sql = "INSERT INTO mall_admin_work_log VALUES ('','{$my_id}','{$start_uid} ~ {$uid} 상품 {$cnt_rt}건 일괄등록','{$_SERVER['REMOTE_ADDR']}','".time()."')";
			//$mysql->query($sql);

			alert($msg,"goods_adds.html");
		}
		else {
			alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		}
	break;

	case "modAll"	: //상품 일괄 수정
		$item = $_POST['item'];				
		if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		$start_uid = '';
		for($i=$j=0,$cnt=count($item);$i<$cnt;$i++) {
			$uid = $item[$i];
			if(!$uid) continue;
			if(!$start_uid) $start_uid = $uid;
			
			$name			= addslashes($_POST['name_'.$uid]);
			$search_name	= str_replace(" ","",$name);
			$brand			= addslashes($_POST['brand_'.$uid]);
			$price			= addslashes($_POST['price_'.$uid]);
			$consumer_price = addslashes($_POST['consumer_price_'.$uid]);
			$s_qty			= $_POST['dsp1_'.$uid][0];
			$qty			= addslashes($_POST['qty_'.$uid]);     
			$reserve		= $_POST['dsp2_'.$uid][0]."|".addslashes($_POST['reserve_'.$uid]);

			if($brand) {
				$sql = "SELECT uid FROM mall_brand WHERE name = '{$brand}' LIMIT 1";
				if(!$brand_id = $mysql->get_one($sql)) {
					$sql = "INSERT INTO mall_brand (name,signdate) VALUES('{$brand}','{$signdate}')";
					$mysql->query2($sql);
						
					$sql = "SELECT MAX(uid) FROM mall_brand";
					$brand_id = $mysql->get_one($sql);
				}
			}
			else $brand_id = '';

			$sql = "UPDATE mall_goods SET  brand='{$brand_id}', name='{$name}', search_name='{$search_name}', price='{$price}', consumer_price='{$consumer_price}', s_qty='{$s_qty}',  qty='{$qty}', reserve='{$reserve}', moddate='{$signdate}' WHERE uid='{$uid}'";
			$mysql->query2($sql);
			$j++;

			############################ 상품 옵션 #################################
			if($_POST['op_uid_'.$uid]) {
				$op_uid = $_POST['op_uid_'.$uid];
				for($i2=0,$cnt2=count($op_uid);$i2<$cnt2;$i2++) {
					if($op_uid[$i2]) {
						$opType1 = $_POST['opType1_'.$op_uid[$i2]];
						$opType2 = $_POST['opType2_'.$op_uid[$i2]];
						$opPrice = $_POST['opPrice_'.$op_uid[$i2]];
						$opQty = $_POST['opQty_'.$op_uid[$i2]];
						
						$sql = "UPDATE mall_goods_option SET option1='{$opType1}', option2='{$opType2}', price='{$opPrice}', qty='{$opQty}' WHERE uid='{$op_uid[$i2]}'";
						$mysql->query2($sql);
					}
				}
			}
			############################ 상품 옵션 #################################
		} 		
		alert($j.'건의 상품을 일괄 수정 처리를 했습니다!',"goods_modify_list.php?".$addstring);
	break;

	case "change" : //일괄수정
		$mode2 = $_POST['mode2'];
		switch($mode2) {
			case "1" : 
				$type = $_POST['type1'];
				$price = $_POST['price'];
				
				if($price>0) {
					if($type!="-") $type = "+";
					$sql = "UPDATE mall_goods SET price = price {$type} {$price} WHERE price>0 && SUBSTRING(cate,1,3)!='999'";
					$mysql->query($sql);
				}
			break;
			case "2" : 
				$type = $_POST['type2'];
				$name = addslashes($_POST['name']);
				
				if($name) {
					if($type=='-') $where = "name = concat('{$name}',name)";
					else $where = "name = concat(name,'{$name}')";
					$sql = "UPDATE mall_goods SET {$where} WHERE SUBSTRING(cate,1,3)!='999'";
					$mysql->query($sql);
				}
			break;
			case "3" : 
				$name1 = addslashes($_POST['name1']);
				$name2 = addslashes($_POST['name2']);
				
				if($name1) {
					$sql = "UPDATE mall_goods SET name = replace(name,'{$name1}','{$name2}') WHERE SUBSTRING(cate,1,3)!='999'";
					echo $sql;
					$mysql->query($sql);
				}
			break;
			case "4" :
				$auto = $_POST['auto'];
				
				$sql = "SELECT cate, number, image1, image2, image3, image4, image5 FROM mall_goods ORDER BY uid ASC";
				$mysql->query($sql);

				while($row = $mysql->fetch_array()){
					
					################ 파일 업로드 ########################
					for($i=1;$i<=$mcnt;$i++) {
						$img_size = $_POST['image'.$i];						
						if($img_size) {
							if($auto=='N' && $i>1) $save_name = $row['image'.$i];
							else $save_name = $row['image1'];

							$thum = createThumbnail($img_path.$save_name,$img_size,'w');	
							@rename($thum, $img_path.$row['image'.$i]);
						}
					}					
				}
			break;
		}
		alert("일괄변경 되었습니다","goods_modify_list.php");
	break;

	case "sdel"	: //선택 상품삭제
		$item = $_POST['item'];		
		if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			goodsDel($item[$i]);
		} 

		alert($i.'건의 상품을 삭제처리를 했습니다!',$url);
	break;

	case "cgVls"	: //수량/경중률변경		
		$qty		= $_GET['qty'];
		$sequence	= $_GET['sequence'];
		
		if(!$sequence || !$uid)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		
		if(strlen($qty)>0) {
			$sql = "UPDATE mall_goods SET qty='{$qty}', sequence='{$sequence}' WHERE s_qty='4' && uid='{$uid}'";		
		}
		else {
			$sql = "UPDATE mall_goods SET sequence='{$sequence}' WHERE uid='{$uid}'";
		}
		$mysql->query($sql);

		movePage("goods_list.php?{$addstring}");
		
	break;

	case "del"	: //상품삭제		
		if(!$uid)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		goodsDel($uid);
		alert('해당 상품을 삭제처리를 했습니다!',$url);
	break;

	case "scopy"	: //선택 상품복사
		$item		= $_POST['item'];		
		if(!$item || !$cate_num)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			goodsCopy($item[$i], $cate_num);	      
		} 

		alert($i.'건의 상품을 복사처리를 했습니다!',$url);
	break;

	case "scate"	: //선택 상품분류변경
		$item		= $_POST['item'];		
		if(!$item || !$cate_num)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			goodsCate($item[$i], $cate_num);	      
		} 

		alert($i.'건의 상품 분류 변경처리를 했습니다!',$url);
	break;

	case "copy" : //상품복사
		if(!$cate_num || !$uid)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다1.','back');            
		     
		goodsCopy($uid, $cate_num);	      
		if($_GET['coop']==1) alert("상품을 복사했습니다!",$url);
		else alert("상품을 복사했습니다!","close4");
	break;

	case "cmodify" : //분류수정
		if(!$cate_num || !$uid)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다1.','back');            
		     
		goodsCate($uid,$cate_num);		

		$msg = "분류를 수정했습니다!"; 		
		alert($msg,"close4");
	break;

	case "modify" :	//상품수정	     
        if(!$uid)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
            			 
		$sql = "SELECT * FROM mall_goods WHERE uid='{$uid}'";
		$row = $mysql->one_row($sql);						
			
		################ 파일 업로드 ########################
		for($i=1;$i<=$mcnt;$i++) {
			$img_size = $IMG_DEFINE['img'.$i];
			$save_name = $row['cate'].$row['number'];
			if(!eregi("none",$_FILES["image".$i]['tmp_name']) && $_FILES["image".$i]['tmp_name']) {
				$up_file = upFile($_FILES["image".$i]['tmp_name'],$_FILES["image".$i]['name'],$img_path.$i."/{$tmp_uid}",'','true',$save_name);
				${"m_image".$i} = ", image{$i} = '{$i}/{$tmp_uid}{$up_file}'";
				if($i==1) $image1 = $up_file;
			}
			else if($thumUse=='Y' && $image1) {			
				$save_ext = getExtension($image1);
				$save_name = $save_name.".".$save_ext;
				$thum = createThumbnail($img_path."1/{$tmp_uid}".$image1,$img_size,'w');								
				@rename($thum, $img_path.$i."/{$tmp_uid}".$save_name);
				${"m_image".$i} = ", image{$i} = '{$i}/{$tmp_uid}{$save_name}'";
			}	
		}		

		######################## 임시 저장 파일 이동 ##################		
		$handle	= @opendir($tmp_gdir);	
		$tmps = array();
		while ($file = @readdir($handle)) {
			if($file != '.' && $file != '..') $tmps[] = $file;			
		}
		@closedir($handle);	

		if(count($tmps)==0) @RmDir($tmp_gdir);						
		else {
			if(!eregi("goods_",$tmp_gdir)) {
				$dir_name = $up_path."goods_".$row['cate'].$row['number'];
				@rename($tmp_gdir,$dir_name);				
				SetCookie("tmp_gdir","",-999,"/"); 
				$other_image = ", other_image = '{$dir_name}' ";
			} else $other_image = ", other_image = '{$tmp_gdir}' ";
		}

		if(is_dir($up_path."goods_".$row['cate'].$row['number'])) {
			$cks = 0;
			$handle	= @opendir($up_path."goods_".$row['cate'].$row['number']);	
			while ($file = @readdir($handle)) {
				if($file != '.' && $file != '..') {
					$cks = 1;
					break;
				}
			}
			@closedir($handle);	
			if($cks!=1) @RmDir($up_path."goods_".$row['cate'].$row['number']);						
		}
		
		$dir_name = "";
		$cks = 0;
		$handle	= @opendir($tmp2_dir);	
		while ($file = @readdir($handle)) {
			if($file != '.' && $file != '..') {
				$cks = 1;
				break;
			}
		}
		@closedir($handle);	

		if($_POST['get_img']=='Y') {
			$arrimgurl=array();
			$p=explode("\n", $explan);
			for($u=0;$u<count($p);$u++) {
				while(eregi("[^=\"']*\.(gif|jpg|bmp|png)([\"|\'| |>]){1}", $p[$u], $val)){
					$arrimgurl[substr($val[0],0,-1)]=substr($val[0],0,-1);
					$p[$u]=str_replace(substr($val[0],0,-1),"",$p[$u]);
				}
			}

			while(list($key,$val)=each($arrimgurl)) {
				$file_url = $val;
				if(substr($file_url,0,7)!="http://") continue;
				if(eregi($_SERVER['HTTP_HOST'],$file_url)) continue;									
				$file_url = str_replace(" ","%20",$file_url);
				$filename = substr(strrchr($file_url,"/"),1);
				$orig_img = getURLimg($file_url);
				
				$filename = urldecode($filename);
				if(@is_file("{$tmp2_dir}{$filename}"))   {   
					$savefilename = proc_file_dup($tmp2_dir, $filename); 
				}
				else $savefilename = $filename;		
				
				writeFile($tmp2_dir.$savefilename,$orig_img);			
				$savefilename = urlencode($savefilename);
				$explan = str_replace($val,$tmp2_dir.$savefilename,$explan);
				$ck = 1;
			}
		}

		if($cks!=1) @RmDir($tmp2_dir);						
		else {
			if(!eregi("goods_",$tmp2_dir)) {
				$dir_name = $up_dir."goods_".$uid;
				@rename($tmp2_dir,$dir_name);				
				SetCookie("tmp2_dir","",-999,"/"); 		
				
				$tmp2_dir = str_replace($up_dir,"",$tmp2_dir);
				$dir_name = str_replace($up_dir,"",$dir_name);
				$explan = str_replace($tmp2_dir,$dir_name."/",$explan);
			}
		}	

		$val_add1 = $val_add2 = $val_add3 = '';
		
		$tmps = explode("|",$row['display']);

		if($disp1!="0") {			
			if($tmps[0]!=$disp1) {			
				$sql = "UPDATE mall_goods SET o_num1 = o_num1 + 1 WHERE SUBSTRING(display,1,1)='{$disp1}'";
				$mysql->query($sql);
				$val_add1 = ", o_num1 = '1'";
			}
		}
		else {						
			if($tmps[0]) {
				$sql = "UPDATE mall_goods SET o_num1 = o_num1 - 1 WHERE o_num1!=0 && o_num1>{$row['o_num1']} && SUBSTRING(display,1,1)='{$tmps[0]}'";
				$mysql->query($sql);
				$val_add1 = ", o_num1 = ''";
			}			
		}

		if($disp2!="0") {								
			if($tmps[1]!=$disp2) {			
				$cate1 = substr($row['cate'],0,3);
				$cate2 = substr($row['cate'],0,6);	

				$val_add2 = ", o_num2 = '1'";
				$val_add3 = ", o_num3 = '1'";

				$sql = "UPDATE mall_goods SET o_num2 = o_num2 + 1 WHERE SUBSTRING(display,3,1)='{$disp2}' && SUBSTRING(cate,1,3)='{$cate1}'";
				$mysql->query($sql);

				$sql = "UPDATE mall_goods SET o_num3 = o_num3 + 1 WHERE SUBSTRING(display,3,1)='{$disp2}' && SUBSTRING(cate,1,6)='{$cate2}'";
				$mysql->query($sql);
			}
		}
		else {
			$sql = "UPDATE mall_goods SET o_num2 = o_num2 - 1 WHERE o_num2!=0 && o_num2>{$row['o_num2']} && SUBSTRING(display,3,1)='{$tmps[1]}'";
			$mysql->query($sql);		
			$sql = "UPDATE mall_goods SET o_num3 = o_num3 - 1 WHERE o_num3!=0 && o_num3>{$row['o_num3']} && SUBSTRING(display,3,1)='{$tmps[1]}'";
			$mysql->query($sql);
		
			$val_add2 = ", o_num2 = ''";
			$val_add3 = ", o_num3 = ''";
		}

		$sql = "UPDATE mall_goods SET  brand='{$brand}', mcate='{$mcate}', special='{$special}', name='{$name}', search_name='{$search_name}', add_msg='{$add_msg}', model='{$model}',comp='{$comp}', made = '{$made}', price='{$price}', consumer_price='{$consumer_price}', price_ment='{$price_ment}', unit='{$unit}', def_qty='{$def_qty}', s_qty='{$s_qty}',  qty='{$qty}', reserve='{$reserve}', carriage = '{$carriage}', op_goods_type='{$op_goods_type}', op_goods='{$op_goods}', display='{$display}', icon='{$icon}', explan='{$explan}', tag='{$tag}', moddate='{$signdate}', sequence='{$sequence}', coop_sdate='{$coop_sdate}', coop_edate='{$coop_edate}', coop_close='{$coop_close}', coop_pay='{$coop_pay}' {$m_image1} {$m_image2} {$m_image3} {$m_image4} {$m_image5} {$other_image} {$val_add1} {$val_add2} {$val_add3} WHERE uid='{$uid}'";
		$mysql->query($sql);
		
		######################## 필수정보 저장하기 ##################
		$opName1	= $_POST['opName1'];
		if($opName1=='Array') $opName1 = "";
		$opContent1	= $_POST['opContent1'];
		if($opContent1=='Array') $opContent1 = "";
		$opName2	= $_POST['opName2'];
		if($opName2=='Array') $opName2 = "";
		$opContent2	= $_POST['opContent2'];
		if($opContent2=='Array') $opContent2 = "";
		$opUid2		= $_POST['opUid2'];		
		if($opUid2=='Array') $opUid2 = ''; 		
		
		$sql = "SELECT uid FROM mall_goods_info WHERE guid='{$uid}'";
		$mysql->query($sql);
		while($row=$mysql->fetch_Array()){
			if(!@in_array($row['uid'],$opUid2)) {
				$sql = "DELETE FROM mall_goods_info WHERE uid='{$row['uid']}'";
				$mysql->query2($sql);
			}
		}

		for($i=$i2=0,$cnt=count($opName1);$i<$cnt;$i++) {
			if($opName1[$i] && $opContent1[$i]) {				
				$i2++;
				
				if($opUid2[$i]) {
					$sql = "UPDATE mall_goods_info SET name1='{$opName1[$i]}', content1='{$opContent1[$i]}',  name2='{$opName2[$i]}', content2='{$opContent2[$i]}', o_num='{$i2}' WHERE uid='{$opUid2[$i]}'";
				}
				else {
					$sql = "INSERT INTO mall_goods_info VALUES('','{$uid}','{$opName1[$i]}','{$opContent1[$i]}','{$opName2[$i]}','{$opContent2[$i]}','{$i2}')";
				}
				$mysql->query($sql);
			}
		}
		######################## 필수정보 저장하기 ##################

		######################## 옵션정보 저장하기 ##################
		$opType1	= $_POST['opType1'];
		$opType2	= $_POST['opType2'];
		$opPrice	= $_POST['opPrice'];
		$opDisplay	= $_POST['opDisplay'];
		$opQty		= $_POST['opQty'];
		$opCode		= $_POST['opCode'];
		if($opCode=='Array') $opCode = ''; 
		$opUid		= $_POST['opUid'];		
		if($opUid=='Array') $opUid = ''; 		
		
		$sql = "SELECT uid FROM mall_goods_option WHERE guid='{$uid}'";
		$mysql->query($sql);
		while($row=$mysql->fetch_Array()){
			if(!@in_array($row['uid'],$opUid)) {
				$sql = "DELETE FROM mall_goods_option WHERE uid='{$row['uid']}'";
				$mysql->query2($sql);
			}
		}

		for($i=$i2=0,$cnt=count($opType1);$i<$cnt;$i++) {
			if($opType1[$i] && $opType2[$i]) {				
				$i2++;
				
				if($opUid[$i]) {
					$sql = "UPDATE mall_goods_option SET option1='{$opType1[$i]}', option2='{$opType2[$i]}', price='{$opPrice[$i]}', display='{$opDisplay[$i]}', qty='{$opQty[$i]}', code='{$opCode[$i]}', o_num='{$i2}' WHERE uid='{$opUid[$i]}'";
				}
				else {
					$sql = "INSERT INTO mall_goods_option VALUES('','{$uid}','{$opType1[$i]}','{$opType2[$i]}','{$opPrice[$i]}','{$opDisplay[$i]}','{$opQty[$i]}','{$opCode[$i]}','{$i2}')";
				}
				$mysql->query($sql);
			}
		}
		######################## 옵션정보 저장하기 ##################

		if(substr($cate_num,0,3)=='999') {
				######################## 신청수량별 판매금액 저장하기 ##################
				$coopQty	= $_POST['coopQty'];
				$coopPrice	= $_POST['coopPrice'];
				$coCode		= $_POST['coopCode'];
				if($coopCode=='Array') $coopCode = ''; 
				$coopUid		= $_POST['coopUid'];		
				if($coopUid=='Array') $coopUid = ''; 	
				
				$sql = "SELECT uid FROM mall_goods_cooper WHERE guid='{$uid}'";
				$mysql->query($sql);
				while($row=$mysql->fetch_Array()){
					if(!@in_array($row['uid'],$coopUid)) {
						$sql = "DELETE FROM mall_goods_cooper WHERE uid='{$row['uid']}'";
						$mysql->query2($sql);
					}
				}

				for($i=$i2=0,$cnt=count($coopQty);$i<$cnt;$i++) {
					if($coopQty[$i]) {				
						$i2++;
						
						if($coopUid[$i]) {
							$sql = "UPDATE mall_goods_cooper SET qty='{$coopQty[$i]}', price='{$coopPrice[$i]}', code='{$coopCode[$i]}', o_num='{$i2}' WHERE uid='{$coopUid[$i]}'";
						}
						else {
							$sql = "INSERT INTO mall_goods_cooper VALUES('','{$uid}','{$coopQty[$i]}','{$coopPrice[$i]}','{$coopCode[$i]}','{$i2}')";
						}
						$mysql->query($sql);
					}
				}
				######################## 신청수량별 판매금액 저장하기 ##################	
			}

        $msg = "상품을 성공적으로 수정했습니다!"; 
		alert($msg,$url);
    break;
  
    default :	//상품등록
		if(!$cate_num || !$name || strlen($price)==0 || !$_FILES["image1"]['tmp_name'] || !$explan)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
        
		// 번호 지정
		$sql = "SELECT number FROM mall_goods WHERE cate = '{$cate_num}' ORDER BY number DESC LIMIT 1";
		$number = $mysql->get_one($sql);
		if($number) $number++;
		else $number = "1000";

		################ 파일 업로드 ########################
		for($i=1;$i<=$mcnt;$i++) {
			$img_size = $IMG_DEFINE['img'.$i];
			$save_name = $cate_num.$number;
			if(!eregi("none",$_FILES["image".$i]['tmp_name']) && $_FILES["image".$i]['tmp_name']) {
				$up_file = upFile($_FILES["image".$i]['tmp_name'],$_FILES["image".$i]['name'],$img_path.$i."/{$tmp_uid}",'','true',$save_name);
				${"image".$i} = $i."/{$tmp_uid}".$up_file;
				$oimage1 = $up_file;
			}
			else if($thumUse=='Y') {			
				$save_ext = getExtension($oimage1);
				$save_name = $save_name.".".$save_ext;
				$thum = createThumbnail($img_path."1/{$tmp_uid}".$oimage1,$img_size,'w');								
				@rename($thum, $img_path.$i."/{$tmp_uid}".$save_name);
				${"image".$i} = $i."/{$tmp_uid}".$save_name;
			}			
		}

		######################## 임시 저장 파일 이동 ##################		
		$handle	= @opendir($tmp_gdir);	
		$tmps = array();
		while ($file = @readdir($handle)) {
			if($file != '.' && $file != '..') $tmps[] = $file;			
		}
		@closedir($handle);	

		if(count($tmps)==0) @RmDir($tmp_gdir);						
		else {
			$dir_name = $up_path."goods_".$cate_num.$number;
			rename($tmp_gdir,$dir_name);				
			SetCookie("tmp_gdir","",-999,"/"); 
			$other_image = $dir_name;
		}	

		$val_add1 = $val_add2 = $val_add3 = '';

		if($disp1!="0") {
			$sql = "UPDATE mall_goods SET o_num1 = o_num1 + 1 WHERE SUBSTRING(display,1,1)='{$disp1}'";
			$mysql->query($sql);
			$val_add1 = 1;
		}
		
		if($disp2!="0") {					
			$cate1 = substr($cate_num,0,3);
			$cate2 = substr($cate_num,0,6);

			$sql = "UPDATE mall_goods SET o_num2 = o_num2 + 1 WHERE SUBSTRING(display,3,1)='{$disp2}' && SUBSTRING(cate,1,3)='{$cate1}'";
			$mysql->query($sql);

			$sql = "UPDATE mall_goods SET o_num3 = o_num3 + 1 WHERE SUBSTRING(display,3,1)='{$disp2}' && SUBSTRING(cate,1,6)='{$cate2}'";
			$mysql->query($sql);
			
			$val_add2 =	$val_add3 = 1;
		}
		
		// 상품정보 데이터베이스에 입력
		$sql = "INSERT INTO mall_goods (uid,cate,mcate,number,brand,special,name,search_name,add_msg,model,comp,made,price,consumer_price,price_ment,unit,def_qty,s_qty,qty,reserve,carriage,op_goods_type,op_goods,display,icon,image1,image2,image3,image4,image5,other_image,explan,tag,o_num1,o_num2,o_num3,sequence,coop_sdate,coop_edate,coop_close,coop_pay,p_id,signdate) 
		VALUES ('','{$cate_num}','{$mcate}','{$number}','{$brand}','{$special}','{$name}','{$search_name}','{$add_msg}','{$model}','{$comp}','{$made}','{$price}','{$consumer_price}','{$price_ment}','{$unit}','{$def_qty}','{$s_qty}','{$qty}','{$reserve}','{$carriage}','{$op_goods_type}','{$op_goods}','{$display}','{$icon}','{$image1}','{$image2}','{$image3}','{$image4}','{$image5}','{$other_image}','{$explan}','{$tag}','{$val_add1}', '{$val_add2}' ,'{$val_add3}','{$sequence}','{$coop_sdate}','{$coop_edate}','{$coop_close}','{$coop_pay}','{$my_id}','{$signdate}')";     
		
		$mysql->query($sql);

		$sql = "SELECT uid FROM mall_goods WHERE cate='{$cate_num}' && number='{$number}' LIMIT 1";
		$uid = $mysql->get_one($sql);
						
		if($uid) {
			
			######################## 필수정보 저장하기 ##################
			$opName1	= $_POST['opName1'];
			if($opName1=='Array') $opName1 = "";
			$opContent1	= $_POST['opContent1'];
			if($opContent1=='Array') $opContent1 = "";
			$opName2	= $_POST['opName2'];
			if($opName2=='Array') $opName2 = "";
			$opContent2	= $_POST['opContent2'];
			if($opContent2=='Array') $opContent2 = "";
			
			for($i=$i2=0,$cnt=count($opName1);$i<$cnt;$i++) {
				if($opName1[$i] && $opContent1[$i]) {				
					$i2++;
					$sql = "INSERT INTO mall_goods_info VALUES('','{$uid}','{$opName1[$i]}','{$opContent1[$i]}','{$opName2[$i]}','{$opContent2[$i]}','{$i2}')";
					$mysql->query($sql);
				}
			}
			######################## 필수정보 저장하기 ##################

			######################## 옵션정보 저장하기 ##################
			$opType1	= $_POST['opType1'];
			$opType2	= $_POST['opType2'];
			$opPrice	= $_POST['opPrice'];
			$opDisplay	= $_POST['opDisplay'];
			$opQty		= $_POST['opQty'];
			$opCode		= $_POST['opCode'];
			if($opCode=='Array') $opCode = ''; 
			
			for($i=$i2=0,$cnt=count($opType1);$i<$cnt;$i++) {
				if($opType1[$i] && $opType2[$i]) {				
					$i2++;
					$sql = "INSERT INTO mall_goods_option VALUES('','{$uid}','{$opType1[$i]}','{$opType2[$i]}','{$opPrice[$i]}','{$opDisplay[$i]}','{$opQty[$i]}','{$opCode[$i]}','{$i2}')";
					$mysql->query($sql);
				}
			}
			######################## 옵션정보 저장하기 ##################			
			
			$cks = 0;
			$handle	= @opendir($tmp2_dir);	
			while ($file = @readdir($handle)) {
				if($file != '.' && $file != '..') {
					$cks = 1;
					break;
				}
			}
			@closedir($handle);	

			if($_POST['get_img']=='Y') {
				$arrimgurl=array();
				$p=explode("\n", $explan);
				for($u=0;$u<count($p);$u++) {
					while(eregi("[^=\"']*\.(gif|jpg|bmp|png)([\"|\'| |>]){1}", $p[$u], $val)){
						$arrimgurl[substr($val[0],0,-1)]=substr($val[0],0,-1);
						$p[$u]=str_replace(substr($val[0],0,-1),"",$p[$u]);
					}
				}

				while(list($key,$val)=each($arrimgurl)) {
					$file_url = $val;				
					if(substr($file_url,0,7)!="http://") continue;
					if(eregi($_SERVER['HTTP_HOST'],$file_url)) continue;																
					$file_url = str_replace(" ","%20",$file_url);
					$filename = substr(strrchr($file_url,"/"),1);
					$orig_img = getURLimg($file_url);
					
					$filename = urldecode($filename);
					if(@is_file("{$tmp2_dir}{$filename}"))   {   
						$savefilename = proc_file_dup($tmp2_dir, $filename); 
					}
					else $savefilename = $filename;		
					
					writeFile($tmp2_dir.$savefilename,$orig_img);			
					$savefilename = urlencode($savefilename);
					$explan = str_replace($val,$tmp2_dir.$savefilename,$explan);
					$cks = 1;
				}
			}

			if($cks!=1) @RmDir($tmp2_dir);						
			else {
				$dir_name = $up_dir."goods_".$uid;
				rename($tmp2_dir,$dir_name);				
				SetCookie("tmp2_dir","",-999,"/"); 		
				$tmp2_dir = str_replace($up_dir,"",$tmp2_dir);
				$dir_name = str_replace($up_dir,"",$dir_name);
				$explan = str_replace($tmp2_dir,$dir_name."/",$explan);
				$sql = "UPDATE mall_goods SET explan = '{$explan}' WHERE uid='{$uid}'";
				$mysql->query($sql);
			}	

			if(substr($cate_num,0,3)=='999') {
				######################## 신청수량별 판매금액 저장하기 ##################
				$coopQty	= $_POST['coopQty'];
				$coopPrice	= $_POST['coopPrice'];
				$coCode		= $_POST['coopCode'];
				if($coopCode=='Array') $coopCode = ''; 
				
				for($i=$i2=0,$cnt=count($coopQty);$i<$cnt;$i++) {
					if($coopQty[$i]) {				
						$i2++;
						$sql = "INSERT INTO mall_goods_cooper VALUES('','{$uid}','{$coopQty[$i]}','{$coopPrice[$i]}','{$coopCode[$i]}','{$i2}')";
						$mysql->query($sql);
					}
				}
				######################## 신청수량별 판매금액 저장하기 ##################	
			}
		}
		 
        $msg = "상품을 성공적으로 등록했습니다!";		
		alert($msg,$url);
	break;	
}

$mysql->query($sql);
alert($msg,$url);

?>