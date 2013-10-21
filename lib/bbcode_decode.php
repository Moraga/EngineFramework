<?php
/**
 * Converts BBCode into HTML
 * @param string $bbcode The BBCode
 * @param string $strip_tags Specify to strip tags and which tags should not be stripped
 * @return string The HTML
 */
function bbcode_decode($bbcode, $strip_tags=false) {
	// default allowable tags
	if ($strip_tags === true) {
		/*
		$strip_tags =
			// group
			'<div>'.
			'<fieldset><legend>'.
			'<blockquote>'.
			'<address>'.
			'<aside><article><section><footer>'.
			// text
			'<h1><h2><h3><h4><h5><h6>'.
			'<p><font><strong><b><em><i><u><a>'.
			'<big><small>'.
			'<sup><sub><cite>'.
			'<del><ins><abbr><acronym><time>'.
			'<pre><code>'.
			'<span><label>'.
			// media
			'<img><object><param><figure><audio><video>'.
			// separators
			'<hr><br>'.
			// list
			'<dl><dt><dd><ul><ol><li>'.
			// table
			'<table><caption><colgroup><col><thead><tbody><tfoot><tr><th><td>'.
			// other
			'<iframe>';
		*/
	}
	
	$html = preg_replace('#\[(\/?[a-z0-9]+[^\]]*)\]#', '<$1>', $bbcode);
	
	if ($strip_tags)
		$html = strip_tags($html, $strip_tags);
	
	return $html;
}

?>