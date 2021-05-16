<?php

namespace is\Model\Masters\Extenders\Content;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Prepare;
use is\Model\Parents\Collection;
use is\Model\Masters\Database;
use is\Model\Components\Router;

use is\Model\Masters\Extenders\Process\Process;

class Content extends Collection {
	
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
		}
		$db -> driver -> filter -> addFilter('parents', '+' . $name . ($parents ? ':+' . $parents : null));
		$db -> launch();
		
		if (!$db -> data -> getFirstData()) {
			
			$db -> clear();
			
			$db -> collection('content');
			$db -> driver -> filter -> addFilter('parents', '+' . $name . ($parents ? ':+' . $parents : null) . ($last ? ':+' . $last : null));
			$db -> launch();
			
		}
		
		$this -> addByList($db -> data -> getData());
		
		$db -> clear();
		
	}
	
	
	
}

?>