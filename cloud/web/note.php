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

$_NOTE = sql_fetch("SELECT * FROM tons_note WHERE MD5(uniqueid) = '$noteid' AND del != 'Y' AND `public` = 'Y'");
if(!$_NOTE['nid']){
	alert($lng['alert_private']);
	exit;
}
$nid = $_NOTE['nid'];

$result = sql_query("SELECT * FROM tons_note_extra WHERE `nid` = {$_NOTE['nid']} AND del != 'Y' AND `public` = 'Y'");
while($row = sql_fetch_array($result)) $_EXTRA[] = $row;

$result = sql_query("SELECT * FROM tons_reply WHERE `nid` = {$_NOTE['nid']} ORDER BY rpid ASC, rid ASC");
while($row = sql_fetch_array($result)) $_REPLY[] = $row;

include_once("./include/note.php");

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