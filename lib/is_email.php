<?php
/**
 * Checks if the input string is a valid email address
 * @param string $str The input string
 * @param boolean $checkdns Checks MS records
 * @return boolean TRUE if is a valid email adress, FALSE otherwise
 */
function is_email($str, $checkdns=true) {
	return preg_match('#^[a-z0-9._-]+@([a-z0-9.-]+\.[a-z]{2,6})$#i', $str, $matches) && !$checkdns || checkdnsrr($matches[1] .'.', 'MX');
}

?>