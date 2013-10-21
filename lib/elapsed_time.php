<?php
/**
 * Get the elapsed time between two dates
 * @param string $start The initial date
 * @param string $end The optional end date that defaults to current time
 * @return string The elapsed time as string
 */
function elapsed_time($start, $end=null) {	
	return unit_decomposer(($end ? (is_numeric($end) ? $end : strtotime($end)) : time()) - (is_numeric($start) ? $ini : strtotime($start)), array(
		86400 => array('%s day',	'%s days'),
		 3600 => array('%s hour',	'%s hours'),
		   60 => array('%s minute',	'%s minutes'),
	));
}

?>