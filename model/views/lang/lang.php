<?php

namespace is\Model\Views\Lang;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Model\Parents\Data;
use is\Model\Components\Language;

class Lang extends Data {
	
	public function __construct() {
		$lang = Language::getInstance();
		$this -> setData($lang -> getData());
	}
	
}

//class Lang {
//	__construct() {
//		$this = Language::getInstance();
//	}
//}

?>