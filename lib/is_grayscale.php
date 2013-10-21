<?php
/**
 * Checks if the image is grayscale
 * @param string $filename Path to the image
 * @return boolean TRUE if image is grayscale, FALSE otherwise
 */
function is_grayscale($filename) {
	// supported types
	$types = array(
		// ext	=> php fn
		'jpg'	=> 'jpeg',
		'gif'	=> 'gif',
		'png'	=> 'png',
	);
	
	// get file extension
	$ext = substr(strrchr($filename, '.'), 1);
	
	// checks file extension
	if (!isset($types[$ext]))
		throw new Exception('Invalid file');
	
	$fn = 'imagecreatefrom'. $types[$ext];
	$is = true;
	
	$im = $fn($filename);
	$iw = imagesx($im);
	$ih = imagesy($im);
	
	// read pixel to pixel
	for ($i=0; $i < $iw; $i++) {
		for ($j=0; $j < $ih; $j++) {
			// get the color of the pixel
			$rgb = imagecolorat($im, $i, $j);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >>  8) & 0xFF;
			$b = $rgb & 0xFF;
			
			$rg = abs($r - $g);
			$gb = abs($g - $b);
			
			if ($rg > 6 || $gb > 6) {
				$is = false;
				break;
			}
		}
	}
	
	return $is;
}

?>