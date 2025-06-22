<?
// 최근 게시물 불러오기 페이지에 맞게 변환
if(!$bo_path) $bo_path= "./pboard";

include "$bo_path/lib/lib.Function.php";
require "$bo_path/dbconn.php";
require "$bo_path/lib/class.Mysql.php";

$mysql = new mysqlClass();

function newArticle($i,$code,$icon_new) { 
	global $mysql;	 
	$code="notice";	    
    $sql ="select no,subject,signdate from pboard_{$code} WHERE idx < 999 && idx > 0 limit {$i},1";        
	if($data = $mysql->one_row($sql)){
		$NEWS='';
		if($data['signdate'] > (time()-(3600*48))) $NEWS = "<img src='{$icon_new}' align='absmiddle'>";	 			 
		$data['subject'] = hanCut($data['subject'],46); 
		$date = date("Y/m/d",$data['signdate']);
		echo "$NEWS<a href='community/sub01.html?pmode=view&no=$data[no]' onfocus=this.blur();>".stripslashes($data[subject])."</a></td><td width='68'>$date</td>";		 
	} 
	else {
	    echo "&nbsp;";
    }
}

?> 

