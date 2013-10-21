<?php
/**
 * Converts days to time expression
 * $param int $days The number of days
 * @return string Time expression
 */
function daystotime($days) {
	return unit_converter($days, array(
		array('year',	'years',	365),
		array('month',	'months',	 31),
		array('day',	'days',		  1)
	));
}

?>