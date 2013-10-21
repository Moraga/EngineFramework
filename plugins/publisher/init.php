<?php

require_once LIB .'json_decode_relaxed.php';
require_once LIB .'report.php';
require $plugindir .'lib/default.php';
require $plugindir .'lib/Media.php';
require $plugindir .'lib/MetaTemplate.php';
require $plugindir .'lib/MetaTemplateGroup.php';
require $plugindir .'lib/MetaTemplateModule.php';
require $plugindir .'lib/MetaTemplateField.php';
require $plugindir .'lib/Content.php';
require $plugindir .'lib/Search.php';

// Publisher absolute URL
define('PUBLISHER_ABSURL', URL . PUBLISHER_URL);

$urls += array(
	PUBLISHER_URL .''			=> '.init.publisher_home',
	PUBLISHER_URL .'load/(.+)'	=> '.init.publisher_load',
	PUBLISHER_URL .'settings'	=> '.init.publisher_settings',
	PUBLISHER_URL .'upload'		=> '.init.publisher_upload',
	PUBLISHER_URL .'logout'		=> '.init.publisher_logout',
);

$medias = array();
$metatemplates = array();

// loads the medias
foreach (glob(PUBLISHER_MEDIA .'*') as $filename) {
	$media = parse_media_file($filename);
	$medias[$media->name] = $media;
}

// loads the meta-templates
foreach (glob(PUBLISHER_METATEMPLATE .'*') as $filename) {
	$metatemplate = parse_metatemplate_file($filename);
	
	// admin index and edit URLs
	$urls[ PUBLISHER_URL .'('. $metatemplate->name . ')/'] = '.init.publisher_content_index';
	$urls[ PUBLISHER_URL .'('. $metatemplate->name . ')/(add|\d+)/'] = '.init.publisher_content_edit';
	
	$metatemplates[$metatemplate->name] = $metatemplate;
}

// content open
$urls['(.*)'] = '.init.publisher_content';

/**
 * Publisher home
 */
function publisher_home() {
	if (!is_auth())
		publisher_login();
	
	global $medias;
	
	media_sort($medias);
	
	return template('index.html', array(
		'medias' => $medias,
	));
}

/**
 * Open/view content
 * @param string $request
 */
function publisher_content($request) {
	require 'lib/template.php';
	
	global $db, $metatemplates;
	
	// finds the content
	$content = Content::instance($request);
	
	if ($content && $content->status == 1) {
		$metatemplate = $metatemplates[$content->metatemplate];
	
		// identifing template
		foreach ($metatemplate->export as $export) {
			if (isset($export['url']) && $export['url'] == '' || strpos($request, $export['url']) === 0) {
				$template = $export['template'];
				break;
			}
		}
		
		// imports content variables to current scope
		extract((array) $content->content);
	}
	else {
		// directory index
		if (!$request)
			$request = 'index.html';
		
		if (file_exists(PUBLISHER_WEBCONTENT . $request)) {
			switch (strrchr($request, '.')) {
				// javascript
				case '.js':
					//require_once LIB .'minify_js.php';
					
					header('Content-Type: application/javascript; charset=utf-8');
					//minify_js(..
					break;
				
				// css
				case '.css':
					require_once LIB .'minify_css.php';
					
					header('Content-Type: text/css; charset=utf-8');
					//header('Expires:..
					echo minify_css(PUBLISHER_WEBCONTENT . $request);
					exit;
				
				// images
				case '.jpg':
				case '.gif':
				case '.png':
					$ext = substr(strrchr($request, '.'), 1);
					
					header('Content-Type: image/' . ($ext == 'jpg' ? 'jpeg' : $ext));
					readfile(PUBLISHER_WEBCONTENT . $request);
					exit;
				
				default:
					break;
			}
			
			$template = $request;
		}
		else {
			header('HTTP/1.0 404 Not Found');
			exit;
		}
	}
	
	ini_set('include_path', PUBLISHER_WEBCONTENT);
	
	$css = array();
	$js  = array();
	
	$ob = ob_start();
	include $template;
	$body = ob_get_contents();
	ob_end_clean();
	
	$css = array_unique($css);
	$js  = array_unique($js);
	
	foreach ($css as &$filename)
		$filename = '<link rel="stylesheet" href="'. $filename .'"/>';
	
	foreach ($js  as &$filename)
		$filename = '<script src="'. $filename .'"></script>';
	
	echo str_replace(
		array(
			'</head>',
			'</body>',
		),
		array(
			implode("\n", $css)."\n</head>",
			implode("\n", $js )."\n</body>",
		),
	$body);
}

