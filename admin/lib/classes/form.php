<?php

// Klasse zu Erstellung für Formulare
class form {
	
	
	var $method;
	var $action;
	
	var $sql;
	
	var $mode = 'add';
	
	var $return = array();
	var $buttons = array();
	
	var $formAttributes = array();
	
	// Beim Senden schauen ob die Forumluar-Einträge schon übernommen worden sind Methode: setPostsVar
	var $isGetPosts = false;
	var $isSubmit;
	
	// Formular wirklich speichern
	var $toSave = true;
	
	public function __construct($table, $where, $action, $method = 'post') {
		
		// Gültige Methode?		
		if(!in_array($method, array('post', 'get'))) {
			// new Exception();
		}
		
		$this->method = $method;
		$this->action = $action;
		
		$sql = new sql();
		$this->sql = $sql->query('SELECT * FROM '.sql::table($table).' WHERE '.$where.' LIMIT 1');		
		
		$this->sql->result();
		
		if($this->sql->num() == 1) {
			$this->setMode('edit');
			$this->setWhere($where);
		}
		
		$this->setTable($table);
		
		$this->loadBackend();
		
		$this->addFormAttribute('class', 'form-horizontal');
		$this->addFormAttribute('action', $this->action);
		$this->addFormAttribute('method', $this->method);

		
	}
	
	
	// Ausgabe der SQL Spalte
	// Falls nicht drin, dann $default zurück
	public function get($value, $default = false) {
		
		// Falls per Post übermittelt
		if(isset($_POST[$value])) {
			
			return $_POST[$value];
			
		}
		
		if($this->sql->get($value))	{
		
			return $this->sql->get($value);
			
		}
		
		return $default;
		
	}
	
	public function setButtons() {
				
		$submit = $this->addSubmitField('save', lang::get('save'));
		$submit->addClass('btn');
		$submit->addClass('btn-default');
			
		$submit = $this->addSubmitField('save-back', lang::get('apply'));
		$submit->addClass('btn');
		$submit->addClass('btn-default');	
		
		$back = $this->addButtonField('back', lang::get('back'));
		$back->addClass('btn');
		$back->addClass('btn-warning');
		$back->addClass('form-back');
		
	}
	
	public function loadBackend() {
	
		$page = type::super('page', 'string');
		$subpage = type::super('subpage', 'string');
		
		$this->addHiddenField('page', $page);
		$this->addHiddenField('subpage', $subpage);
		
	}
	
	public function addFreeField($name, $value, $class, $attributes = array()) {
	
		return new $class($name, $value, $attributes);
		
	}
	
	// Ein Element hinzufügen
	private function addField($name, $value, $class, $attributes = array()) {
		
		$field = new $class($name, $value, $attributes);
		$this->return[$name] = $field;
		
		return $field;
		
	}
	
	public function addTextField($name, $value, $attributes = array()) {
		
		$attributes['type'] = 'text';
		return $this->addField($name, $value, 'formInput', $attributes);
		
	}
	
	public function addPasswordField($name, $value, $attributes = array()) {
		
		$attributes['type'] = 'password';
		return $this->addField($name, $value, 'formInput', $attributes);
		
	}
	
	public function addHiddenField($name, $value, $attributes = array()) {
		
		$attributes['type'] = 'hidden';
		return $this->addField($name, $value, 'formInput', $attributes);
				
	}
	
	public function addSubmitField($name, $value, $attributes = array(), $toButtons = true) {
		
		$attributes['type'] = 'submit';
		$field = $this->addFreeField($name, $value, 'formButton', $attributes);
		if($toButtons) {
			$this->buttons[] = $field;
		}
		return $field;
		
	}
	
	public function addButtonField($name, $value, $attributes = array(), $toButtons = true) {
		
		$attributes['type'] = 'button';
		$field = $this->addFreeField($name, $value, 'formButton', $attributes);
		if($toButtons) {
			$this->buttons[] = $field;
		}
		return $field;
		
	}
	
	public function addResetField($name, $value, $attributes = array(), $toButtons = true) {
		
		$attributes['type'] = 'reset';
		$field = $this->addFreeField($name, $value, 'formButton', $attributes);
		if($toButtons) {
			$this->buttons[] = $field;
		}
		return $field;
		
	}
	
	public function addTextareaField($name, $value, $attributes = array()) {
		
		return $this->addField($name, $value, 'formTextarea', $attributes);
		
	}
	
	public function addRadioField($name, $value, $attributes = array()) {
		
		return $this->addField($name, $value, 'formRadio', $attributes);
		
	}
	
	public function addCheckboxField($name, $value, $attributes = array()) {
		
		$field = $this->addField($name.'[]', $value, 'formCheckbox', $attributes);
		$field->setChecked($value);
		return $field; 
		
	}
	
