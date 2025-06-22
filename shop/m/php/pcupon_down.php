<?
include "sub_init.php";

require "$lib_path/class.Template.php";
$tpl = new classTemplate;

$num	= $_GET['num'];
$gid	= $_GET['gid'];
$down	= $_GET['down'];

if(!$num) alert('데이터가 넘어오지 못했습니다. 다시 시도 하시기 바랍니다.','close4');

$skin = "../skin/{$tmp_skin}";
$skin2 = $skin."/";
$signdate = time();

if(!$my_id) alert('먼저 로그인을 하시기 바랍니다','close4');

$tpl->define("main","{$skin}/pcupon_down.html");	
$tpl->scan_area("main");

$sql = "SELECT * FROM mall_cupon_manager WHERE uid='{$num}'";
$row = $mysql->one_row($sql);

if(!$row) alert('쿠폰이 존재하지 않거나 종료된 쿠폰입니다.','close4');

if($row['type']==3 && !$gid) alert('데이터가 넘어오지 못했습니다. 다시 시도 하시기 바랍니다.','close4');

if($row['sdate'] && $row['edate'] && !$row['days']) {
	if(date("Y-m-d")>$row['edate']) alert('쿠폰발급기간이 만료 되었습니다.','close4');
}

if($down=='ok') {
	if($row['type']==3) {
		$sql = "SELECT count(*) FROM mall_cupon WHERE pid='{$num}' && id='{$my_id}' && gid='{$gid}' && status='A'";
		if($mysql->get_one($sql)>0) alert('이미 쿠폰이 발급 되었습니다.','close4');
	}
	else {
		$sql = "SELECT count(*) FROM mall_cupon WHERE pid='{$num}' && id='{$my_id}' && status='A'";
		if($mysql->get_one($sql)>0) alert('이미 쿠폰이 발급 되었습니다.','close4');
	}

	if($row['down_type']!=1) {
		$sql = "SELECT count(*) FROM mall_cupon WHERE pid='{$num}' && id='{$my_id}'";
		if($mysql->get_one($sql)>0) alert('해당 쿠폰은 한번만 다운 받으실 수 있습니다.','close4');
	}

	if($row['odds']<100) {  //발급률이 100%가 아닐때
		$sql = "SELECT cnts FROM mall_cupon WHERE pid='{$num}' && id='{$my_id}' && status='D'";
		$p_cnts = $mysql->get_one($sql);

		if($p_cnts>=$row['cnts']) alert('다음 기회를 이용하세요','close4');
		
		$rand_cks = array();
		for($i=0;$i<$row['odds'];$i++){
			$rand_cks[] = "Y";
		}	
		for($i=0;$i<(100-$row['odds']);$i++){
			$rand_cks[] = "N";
		}	
		shuffle($rand_cks);
		if($rand_cks[array_rand($rand_cks)]=='N') {				
			if($p_cnts==0) {	
				$sql = "INSERT INTO mall_cupon VALUES('','{$num}','{$gid}','{$my_id}','D','1','','{$signdate}')";
			}
			else {
				$sql = "UPDATE mall_cupon SET cnts = cnts + 1 WHERE pid='{$num}' && id='{$my_id}' && status='D'";
			}
			$mysql->query($sql);				
			$c_false = "Y";
		}
	}

	if($row['sqty']=='1') {
		if($row['qty']<1) alert('쿠폰이 모두 발급 되었습니다. 다음 기회를 이용 하시기 바랍니다.','close4');
		else if($c_false!='Y'){
			$sql = "UPDATE mall_cupon_manager SET qty = qty -1 WHERE uid='{$num}'";
			$mysql->query($sql);
		}		
	}
	
	if($c_false!='Y') {			
		$sql = "INSERT INTO mall_cupon VALUES('','{$num}','{$gid}','{$my_id}','A','','','{$signdate}')";
		$mysql->query($sql);		
		$tpl->parse("cupon_ok");											
	}
	else $tpl->parse("cupon_false");
}
else {
	$NAME = stripslashes($row['name']);
	if($row['stype']=='P') $SALE = number_format($row['sale'])."%";
	else $SALE = number_format($row['sale'])."원";
	
	if($row['sdate'] && $row['edate'] && !$row['days']) {
		$DATES = substr($row['sdate'],0,10)." ~ ".substr($row['edate'],0,10);
	}
	else {
		$DATES = "발급 후 {$row['days']}일";
	}

	if($row['down_type']==1) $tpl->parse("is_type1");
	else $tpl->parse("is_type2");
	if($row['use_type']==1) $tpl->parse("is_type3");
	else $tpl->parse("is_type4");

	$tpl->parse("cupon");
}


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();


?>