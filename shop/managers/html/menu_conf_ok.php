<?
######################## lib include
include "../ad_init.php";

$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

switch($mode) {
	case "sche_ins" :
		$subject = addslashes($_POST['subject']);
		$date = addslashes($_POST['date']);
		$sm = addslashes($_POST['sm']);
		
		$sql = "INSERT mall_schedule_date VALUES ('','{$subject}','{$date}','{$sm}')";
		$mysql->query($sql);
		movePage("schedule_conf.php");
	break;

	case "sche_mod" :
		$uid = isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
		if(!$uid) alert("자료가 제대로 넘어오지 못했습니다. 다시 시도 하시기 바랍니다.","back");
		$subject = addslashes($_POST['subject']);
		$date = addslashes($_POST['date']);
		$sm = addslashes($_POST['sm']);
			
		$sql = "UPDATE mall_schedule_date SET date='{$date}', subject='{$subject}', sm='{$sm}' WHERE uid='{$uid}'";
		$mysql->query($sql);
		movePage("schedule_conf.php");
    break;

	case "sche_del" :
		$uid = isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];
		if(!$uid) alert("자료가 제대로 넘어오지 못했습니다. 다시 시도 하시기 바랍니다.","back");
		
		$sql = "DELETE FROM mall_schedule_date WHERE uid='{$uid}'";
		$mysql->query($sql);
		movePage("schedule_conf.php");
	break;
	
	case "member" :
		if(!$_POST['cnts'] || $_POST['cnts']>20 || !is_numeric($_POST['cnts'])) $_POST['cnts'] = 10;

		$code = "{$_POST['info1']}|{$_POST['info2']}|{$_POST['cnts']}";
		$code = addslashes($code);
		$sql = "SELECT count(*) FROM mall_aconf WHERE mode='R'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_aconf values('','','{$code}','R')";
		}
		else {
			$sql = "UPDATE mall_aconf SET code='{$code}' WHERE mode='R'";
		}
		$mysql->query($sql);

		echo "
			<script>
				parent.location.reload();
			</script>
		";

	break;

	case "goods" :
		if(!$_POST['cnts'] || $_POST['cnts']>9 || !is_numeric($_POST['cnts'])) $_POST['cnts'] = 3;

		$code = "{$_POST['info1']}|{$_POST['info2']}|{$_POST['info3']}|{$_POST['info4']}|{$_POST['info5']}|{$_POST['info6']}|{$_POST['info7']}|{$_POST['info8']}|{$_POST['info9']}|{$_POST['cnts']}";
		$code = addslashes($code);
		$sql = "SELECT count(*) FROM mall_aconf WHERE mode='G'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_aconf values('','','{$code}','G')";
		}
		else {
			$sql = "UPDATE mall_aconf SET code='{$code}' WHERE mode='G'";
		}
		$mysql->query($sql);

		echo "
			<script>
				parent.location.reload();
			</script>
		";

	break;

	case "board" :
		if(!$_POST['cnts'] || $_POST['cnts']>10 || !is_numeric($_POST['cnts'])) $_POST['cnts'] = 5;

		$info5 = $_POST['info5'];
		$info5 = join(",",$info5);
		$code = "{$_POST['info1']}|{$_POST['info2']}|{$_POST['info3']}|{$_POST['info4']}|{$info5}|{$_POST['cnts']}";
		$code = addslashes($code);
		$sql = "SELECT count(*) FROM mall_aconf WHERE mode='B'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_aconf values('','','{$code}','B')";
		}
		else {
			$sql = "UPDATE mall_aconf SET code='{$code}' WHERE mode='B'";
		}
		$mysql->query($sql);

		echo "
			<script>
				parent.location.reload();
			</script>
		";

	break;

	case "order" :
		if(!$_POST['cnts'] || $_POST['cnts']>20 || !is_numeric($_POST['cnts'])) $_POST['cnts'] = 5;
		$code = "{$_POST['info1']}|{$_POST['cnts']}";
		$code = addslashes($code);
		$sql = "SELECT count(*) FROM mall_aconf WHERE mode='O'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_aconf values('','','{$code}','O')";
		}
		else {
			$sql = "UPDATE mall_aconf SET code='{$code}' WHERE mode='O'";
		}
		$mysql->query($sql);

		echo "
			<script>
				parent.location.reload();
			</script>
		";

	break;
	
	case "info" :
		$code = "{$_POST['info1']}|{$_POST['info2']}|{$_POST['info3']}|{$_POST['info4']}|{$_POST['info5']}|{$_POST['info6']}|{$_POST['info7']}|{$_POST['host_hd']}|{$_POST['tf_url']}";
		$code = addslashes($code);
		$sql = "SELECT count(*) FROM mall_aconf WHERE mode='I'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_aconf values('','','{$code}','I')";
		}
		else {
			$sql = "UPDATE mall_aconf SET code='{$code}' WHERE mode='I'";
		}
		$mysql->query($sql);

		echo "
			<script>
				parent.location.reload();
			</script>
		";

	break;

	case "admin" :
		$code = "{$_POST['info1']}|{$_POST['info2']}|{$_POST['info3']}|{$_POST['info4']}|{$_POST['info5']}|{$_POST['info6']}|{$_POST['info7']}|{$_POST['info8']}|{$_POST['info9']}|{$_POST['info10']}";
		$code = addslashes($code);
		$sql = "SELECT count(*) FROM mall_aconf WHERE mode='A'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_aconf values('','','{$code}','A')";
		}
		else {
			$sql = "UPDATE mall_aconf SET code='{$code}' WHERE mode='A'";
		}
		$mysql->query($sql);
		
		SetCookie("__xwzSRW_IDS_DIV_FRAME1","",-999,"/"); 
		SetCookie("__xwzSRW_IDS_DIV_FRAME2","",-999,"/"); 
		echo "
			<script>
				parent.location.reload();
			</script>
		";

	break;

	case "total" :
		$infos = "{$_POST['info21']},{$_POST['info22']},{$_POST['info23']},{$_POST['info24']}";
		$code = "{$_POST['info1']}|{$_POST['info2']}|{$_POST['info3']}|{$_POST['info4']}|{$_POST['info5']}|{$_POST['info6']}|{$_POST['info7']}|{$_POST['info8']}|{$_POST['info9']}|{$_POST['info10']}|{$_POST['info11']}|{$_POST['info12']}|{$_POST['info13']}|{$infos}";
		$code = addslashes($code);
		$sql = "SELECT count(*) FROM mall_aconf WHERE mode='T'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_aconf values('','','{$code}','T')";
		}
		else {
			$sql = "UPDATE mall_aconf SET code='{$code}' WHERE mode='T'";
		}
		$mysql->query($sql);

		echo "
			<script>
				parent.location.reload();
			</script>
		";

	break;

	case "menu" :
		$item = $_POST['item'];
		$item = join("|",$item);
		$item = addslashes($item);
		
		$sql = "SELECT count(*) FROM mall_aconf WHERE mode='M'";
		if($mysql->get_one($sql)==0) {
			$sql = "INSERT INTO mall_aconf values('','','{$item}','M')";
		}
		else {
			$sql = "UPDATE mall_aconf SET code='{$item}' WHERE mode='M'";
		}
		$mysql->query($sql);

		echo "
			<script>
				parent.location.reload();
			</script>
		";

	break;

	case "site" :
		$name	= addslashes($_POST['site_name']);
		$url	= addslashes($_POST['site_url']);
		$type	= $_POST['type'];
		$site_num	= $_POST['site_num'];

		switch($type) {
			case "write" :
				$sql = "SELECT code, mode FROM mall_aconf WHERE mode='S'";
				$tmps = $mysql->one_row($sql);

				$code = "{$name}|{$url}";
				if($tmps['mode']=='S') {
					if($tmps['code']) $code = $tmps['code']."|{$name}|{$url}";
				}
				else {
					$sql = "INSERT INTO mall_aconf values('','','','S')";
					$mysql->query($sql);
				}
			break;

			case "mod" :
				if(!$site_num) alert("정보가 제대로 넘어오지 못했습니다. 다시 시도 하시기 바랍니다","back");
				$num = ($site_num-1)*2;

				$sql = "SELECT code FROM mall_aconf WHERE mode='S'";
				$code = $mysql->get_one($sql);

				$code = explode("|",$code);
				$site_move	= $_POST['site_move'];
				if($site_move) {
					$num = ($site_num-1)*2;
					array_splice($code,$num,2);
					$tmp_num = ($site_move-1)*2;
					array_splice($code,$tmp_num,0,$name);
					array_splice($code,$tmp_num+1,0,$url);
				}
				else {
					$code[$num] = $name;
					$code[$num+1] = $url;				
				}
				$code = join("|",$code);
			break;

			case "del" :
				if(!$site_num) alert("정보가 제대로 넘어오지 못했습니다. 다시 시도 하시기 바랍니다","back");
				$num = ($site_num-1)*2;

				$sql = "SELECT code FROM mall_aconf WHERE mode='S'";
				$code = $mysql->get_one($sql);

				$code = explode("|",$code);
				array_splice($code,$num,2);
				$code = join("|",$code);
			break;
		}

		$sql = "UPDATE mall_aconf SET code='{$code}' WHERE mode='S'";				
		$mysql->query($sql);
		movePage("site_conf.html");

	break;
}
?>