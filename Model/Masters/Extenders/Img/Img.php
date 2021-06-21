<?php

namespace is\Masters\Extenders\Img;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Local;
use is\Parents\Data;
use is\Components\Language;

class Img extends Data {
	
	public function __construct() {
		//$lang = Language::getInstance();
		//$this -> setData($lang -> getData());
	}
	
	public function get($data = null, $null = null) {
		$real = System::server('root') . Strings::replace($data, ':', DS);
		$url = '/' . Strings::replace($data, ':', '/');
		return Local::matchFile($real) ? $url . '?' . filemtime($real) : null;
	}
	
}

?>