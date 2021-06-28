<?php

namespace is\Masters\Extenders\Render;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Parents\Data;

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
	
	public function launch($type, $name, $reinit = null) {
		if (!$type) {
			return;
		}
		// вызов рендеринга
		// например, render('css', 'filename')
		$ri = System::typeIterable($reinit);
		//echo '<pre>' . print_r($this, 1);
		//echo '<pre>' . print_r($reinit, 1);
		$ns = __NAMESPACE__ . '\\' . Prepare::upperFirst($type);
		$render = new $ns(
			$ri && $reinit['from'] ? $reinit['from'] : $this -> from,
			$ri && $reinit['to'] ? $reinit['to'] : $this -> to,
			$ri && $reinit['url'] ? $reinit['url'] : $this -> url
		);
		return $render -> launch($name);
	}
	
}

?>