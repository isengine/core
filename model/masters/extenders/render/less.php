<?php

namespace is\Model\Masters\Extenders\Render;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

use \Less_Parser;

class Less extends Master {
	
	public function launch($name) {
		
		// name - имя файла, без расширения и без пути
		
		$url_folder = $this -> getPathByKey('url', 'less');
		
		$this -> setPathByKey('from', 'less', $name . '.less');
		$this -> setPathByKey('to', 'less', $name . '.css');
		$this -> setPathByKey('url', 'less', $name . '.css');
		
		$this -> setHash();
		
		if (!$this -> matchHash()) {
			$data = $this -> rendering($url_folder);
			if ($data) {
				$this -> write($data);
				$this -> writeHash();
			}
			unset($data);
		}
		
		return '<link rel="stylesheet" rev="stylesheet" type="text/css" href="' . $this -> url . $this -> modificator() . '" />';
		
	}
	
	public function rendering($url) {
		
		// рендеринг
		// from - real путь, где лежит исходний файл
		// url - url-путь, абсолютный или относительный, для ссылки на файл
		
		$less = new Less_Parser(['compress' => true]);
		$less -> parseFile($this -> from, $url);
		return $less -> getCss();
		
	}
	
}

?>