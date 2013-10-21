<?php
/**
 * Meta-template Field
 */
class MetaTemplateField {
	/**
	 * Field type
	 *
	 * Text: text, textarea, html
	 * Options: check, radio, select
	 * Specials: file, email, url, date, datetime
	 *
	 * @var string
	 */
	public $type = 'text';
	
	/**
	 * Field name
	 * @var string
	 */
	public $name;
	
	/**
	 * Field title
	 * @var string
	 */
	public $title;
	
	/**
	 * Field description
	 *
	 * @var string
	 */
	public $description;
	
	/**
	 * Value
	 * @var mixed
	 */
	public $value;
	
	/**
	 * Default value
	 * @var string
	 */
	public $default;
	
	/**
	 * Fill options
	 * @var array
	 */
	public $options = array();
	
	/**
	 * Multiple values
	 * @var boolean
	 */
	public $multiple = false;
	
	/**
	 * Mandatory
	 * @var boolean
	 */
	public $required = false;
	
	/**
	 * Minimum characters length
	 * @var int
	 */
	public $minlength = 0;
	
	/**
	 * Maximum characters length
	 * @var int
	 */
	public $maxlength = 0;
	
	/**
	 * Only positive numbers
	 * @var boolean
	 */
	public $unsigned = true;
	
	/**
	 * Regular expression for validation
	 * @var string
	 */
	public $regex;
	
	/**
	 * File extension
	 * @var array
	 */
	public $extension;
	
	/**
	 * Maximum image width
	 * @var int
	 */
	public $minwidth = 0;
	
	/**
	 * Minimum image width
	 * @var int
	 */
	public $maxwidth = 0;
	
	/**
	 * Maximum image height
	 * @var int
	 */
	public $minheight = 0;
	
	/**
	 * Minimum image height
	 * @var int
	 */
	public $maxheight = 0;
	
	/**
	 * Image ratio
	 * @var float
	 */
	public $ratio = .0;
	
	/**
	 * Enable/disable browser spell check
	 * @var boolean
	 */
	public $spellcheck = true;
	
	/**
	 * Displays character counter
	 * @var boolean
	 */
	public $charcount = false;
	
	/**
	 * Validation error
	 * @var string
	 */
	public $error;
}

?>