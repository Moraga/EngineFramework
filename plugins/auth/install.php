<?php
/**
 * Auth installation
 */

require_once $plugindir .'init.php';
require_once $plugindir .'lib/Group.php';
require_once $plugindir .'lib/GroupWriter.php';
require_once $plugindir .'lib/User.php';
require_once $plugindir .'lib/UserWriter.php';

$dependencies[] = 'mcrypt';

$fields[] = 'Admin';
$fields  += array(
	'ADMIN_NAME'	=> array('Admin name', ),
	'ADMIN_EMAIL'	=> array('Admin email', ),
	'username'		=> array('Username', 'skip' => true),
	'password'		=> array('Password', 'skip' => true),
);

$sql .= <<<E

CREATE TABLE `user` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(60) NOT NULL,
	email VARCHAR(100) NOT NULL,
	username VARCHAR(60) NOT NULL,
	password VARCHAR(64) BINARY NOT NULL,
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY(id),
	UNIQUE INDEX email(email),
	UNIQUE INDEX username(username)
) ENGINE=InnoDB;

CREATE TABLE `group` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(60) NOT NULL,
	rules TEXT,
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY(id),
	UNIQUE KEY name(name)
) ENGINE=InnoDB;

CREATE TABLE `group_user` (
	user_id INT UNSIGNED NOT NULL,
	group_id INT UNSIGNED NOT NULL,
	PRIMARY KEY(user_id, group_id),
	FOREIGN KEY(user_id) REFERENCES `user`(id) ON DELETE CASCADE,
	FOREIGN KEY(group_id) REFERENCES `group`(id) ON DELETE CASCADE
) ENGINE=InnoDB;

E;

$onsuccess[] = 'adduser';

function adduser() {
	$user = new User;
	$user->name = $_POST['ADMIN_NAME'];
	$user->email = $_POST['ADMIN_EMAIL'];
	$user->username = $_POST['username'];
	$user->password = $_POST['password'];
	$user->save(false);
	
	$group = new Group;
	$group->name = 'Admin';
	$group->save(false);
	
	$group->addUser($user);
}

?>