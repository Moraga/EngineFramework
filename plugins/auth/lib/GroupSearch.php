<?php
/**
 * Group search object
 */
class GroupSearch extends GenericSearch {
	/**
	 * Creates new Group Search object
	 * @param array $options
	 * @param string $url
	 * @param mixed $params_prefix
	 */
	function __construct($options=array(), $url='.', $params_prefix=null) {
		// default parameters
		$params = array(
			'q'		=> '', // Query string
			'srt'	=> '', // Sort by
			'p'		=> '', // Page number
		);
		
		$expr  = array('*');
		$table = array('`group`');
		$where = array();
		$order = array();
		
		if (is_string($options))
			parse_str($options, $options);
		
		$params = array_merge($params, array_intersect_key($options, $params));
		
		if ($params['q'])
			$where[] = "name LIKE '%{$params['q']}%'";
		
		parent::__construct(
			implode(', ', $expr),
			implode(', ', $table).
				($where ? ' WHERE '.implode(' AND ', $where) : '').
				($order ? ' ORDER BY '.implode(', ', $order) : ''),
			$params,
			'Group',
			$url,
			$params_prefix
		);
		
		$this->prepare();
	}
}


?>