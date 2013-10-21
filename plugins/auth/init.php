<?php
/**
 * Auth plugin
 */

require $plugindir .'lib/User.php';

// User object
$user = null;

/**
 * Gets the user
 * @return User|null The User object or NULL
 */
function user() {
	global $user;
	return $user;
}

/**
 * Log in
 * @param array $data
 * @return boolean
 */
function auth_login($data) {
	if ($user = User::instance($data)) {
		$_SESSION['user'] = $user;
		return true;
	}
	return false;
}

/**
 * Log out
 */
function auth_logout() {
	session_start();
	unset($_SESSION['user']);
}

/**
 * Checks whether the user is authenticated
 * @return boolean
 */
function is_auth() {
	if (!isset($_SESSION))
		session_start();
	global $user;
	return ($user = isset($_SESSION['user']) ? $_SESSION['user'] : null) && $user instanceof User;
}

/**
 * Checks whether the user is an administrator
 * @return boolean
 */
function is_admin() {
	global $user;
	return in_array('Admin', $user->groups);
}

/**
 * @param string
 * @return boolean
 */
function is_allowed($perm) {
	global $user;
	return is_admin() || in_array($perm, $user->permissions['mt']);
}

/**
 * Encrypts
 * @param string $str The string to be hashed
 * @param int $cost
 * @return string Returns the hashed string
 */
function xcrypt($str, $cost=10) {
	// create a random salt
	$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
	
	// prefix information about the hash so PHP knows how to verify it later
	// "$2a$" means we're using the Blowfish algorithm. The following two digits are the cost parameter
	$salt = sprintf('$2a$%02d$', $cost) . $salt;
	
	return crypt($str,  $salt);
}

/**
 * Checks whether string matches with hash
 * @param string $str The string to be compared
 * @param string $hash Hash
 * @return boolean
 */
function xcrypt_test($str, $hash) {
	return crypt($str, $hash) == $hash;
}

?>