	public function addSelectField($name, $value, $attributes = array()) {
		
		$field = $this->addField($name, $value, 'formSelect', $attributes);
		$field->setSelected($value);
		return $field;
		
	}
	
	public function addRawField($value) {
		
		return $this->addField('', $value, 'formRaw');
		
	}
	
	// Mode setzten
	public function setMode($mode) {
	
		if(in_array($mode, array('add', 'edit'))) {
			
			$this->mode = $mode;
				
		} else {			
			// new Exception();				
		}
		
	}
	
	// Post parameter Action setzten
	public function toAction($action) {
		
		$this->addHiddenField('action', $action);
		
	}
	
	// Ist Edit Mode?
	public function isEditMode() {
	
		return $this->mode == 'edit';
		
	}
	
	// Abfragen ob Formular abgeschickt
	public function isSubmit() {
		
		// Wurde schon isSubmit ausgeführt? dann schnelles Return
		if($this->isSubmit === true || $this->isSubmit === false) {
			return $this->isSubmit;
				
		}
		
		
		$save = false;
		$save_edit = false;
		
		$save = type::post('save', 'string', false);

		if($this->isEditMode()) {
			$save_edit = type::post('save-back', 'string', false);	
		}

		if($save !== false || $save_edit !== false) {
			
			if(!$this->isGetPosts) {
		
				$this->setPostsVar();
				$this->isGetPosts = true;
				
			}
			
			$this->isSubmit = true;
			
			return true;
				
				
		}
		
		$this->isSubmit = false;
		
		return false;
		
		
	}
	
	// Tabelle setzen
	public function setTable($table) {
	
		$this->sql->setTable($table);
			
	}
	
	// Where setzten
	public function setWhere($where) {
	
		$this->sql->setWhere($where);
		
	}
	
	public function setPostsVar() {
	
		foreach($this->return as $ausgabe) {
			
			if($ausgabe->getAttribute('type') == 'hidden') {
				continue;	
			}
			$name = $ausgabe->getName();
			
			if($name != '') {
				
				$val = type::post($name, 'string', '');
				
				// Ist ein Array-Element?
				if(substr($name, -2) == '[]') {						
							
					$name = substr($name , 0, -2);						
					
					$val = type::post($name, 'array');
					
					if(!empty($val)) {
						
						$val = '|'.implode('|', $val).'|';
						
					} else {
						
						$val = '';	
						
					}
					
				}
				
				$this->addPost($name,  $val);
			}
			
		}
		
	}
	
	public function addPost($name, $val) {
		
		$this->sql->addPost($name, $val);
		
	}
	
	public function delPost($name) {
		
		$this->sql->delPost($name);
		
	}
	
	public function setSave($bool) {
	
		if(!is_bool($bool)) {
			//throw new Exception;	
		}
		
		$this->toSave = $bool;
		
	}
	
	private function saveForm() {
	
		if(!$this->toSave)
			return;
	
		if($this->isEditMode()) {
			$this->sql->update();
		} else {
			$this->sql->save();
		}
		
	}
	
	public function isSaveEdit() {
	
		return (bool)(type::post('save-back', 'string', false) !== false);
		
	}
	
	public function addFormAttribute($name, $value) {
		
		$this->formAttributes[$name] = $value;	
		
	}
	
	public function deleteElement($name) {
	
		unset($this->return[$name]);
		
	}
	
	public function show() {
		
		$this->addHiddenField('action', $this->mode);
		
		if($this->isSubmit()) {
			
			$this->saveForm();
			
			if(!$this->isSaveEdit()) {
				//
				$GLOBALS['action'] = '';
				return;
			}
			
		}
		
		$return = '<form'.html_convertAttribute($this->formAttributes).'>'.PHP_EOL;
		
		
		$buttons_echo = '';
		
		foreach($this->return as $ausgabe) {
			
			if($ausgabe->getAttribute('type') == 'hidden') {
				
				$buttons_echo .= $ausgabe->get();
				
			} else {
				$ausgabe->addClass('form-control');
				
				$return .= '<div class="form-group">';
				$return .= '<label>'.$ausgabe->fieldName.'</label>';
				$return .= '<div class="form-wrap-input">'.$ausgabe->prefix . $ausgabe->get() . $ausgabe->suffix.'</div>';
				$return .= '</div>';
				
			}
			
		}		
		
		$this->setButtons();		
		
		foreach($this->buttons as $buttons) {
			$buttons_echo .= $buttons->get();	
		}
		
		$return .= '<div class="form-group">';
		$return .= '<label></label>';
		$return .= '<div class="form-wrap-input btn-group">'.$buttons_echo.'</div>';
		$return .= '</div>';
			
		$return .= '</form>';
		
		return $return;
		
	}
	
}

?>