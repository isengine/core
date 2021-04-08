<?php

namespace is\Model\Views\Layout;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;
use is\Helpers\Prepare;
use is\Model\Parents\Data;

class Layout extends Data {
	
	// общие установки кэша
	
	public $blocks;
	public $pages;
	
	public function __construct() {
	}
	
	public function init($type, $path, $cache, $caching = 'skip') {
		
		$name = __NAMESPACE__ . '\\' . (Prepare::upperFirst($type));
		$this -> $type = new $name;
		
		$this -> $type -> parent = [
			'path' => $path,
			'cache' => $cache
		];
		
		if ($caching !== 'skip') {
			$this -> $type -> caching($caching);
		}
		
	}
	
	public function launch($type, $name, $cache = 'skip') {
		$this -> $type -> includes($name, $cache);
	}
	
	public function clear($type) {
		Local::eraseFolder($this -> $type -> cache);
	}
	
	public function reset($type) {
		$this -> $type = null;
	}
	
}

?>