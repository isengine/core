<?php

namespace is\Masters\Extenders\Lang;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Parents\Data;
use is\Components\Language;

class Lang extends Data {
	
	public function __construct() {
		//$lang = Language::getInstance();
		//$this -> setData($lang -> getData());
	}
	
	public function get($data = null, $null = null) {
		if (!System::set($data)) { return null; }
		return Language::getInstance() -> get($data);
	}
	
	public function add($first = null, $second = null) {
		return Language::getInstance() -> addData($first, $second);
	}
	
}

//class Lang {
//	__construct() {
//	}
//}

?>