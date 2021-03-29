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

use \ScssPhp\ScssPhp\Compiler;
use \ScssPhp\ScssPhp\OutputStyle;

class Scss {
	
	public $render;
	
	public function launch($name, $folder) {
		
		// name - имя файла, без расширения и без пути
		// folder - промежуточная папка
		
		$render = &$this -> render;
		
		$from = $render -> getPrepare('from', $folder, $name . '.scss');
		$from_folder = $render -> getPrepare('from', $folder);
		$to = $render -> getPrepare('to', $folder, $name . '.css');
		$url = $render -> getPrepare('url', $folder, $name . '.css');
		$url_folder = $render -> getPrepare('url', $folder);
		
		$render -> init($from, $to);
		$render -> setHash();
		
		if (!$render -> matchHash()) {
			$data = $this -> rendering($from_folder, $name);
			if ($data) {
				$render -> write($data);
				$render -> writeHash();
			}
			unset($data);
		}
		
		return '<link rel="stylesheet" rev="stylesheet" type="text/css" href="' . $url . $render -> modificator() . '" />';
		
	}
	
	public function rendering($path, $name) {
		
		// рендеринг
		$scss = new Compiler();
		$scss -> setImportPaths($path);
		$scss -> setFormatter('\ScssPhp\ScssPhp\Formatter\Expanded');
		$scss -> setOutputStyle(OutputStyle::COMPRESSED);
		return $scss -> compile('@import "' . $name . '.scss";');
		
	}
	
	public function set($object) {
		$this -> render = &$object;
	}
	
}

?>