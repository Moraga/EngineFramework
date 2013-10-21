<?php
/**
 * Gets a permutation (with repetition) by position
 * combinations = N-chars(set) ^ len
 * @param int $pos Position
 * @param int $len Length
 * @param string $set Set of options
 * @return string The permutation
 */
function permutationsr_rev($pos, $len=3, $set='abcdefghijklmnopqrst') {
	$chs = strlen($set);
	$pos = ceil($pos / pow($chs, $len));
	$ret = '';
	
	for (; $len--;) {
		if ($pos > ($max = pow($chs, $len))) {
			$div = $pos / $max;
			$mod = $pos % $max;
			if (!$mod) {
				$div--;
				$pos = $max;
			}
			else {
				$pos = $mod;
			}
		}
		else {
			$div = 0;
		} 
		
		$ret .= substr($set, $div, 1);
	}
	
	return $ret;
}

?>