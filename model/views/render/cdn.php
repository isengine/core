<?php

namespace is\Model\Views\Render;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\Paths;
use is\Helpers\Parser;

class Cdn extends Master {
	
	public function launch($name) {
		
		$type = Paths::parseFile($name, 'extension');
		
		if ($type === 'js') {
			return '<script src="' . $name . '"></script>';
		}
		
		return '<link href="' . $name . '" rel="stylesheet">';
		
	}
	
}

?>