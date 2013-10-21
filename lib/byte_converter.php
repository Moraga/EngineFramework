<?php
/**
 * Coverts bytes into KB, MB, GB and TB
 * @param int $bytes The number of bytes
 * @return string
 */
function byte_converter($bytes) {
	return unit_converter($bytes, array(
		array('KB', 'KB', 1024),
		array('MB', 'MB', 1024),
		array('GB', 'GB', 1024),
		array('TB', 'TB', 1024),
		array('PB', 'PB', 1024),
		array('EB', 'EB', 1024),
	));
}

?>