/**
 * Meta-template content index
 * @param string $metatempate
 */
function publisher_content_index($metatemplate) {
	if (!is_auth())
		publisher_login();
	elseif (!is_allowed($metatemplate))
		exit('forbidden');
	
	global $metatemplates;
	
	$metatemplate = $metatemplates[$metatemplate];
	
	$index = new Search(array(
		'media'		=> $metatemplate->media->name,
		'portal'	=> $metatemplate->portal,
		'station'	=> $metatemplate->station,
		'channel'	=> $metatemplate->channel,
	));
	
	return template('content-index.html', array(
		'breadcrumbs' => $metatemplate->breadcrumbs(),
		'metatemplate' => $metatemplate,
		'index' => $index,
	));
}

/**
 * Meta-template content edit
 * @param string $metatemplate
 * @param int|string $edit
 */
function publisher_content_edit($metatemplate, $id) {
	if (!is_auth()) {
		publisher_login();
	}
	elseif (!is_allowed($metatemplate))
		exit('forbidden');
	
	global $db, $metatemplates;
	
	$metatemplate = $metatemplates[$metatemplate];
	
	// open / edit
	if (is_numeric($id)) {
		$content = Content::instance($id, true);
		
		// new version
		$content->version--;
	}
	// new
	else {
		$content = new Content;
	}
	
	// post
	if (isset($_POST['action'])) {		
		switch ($action = $_POST['action']) {
			case 1: // publish
			case 2: // save
				require_once LIB .'strtourl.php';
				require_once 'lib/content_save.php';
				require_once 'lib/content_url.php';
				require_once 'lib/content_publish.php';
				require_once 'lib/media_content_save.php';
				
				// takes the action as status
				$content->status = $_POST['action'];
				
				unset($_POST['action'], $_POST['url']);
				
				// updates the content data
				$content->content = $_POST;
				
				if (publisher_content_save($metatemplate, $content)) {
					report($action == 1 ? 'Published successfully' : 'Saved');
					return header('Location: ../'. $content->id .'/');
				}
				else {
					report('Please check the fields marked below.', 2);
				}
				break;
			
			case 'preview':
				break;
			
			case 'URLRename':
				require_once LIB .'strtourl.php';
				require_once 'lib/content_url.php';
				
				publisher_content_url($metatemplate, $content, $_POST['url'], true);
				break;
			
			case 'republish':
				require_once LIB .'strtourl.php';
				require_once 'lib/content_url.php';
				require_once 'lib/content_publish.php';
				
				publisher_content_publish($metatemplate, $content);
				break;
			
			case 'delete':
				require_once 'lib/content_delete.php';
				
				publisher_content_delete($content->id);
				break;
		}
	}
	
	return template('content-edit.html', array(
		'breadcrumbs' => $metatemplate->breadcrumbs(),
		'metatemplate' => $metatemplate,
		'content' => $content,
	));
}

/**
 * Loader
 * @param string $type
 */
