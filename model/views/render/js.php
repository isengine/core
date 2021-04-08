<?php

namespace is\Model\Views\Render;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\Paths;

//use \MatthiasMullie\Minify;

class Js extends Master {
	
	public function launch($name) {
		
		// name - имя файла, без расширения и без пути
		// folder - промежуточная папка
		
		$this -> setPath('js', $name . '.js');
		
		$this -> setHash();
		
		if (!$this -> matchHash()) {
			if ($this -> rendering()) {
				$this -> writeHash();
			}
		}
		
		return '<script type="text/javascript" src="' . $this -> url . $this -> modificator() . '"></script>';
		
	}
	
	public function rendering() {
		
		// рендеринг
		// from - real путь, где лежит исходний файл
		// to - real путь, где будет лежать готовый файл
		
		//$from = Paths::realToRelativeUrl($from);
		//$minifier = new Minify\CSS($from);
		//$minifier = new Minify\JS($from);
		//$minifier -> minify($to);
		
		if (!file_exists($this -> from)) {
			return null;
		}
		Local::createFile($this -> to);
		return Local::copyFile($this -> from, $this -> to);
		
	}
	
}

?>