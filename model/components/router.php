<?php

namespace is\Model\Components;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Match;
use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Paths;
use is\Model\Components\Structure;

use is\Model\Parents\Globals;

class Router extends Globals {
	
	public $structure;
	public $current;
	public $template;
	
	public function init() {
		
		$this -> structure = new Structure;
		
		$this -> template = [
			'name' => null,
			'section' => null
		];
		
	}
	
	public function setStructure($data) {
		$this -> structure -> addByList($data);
	}
	
	public function getStructure() {
		return $this -> structure -> getData();
	}
	
	public function parseStructure($array) {
		$this -> structure -> structure($array);
	}
	
	public function addExtension($data = null) {
		if ($data) {
			$this -> structure -> extension = $data;
		}
	}
	
}

?>