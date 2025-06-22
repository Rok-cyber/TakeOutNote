<?
//메모리제거
if(!function_exists('userAbortFunc')){
	//메모리제거
	function userAbortFunc() {

		global $mysql,$pg,$tpl;
		if(is_object($mysql)) $mysql->close();
		if(is_object($tpl)) $tpl->close();
		if(is_object($pg)) $pg->close();
		
	}
}

@ignore_user_abort(true); 
@register_shutdown_function('userAbortFunc');
?>