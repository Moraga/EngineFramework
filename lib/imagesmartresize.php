<?php
/**
 * Resizes an image forcing width and height. If the image doesn't follow the ratio, it will be cut
 * @param string $filename Path to the image
 * @param int $width The width of the new image
 * @param int $height The height of the new image
 * @param string $filename The path to save the new image, otherwise the image is outputted
 * @param int $quality The quality of the new image. From 0 (worst quality) to 100 (best quality)
 * @return string|null The full path of the new image, or null if the image was outputted
 */
function imagesmartresize($filename, $width, $height, $destination=null, $quality=90) {
	// supported types
	$types = array(
		// mime type	=> [php fn, extension]
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
	
	// new width
	$dst_w = $src_w;
	
	// new height
	$dst_h = $src_h;
	
	$imagecreatefromfn = 'imagecreatefrom'. $types[$type][0];
	$imagefn = 'image'. $types[$type][0];
	
	// loads the original imagem
	$src = $imagecreatefromfn($filename);
	
	// creates a new true color image width the set sizes
	$dst = imagecreatetruecolor($width, $height);
	
	$ratio = $width / $height;
	
	// proportional width and height for the ratio given
	// for horizontal
	if ($src_w >= $src_h) {
		if ($src_h * $ratio > $src_w)
			$dst_h = $src_w / $ratio;
		
		$dst_w = $dst_h * $ratio;
	}
	// for vertical
	else {
		if ($src_w / $ratio > $src_h)
			$dst_w = $src_h * $ratio;
		
		$dst_h = $dst_w / $ratio;
	}
	
	// creates the new image
	imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $dst_w, $dst_h);
	
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