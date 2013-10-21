<?php
/**
 * Decodes a JSON string
 * @param string $json The json string being decoded
 * @param boolean $assoc When TRUE, returned objects will be converted into associative arrays
 * @param int $depth User specified recursion depth
 * @param int $options Bitmask of JSON decode options. Currently only JSON_BIGINT_AS_STRING is supported (default is to cast large integers as floats)
 * @return mixed The value encoded in json in appropriate PHP type
 */
function json_decode_relaxed($json, $assoc=false, $depth=512, $options=0) {
	return json_decode(
		preg_replace(
			array(
				'#//[^\n]*#', // removes single comments
				'#[\t\n\r]+#', // removes extra spacing
				'#/\*.*?\*/#', // removes multiline comments
				'#,\s*([}\]])#', // removes excess commas
				'#(?<!\\\)+\'#', // converts single quotes into double quotes
				'#([\{,])(\s*)([^"]+?)\s*:#', // puts keys in quotes
			),
			array(
				'',	
				'',
				'',
				'\1',
				'"',
				'\1"\3":',
			),
			$json
		),
		$assoc, $depth, $options
	);
}

?>