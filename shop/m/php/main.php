<?
$sql = "SELECT code FROM mall_design WHERE mode='M'";
$main_dsp = explode("|*|",stripslashes($mysql->get_one($sql)));

$img_path = "../image/mobile/";

$tpl->define("main","{$skin}/main.html");
$tpl->scan_area("main");


/***********************  BANNER  ********************************/
for($i=1;$i<3;$i++) {
	$sql = "SELECT name, banner,link,target,edate FROM mall_mobile_banner WHERE location = '{$i}' && status='1' ORDER BY rank ASC";
	$mysql->query($sql);
	$j = 1;
	while($row_ban = $mysql->fetch_array()){
		if(date("Y-m-d") > $row_ban['edate'] && substr($row_ban['edate'],0,4) != '0000') continue;
		if($row_ban['link']) {
			$BLINK = str_replace("&","&amp;",$row_ban['link']);
			if($row_ban['target']=='2') $BTARGET = "target='_blank'";
			else $BTARGET = "";
		}
		else $BLINK = "#\" onclick=\"return false;";

		$BTITLE = stripslashes($row_ban['name']);
		$BANNER = "../image/banner/".$row_ban['banner'];
		
		$tpl->parse("loop_banner{$i}");		
		$tpl->parse("loop_sbanner{$i}");		

		$j++;
	}
	if($j>0) $tpl->parse("is_banner{$i}");	
}
/***********************  BANNER  ********************************/


$disp_arr = Array('',$main_dsp[1],$main_dsp[2],$main_dsp[4]);

for($k=1;$k<4;$k++) {	
	if($disp_arr[$k] != 0){
		$where = " && SUBSTRING(display,1,1)='{$k}'";

		$sql = "SELECT uid,cate,number,name,price,consumer_price,price_ment,comp,image3,icon,event,reserve,s_qty,qty,coop_price,coop_sale FROM mall_goods WHERE s_qty!='3' && type='A' {$where} ORDER BY o_num1 ASC";
		$mysql->query($sql);
		
		$i=0;
		while($data = $mysql->fetch_array()){			
			$gData	= getDisplay($data,'image3');		// 디스플레이 정보 가공 후 가져오기
			$LINK	= $gData['link'];
			$IMAGE	= "../".$gData['image'];
			$NAME	= $gData['name'];
			$COMP	= $gData['comp'];
			$PRICE	= $gData['price'];
			$CPRICE	= $gData['cprice'];
			$CP_PRICE	= $gData['cp_price'];
			$ICON	= $gData['icon'];
			$QLINK	= $data['uid'];
			$CATE	= $data['cate'];
			
			if($CP_PRICE>0) {
				$tpl->parse("is_coupon");
				$tpl->parse("is_coupon{$k}");
				$PRICE = $CP_PRICE;
			}

			if($CPRICE) {
				$tpl->parse("is_cprice");
				$tpl->parse("is_cprice{$k}");
			}

			$PRICE2 = str_replace("원","",$PRICE);
			
			if($data['s_qty']==2 || ($data['s_qty']==4 && $data['qty']<1)) $tpl->parse("is_soldout");
			$i++;	

			$tpl->parse("loop_sicon{$k}");				
			$tpl->parse("loop_goods{$k}");	
			$tpl->parse("is_soldout","2");
			$tpl->parse("is_coupon","2");
			$tpl->parse("is_soldout{$k}","2");
			$tpl->parse("is_coupon{$k}","2");
			$tpl->parse("is_cprice{$k}","2");			
		}	
		
		if($i==0) {
			$tpl->parse("no_goods{$k}");		
		}
		
		$tpl->parse("is_main_goods{$k}");
	}
}

/************************* Notice & News ******************************/
$sql = "SELECT no, subject, signdate FROM pboard_notice WHERE idx < 999 && idx > 0 limit 0,1";
$mysql->query($sql);

while($data = $mysql->fetch_array()){
	$LINK  = "{$Main}?channel=board_view&amp;code=notice&amp;no={$data['no']}";
	$BOARD = htmlspecialchars(stripslashes($data['subject']));
	$D_YYYY	= date("Y",$data['signdate']);
	$D_YY	= date("y",$data['signdate']);
	$D_MM	= date("m",$data['signdate']);
	$D_DD	= date("d",$data['signdate']);
	$tpl->parse("loop_notice");

}
/************************* Notice & News ******************************/

if(!$channel) $tpl->parse("is_main");
else $tpl->parse("is_sub");

$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>