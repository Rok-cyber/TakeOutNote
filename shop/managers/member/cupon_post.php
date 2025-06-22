<?
######################## lib include
include "../ad_init.php";

$field		= $_GET['field'];
$word		= $_GET['word'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$sectype	= $_GET['s_qty'];
$status		= $_GET['status'];
$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$uid		= isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];

if($field && $word) $addstring = "&field=$field&word={$word}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($sectype) $addstring .="&sectype={$sectype}";
if($status) $addstring .="&status={$status}";

if($mode=='del') {
	$item = $_POST['item'];		
	if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

	for($i=0,$cnt=count($item);$i<$cnt;$i++) {
		$sql = "DELETE FROM mall_cupon WHERE pid='{$item[$i]}'";
		$mysql->query($sql);			
		$sql = "DELETE FROM mall_cupon_manager WHERE uid='{$item[$i]}'";
		$mysql->query($sql);			
	} 
	movePage("cupon_list.php?{$addstring}");
}

if($mode=='cdel') {
	$item	= $_POST['item'];		
	$pid	= $_GET['pid'];
	if($pid) $addstring .= "&pid={$pid}";

	if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

	for($i=0,$cnt=count($item);$i<$cnt;$i++) {
		$sql = "DELETE FROM mall_cupon WHERE uid='{$item[$i]}'";
		$mysql->query($sql);					
	} 
	movePage("cupon_down_list.php?{$addstring}");
}

$signdate = time();

if($mode=='write' || $mode=='modify') {
	$name = addslashes($_POST['name']);
	$type = addslashes($_POST['type']);
	$sale = addslashes($_POST['sale']);
	$stype = addslashes($_POST['stype']);
	$scate = addslashes($_POST['scate']);
	$sgoods = addslashes($_POST['sgoods']);

	if($type==3) {
		$scate = '';
		if(!$sgoods) alert("상품을 선택하지 않았습니다.","back");
		$sgoods = "|{$sgoods}|";
	}

	if($_POST['dtype']==1) {
		$sdate = addslashes($_POST['s_date']);
		$edate = addslashes($_POST['e_date']);
		$days = 0;
	}
	else {
		$sdate = $edate = 0;
		$days = addslashes($_POST['days']);
	}

	$sqty = addslashes($_POST['sqty']);
	if($sqty==1) $qty = addslashes($_POST['qty']);
	else $qty = 0;

	$lmt = addslashes($_POST['lmt']);
	$odds = addslashes($_POST['odds']);
	$cnts = addslashes($_POST['cnts']);
	
	if($_POST['down_type']==1) $down_type = 1;
	else $down_type = 0;
	if($_POST['use_type']==1) $use_type = 1;
	else $use_type = 0;
	
}

