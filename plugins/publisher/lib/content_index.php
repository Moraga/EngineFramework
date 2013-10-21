<?php
/**
 * Saves the data in the Media index
 * @param MetaTemplate $metatemplate
 * @param Content $content
 * @return boolean
 */
function publisher_content_index(MetaTemplate $metatemplate, Content $content) {
	global $db;
	
	if (!isset($metatemplate->media->database['columns']))
		return;
	
	$query = array();
	
	$query[] = "portal = '{$metatemplate->portal}'";
	$query[] = "station = '{$metatemplate->station}'";
	$query[] = "channel = '{$metatemplate->channel}'";
	$query[] = "keywords = '{$metatemplate->keywords}'";
	$query[] = "content_id = {$content->id}";
	$query[] = "created = NOW()";
	
	$unquote = array('int', 'bigint', 'current_timestamp', 'now()');
	foreach ($metatemplate->media->database['columns'] as $name => $prop) {
		$quotes = in_array($prop['type'], $unquote) ? '' : '"';
		
		$query[] = $name .' = '. $quotes . current((array) array_key_value($prop['value'], $content->content)) . $quotes;
	}
	
	$db->execute($query = 'REPLACE `'. $metatemplate->media->name .'` SET '. implode(', ', $query));
	
	return true;
}

?>