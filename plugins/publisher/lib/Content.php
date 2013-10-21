<?php
/**
 * Content
 */
class Content {
	/**
	 * Content id
	 * @var int
	 */
	public $id;
	
	/**
	 * Content status
	 * @var string
	 */
	public $status = 1;
	
	/**
	 * Content version
	 * @var string
	 */
	public $version = PUBLISHER_CONTENT_VERSION;
	
	/**
	 * Content data
	 * @var array
	 */
	public $content = array();
	
	/**
	 * Date created
	 * @var string
	 */
	public $created;
	
	/**
	 * Content URL
	 * @var string
	 */
	public $url;
	
	/**
	 * Gets a Content by id or URL
	 * @param int|string $id Content id or URL
	 * @param boolean $assoc Content data as associative array
	 * @return Content|False The Content, or FALSE
	 */
	static function instance($id, $assoc=false) {
		global $db;
		
		// by id
		if (is_numeric($id)) {
			$query = "
				SELECT A.*, B.url
				FROM `content` A LEFT JOIN `content_url` B ON B.content_id = A.id
				WHERE A.id = {$id}
				ORDER BY A.version
				LIMIT 1
			";
		}
		// by URL
		else {
			$query = "
				SELECT A.metatemplate, B.status, B.content
				FROM `content_url` A, `content` B
				WHERE A.id = MD5('{$id}') AND B.id = A.content_id AND status = 1
				LIMIT 1
			";
		}
		
		// tries to get the content
		if ($self = $db->get($query, __CLASS__)) {
			// parses the content data
			$self->content = json_decode($self->content, $assoc);
		}
		
		return $self;
	}
}

?>