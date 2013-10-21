<?php
/**
 * Resizes an image
 * @param string $filename Path to the image
 * @param int $max_size Maximum width and height of the new image
 * @param string $destination The path to save the new image, otherwise the image is outputted
 * @param int $min_size Restricts the minimum width and height for resizing
 * @param int $quality The quality of the new image. From 0 (worst quality) to 100 (best quality)
 * @return string|null The full path of the new image, or null if the image was outputted
 */
function imageresize($filename, $max_size, $destination=null, $min_size=0, $quality=90) {
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
	
	// restricts the minimium width and height for resizing
	if ($min_size && $min_size > min($width, $height))
		throw new Exception('The width and height of the image cannot be lower than '. $min_size .'px.');
	
	// resizes the image if width or height is greater than max_size
	if (max($src_w, $src_h) > $max_size) {
		$imagecreatefromfn = 'imagecreatefrom'. $types[$type][0];
		$imagefn = 'image'. $types[$type][0];
		
		// computes the width and height of the new image
		// for horizontal
		if ($src_w >= $src_h) {
			$dst_w = $max_size;
			$dst_h = round($src_h / $src_w * $max_size);
		}
		// for vertical
		else {
			$dst_h = $max_size;
			$dst_w = round($src_w / $src_h * $max_size);
		}
		
		$src = $imagecreatefromfn($filename);
		$dst = imagecreatetruecolor($dst_w, $dst_h);
		
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		
		// outputs the new image
		if (!$destination)
			header('Content-Type: '. $type);
		
		$imagefn($dst, $destination, $types[$type][0] == 'png' ? ceil(9 - min($quality / 9, 9)) : $quality);
		
		// free memory
		imagedestroy($src);
		imagedestroy($dst);
	}
	// is not necessary to resize
	// copy itself to the new destination
	elseif ($destination) {
		rename($filename, $destination);
		chmod($destination, 0755);
	}
	// outputs itself
	else {
		header('Content-Type: '. $type);
		readfile($filename);
		return;
	}
	
	return $destination;
}

?>