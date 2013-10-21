<?php
/**
 * Resizes an image and fills the blanks with color
 * @param string $filename Path to the image
 * @param int $width The width of the new image
 * @param int $height The height of the new image
 * @param string $destination The path to save the new image, otherwise the image is outputted
 * @param array $fill_color Color in RGB to fills blanks
 * @param int $quality The quality of the new image. From 0 (worst quality) to 100 (best quality)
 * @param int $max_ratio Maximum proportion to increase image
 * @return string|null Returns the full path to the new image, or null if the image was outputted
 */
function imagefillresize($filename, $width, $height, $destination=null, $fill_color=array(255, 255, 255), $quality=90, $max_ratio=1.5) {
	// supported types
	$types = array(
		// mime type	=> array(basename, extension)
		'image/pjpeg'	=> array('jpeg', 'jpg'),
		'image/jpeg'	=> array('jpeg', 'jpg'),
		'image/jpg'		=> array('jpeg', 'jpg'),
		'image/png'		=> array('png',  'png'),
		'image/x-png'	=> array('png',  'png'),
		'image/gif'		=> array('gif',  'gif'),
	);
	
	// get image size and type
	if (!($finfo = getimagesize($filename)) || !isset($types[$finfo['mime']]))
		throw new Exception('Invalid file type. Only allowed images: JPG, GIF and PNG.');
	
	// type
	$type = $finfo['mime'];
	
	// original width
	$src_w = $finfo[0];
	
	// original height
	$src_h = $finfo[1];
	
	// direction
	$dir = '';
	
	// ratio
	$ratio = 0;
	
	// new width
	$dst_w = 0;
	
	// new height
	$dst_h = 0;
	
	// margin left
	$x = 0;
	
	// margin top
	$y = 0;
	
	$imagecreatefromfn = 'imagecreatefrom'. $types[$type][0];
	$imagefn = 'image'. $types[$type][0];
	
	// loads the original image
	$src = $imagecreatefromfn($filename);
	
	// horizontal context
	if ($width >= $height) {
		$dir = 'width';
		
		// vertical image
		if ($src_h > $src_w)
			$dir = 'height';
	}
	// vertical context
	else {
		$dir = 'height';
		
		// horizontal image
		if ($src_w > $src_h)
			$dir = 'width';
	}
	
	if ($$dir / ${"src_$dir"} * ${$dir == 'width' ? 'src_h' : 'src_w'} > ${$dir == 'width' ? 'height' : 'width'})
		$dir = $dir == 'width' ? 'height' : 'width';
	
	$ratio = $$dir / ${"src_$dir"};
	
	// checks ratio
	if ($max_ratio && $ratio > $max_ratio)
		$ratio = $max_ratio;
	
	// calculates the width and height of the new image
	$dst_w = $src_w * $ratio;
	$dst_h = $src_h * $ratio;
	
	// adjusts margin left
	if ($width > $dst_w)
		$x = floor(($width - $dst_w) / 2);
	
	// adjusts margin top
	if ($height > $dst_h)
		$y = floor(($height - $dst_h) / 2);
	
	// creates the new true color image
	$dst = imagecreatetruecolor($width, $height);
	// creates the fill color
	$fill_color = imagecolorallocate($dst, $fill_color[0], $fill_color[1], $fill_color[2]);
	imagefill($dst, 0, 0, $fill_color);
	
	// copy source image to true color image
	imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
	
	// outputs the new image
	if (!$destination)
		header('Content-Type: '. $type);
	
	$imagefn($dst, $destination, $types[$type][0] == 'png' ? ceil(9 - min($quality / 9, 9)) : $quality);
	
	// free memory
	imagedestroy($src);
	imagedestroy($dst);
	
	return $destination;
}

?>