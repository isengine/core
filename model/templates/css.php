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

//use \MatthiasMullie\Minify;

class Css {
	
	public $render;
	
	public function launch($name, $folder) {
		
		// name - имя файла, без расширения и без пути
		// folder - промежуточная папка
		
		$render = &$this -> render;
		
		$from = $render -> getPrepare('from', $folder, $name . '.css');
		$to = $render -> getPrepare('to', $folder, $name . '.css');
		$url = $render -> getPrepare('url', $folder, $name . '.css');
		
		$render -> init($from, $to);
		$render -> setHash();
		
		if (!$render -> matchHash()) {
			if ($this -> rendering($from, $to)) {
				$render -> writeHash();
			}
		}
		
		return '<link rel="stylesheet" rev="stylesheet" type="text/css" href="' . $url . $render -> modificator() . '" />';
		
	}
	
	public function rendering($from, $to) {
		
		// рендеринг
		// from - real путь, где лежит исходний файл
		// to - real путь, где будет лежать готовый файл
		
		//$from = Paths::realToRelativeUrl($from);
		//$minifier = new Minify\CSS($from);
		//$minifier = new Minify\JS($from);
		//$minifier -> minify($to);
		
		if (!file_exists($from)) {
			return null;
		}
		Local::createFile($to);
		return Local::copyFile($from, $to);
		
	}
	
	public function set($object) {
		$this -> render = &$object;
	}
	
}

?>