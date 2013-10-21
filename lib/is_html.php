<?php
/**
 * Checks if the input string contains HTML tags
 * @param string $str The input string
 * @return boolean TRUE if contains HTML, FALSE otherwise
 */
function is_html($str) {
	//return strip_tags($str) != $str;
	return preg_match('#<\w[^<>]*>#', $str);
}

?>