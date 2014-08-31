<?php

class addonNeed {
	use traitMeta;
	
	public static function check($name, $value) {
		
		$method = 'check'.ucfirst($name);
		
		if(!method_exists(get_called_class(), $method)) {
			throw new Exception(sprintf(lang::get('addon_check_error'), __CLASS__, $method));
		}
		
		return self::$method($value);	
		
	}
	
	public static function checkVersion($version) {
		
		if(dyn::checkVersion(dyn::get('version'), $version) !== lang::get('version_fail_version')) {
			return true;	
		}
		
		return lang::get('addon_wrong_version', dyn::get('version'), $version);
		
	}
	
	public static function checkAddon($addons) {
		
		$return = '';
		
		foreach($addons as $name=>$version) {
			
			if(is_int($name)) {
				$name = $version;
				$version = false;
			}
		    if(isset(dyn::get('addons')[$name])) {
			    $config = dyn::get('addons')[$name];
           }
			// Nicht installiert
			if(!isset($config) || !is_array($config)) {
				$return .= lang::get('addon_not_found', $name);
				continue;
			}
			
			if(!addonConfig::isActive($name)) {				
				$return .= lang::get('addon_not_install_active', $name);
				continue;			
			}
			
			if(dyn::checkVersion($config['version'], $version) === false) {
				$return .=	lang::get('addon_need_version', $name, $version);
				continue;
			}
			
		}
		
		if($return == '') {
			return true;
		} else {
			return $return;	
		}
			
	}
	
}

?>