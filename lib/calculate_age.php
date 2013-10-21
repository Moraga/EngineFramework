<?php
/**
 * Calculate age
 * @param string|int $date Date or timestamp
 * @return int The age
 */
function calculate_age($date) {
	list($Y, $M, $D) = explode(' ', date('Y n j'));
	//list($y, $m, $d) = array_map(create_function('$i', 'return ltrim($i, "0");'), explode('-', is_numeric($date) ? date('Y-n-j', $date) : $date));
	list($y, $m, $d) = explode(' ', date('Y n j', strtotime($date)));
	return $Y - $y - ($m > $M || $m == $M && $d > $D);
}

?>