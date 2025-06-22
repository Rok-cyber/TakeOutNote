<?
include_once("./_common.php");
include_once("./_head.php");

/*
Search, Type, Order, Page
GID, NID, MID
_NAV, _PAGING
*/

//$GID = 3;

switch($Type): //썸네일, 리스트 표시 형식에 따른 변수 설정
	case "T":
	break;
	default:
		$_PAGING['rows'] = 10;
		$_PAGING['page'] = $Page ? $Page : 1;
	break;
endswitch;

if($Query): //검색
	$result = sql_query("SELECT N.* FROM tons_note N 
							LEFT OUTER JOIN tons_note_extra E ON E.nid = N.nid 
							LEFT OUTER JOIN tons_reply R ON R.nid = N.nid 
						WHERE
							N.mid = $MID AND N.del != 'Y' 
							AND ( N.title LIKE '%$Query%' OR N.note LIKE '%$Query%' OR E.extra LIKE '%$Query%' OR R.reply LIKE '%$Query%' )
						");

	if(!mysql_num_rows($result)):
		alert("검색된 노트가 없습니다!");
	endif;

	while($row = sql_fetch_array($result)):
		$row['cnt']['memo'] = sql_fetch_one("SELECT COUNT(*) FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'memo'");
		$row['cnt']['alarm'] = sql_fetch_one("SELECT COUNT(*) FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'alarm'");
		$row['cnt']['location'] = sql_fetch_one("SELECT COUNT(*) FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'location'");
		$row['cnt']['record'] = sql_fetch_one("SELECT COUNT(*) FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'record'");

		if($row['cnt']['memo']):
			$row['memo'] = sql_fetch_one("SELECT extra FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'memo' LIMIT 1");
		endif;

		$list[] = $row;
	endwhile;
	include_once("./include/list_{$Type}.php");	

elseif($GID): //노트목록
	$result = sql_query("SELECT * FROM tons_note WHERE `gid` = $GID AND `mid` = $MID AND `del` != 'Y'");

	if(!mysql_num_rows($result)):
		alert("등록된 노트가 없습니다!");
	endif;

	while($row = sql_fetch_array($result)):
		$row['cnt']['memo'] = sql_fetch_one("SELECT COUNT(*) FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'memo'");
		$row['cnt']['alarm'] = sql_fetch_one("SELECT COUNT(*) FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'alarm'");
		$row['cnt']['location'] = sql_fetch_one("SELECT COUNT(*) FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'location'");
		$row['cnt']['record'] = sql_fetch_one("SELECT COUNT(*) FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'record'");

		if($row['cnt']['memo']):
			$row['memo'] = sql_fetch_one("SELECT extra FROM tons_note_extra WHERE `nid` = {$row['nid']} AND `property` = 'memo' LIMIT 1");
		endif;

		$list[] = $row;
	endwhile;
	include_once("./include/list_{$Type}.php");
else: //그룹목록
	$result = sql_query("SELECT * FROM tons_group WHERE `mid` = $MID AND `del` != 'Y'");
	while($row = sql_fetch_array($result)):
		if($row['nid']){
			$row['thumb'] = sql_fetch_one("SELECT note FROM tons_note WHERE `nid` = {$row['nid']}");
		} else {
			$row['thumb'] = sql_fetch_one("SELECT note FROM tons_note WHERE `gid` = {$row['gid']} AND `del` != 'Y' LIMIT 1");
		}
		$row['cnt']['note'] = sql_fetch_one("SELECT COUNT(nid) FROM tons_note WHERE `gid` = {$row['gid']} AND `del` != 'Y'");
		$list[] = $row;
	endwhile;

	if(is_array($list)) include_once("./include/group_{$Type}.php");
endif;

include_once("./_tail.php");
?>

<script type="text/javascript">
$(document).ready(function(){
	$('#contentsList li.basic').on('mouseover', function(e){
		$(this).addClass('on');
	});
	$('#contentsList li.basic').on('mouseout', function(e){
		$(this).removeClass('on');
	});
});
</script>