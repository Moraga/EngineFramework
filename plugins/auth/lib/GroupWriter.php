<?php
/**
 * Abstract Group Writer
 */
abstract class GroupWriter extends Writer {
	/**
	 * Checks for errors in the Group object
	 * @param Group $i The Group object
	 */
	function verify(Written $i) {
		global $db;
		
		// Name
		if (!$i->name)
			$i->err('name', 'Group name required');
		elseif ($db->get("SELECT 1 FROM `group` WHERE name = '{$i->name}' AND id <> '{$i->id}'"))
			$i->err('name', 'Group already exists');
	}
}

/**
 * Writes Group objects in the database
 */
class DBGroupWriter extends GroupWriter {
	/**
	 * Writes the Group object in the database
	 * @param Group $i The Group object
	 */
	function save(Written $i) {
		global $db;
		
		// encodes the rules as JSON
		$rules = json_encode($i->rules);
		
		// new
		if (!$i->id) {
			$db->execute("
				INSERT INTO `group` SET
					name = '{$i->name}',
					rules = '{$rules}',
					created = NOW()
			");
			
			// gets the new group id
			$i->id = $db->insert_id;
		}
		// update
		else {
			$db->execute("
				UPDATE `group` SET
					name = '{$i->name}',
					rules = '{$rules}'
				WHERE id = {$i->id}
			");
		}
	}
}

?>