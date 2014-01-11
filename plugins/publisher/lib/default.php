<?php
/**
 * Rich nedia and meta-template JSON
 * @param array $data
 * @return array
 */
function meta_json_ext($data) {
	$temp = array();
	foreach ($data as $key => $val) {
		if ('import' === $key) {
			$path = explode('::', $val);
			$extd = json_decode(file_get_contents(PUBLISHER_METATEMPLATE. $path[0]), true);
			
			// slice
			if (isset($path[1]))
				foreach (explode('.', $path[1]) as $xpath)
					foreach ($extd as $k => $v)
						if (strpos($k . ' ', $xpath) === 0) {
							$extd = $v;
							break;
						}
			
			$temp = is_array($extd) ? array_merge($temp, meta_json_ext($extd)) : $extd;
			continue;
		}
		elseif (is_array($val))
			$val = meta_json_ext($val);
		
		$temp[$key] = $val;
	}
	
	return $temp;
}

/**
 * Parses a media file
 * @param string $filename
 * @return Media
 */
function parse_media_file($filename) {
	$media = cast(meta_json_ext(json_decode(file_get_contents($filename), true)), 'Media');
	$media->name = basename($filename, '.json');
	return $media;
}

/**
 * Parses a meta-template file
 * @param string $filename
 * @return MetaTemplate
 */
function parse_metatemplate_file($filename) {
	global $medias;
	
	$data = meta_json_ext(json_decode(file_get_contents($filename), true));
	
	$mt = new MetaTemplate;
	$mt->portal = $data['portal'];
	$mt->station = $data['station'];
	$mt->channel = $data['channel'];
	$mt->media = $medias[$data['media']];
	$mt->name = str_replace(array(PUBLISHER_METATEMPLATE, '.json'), '', $filename);
	$mt->filename = substr($filename, strlen(DIR));
	$mt->title = $data['title'];
	$mt->override = !empty($data['override']);
	
	if (isset($data['keywords']))
		$mt->keywords = $data['keywords'];
	
	if (isset($data['export']))
		$mt->export = $data['export'];
	
	foreach ($data['modules'] as $key => $groups) {
		$mt->modules[] = $module = new MetaTemplateModule;
		
		// title
		if ($t0 = strpos($key .= ' ', '<')) {
			$module->title = substr($key, $t0 + 1, ($tf = strrpos($key, '>', -1) - $t0) - 1);
			$key = substr_replace($key, ' ', $t0, $tf + 1);
		}
		
		$module->name = strstr($key, ' ', true);
		$module->multiple = trim(strstr($key, ' '));
		
		foreach ($groups as $gr => $fields) {
			$module->groups[] = $group = new MetaTemplateGroup;
			
			if ($t0 = strpos($gr .= ' ', '<')) {
				$group->title = substr($gr, $t0 + 1, ($tf = strrpos($gr, '>', -1) - $t0) - 1);
				$gr = substr_replace($gr, ' ', $t0, $tf + 1);
			}
			
			$group->name = strstr($gr, ' ', true);
			$group->multiple = trim(strstr($gr, ' '));
			
			foreach ($fields as $key => $fld) {
				$group->fields[] = $field = new MetaTemplateField;
				$field->name = $key;
				foreach ($fld as $k => $v) {
					$field->{$k} = $v;
				}
			}
		}
	}
	
	return $mt;
}

/**
 * Sort media objects
 * @param array $medias The array containing media objects
 */
function media_sort(&$medias) {
	$msr = $mst = array();
	
	foreach ($medias as $media) {
		// group by rank
		if ($media->rank)
			$msr[$media->name] = $media->rank;
		// group by title
		else
			$mst[$media->name] = $media->title;
	}
	
	asort($msr); // sort by rank
	uasort($mst, 'strcoll'); // sort by title

	$medias = array_merge($msr + $mst, $medias);
}

?>