<?
include "../html/top_inc.html"; // 상단 HTML 

######################## lib include
require "{$lib_path}/class.Template.php";

######################## 분류 생성 ##############################
$tmps1	= "CATEname = [[' ==== 1차분류 ==== ',[' ==== 2차분류 ==== ',[' ==== 3차분류 ==== ',' ==== 4차분류 ==== ']]]";
$tmps2	= "CATEnum	= [['',['',['','']]]";
$cnts=0;
$sql = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = 1 ORDER BY number ASC";
$mysql->query($sql);
while($row=$mysql->fetch_array()){    
	$row['cate_name'] = addslashes($row['cate_name']);
	if($row['cate_sub']==1) {
	    $tmps1.= ",['{$row[cate_name]}'";		
		$tmps2.= ",['{$row[cate]}'";		
		$sql2 = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = '2' AND cate_parent = '{$row[cate]}' ORDER BY number ASC";
		$mysql->query2($sql2);
		while($row2=$mysql->fetch_array(2)){
			$row2['cate_name'] = addslashes($row2['cate_name']);
			if($row2['cate_sub']==1) {
				$tmps1.= ",['{$row2[cate_name]}'";	
				$tmps2.= ",['{$row2[cate]}'";	
				$sql3 = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = '3' AND cate_parent = '{$row2[cate]}' ORDER BY number ASC";
				$mysql->query3($sql3);
				while($row3=$mysql->fetch_array(3)){
					$row3['cate_name'] = addslashes($row3['cate_name']);
					if($row3['cate_sub']==1) {
						$tmps1.= ",['{$row3[cate_name]}'";	
						$tmps2.= ",['{$row3[cate]}'";	
						$sql4 = "SELECT cate,cate_name FROM mall_cate WHERE cate_dep = '4' AND cate_parent = '{$row3[cate]}' ORDER BY number ASC";
						$mysql->query4($sql4);						
						while($row4=$mysql->fetch_array(4)){							
							$row4['cate_name'] = addslashes($row4['cate_name']);
							$tmps1.= ",'{$row4[cate_name]}'";
							$tmps2.= ",'{$row4[cate]}'";
						}
						$tmps1.= "]";
						$tmps2.= "]";
					} 
					else {
						$tmps1.= ",['{$row3[cate_name]}']";		
						$tmps2.= ",['{$row3[cate]}']";		
					}	
				}
				$tmps1.= "]";
				$tmps2.= "]";
			} 
			else {
				$tmps1.= ",['{$row2[cate_name]}']";		
				$tmps2.= ",['{$row2[cate]}']";		
            }
		}
    } 
	else {
		$tmps1.= ",['{$row[cate_name]}'";		
		$tmps2.= ",['{$row[cate]}'";		
	}
	$tmps1.= "]";
	$tmps2.= "]";	
	$cnts=1;	
}
$tmps1.= "]";
$tmps2.= "]";
######################## 분류 생성 ##############################

$DAY1 = date("Y-m-d");
$DAY2 = date("Y-m-d", strtotime('-3 DAY', time()));
$DAY3 = date('Y-m-d', strtotime('-1 WEEK', time()));
$DAY4 = date('Y-m-d', strtotime('-1 MONTH', time()));
$DAY5 = date('Y-m-d', strtotime('-3 MONTH', time()));
$DAY6 = date('Y-m-d', strtotime('-6 MONTH', time()));

$skin = ".";

$goods_arr = Array('cate','uid','brand','name','model','comp','made','price','consumer_price','def_qty','qty','op_goods','image1','image2','image3','image4','image5','explan','tag','option');
$goods_arr2 = Array('분류','상품UID','브랜드','상품명','모델명','제조사','원산지','판매가격','소비자가격','기본판매수량','재고량','관련상품','큰이미지','중간이미지','리스트이미지','작은이미지','디스플레이이미지','상세설명','태그','옵션');


// 템플릿
$tpl = new classTemplate;
$tpl->define("main","./data.html");
$tpl->scan_area("main");

for($i=0,$cnt=count($goods_arr);$i<$cnt;$i++){
	$GFIELD = "<input type=checkbox name=item[] id='item1_{$i}' value='{$goods_arr[$i]}' checked /> <label for='item1_{$i}'>{$goods_arr2[$i]}({$goods_arr[$i]})</label>";
	$tpl->parse("loop1");	
}


$order_arr = Array('order_status','order_num','signdate','id','name1','goods_name','pay_total','gooods_total','carriage','use_reserve','use_cupon','pay_type','carriage1','carriage2','tel1','hphone1','email','name2','tel2','hphone2','zipcode','address','message');
$order_arr2 = Array('주문상태','주문번호','주문시간','아이디','주문고객성명','주문상품','총결제금액','주문상품금액','배송비','적립금사용금액','쿠폰사용금액','결제방법','택배회사','송장번호','주문고객연락처1','주문고객연락처2','주문고객E-mail','수취인성명','수취인연락처1','수취인연락처2','수취인 우편번호','수취인주소','주문고객메모');

for($i=0,$cnt=count($order_arr);$i<$cnt;$i++){
	$GFIELD = "<input type=checkbox name=item[] id='item2_{$i}' value='{$order_arr[$i]}' checked /> <label for='item2_{$i}'>{$order_arr2[$i]}({$order_arr[$i]})</label>";
	$tpl->parse("loop2");	
}

$member_arr = Array('id','name','jumin1','tel','hphone','zipcode','address','email','homepage','msn','birth','sex','marr','edu','hobby','job','jobname','info','level','reserve','mailling','add1','add2','add3','add4','add5','signdate');
$member_arr2 = Array('아이디','이름','주민번호','연락처1','연락처2','우편번호','주소','이메일','홈페이지','메신저','생녕월일','성별','결혼유무','최종학력','취미','직업','직장명','남기는말씀','레벨','적립금','메일링','추가필드1','추가필드2','추가필드3','추가필드4','추가필드5','가입일');

for($i=0,$cnt=count($member_arr);$i<$cnt;$i++){
	$GFIELD = "<input type=checkbox name=item[] id='item3_{$i}' value='{$member_arr[$i]}' checked /> <label for='item3_{$i}'>{$member_arr2[$i]}({$member_arr[$i]})</label>";
	$tpl->parse("loop3");	

	$ckk .= "<!-- DYNAMIC @is_t{$member_arr[$i]}@ --><th>{$member_arr2[$i]}</th><!-- DYNAMIC @is_t{$member_arr[$i]}@ -->\n";
}

$sql = "SELECT name, code FROM mall_design WHERE mode='L' && name!='10' ORDER BY name ASC";
$mysql->query($sql);

for($i=2;$i<9;$i++) {
	$row = $mysql->fetch_array();
	while($row['name']!=$i) {
		$LEVEL .= "<option value='{$i}'>LV{$i}</option>";
		if($i==8) break;
		$i++;
	}
	if($row['name']==$i) {
		$tmps = explode("|",$row['code']);
		$LEVEL .= "<option value='{$i}'>".stripslashes($tmps[0])."</option>";		
	}
}

$tpl->parse("main");
$tpl->tprint("main");

 include "../html/bottom_inc.html"; // 하단 HTML