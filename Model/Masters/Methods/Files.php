<?php

namespace is\Masters\Methods;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;

class Files extends Master {
	
	public function txt() {
		echo print_r($this -> getData(), 1);
	}
	
}

?>