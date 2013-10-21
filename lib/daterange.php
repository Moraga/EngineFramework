<?php
/**
 * Creates a date range
 * @param int|string $start Start date
 * @param int|string $end End date
 * @param string $interval Representation of date interval
 * @param string $format The format of the outputted date string
 * @return array An array containing the date range
 */
function daterange($start, $end, $interval='+1 day', $format='Y-m-d') {
	$dates = array();
	
	if (is_string($start))
		$start = strtotime($start);
	
	if (is_string($end))
		$end   = strtotime($end);
	
	do {
		$dates[] = date($format, $start);
	} while ($end >= $start = strtotime($interval, $start));
	
	return $dates;
}

?>