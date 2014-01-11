<?php
/**
 * Find pathnames matching a pattern recursively
 * @param string $pattern The pattern
 * @param int $flags Glob flags
 * @return array An array containing the matched files/directories
 */
function glob_recursive($pattern, $flags=0) {
	$files = glob($pattern, $flags);
	foreach (glob(dirname($pattern) .'/*', GLOB_ONLYDIR) as $dir)
		$files = array_merge($files, glob_recursive($dir .'/'. basename($pattern), $flags));
	return $files;
}

?>