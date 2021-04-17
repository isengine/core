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

class Pages extends Master {
	
	// кэширование страниц
	
	// если дата изменения файла больше даты изменения кэша, то файл кэшируется заново
	
	public function setCache($name = null) {
		
		$section = $this -> get('section');
		
		$path = $this -> parent['cache'] . 'pages' . DS . $this -> get('template') . DS . ($section ? 'sections' . DS . $section . DS : null);
		
		if ($name) {
			$path_route = $this -> parsePath($name);
		} else {
			$route = $this -> get('route');
			$path_route = System::typeIterable($route) ? Strings::join($route, DS) : 'index';
		}
		
		$this -> cache = $path . (!file_exists($path . $path_route) ? 'index' : $path_route) . '.php';
		
	}
	
	// адрес пути страницы
	
	public function parsePath($name = null) {
		if (!$name) {
			return null;
		}
		$array = Parser::fromString($name);
		return Strings::join($array, DS);
	}
	
	public function setPath($name) {
		
		$section = $this -> get('section');
		$route = $this -> get('route');
		
		$path = $this -> parent['path'] . 'html' . DS . ($section ? 'sections' . DS . $section . DS : 'inner' . DS);
		
		$path_index = 'index.php';
		$path_route = System::typeIterable($route) ? Strings::join($route, DS) . '.php' : $path_index;
		
		$this -> path = $path . (!file_exists($path . $path_route) ? $path_index : $path_route);
		
	}
	
	public function getPath() {
		if (!$this -> path) {
			$this -> setPath();
		}
		return $this -> path;
	}
	
	// загрузка страниц
	
	public function loadPath($name = null) {
		if (!$name) {
			$path = $this -> getPath();
		} else {
			$path = $this -> path . 'html' . DS . 'inner' . DS . $this -> parsePath($name) . '.php';
		}
		$this -> load($path);
	}
	
}

?>