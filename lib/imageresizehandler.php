<?php
/**
 * Image resize handler
 *
 * Resizes automatically images (JPG, PNG and GIF) by URL.
 * The new images are saved on the original image directory (to disable, set $destination=null).
 *
 * Using:
 *
 * 1) Include this file on base directory that you want to enable autoresizing
 *
 * Option A) Create new php file and require this file
 * <?php require '/path/to/this/file/imageresizehandler.php' ?>
 *
 * Option B) Alias
 * ln -s /path/to/this/file /path/to/enable/autoresizing
 * "" Don't forget to set LIB constant pointing to engine lib's dir
 *
 * Option C) Copy/paste
 * "" Don't forget to set LIB constant pointing to engine lib's dir
 *
 *
 * 2) Set to redirect non-files requests to your new file
 *
 * RewriteEngine On
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteRule . fileThatYouCreated.php [L]
 *
 * Option A) Create new .htacess file with the content above in the base images directory
 *
 * Option B) VirtualHost
 * <Location /path/to/base/images/dir>
 * RewriteEngine On
 * Rewri...
 * </Location>
 *
 */

if (!defined('LIB'))
	define('LIB', dirname(__FILE__) .'/');

// set the default resize type
define('DEFAULT_RESIZE_TYPE', 's');

// list of resizes allowed
// format: type + width + [x height]
$allowed_sizes = array(
	//'142x100',
	//'300x200',
	//'300x200',
	//'s200',
);

// separator, examples: -, _ or empty
$re = '-';

// group resizing parameters
$re .= '(?<params>';

// resizing type
$re .= '(?P<type>[nsf])?';

// new width and height
$re .= '(?<width>\d+)(?:x(?<height>\d+))?';

// end of parameters
$re .= ')';

// file extension
$re .= '\.(?<extension>[a-z0-9]{3})';

// relative path
$uri = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['PHP_SELF'], '/') + 1);

// remove query string
if ($_SERVER['QUERY_STRING'])
	$uri = substr($uri, 0, strpos($uri, '?'));

if (
	// check url pattern
	!preg_match("#(?<base>.*){$re}$#", $uri, $matches) ||
	
	// check allowed sizes
	($allowed_sizes && !in_array($matches['params'], $allowed_sizes)) ||
	
	// check if original file exists
	!file_exists($src = './'. $matches['base'] .'.'. $matches['extension'])
) {
	header('HTTP/1.0 404 Not Found');
	exit('404');
}

if ($matches['type'] == '')
	$matches['type'] = DEFAULT_RESIZE_TYPE;

// destination
$destination = $matches[0];
// uncomment the line below to not save the new image
//$destination = null;

switch ($matches['type']) {
	case 'n':
		$fn = 'imageresize';
		$params = array($src, $matches['width'], $destination);
		break;
	
	case 's':
		$fn = 'imagesmartresize';
		$params = array($src, $matches['width'], $matches['height'], $destination);
		break;
	
	case 'f':
		$fn = 'imagefillresize';
		$params = array($src, $matches['width'], $matches['height'], $destination);
		break;
}

require LIB . $fn . '.php';

try {
	header('content-type: text/plain;');
	call_user_func_array($fn, $params);
	if ($destination)
		header('Location: '. $_SERVER['REQUEST_URI']); // refresh
}
catch (Exception $e) {
	header('HTTP/1.1 503 Service Unavailable');
}

?>