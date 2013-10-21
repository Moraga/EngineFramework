<?php
/**
 * DB
 */
class DB extends mysqli {
	/**
	 * Current connection
	 * @var DB
	 */
	private static $instance;
	
	/**
	 * Open a new connection to the MySQL server
	 * @param string $host Host name or an IP address
	 * @param string $user The MySQL user name
	 * @param string $pass The MySQL user password
	 * @param string $db If provided will specify the default database to be used when performing queries
	 * @param string $port Specifies the port number to attempt to connect to the MySQL server
	 */
	function __construct($host=DB_HOST, $user=DB_USER, $pass=DB_PASS, $db=DB_NAME, $port=DB_PORT) {
		parent::__construct($host, $user, $pass, $db, $port);
	}
	
	/**
	 * Performs a query on the database
	 * @param string $query The query string
	 */
	function execute($query) {
		//return parent::query($query);
		return parent::query(DB_BIND ? preg_replace('#`([^`]+)`#', '`'. DB_BIND .'\1`', $query) : $query);
	}
	
	/**
	 * Performs a query and fetch first result
	 * @param string $query The query string
	 * @param string $class_name The name of the class to instantiate
	 * @return mixed The object, or FALSE on failure
	 */
	function get($query, $class_name='stdClass') {
		return ($result = $this->execute($query)) ? ($class_name ? $result->fetch_object($class_name) : $result->fetch_assoc()) : false;
	}
	
	/**
	 * Performs a query and fetch results returning an array containg all fetched rows
	 * @param string $query The query string
	 * @param string $class_name The name of the class to instantiate
	 * @return array An array containing all fetched rows
	 */
	function all($query, $class_name='stdClass') {
		$rows = array();
		if ($result = $this->execute($query))
			while ($row = $result->fetch_object($class_name))
				$rows[] = $row;
		return $rows;
	}
	
	/**
	 * Gets the current DB connection
	 * If there is no connection, one will be created
	 * @return DB
	 */
	static function cursor() {
		if (!self::$instance)
			self::$instance = new self;
		return self::$instance;
	}
}

?>