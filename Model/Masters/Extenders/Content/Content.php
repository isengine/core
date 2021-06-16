<?php

namespace is\Masters\Extenders\Content;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Parents\Entry;
use is\Masters\Database;
use is\Components\Router;

class Content extends Entry {
	
	public function __construct() {
		
		// инициализация настроек
		
		$router = Router::getInstance();
		
		$name = $router -> content['name'];
		$last = Objects::last($router -> content['array'], 'value');
		$parents = Objects::unlast($router -> content['array']);
		$parents = System::typeIterable($parents) ? Strings::join($parents, ':+') : null;
		
		$db = Database::getInstance();
		$db -> collection('content');
		
		if ($last) {
			$db -> driver -> filter -> addFilter('name', '+' . $last);
			$db -> driver -> filter -> addFilter('parents', '+' . $name . ($parents ? ':+' . $parents : null));
			$db -> launch();
			$this -> setEntry($db -> data -> getFirst());
		}
		
		$db -> clear();
		
	}
	
}

?>