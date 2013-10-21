<?php
/**
 * Saves the content
 * @param MetaTemplate $metatemplate Content meta-template
 * @param Content $content The Content
 * @param boolean $ref
 * @return boolean
 */
function publisher_content_save(MetaTemplate $metatemplate, Content $content, $ref=false) {
	$pass = true;
	
	// checks for errors
	foreach ($metatemplate->modules as $module) {
		$ms = isset($content->content[$module->name]) ? 
				(is_numeric(key($content->content[$module->name])) ? 
					$content->content[$module->name] : array($content->content[$module->name])) : 
				array(array());
		
		foreach ($ms as $m) {
			foreach ($module->groups as $group) {
				$gs = isset($m[$group->name]) ? 
						(is_numeric(key($m[$group->name])) ? 
							$m[$group->name] : array($m[$group->name])) : 
						array(array());
				
				foreach ($gs as $g) {
					foreach ($group->fields as $field) {
						$fs = isset($g[$field->name]) ? 
								(is_array($g[$field->name]) && is_numeric(key($g[$field->name])) ? 
									$g[$field->name] : array($g[$field->name])) : 
								array(array());
						
						foreach ($fs as $value) {
							if ($value === '') {
								if ($field->required)
									$field->error = 'Obrigatório';
							}
							elseif ($field->minlength && $field->minlength > strlen($value))
								$field->error = "Preenchimento mínimo de {$field->minlength} caracteres";
							elseif ($field->maxlength && strlen($value) > $field->maxlength)
								$field->error = "Preenchimento máximo de {$field->maxlength} caracteres";
							elseif ($field->regex && !preg_match("#{$field->regex}#", $value))
								$field->error = "Valor inválido";
							else {
								switch ($field->type) {
									case 'email':
										if (!is_email($value))
											$field->error = 'Endereço de e-mail inválido';
										break;
									
									case 'number':
										if (!is_numeric($value))
											$field->error = 'Valor inválido';
										elseif ($field->unsigned && 0 > $value)
											$field->error = 'Apenas número positivos';
										else
											$value = (float) $value;
										break;
									
									case 'check':
										$options = array();
										foreach ($field->options as $option) {
											
											if (is_array($option))
												$option = isset($option['value']) ? $option['value'] : $option[0];
											
											$options[] = $option;
										}
										
										$values = is_array($value) ? $value : (array) $value;
										
										if ($diff = array_diff($values, $options))
											$field->error = 'Valor(es) inválido(s): '. implode($diff);
										
										break;
										
									case 'radio':
									case 'select':
										$match = false;
										foreach ($field->options as $option)
											if (is_string($option) && $option == $value || isset($option[0]) && $option[0] == $value || isset($option['value']) && $option['value'] == $value) {
												$match = true;
												break;
											}
										
										if (!$match)
											$field->error = 'Valor inválido';
										
										break;
								}
							}
							
							// dev
							if ($field->error)
								$pass = false;
						}
					}
				}
			}
		}
	}
	
	// saves only if there are errors in the content
	if ($pass) {
		global $db, $metatemplates;
		
		$title = current((array) array_key_value('title', $content->content));
		$content->created = date('Y-m-d H:i:s');
		$changed = date('Y-m-d H:i:s');
		
		if (!$ref) {
			$datajson = $db->escape_string(json_encode($content->content));
			
			// new
			if (!$content->id) {
				$db->execute("
					INSERT INTO content SET
						status = {$content->status},
						version = {$content->version},
						content = '{$datajson}',
						created = '{$content->created}'
				");
				
				// gets the new content id
				$content->id = $db->insert_id;
			}
			// update
			else {
				$db->execute("
					INSERT INTO content SET
						id = '{$content->id}',
						status = {$content->status},
						version = {$content->version},
						content = '{$datajson}',
						created = '{$content->created}'
				");
			}
		}
		
		// public index
		publisher_content_index($metatemplate, $content);
		
		// admin index
		$db->execute("
			REPLACE content_index SET
				portal = '{$metatemplate->portal}',
				station = '{$metatemplate->station}',
				channel = '{$metatemplate->channel}',
				content_id = {$content->id},
				status = {$content->status},
				title = '{$title}',
				created = '{$content->created}'
		");
		
		// publish / export
		if ($content->version == PUBLISHER_CONTENT_VERSION) {
			publisher_content_publish($metatemplate, $content);
		}
	}
	
	return $pass;
}

?>