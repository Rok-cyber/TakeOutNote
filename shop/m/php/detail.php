<?
$tpl->define("main","{$skin}/detail.html");
$tpl->scan_area("main");

$uid	= isset($_POST['uid']) ? $_POST['uid'] : $_GET['uid'];

if(!$uid) alert('정보가 제대로 넘어오지 못했습니다!\\n\\n다시 시도해 주시기 바랍니다.','back');

$sql =  "SELECT uid,cate,number,name,price,price_ment,image3,image4,icon,comp,reserve,c_cnt,event,tag,s_qty,qty,explan FROM mall_goods WHERE uid='{$uid}'";
if(!$row = $mysql->one_row($sql)) alert('해당상품이 삭제되었거나 존재하지 않습니다.','back');

$gData	= getDisplay($row,'image4');		// 디스플레이 정보 가공 후 가져오기
$GOODS_LINK		= $gData['link'];
$GOODS_IMAGE	= "../".$gData['image'];
$GOODS_NAME		= $gData['name'];
$GOODS_PRICE	= $gData['price']; //판매가
$GOODS_PRICE2	= str_replace("원","",$gData['price']);	

if($row['m_explan']) $row['explan'] = $row['m_explan'];
$GOODS_EXPL		= stripslashes($row['explan']);
$GOODS_EXPL		= str_replace("../../image","../image",$GOODS_EXPL);


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>