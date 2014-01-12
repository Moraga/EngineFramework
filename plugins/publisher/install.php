<?php
/**
 * Publisher installer
 */

$fields[] = 'Upload';
$fields  += array(
	'UPLOAD_DIR' => array(
		'Upload dir',
		'default'	=> 'upload/',
		'apply'		=> 'endslash',
		'php'		=>
			"define('UPLOAD_DIR', DIR . '%');\n".
			"define('UPLOAD_URL', URL . '%');"
	),
	
	'UPLOAD_ORD' => array(
		'Archiving files: date or combinatory',
		'default'	=> 'date',
	),
);

$fields[] = 'Publisher';
$fields  += array(
	'PUBLISHER_URL' => array(
		'Publisher URL',
		'default'	=> 'admin/',
	),
	
	'PUBLISHER_MEDIA' => array(
		'Media directory',
		'default'	=> 'plugins/publisher/media/',
		'php'		=> "define('PUBLISHER_MEDIA', DIR . '%');",
	),
	
	'PUBLISHER_METATEMPLATE' => array(
		'Meta-template directory',
		'default'	=> 'plugins/publisher/metatemplate/',
		'php'		=> "define('PUBLISHER_METATEMPLATE', DIR . '%');",
	),
	
	'PUBLISHER_WEBCONTENT' => array(
		'Web content',
		'default'	=> 'plugins/publisher/webcontent/',
		'apply'		=> 'endslash',
		'php'		=> "define('PUBLISHER_WEBCONTENT', DIR . '%');",
	),
	
	'PUBLISHER_CONTENT_VERSION' => array(
		'Content version limit',
		'default'	=> 9999,
	),
);

$sql .= <<<E

CREATE TABLE content (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	status TINYINT UNSIGNED NOT NULL DEFAULT 1,
	version SMALLINT UNSIGNED NOT NULL DEFAULT 9999,
	content MEDIUMTEXT NOT NULL,
	created DATETIME NOT NULL,
	PRIMARY KEY (id, status, version)
) ENGINE=InnoDB;

CREATE TABLE content_url (
	id CHAR(32) BINARY NOT NULL PRIMARY KEY,
	content_id INT UNSIGNED NOT NULL,
	url VARCHAR(300) NOT NULL,
	metatemplate VARCHAR(30) NOT NULL,
	FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE content_admin (
	media VARCHAR(30) NOT NULL,
	portal VARCHAR(30) NOT NULL,
	station VARCHAR(30) NOT NULL,
	channel VARCHAR(30) NOT NULL,
	content_id INT UNSIGNED NOT NULL,
	title VARCHAR(255) NOT NULL,
	status TINYINT UNSIGNED NOT NULL,
	created DATETIME NOT NULL,
	PRIMARY KEY (media, portal, station, channel, content_id),
	FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE file (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(300) NOT NULL,
	type VARCHAR(255) NOT NULL,
	size INT UNSIGNED NOT NULL DEFAULT 0,
	file VARCHAR(300) NOT NULL,
	created DATETIME NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB;

E;

$onsuccess[] = 'createdirs';

function createdirs() {
	// create upload dir and enable resize handler
	mkdir(UPLOAD_DIR, 0777, true);
	file_put_contents(UPLOAD_DIR .'index.php', '<?php require "'. LIB .'imageresizehandler.php" ?>');
	file_put_contents(UPLOAD_DIR .'.htaccess',
		"RewriteEngine On\n".
		"RewriteCond %{REQUEST_FILENAME} !-f\n".
		"RewriteRule . index.php [L]\n"
	);
}

$onsuccess[] = 'gotoadmin';

function gotoadmin() {
	header('Location:'. URL . PUBLISHER_URL);
	exit;
}

?>