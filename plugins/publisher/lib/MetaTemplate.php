<?php
/**
 * Meta-template
 */
class MetaTemplate {
	/**
	 * Portal
	 * @var string
	 */
	public $portal;
	
	/**
	 * Station
	 * @var string
	 */
	public $station;
	
	/**
	 * Channel
	 * @var string
	 */
	public $channel;
	
	/**
	 * Title
	 * @var string
	 */
	public $title;
	
	/**
	 * Keywords
	 * @var string
	 */
	public $keywords;
	
	/**
	 * Media
	 * @var Media
	 */
	public $media;
	
	/**
	 * Override
	 * @var boolean
	 */
	public $override = false;
	
	/**
	 * Export
	 * @var array
	 */
	public $export = array();
	
	/**
	 * Modules
	 * @var array
	 */
	public $modules = array();
	
	function name() {
		
	}
	
	/**
	 * @return string
	 */
	function breadcrumbs() {
		return str_replace(' >> ', ' <span>&raquo;</span> ', implode(' >> ', array_filter(array($this->portal, $this->station, $this->channel, $this->title))));
	}
}

?>