<?php

/**
 * Pass function
 * @return mixed
 */
function pass($arg) {
	return $arg;
}

/**
 * Converts array to object
 * @param array $arr The array to being converted
 * @param string $class_name The class name of the new object
 * @param boolean $construct Calls the constructor method
 * @return object The object
 */
function cast($arr, $class_name, $construct=false) {
	$obj = unserialize('O:'. strlen($class_name) .':"'. $class_name .'"'. substr(serialize($arr), 1));
	if ($construct && method_exists($obj, '__construct'))
		$obj->__construct();
	return $obj;
}

/**
 * Template
 * @param string $filename
 * @param array $vars
 */
function template($filename, $vars=array()) {
	// auth plugin
	global $user;
	
	// points include_path to plugin templates dir
	ini_set('include_path', PLUGIN_DIR .'templates/');	
	extract($vars);
	include $filename;
}

/**
 * Gets the singular or plural by count
 * @param string $sigular Singular
 * @param string $plural Plural
 * @param int $count Count
 * @return string The singular or plural according count
 */
function __($singular, $plural, $count) {
	return sprintf($count <= 1 ? $singular : $plural, $count);
}

/**
 * Binary safe string comparison
 * @param string $a The first string
 * @param string $b The second string
 * @return boolean TRUE if they are equal, FALSE otherwise
 */
function strequal($a, $b) {
	return strcmp($a, $b) === 0;
}

/**
 * Find the first occurence of a string
 * @param string $haystack The input string
 * @param string $needle The needle
 * @return Returns the part of the haystack before the first occurrence of the needle (excluding the needle)
 */
function strleft($haystack, $needle) {
	return substr($haystack, 0, strpos($haystack, $needle));
}

/**
 * Strip accents
 * @param string $str The input string
 * @return string Returns the string without accents
 */
function unaccent($str) {
	return preg_replace('#&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);#i', '$1', htmlentities($str, ENT_QUOTES, 'UTF-8'));
}

/**
 * @param mixed $needle
 * @param array $haystack
 * @return mixed
 */
function array_key_value($needle, $haystack) {
	foreach ($haystack as $key => $value)
		if ($key === $needle || is_array($value) && ($value = array_key_value($needle, $value)))
			return $value;
	return false;
}

/**
 * Split string by a regular expression recursively
 * @param string $pattern The pattern to search for, as a string
 * @param mixed $subject The input value
 * @param mixed $limit A preg_split limit
 * @param mixed $flag A preg_split flags
 * @return array Returns an array containing substrings of subject split along boundaries matched by pattern
 */
function preg_split_recursive($pattern, $subject, $limit=-1, $flags=0) {
	$ret = array();
	foreach ($subject as $item)
		$ret = array_merge($ret, is_array($item) ? preg_split_recursive($pattern, $item, $limit, $flags) : preg_split($pattern, $item, $limit, $flags));
	return $ret;
}

/**
 * Checks whether a variable is decimal (float without type)
 * @param mixed $var The variable being evaluated
 * @return boolean Returns TRUE if var is a decimal number, FALSE otherwise
 */
function is_decimal($var) {
	return $var != (int) $var;
}

/**
 * Calculates the triagular number
 * @param int $n The number
 * @return int Returns the triangular number
 */
function triangular_number($n) {
	return $n * ($n + 1) / 2;
}

/**
 * Calculates the factorial of a number
 * @param int $n The number
 * @return int Returns the factorial
 */
function factorial($n) {
	for ($i=1; $n > 1; $i *= $n--);
	return $i;
}

/**
 * Map plugin's URLs
 * @param string $v Execution path
 * @return string Returns
 */
function plugins_url_map($v) {
	return ($v{0} == '.' ? $GLOBALS['pluginname'] : '') . $v;
}

?>