<?php
/**
 * Creates a HTML checkboxes from an array or GenericSearch instance
 * @param string $name The HTML input checkbox name
 * @param array|GenericSearch Values
 * @param string $name The checkbox name
 * @param mixed $value Checked values
 * @return string The HTML checkboxes
 */
function html_checkbox($items, $name=null, $value=null) {
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
				$ret .= '<label><input type="checkbox" name="'. $name .'" value="'. $item->$key .'"'. ($equal($item->$key, $value) ? ' checked="checked"' : '') .'/> '. $item->$lbl .'</label> ';
			}
		}
		// key => value
		else {
			foreach ($items as $key => $lbl) {
				$ret .= '<label><input type="checkbox" name="'. $name .'" value="'. $key .'"'. ($equal($key, $value) ? ' checked="checked"' : '') .'/>'. $lbl .'</label> ';
			}
		}
	}
	// instance of GenericSearch
	elseif ($items instanceof GenericSearch) {
		$key = null;
		$lbl = null;
		
		while ($item = $items->fetch()) {
			if (!$key) {
				$key = isset($item->id) ? 'id' : 'name';
				$lbl = isset($item->label) ? 'label' : 'name';
			}
			
			$ret .= '<label><input type="checkbox" name="'. $name .'" value="'. $item->$key .'"'. ($equal($item->$key, $value) ? ' checked="checked"' : '') .'/>'. $item->$lbl .'</label>';
		}
	}
	
	return $ret;
}

?>