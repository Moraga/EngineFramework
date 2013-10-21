<?php
/**
 * Generate a pseudo random string
 *
 * Level and number of possible characters for each position
 * 1 - 10
 * 2 - 36
 * 3 - 62
 * 4 - 64
 * 5 - 84
 *
 * @param int $len The number of characters of the random string thats defaults to 10
 * @param int $lvl The level of complexity of the random string thats defaults to 3
 * @return string Returns a pseudo random string
 */
function randstr($len=10, $lvl=3) {
	$set = implode(
			array_slice(
				array(
					// lvl 1
					'1234567890',
					// lvl 2
					'abcdefghijklmnopqrstuvwxyz',
					// lvl 3
					'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
					// lvl 4
					'-_',
					// lvl 5
					',.!@$%*()[]{}<>/\=+|'
				), 0, $lvl));
	
	for ($ret = '', $max = strlen($set) - 1; $len; $ret .= $set{rand(0, $max)}, $len--);
	return $ret;
}

?>