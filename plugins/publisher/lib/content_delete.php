<?php
/**
 * Deletes the content by id
 * @param int $id Content id
 * @return boolean
 */
function publisher_content_delete($id) {
	global $db;
	
	// change status, keep the content
	$db->execute("UPDATE `content` SET status = 9 WHERE id = {$id}");
	
	// delete data
	//$db->execute("DELETE FROM `content` WHERE id = {$id}");
	
	return true;
}

?>