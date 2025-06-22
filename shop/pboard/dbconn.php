<?
############ 디비설정 ####################
if(!$$LANG_ERR_MSG[3]) $LANG_ERR_MSG[3] = 'config.php파일이 없습니다.\\nDB설정을 먼저 하십시요!';
if(!$info = readFiles("{$bo_path}/config.php")) alert($LANG_ERR_MSG[3],'back');  

$info  = explode("||",$info);
$DBConf['host']         = $info[1];				//디비호스트명
$DBConf['database']  = $info[2];				// 디비명
$DBConf['user']         = $info[3];				// 유저명
$DBConf['passwd']     = $info[4];				// 비밀번호
$DBConf['debug']       = "N";                      // 디비에러를 출력한다. "Y"
$cook_rand = $info[5];                             //쿠키값 체크를 위한 임의값
$socket_mail = "N";
?>
