<?php

namespace is\Masters\Extenders\Render;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\Paths;
use is\Helpers\Parser;

//use \MatthiasMullie\Minify;

class Font extends Master {
	
	public function launch($name) {
		
		$item = Parser::fromString($name);
		
		$print = '<link href="https://fonts.googleapis.com/css2?family=' . Strings::replace($item[0], ' ', '+');
		
		if ($item[1]) {
			$item[1] = Strings::split($item[1], ';, ');
			$print .= ':ital,wght@' .
				'0,' . Strings::join($item[1], ';0,') . ';' .
				'1,' . Strings::join($item[1], ';1,') . ';';
		}
		
		if ($item[2]) {
			$print .= '&subset=' . $item[2];
		}
		
		$print .= '&display=swap" rel="stylesheet">';
		
		return $print;
		
	}
	
}

?>