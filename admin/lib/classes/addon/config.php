<?php

class addonConfig {
	
	static $all = [];
	static $allConfig = [];
	
	public static function isSaved($addon, $save = true) {
	
		$sql = sql::factory();
		$num = $sql->num('SELECT 1 FROM '.sql::table('addons').' WHERE `name` = "'.$addon.'"');
		if(!$num && $save) {
			$save = sql::factory();
			$save->setTable('addons');
			$save->addPost('name', $addon);
			$save->save();
		}
		
		return $num;
		
	}
	
	public static function getAll() {
		
		if(!count(self::$all)) {	
		
			$sql = sql::factory();		
			$sql->query('SELECT name FROM '.sql::table('addons').' WHERE `install` = 1  AND `active` = 1')->result();
			while($sql->isNext()) {
				self::$all[] = $sql->get('name');
				$sql->next();		
			}
			
		}
				
		return self::$all;
		
	}
	
	public static function includeAllConfig() {
		
		$return = [];
		
		foreach(self::getAll() as $name) {
			$return[] = dir::addon($name, 'config.php');
		}
		
		return $return;
		
	}
	
	public static function includeAllLangFiles() {
		
		foreach(self::getAll() as $name) {
			
			$file = dir::addon($name, 'lang/'.lang::getLang().'.json');
			if(file_exists($file)) {				
				lang::loadLang($file);
			}
			
			$defaultFile = dir::addon($name, 'lang/'.lang::getDefaultLang().'.json');
			if(file_exists($defaultFile)) {				
				lang::loadLang($defaultFile, true);
			}
			
		}
		
	}
	
	public static function getConfig($name) {
	
		$configFile = dir::addon($name, 'config.json');
		
		if(file_exists($configFile)) {
			return json_decode(file_get_contents($configFile), true);
		}
		
		return false;
			
	}
	
	public static function getAllConfig() {
		
		if(!count(self::$allConfig)) {
		
			foreach(self::getAll() as $name) {
				self::$allConfig[$name] = self::getConfig($name);
			}
		
		}
		
		return self::$allConfig;
		
	}
	
	public static function includePage() {
		
		$page = type::super('page', 'string');
		
		foreach(self::getAllConfig() as $name=>$config) {
			
			if(!array_key_exists($page, $config['page'])) {
				continue;
			}
			
			foreach($config['need'] as $Needname=>$Needvalue) {
				$message = addonNeed::check($Needname, $Needvalue);
				if(is_string($message)) {
					echo message::warning('Das Addon '.$config['name'].' hat folgende Fehler:<br />'.$message, true);
					continue 2;
				}
				
			}
			
			return dir::addon($name, $config['page'][$page]);
			
		}
		
		return false;
		
	}
	
	public static function isActive($name) {
		
		return in_array($name, self::$all);
	}
	
}

?>