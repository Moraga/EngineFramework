<?php
/**
 * Get the last day of a month
 * @param string|int|null A date string, month number (considers the current year), timestamp or null (considers the current date)
 * @param boolean|string $full The format of the outputted date string
 * @return string The last day of a month
 */
function last_day($date=null, $format='Y-m-d') {
	return date(
		// output format
		$format,
		// date calc
		strtotime('-1 second', strtotime('+1 month', // base calc
			// reference date
			strtotime(
				// empty or timestamp given
				!$date || is_numeric($date) && $date > 12 ?
					date('Y-m-01', $date ? $date : time()) :
					// month or full date
					(is_numeric($date) ? date("Y-{$date}-01") : substr($date, 0, 8) .'01')
			))
		)
	);
}

?>