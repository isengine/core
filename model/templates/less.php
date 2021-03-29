<?php

namespace is\Model\Templates;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

class Less {
	
	public $less;
	
	// инициализация
	
	public function initLess($from, $to) {
		$this -> less = [];
		$this -> less['from'] = $from;
		$this -> less['to'] = $to;
	}
	
	public function less() {
		// проверка и рендеринг
		
		
		
		echo print_r($this -> less, 1);
		
	}
	
	
	
	
	
	
}

?>