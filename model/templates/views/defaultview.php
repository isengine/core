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

use lessc;
use Leafo\ScssPhp;

class DefaultView extends View {
	
	public $render;
	public $pages;
	public $detect;
	public $less;
	
	public function init($path = null) {
		parent::init($path);
		$this -> render = new Render;
		$this -> pages = new Pages;
		$this -> detect = new Detect;
		$this -> less = new Less;
	}

	
}

?>