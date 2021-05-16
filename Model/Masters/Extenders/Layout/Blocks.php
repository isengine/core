<?php

namespace is\Model\Masters\Extenders\Layout;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

class Blocks extends Master {
	
	public function setCache() {
		return $this -> paths['cache'] . $this -> template . DS . 'blocks' . DS . $this -> name . '.php';
	}
	
	public function setPath() {
		return $this -> paths['base'] . $this -> template . DS . 'html' . DS . 'blocks' . DS . $this -> name . '.php';
	}
	
}

?>