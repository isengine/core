<?php

namespace is\Masters\Extenders\Time;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Parents\Data;
use is\Components\Datetime;

class Time extends Data {
	
	public function __construct() {
	}
	
	public function get($data) {
		return Datetime::getInstance() -> getData($data);
	}
	
} 

?>