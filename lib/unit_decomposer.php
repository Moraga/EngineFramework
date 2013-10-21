<?php
/**
 * Unit decomposer
 * @param int $val The input value
 * @param array $units Units
 * @return string
 */
function unit_decomposer($val, $units) {
	$ret = array();
	
	foreach ($units as $base => $data) {
		if ($val < $base)
			continue;
		else {
			$ret[] = __($data[0], $data[1], floor($val / $base));
			$val %= $base;
		}
	}
	
	return implode(' ', $ret);
}

?>