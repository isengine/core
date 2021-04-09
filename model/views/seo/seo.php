<?php

namespace is\Model\Views\Seo;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Model\Parents\Data;
use is\Model\Databases\Database;
use is\Model\Components\Router;

class Seo extends Data {
	
	public function __construct() {
		
		// инициализация настроек
		
		$router = Router::getInstance();
		
		$db = Database::getInstance();
		
		$db -> collection('seo');
		$db -> driver -> filter -> addFilter('name', 'default');
		$db -> driver -> filter -> addFilter('type', 'settings');
		$db -> launch();
		
		$this -> setData( $db -> data -> getFirstData() );
		
		$db -> clear();
		
		$page = System::typeClass($router -> current, 'entry') ? $router -> current -> getEntryData('name') : 'index';
		
		if ($page) {
			$db -> driver -> filter -> addFilter('name', '+' . $page);
			$db -> launch();
			
			$this -> mergeData( $db -> data -> getFirstData() );
			
			$db -> clear();
		}
		
	}
	
}

?>