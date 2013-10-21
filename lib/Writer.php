<?php
/**
 * Written object
 */
class Written {
	/**
	 * Container of errors
	 * @var array
	 */
	public $__e = array();
	
	/**
	 * Saves the object
	 * @param boolean $checkError Determines whether errors should be checked
	 * @param string $prefix Writer class prefix. Defaults to DB
	 * @return boolean TRUE on success or FALSE on failure
	 */
	function save($checkError=true, $prefix='DB') {
		$writerClass = get_class($this);
		$writerClass = (($lp = strrpos($writerClass, '_')) !== false) ?
			substr($writerClass, 0, $lp + 1) . $prefix . substr($writerClass, $lp + 1) . 'Writer' :
			$prefix . $writerClass . 'Writer';
		$writer = new $writerClass;
		$writer->attach($this);
		if ($checkError && $writer->hasError())
			return false;
		$writer->write();
		return true;
	}
	
	/**
	 * Get or set error
	 * @param string $key Error key
	 * @param mixed $value Optional error value
	 * @return mixed The error, or FALSE
	 */
	function err($key, $value=null) {
		if ($value === null)
			return isset($this->__e[$key]) ? $this->__e[$key] : false;
		else
			$this->__e[$key] = $value;
	}
	
	function e($key, $format=null, $echo=true) {
		// default format
		if ($format === null)
			$format = '<p class="err">%s</p>';
		
		$echo = $echo ? 'printf' : 'sprintf';
		
		// get first error
		if ($key === true)
			$key = key($this->__e);
		
		if (isset($this->__e[$key]))
			return $echo($format, $this->__e[$key]);
	}
}

/**
 * Abstract Writer
 */
abstract class Writer {
	/**
	 * Container of Written objects
	 * @var array
	 */
	public $objects = array();
	
	/**
	 * Container of Written objects that failed verification
	 * @var array
	 */
	public $invalids = array();
	
	/**
	 * Adds a Written object
	 * @param Written $object The Written object
	 */
	function attach(Written $object) {
		$this->objects[] = $object;
	}
	
	/**
	 * Removes a Written object
	 * @param Written $object The Written object
	 */
	function detach(Written $object) {
		$objects = array();
		foreach ($this->objects as $i)
			if ($i !== $object)
				$object[] = $i;
		$this->objects = $objects;
	}
	
	/**
	 * Checks for errors in the objects in the container.
	 * The objects that have errors are transferred to invalid's container
	 * @return boolean TRUE if any error, FALSE otherwise
	 */
	function hasError() {
		$objects = array();
		foreach ($this->objects as $i) {
			$this->verify($i);
			if (!$i->__e)
				$objects[] = $i;
			else
				$this->invalids[] = $i;
		}
		$this->objects = $objects;
		return !!$this->invalids;
	}
	
	/**
	 * Writes the objects of the container
	 */
	function write() {
		while (list(, $object) = each($this->objects))
			$this->save($object);
	}
	
	/**
	 * Abstract method of verification
	 * @param Written $object A Written object
	 */
	abstract function verify(Written $object);
	
	/**
	 * Abstract method of writing
	 * @param Written $object A Written object
	 */
	abstract function save(Written $object);
}

?>