<?php

namespace is\Model\Views\Render;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Model\Parents\Data;

class Render extends Data {
	
	public $from;
	public $to;
	public $url;
	
	public function __construct() {
	}
	
	public function init($from, $to, $url) {
		$this -> from = $from;
		$this -> to = $to;
		$this -> url = $url;
	}
	
	public function launch($type, $name) {
		// вызов рендеринга
		// например, render('css', 'filename')
		$ns = __NAMESPACE__ . '\\' . Prepare::upperFirst($type);
		$render = new $ns(
			$this -> from,
			$this -> to,
			$this -> url
		);
		return $render -> launch($name);
	}
	
}

?>