<?php

namespace is\Masters\Extenders\Content;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Parents\Entry;
use is\Parents\Data;
use is\Masters\Database;
use is\Components\Router;
use is\Components\Cache;
use is\Components\Config;

class Content extends Entry {
	
	public function __construct() {
		
		// инициализация настроек
		
		$router = Router::getInstance();
		
		$name = $router -> content['name'];
		$parents = $router -> content['parents'];
		
		$db = Database::getInstance();
		$db -> collection('content');
		
		if ($name) {
			
			$db -> driver -> filter -> addFilter('name', '+' . $name);
			
			if (System::typeIterable($parents)) {
				$db -> driver -> filter -> addFilter('parents', '+' . Strings::join($parents, ':+'));
			}
			
			$db -> launch();
			
			if ($db -> data -> count()) {
				$this -> setEntry($db -> data -> getFirst());
			}
			
			// сюда можно было бы добавить еще условия проверки,
			// является ли имя родительской категорией
			// типа !count ... addFilter(parents, +names:+parents) ... launch ...
			// но это не имеет смысла,
			// т.к. либо не будет материала,
			// либо это будет категория со множеством материалов
			// и в любом случае запись останется пустой
			
		}
		
		$db -> clear();
		
	}
	
}

?>