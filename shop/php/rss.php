<?
$tpl->define("main","{$skin}/rss.html");
$tpl->scan_area("main");

######################## 분류 생성 ##############################
$tmps1	= "CATEname = [[' ==== 1차분류 ==== ',[' ==== 2차분류 ==== ',[' ==== 3차분류 ==== ',' ==== 4차분류 ==== ']]]";
$tmps2	= "CATEnum	= [['',['',['','']]]";
$cnts=0;
$sql = "SELECT cate,cate_name,cate_sub FROM mall_cate WHERE cate_dep = 1 && cate != '999000000000' ORDER BY number ASC";
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


$URL = "http://".$_SERVER["HTTP_HOST"]."/{$ShopPath}";
$sql = "SELECT cate,cate_name,cate_sub,img2,img3 FROM mall_cate WHERE cate_dep ='1' && valid ='1' && cate != '999000000000' ORDER BY number ASC";
$mysql->query($sql);

while($row = $mysql->fetch_array()){
	$CNAME = stripslashes($row['cate_name']);
	$CLINK1 = "{$URL}rss/newGoods.php?cate={$row['cate']}";
	$CLINK2 = "{$URL}rss/bestGoods.php?cate={$row['cate']}";
	$tpl->parse("loop_cate");
}


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>