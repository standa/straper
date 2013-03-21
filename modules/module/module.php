<?php if (!defined('ROOT')) die('ROOT const not set.');

class Module {

	var $loaded_libs = array();

	function __construct() {

	}

	function name() {
		$name = strtolower(get_class());
		if (empty($name)) {
			$name =  strtolower(get_class($this));
			if (empty($name)) {
				$name = 'module';
			}
		} 
		return $name;		
	}

	/**
	 * execute a bash command and return the result of it
	 */
	function exec($cmd) {
		return shell_exec($cmd);
	}

	/**
	 * load library from libs directory
	 * loading from current module libs, from globals module libs
	 * or from the ROOT/libs dir
	 *
	 * @return boolean true if lib found, false otherwise
	 */
	function load_lib($name) {

		if ($this->lib_loaded($name)) {
			return true;
		}

		$class = $this->name();
		
		$candidates = array(
			ROOT.'/modules/'.$class.'/libs/'.$name.'.php', 
			ROOT.'/modules/module/libs/'.$name.'.php',
			ROOT.'/libs/'.$name.'.php'
		);
		foreach ($candidates as $c) {
			if (file_exists($c)) {
				require_once $c;
				$this->loaded_libs[] = $name;
				return true;
			}
		}
		die ('Library '.$name.' was not found.');
	}

	function lib_loaded($name) {
		return in_array($name, $this->loaded_libs);
	}
	
	/**
	 * parse html and return the simplehtmldom object
	 * http://simplehtmldom.sourceforge.net/manual.htm
	 *
	 * use jquery syntax to traverse the object
	 *
	 * @return simple_html_dom object
	 */
	function simple_html_dom($html) {
		
		$this->load_lib('simple_html_dom');

		$obj = str_get_html($html);

		return $obj;
	}


	/**
	 * simple filesystem cache implementation
	 */
	function cache($key, $value = '') {

		$key = preg_replace( '/[^[:print:]]/', '_', $key);
		
		$cache_dir = ROOT.'/cache';

		
		if (!is_dir($cache_dir) || !is_writable($cache_dir)) {
			echo 'Cache dir not exists or not writable!';
			exit;
		}

		$candidate = $cache_dir.'/'.$this->name().'-'.$key;

		if (file_exists($candidate)) {
			if (empty($value)) {
				return file_get_contents($candidate);
			} else {
				file_put_contents($candidate, $value);
			}
		} else {
			if (empty($value)) {
				return false;
			} else {
				file_put_contents($candidate, $value);	
			}
		}

	}

	function snoopy() {
		$this->load_lib('snoopy');		
		return new Snoopy();
	}

}