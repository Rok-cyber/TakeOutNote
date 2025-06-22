<?
include "sub_init.php";

if(!eregi($_SERVER['HTTP_HOST'],$_SERVER['HTTP_REFERER'])) alert('정상적으로 등록하세요!','back');

$type	= isset($_GET['type']) ? $_GET['type'] : $_POST['type'];
$uid	= isset($_GET['uid']) ? $_GET['uid'] : $_POST['uid'];
$cate	= $_GET['cate'];
$number = $_GET['number'];
$signdate = time();

if(!$my_id){
	if($type=='del') alert("먼저 로그인 하시기 바랍니다.","back");
	else if($type!='cart2') {
		echo "<script>parent.messageBox.show('먼저 로그인 하시기 바랍니다.','280','100','Error');</script>";	
			exit;
	}
}

switch($type) {
	case "wdel" :
		$item = isset($_POST['item'])? $_POST['item']:'';
		if(!$item) {
			echo "<script>parent.messageBox.show('선택한 항목이 없습니다.','280','100','Error');</script>";	
			exit;
		}
		echo "<script>";
		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			$sql = "DELETE FROM mall_wish WHERE uid='{$item[$i]}' && id='{$my_id}'";
			$mysql->query($sql);
			echo "parent.document.getElementById('listWish{$item[$i]}').style.display='none';";
		}

		echo "	if(parent.document.getElementById('quickBarWishBox')) parent.window.location.reload();				
		        parent.messageBox.show('관심상품에서 {$i}개의 상품이 삭제 되었습니다.','280','100');
				f = parent.document.wishForm;
				for (i=cnt=0;i<f.elements.length;i++) {
					if(f.elements[i].name == 'item[]') f.elements[i].checked = false;
				}
			</script>
			";	
		exit;
	break;

	case "cart" :
		$item = isset($_POST['item'])? $_POST['item']:'';
		if(!$item) {
			echo "<script>parent.messageBox.show('선택한 항목이 없습니다.','280','100','Error');</script>";	
			exit;
		}

		// 임시장바구니번호 존재확인
		if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
			if($my_id) $tempid = $my_id;
			else $tempid = md5(uniqid(rand()));
			SetCookie("tempid",$tempid,0,"/");
		} 
		else $tempid = $_COOKIE['tempid'];
		
		echo "<script>";

		for($i=0,$ck=0,$cnt=count($item);$i<$cnt;$i++) {			
			$sql = "SELECT p_cate, p_number FROM mall_wish WHERE uid='{$item[$i]}' && id='{$my_id}'";
			$data = $mysql->one_row($sql);

			$sql = "SELECT price, reserve, s_qty, qty FROM mall_goods WHERE uid='{$data['p_number']}'";
			$row = $mysql->one_row($sql);

			if($row['s_qty']==4 && $row['qty']<1) continue;

			$sql= "SELECT access_level FROM mall_cate WHERE cate='{$data['p_cate']}'";
			$tmps = $mysql->get_one($sql);
			if($tmps && $my_level<9) {
				$access_level = explode("|",$tmps);
				if(($access_level[1]=='!=' && $access_level[0]!=$my_level) || ($access_level[1]=='<' && $access_level[0]>$my_level)) continue;
			}
			
			$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$tempid}' && p_number='{$data['p_number']}' && p_cate='{$data['p_cate']}'";
					
			if($mysql->get_one($sql)==0) {	
		
				echo "parent.gToCart.cartAdd('{$data['p_cate']}','{$data['p_number']}');";
				if($ck==0) echo "if(typeof(parent.rBoxDiv)!='undefined') { if(parent.rBoxDiv.snum==1) parent.rBoxDiv.scroll('2'); }";				
				$ck++;

			}
		}

		if($i!=$ck) $msg = "품절, 이미 등록된 상품, 구매 권한이 없는 상품을 제외한";

		echo "			
				parent.messageBox.show('관심상품에서 {$msg} {$ck}개의 상품이 장바구니에 추가 되었습니다.','280','150');
				f = parent.document.wishForm;
				for (i=cnt=0;i<f.elements.length;i++) {
					if(f.elements[i].name == 'item[]') f.elements[i].checked = false;
				}
			</script>
			";	
		exit;
	break;

	case "cart2" :
		$item = isset($_GET['item'])? $_GET['item']:'';
		if(!$item) {
			echo "<script>parent.messageBox.show('선택한 항목이 없습니다.','280','100','Error');</script>";	
			exit;
		}

		$item = explode("|",$item);

		// 임시장바구니번호 존재확인
		if(!$_COOKIE['tempid'] || $_COOKIE['tempid'] == "NULL") {
			if($my_id) $tempid = $my_id;
			else $tempid = md5(uniqid(rand()));
			SetCookie("tempid",$tempid,0,"/");
		} 
		else $tempid = $_COOKIE['tempid'];
		
		echo "<script>";

		for($i=0,$ck=0,$cnt=(count($item)-1);$i<$cnt;$i++) {			
			
			$sql = "SELECT cate, uid, price, reserve, s_qty, qty FROM mall_goods WHERE uid='{$item[$i]}'";
			$row = $mysql->one_row($sql);

			if($row['s_qty']==4 && $row['qty']<1) continue;

			$sql= "SELECT access_level FROM mall_cate WHERE cate='{$row['cate']}'";
			$tmps = $mysql->get_one($sql);
			if($tmps && $my_level<9) {
				$access_level = explode("|",$tmps);
				if(($access_level[1]=='!=' && $access_level[0]!=$my_level) || ($access_level[1]=='<' && $access_level[0]>$my_level)) continue;
			}
			
			$sql = "SELECT count(*) FROM mall_cart WHERE tempid='{$tempid}' && p_number='{$row['uid']}'";
	
			if($mysql->get_one($sql)==0) {	
		
				echo "parent.gToCart.cartAdd('{$row['cate']}','{$row['uid']}');";
				if($ck==0) echo "if(typeof(parent.rBoxDiv)!='undefined') { if(parent.rBoxDiv.snum==1) parent.rBoxDiv.scroll('2') };";	
				$ck++;

			}
		}

		if($i!=$ck) $msg = "품절, 이미 등록된 상품, 구매 권한이 없는 상품을 제외한";

		echo "			
				parent.messageBox.show('{$msg} {$ck}개의<br /> 상품이 장바구니에 추가 되었습니다.','280','120');
				f = parent.document.listForm;
				for (i=cnt=0;i<f.elements.length;i++) {
					if(f.elements[i].name == 'compare[]') f.elements[i].checked = false;
				}
			</script>
			";	
		exit;
	break;

	case "mod" : 				
		if(!$uid) {
			echo "<script>parent.messageBox.show('자료가 넘어오지 못했습니다.<br /> 다시 시도하시기 바랍니다!','280','120','Error');</script>";	
			exit;
		}
		$memo = addslashes($_POST['memo'.$uid]);
		$sql = "UPDATE mall_wish SET memo = '{$memo}' WHERE uid = '{$uid}' && id='{$my_id}'";
		$mysql->query($sql);
		echo "<script>parent.messageBox.show('메모가 수정 되었습니다.!','280','100');</script>";	
		exit;
	break;
	case "adds" :
		$vls = $_GET['vls'];
	
		if(!$vls) {
			echo "<script>parent.messageBox.show('자료가 넘어오지 못했습니다.<br /> 다시 시도하시기 바랍니다!','280','120','Error');</script>";	
			exit;
		}
		$vls = explode("|",$vls);

		for($i=0,$cnt=count($vls);$i<$cnt;$i++) {
			$sql = "SELECT p_cate, p_number FROM mall_cart WHERE tempid='{$my_id}' && uid='{$vls[$i]}'";
			if($row = $mysql->one_row($sql)) {
				$ck1 = 1;
				$sql = "SELECT count(*) FROM mall_wish WHERE p_cate = '{$row['p_cate']}' && p_number = '{$row['p_number']}' && id='{$my_id}'";
				if($mysql->get_one($sql)==0) {
					$sql = "INSERT INTO mall_wish VALUES ('','{$my_id}','{$row['p_number']}','{$row['p_cate']}','','{$signdate}')";
					$mysql->query($sql);
					$ck2 = 1;
				}
			}
		}

		if($ck1==1 && $ck2==1) {
			echo "<script>parent.messageBox.show('관심상품 목록에 등록 되었습니다.','280','100');</script>";	
		}
		else if($ck1==1) {
			echo "<script>parent.messageBox.show('이미 관심상품 목록에 등록 되어 있습니다!','280','100');</script>";	
		}
	break;

	case "adds2" :
		$item = isset($_GET['item'])? $_GET['item']:'';
		if(!$item) {
			echo "<script>parent.messageBox.show('선택한 항목이 없습니다.','280','100','Error');</script>";	
			exit;
		}

		$item = explode("|",$item);

		for($i=0,$ck=0,$cnt=(count($item)-1);$i<$cnt;$i++) {			
			
			$sql = "SELECT cate, uid FROM mall_goods WHERE uid='{$item[$i]}'";
			$row = $mysql->one_row($sql);

			$sql = "SELECT count(*) FROM mall_wish WHERE p_cate = '{$row['cate']}' && p_number = '{$row['uid']}' && id='{$my_id}'";
			if($mysql->get_one($sql)==0) {
				$sql = "INSERT INTO mall_wish VALUES ('','{$my_id}','{$row['uid']}','{$row['cate']}','','{$signdate}')";
				$mysql->query($sql);
				$ck++;
			}			
		}

		if($i!=$ck) $msg = "이미 등록된 상품을 제외한";

		echo "<script>			
				parent.messageBox.show('{$msg} {$ck}개의<br /> 상품이 관심상품에 추가 되었습니다.','280','120');
				f = parent.document.listForm;
				for (i=cnt=0;i<f.elements.length;i++) {
					if(f.elements[i].name == 'compare[]') f.elements[i].checked = false;
				}
			  </script>
			";	
		exit;
	break;

	case "del": // 삭제모드
	    if(!$uid) {
			echo "<script>parent.messageBox.show('자료가 넘어오지 못했습니다.<br /> 다시 시도하시기 바랍니다!','280','120','Error');</script>";	
			exit;
		}
		$sql = "DELETE FROM mall_wish WHERE uid='{$uid}' && id='{$my_id}'";
		$mysql->query($sql);				
		echo "
			<script>
				if(parent.document.getElementById('quickBarWishBox')) parent.window.location.reload();
				parent.document.getElementById('listWish{$uid}').style.display='none';
				parent.messageBox.show('관심상품에서 삭제 되었습니다.','280','100');
			</script>
			";	
		exit;
	break;
		  
	default : //찜 상품 담기
		if(!$number || !$cate) {
			echo "<script>parent.messageBox.show('자료가 넘어오지 못했습니다.<br /> 다시 시도하시기 바랍니다!','280','150','Error');</script>";	
			exit;
		}	
	    $sql = "SELECT count(*) FROM mall_wish WHERE p_cate = '{$cate}' && p_number = '{$number}' && id='{$my_id}'";
		if($mysql->get_one($sql) >0) {
			echo "<script>parent.messageBox.show('이미 관심상품 목록에 등록 되어 있습니다!','280','100');</script>";	
        } 
		else {
	        $sql = "INSERT INTO mall_wish(uid,id,p_number,p_cate,memo,signdate) VALUES ('','{$my_id}','{$number}','{$cate}','','{$signdate}')";					
            $mysql->query($sql);
			echo "<script>parent.messageBox.show('관심상품 목록에 등록 되었습니다.','280','100');</script>";	
		}            
	break;
}
?>