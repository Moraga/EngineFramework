<?php
/**
 * Publishes the content
 * @param MetaTemplate $metatemplate Content meta-template
 * @param Content $content The Content
 * @return boolean
 */
function publisher_content_publish(MetaTemplate $metatemplate, Content $content) {
	global $db;
	
	// clones the content data to add the default values
	$urldata = $content->content;
	
	// default keywords
	$urldata['Y'] = date('Y');
	$urldata['m'] = date('m');
	$urldata['d'] = date('d');
	
	foreach ($metatemplate->export as $name => $export) {
		// correlation
		if (is_string($export)) {
			// duplicates the content
			if ($metatemplates[$export]->override) {
				publisher_content_save($metatemplates[$export], $content, true);
			}
			// only adds in the media index
			else {
				publisher_content_index($metatemplates[$export], $content);
			}
		}
		// URL
		elseif (isset($export['url']) && $content->version == PUBLISHER_CONTENT_VERSION) {
			// merges meta-template URL and media URI
			// full rewrite
			if (substr($export['url'], 0, 1) == '/') {
				$url = substr($export['url'], 1);
			}
			// partial rewrite
			else {
				preg_match('#(.*?)([^/.]*)(\.?[^/.]*)$#', $export['url'], $urlmatches);
				preg_match('#(.*?)([^/.]*)(\.?[^/.]*)$#', $metatemplate->media->uri, $urimatches);
				
				// appends basedir
				if ($urlmatches[1] != '/')
					$urimatches[1] = $urlmatches[1] . $urimatches[1];
				
				// rewrites file path
				// comment the lines below to stop the rewrite through meta-template
				if ($urlmatches[2])
					$urimatches[2] = $urlmatches[2];
				
				// extension
				if ($urlmatches[3])
					$urimatches[3] = $urlmatches[3];

				$url = $urimatches[1] . $urimatches[2] . $urimatches[3];
			}
			
			// replaces keywords with values
			if (preg_match_all('#{([^}]+)}#', $url, $matches)) {
				// find and replacement
				$repl = array();
				
				foreach($matches[1] as $k => $t)
					$repl[$matches[0][$k]] = ($value = array_key_value($t, $urldata)) ? strtourl(current((array) $value)) : $t;
				
				$url = CONTENT_URL . str_replace(array_keys($repl), $repl, $url);
			}
			
			publisher_content_url($metatemplate, $content, $url);
		}
	}
}

?>