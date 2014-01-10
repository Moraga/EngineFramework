<?php
/**
 * Search on Media indexes
 */
class Search extends GenericSearch {
	/**
	 * Creates a new Search object
	 * @param array $options Query parameters
	 * @param string $url Paging URL
	 * @param mixed $params_prefix Groups paging variables
	 */
	function __construct($options=array(), $url='.', $params_prefix=null) {
		// default parameters
		$params = array(
			'admin'		=> '',
			'media'		=> '', // Media / repository
			'fields'	=> '', // Fields
			'portal'	=> '',
			'station'	=> '',
			'channel'	=> '',
			'title'		=> '',
			'keywords'	=> '', // Meta-template keywords
			'q'			=> '', // Query string
			'rpp'		=> '', // Results per page
			'srt'		=> 'created', // Sort by (dev)
			'p'			=> '', // Page number
		);
		
		// allows options as URL encoded
		if (is_string($options))
			parse_str($options, $options);
		
		// merges params and options preserving keys and order
		$params = array_merge($params, array_intersect_key($options, $params));
		
		$expr  = array('*');
		$table = array();
		$where = array();
		$order = array();
		$group = array();
		
		if ($params['admin']) {
			$table[] = 'content_admin A';
			
			if ($params['media'])
				$where[] = "A.media = '{$params['media']}'";
		}
		else {
			$table[] = $params['media'] .' A';
		}
		
		if ($params['portal'])
			$where[] = "A.portal = '{$params['portal']}'";
		
		if ($params['station'])
			$where[] = "A.station = '{$params['station']}'";
		
		if ($params['channel'])
			$where[] = "A.channel = '{$params['channel']}'";
		
		if ($params['keywords']) {
			$params['keywords'] = preg_split_recursive('#\s*,\s*#', (array) $params['keywords']);
		}
		
		switch ($params['media']) {
			case 'post':
				break;
			
			default:
				break;
		}
		
		// sort
		if ($params['srt'])
			$order[] = 'A.created DESC';
		
		// get content url
		$table[0] .= ' LEFT JOIN content_url B ON A.content_id = B.content_id';
		$extr[] = 'B.url';
		
		parent::__construct(
			implode(', ', $expr),
			implode(', ', $table).
				($where ? ' WHERE '.implode(' AND ', $where) : '').
				($group ? ' GROUP BY '.implode(', ', $group) : '').
				($order ? ' ORDER BY '.implode(', ', $order) : ''),
			$params,
			'stdClass',
			$url,
			$params_prefix
		);
		
		// results are ready to be fetched
		$this->prepare();
	}
}

?>