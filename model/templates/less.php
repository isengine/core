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

use \Less_Parser;

class Less {
	
	public $render;
	
	public function launch($name, $folder) {
		
		// name - имя файла, без расширения и без пути
		// folder - промежуточная папка
		
		$render = &$this -> render;
		
		$from = $render -> getPrepare('from', $folder, $name . '.less');
		$to = $render -> getPrepare('to', $folder, $name . '.css');
		$url = $render -> getPrepare('url', $folder, $name . '.css');
		$url_folder = $render -> getPrepare('url', $folder);
		
		$render -> init($from, $to);
		$render -> setHash();
		
		if (!$render -> matchHash()) {
			$data = $this -> rendering($from, $url_folder);
			if ($data) {
				$render -> write($data);
				$render -> writeHash();
			}
			unset($data);
		}
		
		return '<link rel="stylesheet" rev="stylesheet" type="text/css" href="' . $url . $render -> modificator() . '" />';
		
	}
	
	public function rendering($from, $url) {
		
		// рендеринг
		// from - real путь, где лежит исходний файл
		// url - url-путь, абсолютный или относительный, для ссылки на файл
		
		$less = new Less_Parser(['compress' => true]);
		$less -> parseFile($from, $url);
		return $less -> getCss();
		
	}
	
	public function set($object) {
		$this -> render = &$object;
	}
	
}

?>