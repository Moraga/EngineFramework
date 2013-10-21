<?php
/**
 * Template functions
 */

/**
 * Handles searches
 */
global $search;

/**
 * Handles search results
 */
global $result;

/**
 * Executes queries on media indexes
 *
 * Usage:
 * if (search('media=post&portal=x&station=...')) {
 *     while (have_results()) {
 *         $result;
 *     }
 *     
 *     echo pagination();
 * }
 *
 *
 * @param array $options Query parameters
 * @return Search|false
 */
function search($options) {
	global $search;
	$search = new Search($options);
	return $search->rows ? $search : false;
}

/**
 * Checks and fetches search results
 *
 * Usage:
 * while (have_results()) {
 *     $result;
 * }
 *
 * @return object|false The fetched row, FALSE otherwise
 */
function have_results() {
	global $search, $result;
	return $result = $search->fetch();
}

/**
 * Gets search pagination
 * @return string
 */
function pagination() {
	global $search;
	return $search->pagination();
}

?>