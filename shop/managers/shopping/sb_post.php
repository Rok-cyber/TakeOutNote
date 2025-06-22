<?
######################## lib include
include "../ad_init.php";

$field		= $_GET['field'];
$word		= $_GET['word'];
$page		= $_GET['page'];
$order		= $_GET['order'];
$limit		= $_GET['limit'];
$seccate	= $_GET['seccate'];
$s_qty		= $_GET['s_qty'];
$brand		= $_GET['brand'];
$special	= $_GET['special'];
$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$uid		= isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];

if($field && $word) $addstring = "&field=$field&word={$word}";
if($seccate) $addstring .= "&seccate={$seccate}";
if($page) $addstring .="&page={$page}";
if($order) $addstring .="&order={$order}";
if($limit) $addstring .="&limit={$limit}";
if($s_qty) $addstring .="&s_qty={$s_qty}";
if($brand) $addstring .="&brand={$brand}";
if($special) $addstring .="&special={$special}";

switch($mode) {
	case "disp" :
		$item = $_POST['item'];		
		$sec = $_GET['sec'];
		$sec2 = $_GET['sec2'];

		if($item) {
			for($i=0,$cnt=count($item);$i<$cnt;$i++) {
				$sql = "SELECT display, o_num1, o_num2, o_num3, cate FROM mall_goods WHERE uid='{$item[$i]}'";
				$row = $mysql->one_row($sql);

				$display = $row['display'];
				
				$tmps = explode("|",$display);
				if(!$sec2) {
					if($tmps[0]) {
						$sql = "UPDATE mall_goods SET o_num1 = o_num1 - 1 WHERE o_num1!=0 && o_num1>{$row['o_num1']} && SUBSTRING(display,1,1)='{$tmps[0]}'";
						$mysql->query($sql);
					}
					$sql = "UPDATE mall_goods SET o_num1 = o_num1 + 1 WHERE SUBSTRING(display,1,1)='{$sec}'";
					$mysql->query($sql);
					$sql = "UPDATE mall_goods SET display='{$sec}|{$tmps[1]}', o_num1 ='1' WHERE uid='{$item[$i]}'";
				}
				else {
					$cate1 = substr($row['cate'],0,3);
					$cate2 = substr($row['cate'],0,6);

					if($tmps[1]) {
						$sql = "UPDATE mall_goods SET o_num2 = o_num2 - 1 WHERE o_num2!=0 && o_num2>{$row['o_num2']} && SUBSTRING(display,3,1)='{$tmps[1]}' && SUBSTRING(cate,1,3)='{$cate1}'";
						$mysql->query($sql);
						
						$sql = "UPDATE mall_goods SET o_num3 = o_num3 - 1 WHERE o_num3!=0 && o_num3>{$row['o_num3']} && SUBSTRING(display,3,1)='{$tmps[1]}' && SUBSTRING(cate,1,6)='{$cate2}'";
						$mysql->query($sql);	
					}
					$sql = "UPDATE mall_goods SET o_num2 = o_num2 + 1 WHERE SUBSTRING(display,3,1)='{$sec}' && SUBSTRING(cate,1,3)='{$cate1}'";
					$mysql->query($sql);

					$sql = "UPDATE mall_goods SET o_num3 = o_num3 + 1 WHERE SUBSTRING(display,3,1)='{$sec}' && SUBSTRING(cate,1,6)='{$cate2}'";
					$mysql->query($sql);

					$sql = "UPDATE mall_goods SET display='{$tmps[0]}|{$sec}', o_num2 ='1', o_num3 ='1' WHERE uid='{$item[$i]}'";						
				}
				$mysql->query($sql);				
			}
			$addstring = "&seccate={$sec2}";
		}
		else {
			if(!$uid) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
			$sql = "SELECT display, o_num1, o_num2, o_num3, cate FROM mall_goods WHERE uid='{$uid}'";
			$row = $mysql->one_row($sql);

			$display = $row['display'];
			
			$tmps = explode("|",$display);
			if(!$sec2) {
				if($tmps[0]) {
					$sql = "UPDATE mall_goods SET o_num1 = o_num1 - 1 WHERE o_num1!=0 && o_num1>{$row['o_num1']} && SUBSTRING(display,1,1)='{$tmps[0]}'";
					$mysql->query($sql);
				}
				$sql = "UPDATE mall_goods SET o_num1 = o_num1 + 1 WHERE SUBSTRING(display,1,1)='{$sec}'";
				$mysql->query($sql);
				$sql = "UPDATE mall_goods SET display='{$sec}|{$tmps[1]}', o_num1 ='1' WHERE uid='{$uid}'";
			}
			else {
				$cate1 = substr($row['cate'],0,3);
				$cate2 = substr($row['cate'],0,6);

				if($tmps[1]) {
					$sql = "UPDATE mall_goods SET o_num2 = o_num2 - 1 WHERE o_num2!=0 && o_num2>{$row['o_num2']} && SUBSTRING(display,3,1)='{$tmps[1]}' && SUBSTRING(cate,1,3)='{$cate1}'";
					$mysql->query($sql);
					
					$sql = "UPDATE mall_goods SET o_num3 = o_num3 - 1 WHERE o_num3!=0 && o_num3>{$row['o_num3']} && SUBSTRING(display,3,1)='{$tmps[1]}' && SUBSTRING(cate,1,6)='{$cate2}'";
					$mysql->query($sql);	
				}
				$sql = "UPDATE mall_goods SET o_num2 = o_num2 + 1 WHERE SUBSTRING(display,3,1)='{$sec}' && SUBSTRING(cate,1,3)='{$cate1}'";
				$mysql->query($sql);

				$sql = "UPDATE mall_goods SET o_num3 = o_num3 + 1 WHERE SUBSTRING(display,3,1)='{$sec}' && SUBSTRING(cate,1,6)='{$cate2}'";
				$mysql->query($sql);

				$sql = "UPDATE mall_goods SET display='{$tmps[0]}|{$sec}', o_num2 ='1', o_num3 ='1' WHERE uid='{$uid}'";			
				$addstring = "&seccate={$sec2}";
			}
			$mysql->query($sql);			
		}
	 
		echo "<script>parent.location.href='goods_display.php?disps={$sec}{$addstring}'</script>";
		exit;
	break;

	case "event" :
		$item = $_POST['item'];		
		$sec = $_GET['sec'];
		
		if($item) {
			for($i=0,$cnt=count($item);$i<$cnt;$i++) {
				$sql = "SELECT sgoods FROM mall_event WHERE uid='{$sec}'";
				if($sgoods=$mysql->get_one($sql)) {
					$tmps = explode("|",$sgoods);
					if(!in_array($item[$i],$tmps)) $tmps[] = $item[$i];
					$sgoods = join("|",$tmps);

					$sql = "UPDATE mall_event SET sgoods = '{$sgoods}' WHERE uid='{$sec}'";
					$mysql->query($sql);
				}

				$sql = "UPDATE mall_goods SET event = '{$sec}' WHERE uid='{$item[$i]}'";
				$mysql->query($sql);					
			}
		}
		else {
			if(!$uid) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
			$sql = "SELECT sgoods FROM mall_event WHERE uid='{$sec}'";
			if($sgoods=$mysql->get_one($sql)) {
				$tmps = explode("|",$sgoods);
				if(!in_array($uid,$tmps)) $tmps[] = $item[$i];
				$sgoods = join("|",$tmps);

				$sql = "UPDATE mall_event SET sgoods = '{$sgoods}' WHERE uid='{$sec}'";
				$mysql->query($sql);
			}
			$sql = "UPDATE mall_goods SET event = '{$sec}' WHERE uid='{$uid}'";
			$mysql->query($sql);			
		}
	 
		echo "<script>parent.location.href='goods_event.php?event={$sec}'</script>";
		exit;
	break;

	case "brand" :
		$item = $_POST['item'];		
		$sec = $_GET['sec'];
		
		if($item) {
			for($i=0,$cnt=count($item);$i<$cnt;$i++) {
				$sql = "UPDATE mall_goods SET brand = '{$sec}' WHERE uid='{$item[$i]}'";
				$mysql->query($sql);			
			}
		}
		else {
			if(!$uid) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
			$sql = "UPDATE mall_goods SET brand = '{$sec}' WHERE uid='{$uid}'";
			$mysql->query($sql);			
		}
	 
		echo "<script>parent.location.href='goods_brand.php?brand={$sec}'</script>";
		exit;
	break;

	case "special" :
		$item = $_POST['item'];		
		$sec = $_GET['sec'];
		
		if($item) {
			for($i=0,$cnt=count($item);$i<$cnt;$i++) {
				$sql = "SELECT special FROM mall_goods WHERE uid='{$item[$i]}'";
				$ori_special = $mysql->get_one($sql);

				if($ori_special) $ori_special = ",".substr($ori_special,1,-1).",{$sec},";
				else $ori_special = ",{$sec},";
								
				$sql = "UPDATE mall_goods SET special = '{$ori_special}' WHERE uid='{$item[$i]}'";
				$mysql->query($sql);
			}
		}
		else {
			if(!$uid) alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
			
			$sql = "SELECT special FROM mall_goods WHERE uid='{$uid}'";
			$ori_special = $mysql->get_one($sql);

			if($ori_special) $ori_special = ",".substr($ori_special,1,-1).",{$sec},";
			else $ori_special = ",{$sec},";
		
			$sql = "UPDATE mall_goods SET special = '{$ori_special}' WHERE uid='{$uid}'";
			$mysql->query($sql);
		}
	 
		echo "<script>parent.location.href='goods_special.php?special={$sec}'</script>";
		exit;
	break;

	case "bdel" :
		if(!$uid) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');		
		$sql = "UPDATE mall_goods SET brand = '' WHERE uid='{$uid}'";
		$mysql->query($sql);
		
		movePage("goods_brand.php?{$addstring}");
	break;

	case "bdel2" :
		$item = $_POST['item'];		
		if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			$sql = "UPDATE mall_goods SET brand = '' WHERE uid='{$item[$i]}'";
			$mysql->query($sql);			
		} 
		movePage("goods_brand.php?{$addstring}");
	break;

	case "sdel" :
		if(!$uid || !$special) alert('자료가 넘어오지 못했습니다. 다시 시도하시기 바랍니다!','back');		
		$sql = "SELECT special FROM mall_goods WHERE uid='{$uid}'";
		$ori_special = $mysql->get_one($sql);

		$ori_special = str_replace(",{$special},",",",$ori_special);
	
		$sql = "UPDATE mall_goods SET special = '{$ori_special}' WHERE uid='{$uid}'";
		$mysql->query($sql);
		
		movePage("goods_special.php?{$addstring}");
	break;

	case "sdel2" :
		$item = $_POST['item'];		
		if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			$sql = "SELECT special FROM mall_goods WHERE uid='{$item[$i]}'";
			$ori_special = $mysql->get_one($sql);

			$ori_special = str_replace(",{$special},",",",$ori_special);
		
			$sql = "UPDATE mall_goods SET special = '{$ori_special}' WHERE uid='{$item[$i]}'";
			$mysql->query($sql);
		} 
		movePage("goods_special.php?{$addstring}");
	break;

	case "edel" :		
		if(!$uid)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');
		$sql = "SELECT event FROM mall_goods WHERE uid='{$uid}'";
		$event = $mysql->get_one($sql);

		$sql = "SELECT sgoods FROM mall_event WHERE uid='{$event}'";
		if($sgoods=$mysql->get_one($sql)) {
			$tmps = explode("|",$sgoods);
			for($i=0,$cnt=count($tmps);$i<$cnt;$i++) {
				if($uid==$tmps[$i]) {
					array_splice($tmps,$i,1);	
					break;
				}
			}

			$sgoods = join("|",$tmps);

			$sql = "UPDATE mall_event SET sgoods = '{$sgoods}' WHERE uid='{$event}'";
			$mysql->query($sql);
		}

		$sql = "UPDATE mall_goods SET event = '0' WHERE uid='{$uid}'";
		$mysql->query($sql);
		movePage("goods_event.php?{$addstring}");
	break;

	case "edel2" :
		$item = $_POST['item'];		
		if(!$item)  alert('정보가 제대로 넘어오지 못했습니다. 다시 시도하시기 바랍니다.','back');

		for($i=0,$cnt=count($item);$i<$cnt;$i++) {
			
			$sql = "SELECT event FROM mall_goods WHERE uid='{$item[$i]}'";
			$event = $mysql->get_one($sql);

			$sql = "SELECT sgoods FROM mall_event WHERE uid='{$event}'";
			if($sgoods=$mysql->get_one($sql)) {
				$tmps = explode("|",$sgoods);
				for($j=0,$cnt=count($tmps);$j<$cnt;$j++) {
					if($item[$i]==$tmps[$j]) {
						array_splice($tmps,$j,1);	
						break;
					}
				}

				$sgoods = join("|",$tmps);

				$sql = "UPDATE mall_event SET sgoods = '{$sgoods}' WHERE uid='{$event}'";
				$mysql->query($sql);
			}

			$sql = "UPDATE mall_goods SET event = '0' WHERE uid='{$item[$i]}'";
			$mysql->query($sql);			
		} 
		movePage("goods_event.php?{$addstring}");
	break;

}

?>