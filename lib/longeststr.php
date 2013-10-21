<?php
/**
 * Finds the longest string in an array
 * @param array $array The array to search in
 * @return string Returns the longest string in the array
 */
function longeststr($array) {
	return array_reduce($array, create_function('$v, $w', 'return strlen($w) > strlen($v) ? $w : $v;'));
}

?>