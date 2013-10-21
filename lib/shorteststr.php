<?php
/**
 * Finds the shortest string in an array
 * @param array $array The array to search in
 * @return string Returns the shortest string in the array
 */
function shorteststr($array) {
	return array_reduce($array, create_function('$v, $w', 'return $v === null || strlen($v) > strlen($w) ? $w : $v;'));
}

?>