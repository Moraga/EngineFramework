<?php
/**
 * User Search
 */
class UserSearch extends GenericSearch {
	/**
	 * Creates new User Search object
	 * @param array $options
	 * @param string $url
	 * @param mixed $params_prefix
	 */
	function __construct($options=array(), $url='.', $params_prefix=null) {
		$params = array(
			'q'		=> '', // Query string
			'srt'	=> '', // Sort by
			'p'		=> '', // Page number
		);
		
		$expr  = array('A.id, A.name, A.email, A.username, A.created');
		$table = array('`user` A');
		$where = array();
		$order = array();
		
		if (is_string($options))
			parse_str($options, $options);
		
		$params = array_merge($params, array_intersect_key($options, $params));
		
		parent::__construct(
			implode(', ', $expr),
			implode(', ', $table).
				($where ? ' WHERE '.implode(' AND ', $where) : '').
				($order ? ' ORDER BY '.implode(', ', $order) : ''),
			$params,
			'User',
			$url,
			$params_prefix
		);
		
		$this->prepare();
	}
}

?>