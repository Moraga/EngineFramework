<?php
/**
 * Creates all the permutations of the input
 * @param array $set The input
 * @return array All the permutations of the input
 */
function permutations($set) {
	if (count($set) == 1)
		return array($set);
	
	$c = array();
	
	foreach ($set as $k => $v) {
		$new = $set;
		unset($new[$k]);
		
		$b = permutations($new);
		
		foreach ($b as &$V)
			array_unshift($V, $v);
		
		$c = array_merge($c, $b);
	}
	
	return $c;
}

?>