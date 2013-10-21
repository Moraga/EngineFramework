<?php
/**
 * Abstract User Writer
 */
abstract class UserWriter extends Writer {
	/**
	 * Checks for errors in the User object
	 * @param User $i The User object
	 */
	function verify(Written $i) {
		global $db;
		
		// Name
		if (!$i->name)
			$i->err('name', 'Set a name');
		
		// Email
		if (!$i->email)
			$i->err('email', 'Set a email');
		elseif (!is_email($i->email))
			$i->err('email', 'Invalid email');
		elseif ($db->get("SELECT 1 FROM `user` WHERE email = '{$i->email}' AND id <> '{$i->id}'"))
			$i->err('email', 'Already used');
		
		// Username
		if (!$i->username)
			$i->err('username', 'Set a username');
		elseif ($db->get("SELECT 1 FROM `user` WHERE username = '{$i->username}' AND id <> '{$i->id}'"))
			$i->err('username', 'Already used');
		
		// Password
		if (!$i->id && !$i->password)
			$i->err('password', 'Set a password');
	}
}

/**
 * Writes User objects in the database
 */
class DBUserWriter extends UserWriter {
	/**
	 * Writes the User object in the database
	 * @param User $i The User object
	 */
	function save(Written $i) {
		global $db;
		
		// new
		if (!$i->id) {
			// encrypts the password
			$i->password = xcrypt($i->password);
			
			$db->execute("
				INSERT INTO `user` SET
					name = '{$i->name}',
					email = '{$i->email}',
					username = '{$i->username}',
					password = '{$i->password}',
					created = NOW()
			");
			
			// unset password
			$i->password = null;
			
			// gets the new user id
			$i->id = $db->insert_id;
		}
		// update
		else {
			$db->execute("
				UPDATE `user` SET
					name = '{$i->name}',
					email = '{$i->email}',
					username = '{$i->username}'
			");
		}
	}
}

?>