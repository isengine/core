<?php

namespace is\Model\Views\Template;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Model\Parents\Data;
use is\Model\Components\Router;
use is\Model\Databases\Database;

class Template extends Data {
	
	public function __construct() {
		
		// инициализация настроек
		
		$router = Router::getInstance();
		
		$db = Database::getInstance();
		
		$db -> collection('templates');
		$db -> driver -> filter -> addFilter('name', $router -> template['name']);
		$db -> launch();
		
		$this -> setData( $db -> data -> getFirstData() );
		
		$db -> clear();
		
	}
	
}

?>