function publisher_load($type) {
	if (!is_auth())
		exit('[]');
	
	require_once PLUGINS .'auth/lib/Group.php';
	require_once PLUGINS .'auth/lib/GroupSearch.php';
	require_once PLUGINS .'auth/lib/UserSearch.php';
	
	$ret = array();
	
	switch ($type) {
		case 'user':
			if (isset($_REQUEST['id']))
				$ret = User::instance($_REQUEST['id']);
			else {
				$users = new UserSearch($_REQUEST);
				$ret = $users->all();
			}
			break;
		
		case 'group':
			if (isset($_REQUEST['id']))
				$ret = Group::instance($_REQUEST['id']);
			else {
				$groups = new GroupSearch($_REQUEST);
				$ret = $groups->all();
			}
			break;
		
		case 'metatemplate':
			global $metatemplates;
	
			foreach ($metatemplates as $metatemplate) {
				if ($metatemplate->media->name == $_REQUEST['media']) {
					$ret[] = (object) array(
						'name'		=> $metatemplate->name,
						'portal'	=> $metatemplate->portal,
						'station'	=> $metatemplate->station,
						'channel'	=> $metatemplate->channel,
						'title'		=> $metatemplate->title,
						'url'		=> PUBLISHER_URL . $metatemplate->media->name .'/add/',
					);
				}
			}
			break;
		
		default:
			break;
	}
	
	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode($ret);
	exit;
}

/**
 * Upload file
 */
function publisher_upload() {
	if (!is_auth())
		exit;
	
	if ($_FILES) {
		require_once LIB .'strtourl.php';
		require_once LIB .'permutationsr_rev.php';
		require 'lib/File.php';
		
		$file = cast($_FILES['file'], 'File');
		$file->save();
		unset($file->tmp_name);
		echo json_encode($file);
		exit;
	}
}

/**
 * Publisher settings
 */
function publisher_settings() {
	if (!is_auth()) {
		publisher_login();
	}
	elseif (!is_admin()) {
		report('Forbidden', 2);
		return header('Location: '. PUBLISHER_ABSURL);
	}
	
	require_once LIB .'html_select.php';
	require_once LIB .'html_checkbox.php';
	require_once PLUGINS .'auth/lib/UserSearch.php';
	require_once PLUGINS .'auth/lib/Group.php';
	require_once PLUGINS .'auth/lib/GroupSearch.php';
	
	global $medias, $metatemplates;
	
	if (isset($_REQUEST['action'])) {
		switch ($_REQUEST['action']) {
			case 'userAdd':
			case 'userEdit':
				require_once LIB .'is_email.php';
				require_once PLUGINS .'auth/lib/UserWriter.php';
				
				$user = cast($_POST, 'User');
				
				// check errors and save
				if ($user->save()) {
					exit('if');
				}
				else {
					exit('else');
				}
				
				exit;
				break;
			
			case 'userDelete':
				if ($user = User::instance($_REQUEST['user'])) {
					$user->delete();
				}
				
				break;
			
			case 'groupAdd':
			case 'groupEdit':
				require_once PLUGINS .'auth/lib/GroupWriter.php';
				
				$group = cast($_POST, 'Group');
				
				// check errors and save
				if ($group->save()) {
					exit('saved');
				}
				else {
					print_r($group);
					echo "\n";
					
					exit('catch errors');
				}
				
				break;
			
			case 'groupDelete':
				if ($group = Group::instance($_REQUEST['group'])) {
					$group->delete();
				}
				break;
			
			case 'metaTemplateUpdate':
				break;
			
			case 'metaTemplateDelete':
				break;
			
			case 'mediaUpdate':
				require_once 'lib/media_add.php';
				
				publisher_media_add($medias[$_POST['media']]);
				break;
			
			default:
				break;
		}
	}
	
	// gets users
	$users = new UserSearch;
	$users = $users->all();
	
	// gets groups
	$groups = new GroupSearch;
	$groups = $groups->all();
	
	return template('settings.html', array(
		'medias' => $medias,
		'metatemplates' => $metatemplates,
		'users' => $users,
		'groups' => $groups,
	));
}

/**
 * Publisher user log in
 */
function publisher_login() {
	if (is_auth())
		return header('Location: '. PUBLISHER_ABSURL);
	
	if ($_POST) {
		if (auth_login($_POST)) {
			header('Location: '. $_SERVER['REQUEST_URI']);
			exit;
		}
		else {
			echo 'Username or password invalid';
		}
	}
	
	template('login.html');
	
	exit;
}

/**
 * Publisher user log out
 */
function publisher_logout() {
	auth_logout();
	return header('Location: '. PUBLISHER_ABSURL);
}

?>