<?php

ini_set('display_errors', 1);
error_reporting(-1);

define('DIR', dirname(__FILE__) .'/');
define('URL', dirname($_SERVER['PHP_SELF']) .'/');

require DIR .'settings.php';
require LIB .'DB.php';
require LIB .'GenericSearch.php';
require LIB .'Writer.php';
require LIB .'default.php';

// database connection
$db = DB::cursor();

// URL patterns
$urls = array();

// loads the plugins
foreach (glob(PLUGINS .'*', GLOB_ONLYDIR) as $plugindir) {
	$pluginname = basename($plugindir);
	require ($plugindir .= '/') . 'init.php';
	$urls = array_map('plugins_url_map', $urls);
}

// request uri: without base URL
$request_uri = substr($_SERVER['REQUEST_URI'], strlen(URL));

// removes the query string
if ($_SERVER['QUERY_STRING'])
	$request_uri = substr($request_uri, 0, strlen($_SERVER['QUERY_STRING']) * -1 -1);

// runs through each URL pattern
foreach ($urls as $url => $callback) {
	// stops at the first one that matches the requested URL
	if (preg_match('#^'. $url .'$#', $request_uri, $request)) {
		// plugin . file [. function]
		$callback = explode('.', $callback);
		
		define('PLUGIN_DIR', PLUGINS . $callback[0] .'/');
		define('PLUGIN_URL', URL .'plugins/'. $callback[0] .'/');
		
		// imports the file
		require_once PLUGINS . $callback[0] .'/'. $callback[1] . '.php';
		
		// calls the function
		if (isset($callback[2]))
			call_user_func_array($callback[2], array_slice($request, 1));
		
		exit;
	}
}

// none URL pattern matched
header('HTTP/1.0 404 Not Found');

?>