<?php
/**
 * Installation
 */

ini_set('display_errors', 1);
error_reporting(-1);

define('DIR', dirname(__FILE__) .'/');
define('URL', dirname($_SERVER['PHP_SELF']) .'/');

require DIR .'lib/Writer.php';

// dependencies
$dependencies = array();

// functions applied on success
$onsuccess = array();

// settings
$fields = array();

// general settings
$fields[] = 'Database';
$fields  += array(
	'DB_NAME' => array('Database name', ),
	'DB_USER' => array('User Name', ),
	'DB_PASS' => array('Password', ),
	'DB_HOST' => array('Database Host', 'default' => 'localhost', ),
	'DB_PORT' => array('Database Port', 'apply' => 'db_port', ),
	'DB_BIND' => array('Table prefix', ),
);

$fields[] = 'Dir';
$fields  += array(
	'LIB'	=> array(
		'Library',
		'default'	=> 'lib/',
		'apply'		=> 'endslash',
		'php'		=> "define('LIB', DIR . '%');",
	),
);
$fields  += array(
	'PLUGINS' => array(
		'Plugins dir',
		'default'	=> 'plugins/',
		'apply'		=> 'endslash',
		'php'		=> "define('PLUGINS', DIR . '%');",
	)
);

$fields[] = 'Local & Timezone';
$fields  += array(
	'TIME_ZONE'			=> array(
		'Time zone',
		'empty' => false,
		'php' => "ini_set('date.timezone', '%');",
	),
	
	'LC_COLLATE'		=> array(
		'Language (Locale)',
		'empty' => false,
		'php' => "setlocale(LC_COLLATE, '%');",
	),
	
	'DEFAULT_CHARSET' 	=> array(
		'Charset',
		'empty' => false,
		'php' => "ini_set('default_charset', '%');",
	)
);

// loads the plugins installation files
foreach (glob(DIR . 'plugins/*', GLOB_ONLYDIR) as $plugindir)
	@include ($plugindir .= '/') .'install.php';

// start installation
if ($_POST) {	
	$php = '';
	
	foreach ($fields as $k => $prop) {
		if (!empty($prop['skip']))
			continue;
		
		if (is_int($k)) {
			$php .= "\n// {$prop}\n";
			continue;
		}
		
		if (!empty($_POST[$k]))
			$v = $_POST[$k];
		elseif (!isset($prop['empty']) || $prop['empty'])
			$v = '';
		else
			continue;
		
		if (isset($prop['apply'])) {
			$v = $prop['apply']($v);
		}
		
		$php .= isset($prop['php']) ? str_replace('%', $v, $prop['php']) : "define('$k', ". (is_numeric($v) || strtoupper($v) == 'NULL' ? $v : "'{$v}'") .");";
		$php .= " # {$prop[0]}\n";
	}
	
	header('content-type: text/plain');
	
	/** exit("<?php\n". $php ."\n?>"); /**/
	
	eval($php);
	
	require DIR .'lib/DB.php';
	
	$db = DB::cursor();
	
	// applies table prefix
	$sql = preg_replace('#CREATE TABLE (`)?#', 'CREATE TABLE IF NOT EXISTS \1' . DB_BIND, $sql);
	
	// creates database structure
	$db->multi_query($sql);
	while ($db->more_results() && $db->next_result());
	
	// creates settings file
	file_put_contents(DIR . 'settings.php', "<?php\n". $php ."\n?>");
	
	array_map('call_user_func', $onsuccess);
	
	exit('Welcome');
}

/**
 * formatting functions
 */

function db_port($v) {
	return $v ? $v : 'NULL';
}

function endslash($str) {
	return $str . (substr($str, -1) != '/' ? '/' : '');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Engine Framework Installation</title>
<style>
@import url(http://fonts.googleapis.com/css?family=Open+Sans);

body {background:#f9f9f9; font-size:85%;}
body, input {font-family:'Open Sans', Verdana, Arial, sans-serif;}
input {font-size:120%; padding:4px 0;}

#main {background:#fff; border:1px solid #dfdfdf; border-radius:6px; max-width:520px; margin:0 auto; padding:20px 40px;}

input {width:99%;}

.row {margin-bottom:10px;}
.label {margin-bottom:2px;}

.row + h2 {margin-top:40px;}
</style>
</head>
<body>
<div id="main">
	<h1>Engine Framework</h1>
	<form action="install.php" method="post">
		<?php foreach ($fields as $name => $prop): ?>
		
		<?php if (is_int($name)) { echo "<h2>{$prop}</h2>"; continue; } ?>
		
		<div class="row">
			<div class="label">
				<label><?= $prop[0] ?></label>
			</div>
			<div class="value">
				<input type="text" name="<?= $name ?>" value="<?= isset($prop['default']) ? $prop['default'] : '' ?>"/>
			</div>
		</div>
		<?php endforeach ?>
		<div class="row">
			<div class="value">
				<button type="submit">Install</button>
			</div>
		</div>
	</div>
</div>
</body>
</html>