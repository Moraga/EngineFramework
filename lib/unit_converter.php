<?php
/**
 * Unit converter
 * @param int $n The input value
 * @param array $units Units
 * @return string
 */
function unit_converter($n, $units) {
	$str = array();

	foreach ($units as $unit) {
		if ($n >= $unit[2]) {
			$tmp = floor($n / $unit[2]);
			$n -= $tmp * $unit[2];
			$str[] = $tmp .' '. $unit[$tmp > 1];
		}
	}
	
	return implode(' ', $str);
}

?>