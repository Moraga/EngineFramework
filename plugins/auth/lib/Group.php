<?php
/**
 * Group
 */
class Group extends Written {
	/**
	 * Group id
	 * @var int
	 */
	public $id;
	
	/**
	 * Group name
	 * @var string
	 */
	public $name;
	
	/**
	 * Group rules
	 * @var string|object
	 */
	public $rules = array();
	
	/**
	 * Date created
	 * @var string
	 */
	public $created;
	
	/**
	 * Adds a User to this Group
	 * @param User $user The User
	 */
	function addUser(User $user) {
		global $db;
		
		$db->execute("
			INSERT INTO `group_user` SET
				user_id  = {$user->id},
				group_id = {$this->id}
		");
	}
	
	/**
	 * Deletes the Group
	 * @return boolean
	 */
	function delete() {
		global $db;
		
		// deletes the group
		$db->execute("DELETE FROM `group` WHERE id = {$this->id}");
		
		return !!$db->affected_rows;
	}
	
	/**
	 * Get Group by id
	 * @param int $id Group id
	 * @return Group|false The Group, or FALSE
	 */
	static function instance($id) {
		global $db;
		
		$self = $db->get("
			SELECT *
			FROM `group`
			WHERE id = {$id}
		", __CLASS__);
		
		if ($self) {
			$self->rules = json_decode($self->rules);
		}
		
		return $self;
	}
}

?>