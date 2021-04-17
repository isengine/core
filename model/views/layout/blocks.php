<?php

namespace is\Model\Views\Layout;

use is\Helpers\Sessions;
use is\Helpers\Parser;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Local;
use is\Helpers\System;
use is\Helpers\Match;
use is\Helpers\Paths;

class Blocks extends Master {
	
	// кэширование блоков
	
	// если дата изменения файла больше даты изменения кэша, то файл кэшируется заново
	
	public function setCache($name) {
		$array = $this -> parsePath($name);
		$this -> cache = $this -> parent['cache'] . 'blocks' . DS . $array[0] . DS . $array[1] . '.php';
	}
	
	// адрес пути блока
	
	public function parsePath($name = null) {
		if (!$name) {
			return null;
		}
		$array = Parser::fromString($name);
		if (!$array[1]) {
			$array[1] = $array[0];
			$array[0] = $this -> get('template');
		}
		return $array;
	}
	
	public function setPath($name) {
		$array = $this -> parsePath($name);
		$parent = Paths::toReal(Paths::parent($this -> parent['path']));
		$this -> path = $parent . $array[0] . DS . 'html' . DS . 'blocks' . DS . $array[1] . '.php';
	}
	
	public function getPath($name = null) {
		if ($name) {
			$this -> setPath($name);
		} elseif (!$this -> path) {
			return null;
		}
		return $this -> path;
	}
	
	// загрузка блоков
	
	public function loadPath($name) {
		$path = $this -> getPath($name);
		$this -> load($path);
	}
	
}

?>