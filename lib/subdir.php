<?php
/**
 * Get sub directories
 *
 * Examples:
 *
 * Back 1 dir
 * subdir('/one/two/three/four', 1) or subdir('/one/two/three/four/', 1)
 * >> /one/two/three
 *
 * Back 2 dir
 * subdir('/one/two/three/four', 2)
 * >> /one/two
 *
 * Get two last dir
 * subdir('/one/two/three/four', -2)
 * >> three/four
 *
 * @param string $path The base directory path
 * @param int $times Positive to back or negative to get last
 * @return string The path
 */
function subdir($path, $times=1) {
	return $times > -1 ?
		implode('/', array_slice(explode('/', substr($path, -1) == '/' ? substr($path, 0, -1) : $path), 0, $times * -1)) :
		implode('/', array_slice(explode('/', $path), $times));
}

?>