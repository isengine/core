<?php

namespace is\Masters\Extenders\Render;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\Paths;
use is\Helpers\Parser;

class Link extends Master {
	
	public function launch($name) {
		
		$file = DI . Paths::toReal($name);
		
		if (!file_exists($file)) {
			return;
		}
		
		$type = Paths::parseFile($name, 'extension');
		$mtime = filemtime($file);
		
		if ($type === 'js') {
			return '<script src="/' . $name . '?' . $mtime . '"></script>';
		}
		
		return '<link href="/' . $name . '?' . $mtime . '" rel="stylesheet">';
		
	}
	
}

?>