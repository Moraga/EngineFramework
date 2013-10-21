<?php
/**
 * Converts HTML into BBCode
 * @param string $html The HTML
 * @return string BBCode
 */
function bbcode_encode($html) {
	return preg_replace('#<(\/?[a-z0-9]+[^>]*)>#', '[$1]', $html);
}

?>