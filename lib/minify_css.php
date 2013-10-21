<?php
/**
 * Minify CSS
 * @param string $css The CSS
 * @return string The CSS minified
 */
function minify_css($css) {
	$re = array();

	// remove comments
	//$rgx['#/\*[^/]+\*/#'] = '';
	$re['#/\*[^*]*\*+([^/][^*]*\*+)*/#'] = '';

	// selector, selector |  prop; prop;
	$re['#\s*([{>,;])\s*#'] = '\1';

	// end elem {prop:value;}
	$re['#[;\n]}#'] = '}';

	// remove empty declarations
	$re['#[^}]+[^{]{}#'] = '';

	// remove tabs and new lines
	$re['#[\t\r\n]+#'] = '';

	return preg_replace(array_keys($re), $re, file_get_contents($css));
}

?>