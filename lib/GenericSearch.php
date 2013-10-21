<?php

class GenericSearch {
	/**
	 * Database connection
	 * @var DB
	 */
	public $db;
	
	/**
	 * Select expression
	 * @var string
	 */
	public $expr;
	
	/**
	 * SQL statements
	 * @var string
	 */
	public $synt;
	
	/**
	 * Query parameters
	 * @var array
	 */
	public $params;
	
	/**
	 * Can be used to perform more than one search
	 * and distinct GET variables
	 * @var mixed
	 */
	public $params_prefix;
	
	/**
	 * Class name used to instantiate the result rows
	 * @var string
	 */
	public $class_name;
	
	/**
	 * Query result
	 * @var DB_Result
	 */
	private $result;
	
	/**
	 * Number of rows in the result set
	 * @var int
	 */
	public $rows;
	
	/**
	 * Number of results per page
	 * @var int
	 */
	public $rpp = 20;
	
	/**
	 * Current page number
	 * @var int
	 */
	public $p = 1;
	
	/**
	 * Last fetched row number
	 * @var int
	 */
	public $i = 0;
	
	/**
	 * Creates a new Search object
	 * @param string $expr SQL expression
	 * @param strign $synt SQL statement
	 * @param array $params Query parameters
	 * @param string $class_name The name of the class to instantiate, set the properties of and return the results. If not specified, a stdClass object is returned
	 * @param string $url Paging URL
	 * @param string $param_prefix Used to distinct GET variables on paging
	 */
	function __construct($expr, $synt, $params=array(), $class_name='stdClass', $url='.', $params_prefix=null) {
		global $db;
		
		$this->db = $db;
		$this->expr = $expr;
		$this->synt = $synt;
		$this->params = $params;
		$this->class_name = $class_name;
		$this->url = $url;
		$this->params_prefix = $params_prefix;
		
		// get number of rows in result
		$this->db->execute('SELECT SQL_CALC_FOUND_ROWS 1 FROM '. $this->synt);
		$this->rows = $this->db->execute('SELECT FOUND_ROWS() rows')->fetch_object()->rows;
		
		if (!empty($params['p']))
			$this->p = $params['p'];
		
		if (!empty($params['rpp']))
			$this->rpp = $params['rpp'];	
	}
	
	/**
	 * Sends the structured SQL queries
	 * @param int $offset The offset of the first row to return
	 * @param int $rows_count Maximum number of rows to return
	 * @return DB_Result DB_object or FALSE on failure
	 */
	private function execute($offset, $row_count=1) {
		return $this->db->execute("SELECT {$this->expr} FROM {$this->synt} LIMIT {$offset}, {$row_count}");
	}
	
	/**
	 * Sends the query
	 * @param int $p Page number
	 * @param int $rrp Number of results per page
	 * @return this
	 */
	function prepare($p=null, $rpp=null) {
		if ($p)
			$this->p = $p;
		
		if ($rpp)
			$this->rpp = $rpp;
		
		$this->result = $this->execute(($this->p - 1) * $this->rpp, $this->rpp);
		
		return $this;
	}
	
	/**
	 * Fetch a result row as an object
	 * @return mixed The current row of a result set as an object
	 */
	function fetch() {
		$this->i++;
		return $this->result->fetch_object($this->class_name);
	}
	
	/**
	 * Fetch all results rows
	 * @return array An array containing all fetched rows
	 */
	function all() {
		$results = array();
		while ($result = $this->fetch())
			$results[] = $result;
		return $results;
	}
	
	/**
	 * @param array $append Add in the return
	 * @param array $detach Remove from the return
	 * @return array Parameters
	 */
	function params($append=array(), $detach=array()) {
		return array_diff_key(array_merge(array_filter($this->params, create_function('$i', 'return $i !== "";')), $append), array_fill_keys($detach, ''));
	}
	
	/**
	 * Get the URL
	 * @param boolean $encode Encodes the URL
	 * @param array $append Add in the query string
	 * @param array $detach Remove from the query string
	 * @return string
	 */
	function url($encode=true, $append=array(), $detach=array()) {
		$inc = array();
		
		// puts the page number at the end of string
		if (!in_array('p', $detach)) {
			$detach[] = 'p';
			$inc['p'] = isset($append['p']) ? $append['p'] : $this->p;
		}
		
		$params = $this->params($append, $detach) + $inc;
		
		if ($this->params_prefix)
			$params = array($this->params_prefix => $params);
		
		return $this->url .'?'. http_build_query($params, $encode ? '&amp;' : '&');
	}
	
	/**
	 * @return int The total number of pages
	 */
	function numPages() {
		return ceil($this->rows / $this->rpp);
	}
	
	/**
	 * Checks whether pages are needed to list all results
	 * @return boolean
	 */
	function hasPages() {
		return $this->rows > $this->rpp;
	}
	
	/**
	 * Checks if the page exists in the range of pages
	 * @param int $p A page number
	 * @return boolean
	 */
	function hasPage($p) {
		return $p > 0 && $this->rows / $this->rpp >= $p;
	}
	
	/**
	 * Checks if exists a next results page
	 * @return boolean
	 */
	function hasNextPage() {
		return $this->hasPage($this->p + 1);
	}
	
	/**
	 * @return string|null The URL to the next results, or NULL
	 */
	function nextPage() {
		return $this->hasNextPage() ? $this->url(array('p' => $this->p + 1)) : null;
	}
	
	/**
	 * Checks if exists a previous results page
	 * @return boolean
	 */
	function hasPrevPage() {
		return $this->hasPage($this->p - 1);
	}
	
	/**
	 * @return string|null The URL to the previous results, or NULL 
	 */
	function prevPage() {
		return $this->hasPrevPage() ? $this->url(array('p' => $this->p + 1)) : null;
	}
	
	/**
	 * @param int $y
	 * @return string The mark-up tag representing the pagination
	 */
	function pagination($y=5) {
		if (!$this->rows)
			return '';
		
		$pages = $this->numPages();
		$i = $this->p - $y >= $y ? ($this->p > $pages - $y ? $pages - $y : $this->p - $y) : 1;
		$e = $pages > $this->p + $y ? $this->p + $y : $pages;
		
		// get the url without page number
		$url = substr($url = $this->url(), 0, strrpos($url, '=') + 1);
		
		$str = '';
		
		// previous page
		if ($this->p > 1)
			$str .= '<a class="prev" href="'.$url.($this->p - 1).'">&laquo; Previous</a>';
		
		for (; $i <= $e; $i++)
			$str .= '<a'.($this->p == $i ? ' class="curr"' : '').' href="'.$url.$i.'">'.$i.'</a>';
		
		// next page
		if ($this->p < $pages)
			$str .= '<a class="next" href="'.$url.($this->p + 1).'">Next &raquo;</a>';
		
		return $str;
	}
}

?>