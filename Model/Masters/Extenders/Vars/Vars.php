<?php

namespace is\Model\Masters\Extenders\Vars;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Model\Parents\Data;

class Vars extends Data {
	
	public function __construct() {
	}
	
	public function set($key, $value = null) {
		$this -> addDataKey($key, $value);
	}
	
	public function match($key, $value) {
		return $this -> getData($key) === $value;
	}
	
	public function is($key) {
		return System::set($this -> get($key));
	}
	
}

?>