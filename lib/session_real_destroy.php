<?php
/**
 * Destroy all data registered to a session and cookie
 */
function session_real_destroy() {
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 4200, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	
	session_destroy();
	unset($_SESSION);
}

?>