<?php
/**
 * Creates a HTML select element from an array or GenericSearch instance
 * @param array|GenericSearch Values
 * @param string $name The HTML select name
 * @param mixed $value Selected values
 * @param mixed $empty Creates a option tag without value
 * @param string $attr HTML attributes
 * @return string The HTML select element
 */
function html_select($items, $name=null, $value=null, $empty=true, $attr=null) {
	$ret = '';
	
	// comparison function
	$equal = is_array($value) ? 'in_array' : 'strequal';
	
	// array
	if (is_array($items)) {
		// objects container
		if (is_object(current($items))) {
			$key = isset(current($items)->id) ? 'id' : 'name';
			$lbl = isset(current($items)->label) ? 'label' : 'name';
			
			foreach ($items as $item) {
				$ret .= '<option value="'. $item->$key .'"'. ($equal($item->$key, $value) ? ' selected="selected"' : '') .'>'. $item->$lbl .'</option>';
			}
		}
		// key => value
		else {
			foreach ($items as $key => $lbl) {
				$ret .= '<option value="'. $key .'"'. ($equal($key, $value) ? ' selected="selected"' : '') .'>'. $lbl .'</option>';
			}
		}
	}
	// instance of GenericSearch
	elseif ($items instanceof GenericSearch) {
		while ($item = $items->fetch())
			$ret .= '<option value="'. $item->id .'"'. ($equal($item->id, $value) ? ' selected="selected"' : '') .'>'. $item->name .'</option>';
	}
	
	return
		'<select'.
			($name ? ' name="'. $name .'"' : '').
			($attr ? ' '. $attr : '').
			(substr($name, -2) == '[]' && strpos($attr, '[]') == -1 ? ' multiple="multiple"' : '').
		'>'.
			($empty ? '<option>'. ($empty !== true ? $empty : '') .'</option>' : '').
			$ret.
		'</select>';	
}

?>