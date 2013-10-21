<?php
/**
 * Sets or updates the URL to the content
 * @param MetaTemplate $metatemplate Content meta-template
 * @param Content $content The Content
 * @param string $url The new URL to the content
 * @param boolean $force
 * @return boolean
 */
function publisher_content_url(MetaTemplate $metatemplate, Content $content, $url, $force=false) {
	global $db;
	
	if ($force) {
		$db->execute("
			DELETE FROM `content_url`
			WHERE content_id = {$content->id} AND metatemplate = '{$metatemplate->name}'
		");
	}
	
	$db->execute("
		INSERT IGNORE INTO content_url SET
			id = MD5('{$url}'),
			content_id = {$content->id},
			url = '{$url}',
			metatemplate = '{$metatemplate->name}'
	");
	
	return true;
}

?>