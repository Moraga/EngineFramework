<?php
/**
 * Copy all files and folders from directory
 * @param string $source Source path
 * @param string $destination Destination path
 * @param bool $verbose
 */
function dir_copy($source, $destination, $verbose=false) {
	if ($destination{strlen($destination) - 1} == '/')
		$destination = substr($destination, 0, -1);

	if (!is_dir($destination)) {
		if ($verbose)
			echo "Creating directory {$destination}\n";
		mkdir($destination, 0755);
	}

	$folder = opendir($source);
	
	while ($item = readdir($folder)) {
		if ($item == '.' || $item == '..')
			continue;
		
		if (is_dir("{$source}/{$item}"))
			dir_copy("{$source}/{$item}", "{$destination}/{$item}", $verbose);
		else {
			if ($verbose)
				echo "Copying {$item} to {$destination}\n";
			copy("{$source}/{$item}", "{$destination}/{$item}");
		}
	}
}

?>