switch($mode) {	
	case "member" :
		$pid = $_POST['pid'];		
		$range = $_POST['range'];

		if(!$pid || !$range) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		$sql = "SELECT uid,sqty,qty FROM mall_cupon_manager WHERE uid='{$pid}'";
		if(!$data = $mysql->one_row($sql)) alert('쿠폰이 삭제 되었거나 없는 쿠폰 입니다.','back');
		
		switch($range) {
			case "1" : 
				$sql = "SELECT id FROM pboard_member WHERE uid>1 && level>1";
				$mysql->query($sql);
				$cnts = 0;
				while($row = $mysql->fetch_array()){
					if($row['id']) {
						$sql = "SELECT count(*) FROM mall_cupon WHERE id='{$row['id']}' && pid='{$pid}' && status='A'";
						if($mysql->get_one($sql)==0) {
							if($data['sqty']=='1') {
								if($data['qty']<1) alert('쿠폰이 모두 발급되어 중단되었습니다. 쿠폰수량을 변경 하시고 다시 처리 하시기 바랍니다.',"cupon_down_list.php?pid={$pid}{$addstring}");
								else {
									$sql = "UPDATE mall_cupon_manager SET qty = qty -1 WHERE uid='{$data['uid']}'";
									$mysql->query2($sql);
									$data['qty']--;
								}		
							}
							
							$sql = "INSERT INTO mall_cupon VALUES ('','{$pid}','','{$row['id']}','A','0','','{$signdate}')";
							$mysql->query2($sql);
							$cnts++;
						}
					}
				}
			break;

			case "2" :
				$slevel = explode("|",$_POST['slevel']);
				$cnts = 0;
				for($i=0,$cnt=count($slevel);$i<$cnt;$i++) {
					if($slevel[$i]>1 && $slevel[$i]<11) {
						$sql = "SELECT id FROM pboard_member WHERE uid>1 && level='{$slevel[$i]}'";
						$mysql->query($sql);
						while($row = $mysql->fetch_array()){
							if($row['id']) {
								$sql = "SELECT count(*) FROM mall_cupon WHERE id='{$row['id']}' && pid='{$pid}' && status='A'";
								if($mysql->get_one($sql)==0) {
									if($data['sqty']=='1') {
										if($data['qty']<1) alert('쿠폰이 모두 발급되어 중단되었습니다. 쿠폰수량을 변경 하시고 다시 처리 하시기 바랍니다.',"cupon_down_list.php?pid={$pid}{$addstring}");
										else {
											$sql = "UPDATE mall_cupon_manager SET qty = qty -1 WHERE uid='{$data['uid']}'";
											$mysql->query2($sql);
											$data['qty']--;
										}		
									}

									$sql = "INSERT INTO mall_cupon VALUES ('','{$pid}','','{$row['id']}','A','0','','{$signdate}')";
									$mysql->query2($sql);
									$cnts++;
								}
							}
						}						
					}
				}
			break;
			
			case "3" :
				$smem = explode("|",$_POST['smem']);
				$cnts = 0;
				for($i=0,$cnt=count($smem);$i<$cnt;$i++) {
					if($smem[$i]) {
						$sql = "SELECT count(*) FROM mall_cupon WHERE id='{$smem[$i]}' && pid='{$pid}' && status='A'";
						if($mysql->get_one($sql)==0) {
							if($data['sqty']=='1') {
								if($data['qty']<1) alert('쿠폰이 모두 발급되어 중단되었습니다. 쿠폰수량을 변경 하시고 다시 처리 하시기 바랍니다.',"cupon_down_list.php?pid={$pid}{$addstring}");
								else {
									$sql = "UPDATE mall_cupon_manager SET qty = qty -1 WHERE uid='{$data['uid']}'";
									$mysql->query2($sql);
									$data['qty']--;
								}		
							}

							$sql = "INSERT INTO mall_cupon VALUES ('','{$pid}','','{$smem[$i]}','A','0','','{$signdate}')";
							$mysql->query2($sql);
							$cnts++;
						}
					}			
				}
			break;
			default : 
				alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
			break;				
		}
		
		alert("기존 발급 후 사용하지 않은 회원을 제외한 {$cnts}명의 회원에게 쿠폰을 발급 하였습니다.","cupon_down_list.php?pid={$pid}");
	break;

	case "write" :				
		if(!$name || !$type || !$sale || !$stype) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		$sql = "INSERT INTO mall_cupon_manager VALUES('','{$name}','{$type}','{$sale}','{$stype}','{$scate}','{$sgoods}','{$sqty}','{$qty}','{$sdate}','{$edate}','{$days}','{$odds}','{$cnts}','{$lmt}','{$down_type}','{$use_type}','{$signdate}')";
		$msg = "쿠폰이 등록되었습니다.";
		
	break;

	case "modify" :
		if(!$uid || !$name || !$type || !$sale || !$stype) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		$sql = "UPDATE mall_cupon_manager SET name='{$name}', type='{$type}', sale='{$sale}', stype='{$stype}', scate='{$scate}', sgoods='{$sgoods}', sqty='{$sqty}', qty='{$qty}', sdate='{$sdate}', edate='{$edate}', days='{$days}', odds='{$odds}', cnts='{$cnts}', lmt='{$lmt}', down_type='{$down_type}', use_type='{$use_type}' WHERE uid='{$uid}'";
		$msg = "쿠폰이 수정되었습니다.";

	break;

	default : alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

}
$mysql->query($sql);
alert($msg,"cupon_list.php?{$addstring}");

?>