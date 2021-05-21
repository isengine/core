<?php

namespace is\Masters\Extenders\Special;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Parents\Data;

class Special extends Data {
	
	// этот класс управляет специальными страницами из структуры
	// они определены в настройках шаблона
	// и позволяют добавлять разные блоки в страницы
	// и классы стилей по названию группы таких страниц
	
	public function __construct() {
	}
	
	public function init($data) {
		$this -> setData($data);
	}
	
	public function search($name) {
		$type = [];
		foreach ($this -> getData() as $key => $item) {
			if (!System::typeIterable($item)) {
				continue;
			}
			if (Objects::match($item, $name)) {
				$type[] = $key;
			}
		}
		unset($key, $item);
		return $type;
	}
	
}

?>