<?php
/**
 * Publisher media add/update
 * @param Media $media The Media object
 */
function publisher_media_add(Media $media) {
	global $db;
	
	$table = array();
	$table[] = 'portal VARCHAR(30) NOT NULL';
	$table[] = 'station VARCHAR(30) NOT NULL';
	$table[] = 'channel VARCHAR(30) NOT NULL';
	$table[] = 'keywords VARCHAR(50) NOT NULL';
	$table[] = 'content_id INT UNSIGNED NOT NULL';
	
	foreach ($media->database['columns'] as $col_name => $col_prop) {
		$expr = '';
		$expr = $col_name;
		
		switch ($col_prop['type']) {
			case 'char':
			case 'varchar':
				$expr .= " {$col_prop['type']} ({$col_prop['length']})";
				break;
			
			default:
				$expr .= " {$col_prop['type']}";
				break;
		}
		
		if (!isset($col_prop['null']) || $col_prop['null'])
			$expr .= ' NOT NULL';
		
		if (isset($col_prop['default']))
			$expr .= " DEFAULT '{$col_prop['default']}'";
		
		$table[] = $expr;
	}
	
	$table[] = 'created DATETIME NOT NULL';
	$table[] = 'changed DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
	
	if (isset($media->database['index'])) {
		foreach ($media->database['index'] as $index_name => $index_prop) {
			if (!isset($index_prop['columns']))
				$index_prop['columns'] = $index_prop;
			
			$expr = 'INDEX '. (isset($index_prop['type']) ? "{$index_prop['type']} " : '') . $index_name .'('. implode(', ', $index_prop['columns']) .')';
			$table[] = $expr;
		}
	}
	
	$table[] = 'PRIMARY KEY(portal, station, channel, content_id)';
	$table[] = 'FOREIGN KEY(content_id) REFERENCES content(id) ON UPDATE CASCADE ON DELETE CASCADE';
	
	$db->execute('DROP TABLE IF EXISTS `'. $media->name .'`');
	$db->execute('CREATE TABLE `'. $media->name .'` ('. implode(', ', $table). ') ENGINE=InnoDB');
}

?>