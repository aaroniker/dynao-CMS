<?php

class formCheckbox extends formField {
	
	var $output = array();
	
	public function setChecked($checked) {
		
		if(!is_array($checked)) {
			$checked = explode('|', $checked);
		}
		
		$checked = array_flip($checked);
		
		$this->value = $checked;
		
		return $this;
		
	} 
	
	public function add($name, $value, $attributes = array()) {
		
		$attributes['type'] = 'checkbox';
		$attributes['value'] = $name;
		$attributes['name'] = $this->name.'[]';	
		
		if(isset($this->value[$attributes['value']]))
			$attributes['checked'] = 'checked';
			
		$this->output[$attributes['value']] = array('value'=>$value, 'attr'=>$attributes); //Name als Key speicher, für Methode del();		
		
		return $this;
			
	}
	
	public function del($name) {	
		
		unset($this->output[$name]);
		
		return $this;
			
	}
	
	public function get() {
		
		$return = '';
		foreach($this->output as $val) {
			$return .= '<label class="checkbox-inline"><input'.$this->convertAttr($val['attr']).'> '.$val['value'].'</label>';
		}
		
		return $return;
		
	}
	
	
	
}


?>