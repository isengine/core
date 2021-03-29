<?php

namespace is\Model\Templates\Views;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

use is\Model\Templates\View;
use is\Model\Templates\Pages;
use is\Model\Templates\Render;
use is\Model\Templates\Detect;
use is\Model\Templates\Less;
use is\Model\Templates\Scss;
use is\Model\Templates\Css;
use is\Model\Templates\Js;

class DefaultView extends Pages {
	
	public $render;
	public $detect;
	public $less;
	public $scss;
	public $css;
	public $js;
	
	public function init($path = null) {
		parent::init($path);
		$this -> render = new Render;
		$this -> detect = new Detect;
		$this -> less = new Less;
		$this -> scss = new Scss;
		$this -> css = new Css;
		$this -> js = new Js;
	}
	
	public function prepare($from, $to, $url) {
		$this -> render -> setPrepare($from, $to, $url);
	}
	
	public function less($name, $folder) {
		
		$this -> less -> set($this -> render);
		$result = $this -> less -> launch($name, $folder);
		
		return $result ? $result : null;
		
	}
	
	public function scss($name, $folder) {
		
		$this -> scss -> set($this -> render);
		$result = $this -> scss -> launch($name, $folder);
		
		return $result ? $result : null;
		
	}
	
	public function css($name, $folder) {
		
		$this -> css -> set($this -> render);
		$result = $this -> css -> launch($name, $folder);
		
		return $result ? $result : null;
		
	}
	
	public function js($name, $folder) {
		
		$this -> js -> set($this -> render);
		$result = $this -> js -> launch($name, $folder);
		
		return $result ? $result : null;
		
	}
	
}

?>