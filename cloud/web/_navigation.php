<?

if(stristr($_SERVER['PHP_SELF'],"list.php")):
	if($GID) $_NAV['current'] = sql_fetch_one("SELECT name FROM tons_group WHERE `gid` = $GID");
	else $_NAV['current'] = $lng['group'] . " " . $lng['list'];
endif; //그룹/노트목록

if(stristr($_SERVER['PHP_SELF'],"mypage.php")):
	$_NAV['current'] = $lng['mypage'];
endif; //그룹/노트목록

if(stristr($_SERVER['PHP_SELF'],"view.php")):
	$_TMP['note'] = sql_fetch("SELECT title, public FROM tons_note WHERE `nid` = $NID");
	$_TMP['icon'] = $_TMP['note']['public']=="Y" ? "lock_open" : "lock_closed";

//	$_NAV['current'] = sql_fetch_one("SELECT title FROM tons_note WHERE `nid` = $NID");
	$_NAV['current'] = "<img id='notePublicImg' src='imgs/icon/{$_TMP['icon']}.png' style='height: 28px;margin-bottom: -5px;' onClick=\"noteSetPublic();\"/> ";
	$_NAV['current'] .= urldecode($_TMP['note']['title']);
endif; //그룹/노트목록

if(stristr($_SERVER['PHP_SELF'],"note.php")):
	$_TMP['note'] = sql_fetch("SELECT title, public FROM tons_note WHERE MD5(uniqueid) = '$noteid'");
	$_NAV['current'] = $_TMP['note']['title'];
endif; //그룹/노트목록

if($Query):
	$_NAV['current'] = $lng['search'] . " " . $lng['result'];
endif; //검색결과

?>