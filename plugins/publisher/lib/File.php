<?php
/**
 * File
 */
class File {
	/**
	 * File id
	 * @var int
	 */
	public $id;
	
	/**
	 * File name
	 * @var string
	 */
	public $name;
	
	/**
	 * File type
	 * @var string
	 */
	public $type;
	
	/**
	 * File size
	 * @var int
	 */
	public $size;
	
	/**
	 * File path
	 * @var string
	 */
	public $file;
	
	/**
	 * Temporary name
	 * @var string
	 */
	public $tmp_name;
	
	/**
	 * Date created
	 * @var string
	 */
	public $created;
	
	/**
	 * Saves the File
	 */
	function save() {
		global $db;
		
		// separates name and extension
		$finfo = preg_split('#\.*(\.[^\.]+)?$#', $this->name, 2, PREG_SPLIT_DELIM_CAPTURE);
		
		// normalizes extension
		$finfo[1] = strtolower($finfo[1]);
		
		$this->name = implode($finfo);
		
		// normalizes basename
		$finfo[0] = strtourl($finfo[0], false);
		
		$this->create_time = date('Y-m-d H:i:s', $time = time());
		
		$db->execute('LOCK TABLE file WRITE');
		$this->id = (int) $db->get('SHOW TABLE STATUS LIKE "file"')->Auto_increment;
		
		switch (UPLOAD_ORD) {
			case 'combinatory':
				$dir = permutationsr_rev($this->id);
				break;
			
			case 'date':
				$dir = date('Y/m/d', $time);
				break;
		}
		
		try {
			if (!is_dir(UPLOAD_DIR . $dir) && !@mkdir(UPLOAD_DIR . $dir, 0755, true))
				throw new Exception('');
			
			$this->file = "{$dir}/{$this->id}-";
			$this->file .= implode($finfo);
			
			if ($this->tmp_name) {
				if (!@move_uploaded_file($this->tmp_name, UPLOAD_DIR . $this->file))
					throw new Exception('Não foi possível enviar o arquivo');
			}
			
			$this->file = UPLOAD_URL . $this->file;
			
			$db->execute('UNLOCK TABLES');
			
			$db->execute("
				INSERT INTO file SET
					id = {$this->id},
					name = '{$this->name}',
					type = '{$this->type}',
					size = '{$this->size}',
					file = '{$this->file}',
					create_time = '{$this->create_time}'
			");
		}
		catch(Exception $e) {
			echo $e->getMessage();
			
			$db->execute('UNLOCK TABLES');
		}		
	}
}

?>