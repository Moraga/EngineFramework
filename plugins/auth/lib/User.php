<?php
/**
 * User
 */
class User extends Written {
	/**
	 * User id
	 * @var int
	 */
	public $id;
	
	/**
	 * User name
	 * @var string
	 */
	public $name;
	
	/**
	 * User email
	 * @var string
	 */
	public $email;
	
	/**
	 * User username
	 * @var string
	 */
	public $username;
	
	/**
	 * User password
	 * @var string
	 */
	public $password;
	
	/**
	 * Date created
	 * @var string
	 */
	public $created;
	
	/**
	 * @return string The user first name
	 */
	function firstName() {
		return ($sep = strpos($this->name, ' ')) ? substr($this->name, 0, $sep) : $this->name;
	}
	
	/**
	 * @return string The user last name
	 */
	function lastName() {
		return strpos($this->name, ' ') ? substr(strrchr($this->name, ' '), 1) : $this->name;
	}
	
	/**
	 * @return string|null The user middle name, or NULL
	 */
	function middleName() {
		return ($sep1 = strpos($this->name, ' ')) && ($sep1 != ($sep2 = strrpos($this->name, ' '))) ? substr($this->name, $sep1 + 1, $sep2 - $sep1 - 1) : null;
	}
	
	/**
	 * Deletes the User
	 * @return boolean
	 */
	function delete() {
		global $db;
		
		// deletes the user from database
		$db->execute("DELETE FROM `user` WHERE id = {$this->id}");
		
		return !!$db->affected_rows;
	}
	
	/**
	 * Get User by id, username or email
	 * @param mixed $id User id, username or email
	 * @return User|false The User, or FALSE
	 */
	static function instance($id) {
		global $db;
		
		$where = array();
		
		// by id
		if (is_numeric($id))
			$where[] = "A.id = {$id}";
		// by username or email
		else {
			if (isset($id['login']))
				$id[strpos($id['login'], '@') ? 'email' : 'username'] = $id['login'];
			
			if (isset($id['email']))
				$where[] = "A.email = '{$id['email']}'";
			elseif (isset($id['username']))
				$where[] = "A.username = '{$id['username']}'";
		}
		
		// gets the user and their permissions
		$user = $db->get("
			SELECT A.*, GROUP_CONCAT(C.name) groups, CONCAT('[', GROUP_CONCAT(C.rules SEPARATOR '],['), ']') permissions
			FROM `user` A LEFT JOIN `group_user` B ON B.user_id = A.id INNER JOIN `group` C ON C.id = B.group_id
			WHERE ". implode(' AND ', $where), __CLASS__);
		
		if ($user) {
			// verifies the password, if it was given
			if (!isset($id['password']) || xcrypt_test($id['password'], $user->password)) {
				// do not store the password in the session
				$user->password = null;
				$user->groups = explode(',', $user->groups);
				$user->permissions = call_user_func_array('array_merge_recursive', json_decode($user->permissions, true));
			}
			else {
				$user = false;
			}
		}
		
		return $user;
	}
}

?>