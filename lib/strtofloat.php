<?php
/**
 * Get float value of a variable
 * @param string $str Input string
 * @return float The float value of the given variable
 */
function strtofloat($str) {
	$arr = array('', '');
	for ($i = strlen($str) - 1, $j = 0; $i > -1; $i--) {
		if (!is_numeric($str{$i})) {
			if (!$j) {
				// considers values beginning with comma as float
				if (!$i && $str{$i} == ',') {
					$arr[1] = '0';
				}
				$j = 1;
			}
			continue;
		}
		$arr[$j] .= $str{$i}; 
	}
	
	return (float) ($arr[1] != '' ? strrev($arr[1]) .'.'. strrev($arr[0]) : strrev($arr[0]));
}

?>