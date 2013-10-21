<?php
/**
 * Get the words from string
 * @param string $str The input string
 * @return array An array containing the words found
 */
function words($str) {
	return preg_match_all('#[a-zà-ú]+(?:-[a-zà-ú]+)*#i', $str, $matches) ? $matches[0] : array();
}

?>