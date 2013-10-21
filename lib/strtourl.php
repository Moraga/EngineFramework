<?php
/**
 * Converts a string to URL format
 * @param string $str input string
 * @param boolean $lowercase Converts to lowercase
 * @param string $separator Spaces separator
 * @return string The string in URL format
 */
function strtourl($string, $lowercase=true, $separator='-') {
	$lowercase = $lowercase ? 'strtolower' : 'pass';
	return trim(preg_replace('#([^.A-Za-z0-9]+)#', $separator, $lowercase(unaccent(strtr(html_entity_decode($string, ENT_NOQUOTES, 'UTF-8'), '&', ' ')))), '-');
}

?>