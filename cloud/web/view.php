<?
include_once("./_common.php");
include_once("./_head.php");

/*
Search, Type, Order, Page
GID, NID, MID
_NAV, _PAGING
*/

//$GID = 3;
//$MID = 3;

if($public):
	$_PUBLIC = explode("|^", $public);
	switch($_PUBLIC[0]):
		case "N": //노트
			@sql_query("UPDATE tons_note SET `public` = '".$_PUBLIC[1]."' WHERE `nid` = $NID");
		break;
		case "E" : //EXTRA
			@sql_query("UPDATE tons_note_extra SET `public` = '".$_PUBLIC[1]."' WHERE `eid` = $EID");
		break;
	endswitch;
endif;

$_NOTE = sql_fetch("SELECT * FROM tons_note WHERE `nid` = $NID AND del != 'Y'");

$result = sql_query("SELECT * FROM tons_note_extra WHERE `nid` = $NID AND del != 'Y'");
while($row = sql_fetch_array($result)) $_EXTRA[] = $row;

$result = sql_query("SELECT * FROM tons_reply WHERE `nid` = $NID ORDER BY rpid ASC, rid ASC");
while($row = sql_fetch_array($result)) $_REPLY[] = $row;

include_once("./include/view.php");

include_once("./_tail.php");
?>

<script type="text/javascript">
$(document).ready(function(){
	if($(window).width() > 1000){
		coverHeight = Math.floor(( $(window).height() - 135 ) * 0.7);

		if(coverHeight < $('#contentsView .cover img').height()){
			$('#contentsView .cover img').height( coverHeight );
		}
	}
});
</script>