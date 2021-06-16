<?php

namespace is\Masters\Extenders\Render;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\Parser;
use is\Helpers\Paths;

//use \MatthiasMullie\Minify;

class Folder extends Master {
	
	public function launch($name) {
		
		// name - папка, где путь разделен ':'
		
		$this -> to[1] = DS;
		$this -> url[1] = '/';
		
		$name = Parser::fromString($name, ['simple' => null]);
		
		if (!$name[1]) {
			$name[1] = $name[0];
		}
		
		$this -> from = $this -> from[0] . Strings::join($name[0], DS) . $this -> from[1];
		$this -> to = $this -> to[0] . Strings::join($name[1], DS) . $this -> to[1];
		$this -> url = $this -> url[0] . Strings::join($name[1], '/') . $this -> url[1];
		
		$this -> hash = (string) filemtime($this -> from);
		$hash = Local::readFile($this -> to . 'hash.md5');
		
		if (!$this -> hash || !$hash || $this -> hash !== $hash) {
			Local::createFolder($this -> to);
			$this -> rendering();
			Local::writeFile($this -> to . 'hash.md5', $this -> hash);
		}
		
		//return '<link rel="stylesheet" type="text/css" href="' . $this -> url . $this -> modificator() . '" />';
		
	}
	
	public function rendering() {
		
		// рендеринг
		// from - real путь, где лежит исходний файл
		// to - real путь, где будет лежать готовый файл
		
		$list = Local::search($this -> from, ['return' => 'files', 'subfolders' => true, 'merge' => true]);
		
		Objects::each($list, function($item){
			Local::createFolder($this -> to . $item['path']);
			Local::copyFile($item['fullpath'], $this -> to . $item['path'] . $item['name']);
		});
		
	}
	
}

?>